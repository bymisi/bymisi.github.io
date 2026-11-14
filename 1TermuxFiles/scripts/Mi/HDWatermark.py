#!/usr/bin/env python3
"""Burn a watermark (recording date + custom text) onto videos in Termux.

Pick videos from a numbered list, type the text once, and the script
re-encodes each video with ffmpeg while showing a progress bar.

Requirements:
  pkg install python ffmpeg ttf-dejavu
  (optional, only for the popup dialog: pkg install termux-api
   and the Termux:API app)

Usage:
  python watermark.py              -> lists the videos in the current folder
  python watermark.py /path/to/dir -> lists the videos in the given folder
"""
import json
import os
import re
import shutil
import subprocess
import sys
import tempfile
import time
from datetime import date, datetime

# ---------------------------------------------------------------- settings
FONT = "/data/data/com.termux/files/usr/share/fonts/TTF/DejaVuSans.ttf"
DEFAULT_TEXT = "BY_[MiSi], "  # you type the city and country after this
MONTHS = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN",
          "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"]
DATE_SEPARATOR = " "          # placed between the date and your text
FONT_COLOR = "lightgreen"    # any ffmpeg color name or #RRGGBB hex code
OUTPUT_SUFFIX = "_watermark"  # video.mp4 -> video_watermark.mp4
USE_POPUP = False             # True: termux-dialog popup (needs Termux:API)
VIDEO_EXTENSIONS = (".mp4", ".mov", ".mkv", ".3gp", ".webm", ".avi")


# ------------------------------------------------------------------- dates
def recording_date(path):
    """Return the day the video was recorded.

    Sources, in order: 1) the file name (e.g. VID_20261031_...),
    2) the video's metadata (creation_time), 3) the file's modified time.
    """
    name = os.path.basename(path)
    match = re.search(r"(?<!\d)(20\d{2})(0[1-9]|1[0-2])([0-2]\d|3[01])(?!\d)",
                      name)
    if match:
        try:
            return date(int(match.group(1)), int(match.group(2)),
                        int(match.group(3)))
        except ValueError:
            pass  # e.g. 20261231 is fine, but 20260231 is not a real date

    try:
        result = subprocess.run(
            ["ffprobe", "-v", "error", "-show_entries",
             "format_tags=creation_time", "-of", "default=nw=1:nk=1", path],
            capture_output=True, text=True, timeout=20)
        created = result.stdout.strip()
        if created:
            # Metadata is stored in UTC; convert to the local time zone.
            return datetime.fromisoformat(
                created.replace("Z", "+00:00")).astimezone().date()
    except Exception:
        pass

    return date.fromtimestamp(os.path.getmtime(path))


def format_date(day):
    """Format a date like NOV-07-2026."""
    return f"{MONTHS[day.month - 1]}-{day.day:02d}-{day.year}"


# ------------------------------------------------------------------- input
def ask_watermark_text():
    """Ask for the watermark text in the terminal (default is editable).

    Returns the text, "" if it was left empty (no text), or None if the
    user cancelled.
    """
    if USE_POPUP:
        try:
            result = subprocess.run(
                ["termux-dialog", "text", "-t", "Watermark text",
                 "-i", DEFAULT_TEXT],
                capture_output=True, text=True, timeout=30)
            data = json.loads(result.stdout)
            if data.get("code") != -1:
                return None  # the user cancelled
            return data.get("text", "").strip()
        except Exception:
            print("(The popup did not appear, asking here instead.)")

    print("\nWatermark text (the date is added in front automatically).")
    print("Type the city and country, then press Enter (empty = no text):")

    # readline lets us pre-fill the input line so the default is editable.
    try:
        import readline
        readline.set_startup_hook(lambda: readline.insert_text(DEFAULT_TEXT))
        hint = ""
    except ImportError:
        readline = None
        hint = f" [{DEFAULT_TEXT}]"

    try:
        text = input("> " + hint).strip()
    except EOFError:
        return None
    finally:
        if readline:
            readline.set_startup_hook()

    if not text and readline is None:
        text = DEFAULT_TEXT
    return text


def ask_date_mode():
    """Ask whether to use the automatic date or a manually typed one.

    Returns None for the automatic date, or the typed date text (used
    exactly as typed) for all selected videos. An empty string means
    "no date at all" (only your text goes on the watermark).
    """
    print("\nDate in the watermark:")
    print("  A = automatic (recording date of each video)")
    print("  M = manual (you type any date, or leave it empty for no date)")
    while True:
        answer = input("A / M [A]: ").strip().lower()
        if answer in ("", "a"):
            return None
        if answer == "m":
            return input("Type the date (empty = no date): ").strip()
        print("Invalid input. Type A or M.")


def ask_resolution():
    """Ask for the output resolution.

    Returns None to keep the original size (the default), or the size of
    the video's SHORTER side in pixels (720 means 1280x720 landscape or
    720x1280 portrait). Videos are never enlarged.
    """
    print("\nOutput resolution:")
    print("  Enter = keep the original size (automatic)")
    print("  or type a number, e.g. 720, 640, 480, 360")
    while True:
        answer = input("Resolution [original]: ").strip().lower()
        if answer.endswith("p"):
            answer = answer[:-1].strip()
        if not answer:
            return None
        if answer.isdigit() and 100 <= int(answer) <= 4320:
            return int(answer) // 2 * 2  # video sizes must be even
        print("Invalid input. Type a number like 720, or just press Enter.")


def parse_selection(answer, count):
    """Turn '1,3,5' / '2-4' / 'x' into a list of zero-based indexes."""
    answer = answer.strip().lower()
    if answer in ("x", "all"):
        return list(range(count))

    numbers = []
    for part in answer.replace(" ", "").split(","):
        if not part:
            continue
        if "-" in part:
            start, end = part.split("-", 1)
            if not (start.isdigit() and end.isdigit()):
                raise ValueError(part)
            numbers.extend(range(int(start), int(end) + 1))
        elif part.isdigit():
            numbers.append(int(part))
        else:
            raise ValueError(part)

    for number in numbers:
        if not 1 <= number <= count:
            raise ValueError(str(number))
    return [number - 1 for number in numbers]


# ------------------------------------------------------------- video info
def video_duration(path):
    """Return the video length in seconds (via ffprobe), or None."""
    try:
        result = subprocess.run(
            ["ffprobe", "-v", "error", "-show_entries", "format=duration",
             "-of", "default=nw=1:nk=1", path],
            capture_output=True, text=True)
        return float(result.stdout.strip())
    except (ValueError, FileNotFoundError):
        return None


def video_size(path):
    """Return (width, height) as the video is displayed (rotation applied).

    Phone videos are often stored as landscape with a rotation flag, so the
    coded size alone would give the wrong orientation.
    """
    try:
        result = subprocess.run(
            ["ffprobe", "-v", "error", "-select_streams", "v:0",
             "-show_entries",
             "stream=width,height:stream_tags=rotate:stream_side_data=rotation",
             "-of", "json", path],
            capture_output=True, text=True)
        stream = json.loads(result.stdout)["streams"][0]
        width, height = int(stream["width"]), int(stream["height"])

        rotation = 0
        for side_data in stream.get("side_data_list", []):
            if "rotation" in side_data:
                rotation = int(float(side_data["rotation"]))
        if not rotation:
            rotation = int(stream.get("tags", {}).get("rotate", 0))

        if abs(rotation) % 180 == 90:
            width, height = height, width
        return width, height
    except Exception:
        return None


# ----------------------------------------------------------------- layout
def fit_text(text, width, height):
    """Choose a font size so the text always fits on the video.

    If the text is very long, it is split into two lines.
    Returns (text, font_size).
    """
    def font_size_for(lines):
        longest = max(len(line) for line in lines)
        # 0.62 is a safe average character width (in em) for DejaVu Sans.
        return min(height / 25, (width * 0.9) / (0.62 * longest))

    lines = text.split("\n")
    size = font_size_for(lines)

    spaces = [i for i, char in enumerate(text) if char == " "]
    if size < height / 40 and len(lines) == 1 and spaces:
        # Split at the space closest to the middle of the text.
        middle = min(spaces, key=lambda i: abs(i - len(text) // 2))
        lines = [text[:middle].strip(), text[middle + 1:].strip()]
        size = font_size_for(lines)

    return "\n".join(lines), max(int(size), 10)


def format_time(seconds):
    """Format seconds as H:MM:SS."""
    seconds = int(max(seconds, 0))
    return f"{seconds // 3600}:{seconds % 3600 // 60:02d}:{seconds % 60:02d}"


# ----------------------------------------------------------------- encode
def add_watermark(source, target, text, prefix="", resolution=None):
    """Encode one video with the watermark and show a progress bar.

    resolution: None keeps the original size, or the shorter side in pixels.

    The text is passed to ffmpeg through a temporary file, so brackets,
    colons, quotes and accents need no escaping.
    """
    duration = video_duration(source)
    size = video_size(source)

    text_path = None  # stays None when there is no text to write
    filters = []

    if resolution:
        if size:
            width, height = size
            short_side = min(width, height)
            if short_side > resolution:  # never enlarge a video
                ratio = resolution / short_side
                if width >= height:
                    width, height = round(width * ratio / 2) * 2, resolution
                else:
                    width, height = resolution, round(height * ratio / 2) * 2
                size = (width, height)
                filters.append(f"scale={width}:{height}")
        else:  # ffprobe gave no size: assume landscape
            filters.append(f"scale=-2:{resolution}")

    if text and size:  # the text is sized for the final (scaled) video
        width, height = size
        text, font_size = fit_text(text, width, height)
        border = max(4, font_size // 5)
        position = f"x={int(width * 0.04)}:y=h-th-{int(height * 0.02)}"
        size_options = f"fontsize={font_size}:boxborderw={border}"
    elif text:  # ffprobe gave no size: fall back to simple fixed values
        position = "x=20:y=h-th-20"
        size_options = "fontsize=h/30:boxborderw=8"

    if text:
        with tempfile.NamedTemporaryFile("w", suffix=".txt", delete=False,
                                         encoding="utf-8") as text_file:
            text_file.write(text)
            text_path = text_file.name
        filters.append(
            f"drawtext=textfile={text_path}:expansion=none:"
            f"fontfile={FONT}:{size_options}:fontcolor={FONT_COLOR}:"
            f"box=1:boxcolor=black@0.5:line_spacing=4:{position}")

    command = ["ffmpeg", "-y", "-loglevel", "error", "-nostats",
               "-progress", "pipe:1", "-i", source]
    if filters:
        command += ["-vf", ",".join(filters)]
    command += ["-preset", "veryfast", "-c:a", "copy", target]

    started = time.time()
    errors = []
    last_line = ""
    try:
        process = subprocess.Popen(command, stdout=subprocess.PIPE,
                                   stderr=subprocess.STDOUT, text=True)
        for line in process.stdout:
            line = line.strip()
            if line.startswith(("out_time_us=", "out_time_ms=")):
                try:
                    done_seconds = int(line.split("=", 1)[1]) / 1_000_000
                except ValueError:
                    continue

                elapsed = time.time() - started
                if duration:
                    fraction = max(0.0, min(done_seconds / duration, 1.0))
                    filled = int(fraction * 15)
                    bar = "#" * filled + "-" * (15 - filled)
                    remaining = (elapsed / fraction * (1 - fraction)
                                 if fraction > 0.01 else None)
                    eta = format_time(remaining) if remaining is not None else "?"
                    status = f"{prefix}[{bar}] {fraction * 100:3.0f}% ~{eta}"
                else:
                    status = f"{prefix}{format_time(done_seconds)} done"

                # Keep the line shorter than the terminal so it never wraps.
                columns = shutil.get_terminal_size((50, 20)).columns
                status = status[:columns - 1]
                if status != last_line:
                    sys.stdout.write("\r" + status + "\033[K")
                    sys.stdout.flush()
                    last_line = status
            elif "=" not in line and line:
                errors.append(line)  # a real ffmpeg error message

        process.wait()
        sys.stdout.write("\n")
        if process.returncode != 0:
            for error in errors[-5:]:
                print("  ", error)
            raise subprocess.CalledProcessError(process.returncode, command)
    finally:
        if text_path:
            os.remove(text_path)


# ------------------------------------------------------------------- main
def main():
    folder = os.path.abspath(sys.argv[1]) if len(sys.argv) > 1 else os.getcwd()
    if not os.path.isdir(folder):
        return print("No such folder:", folder)

    print("\n=== Video watermarker ===\n")
    videos = sorted(
        name for name in os.listdir(folder)
        if name.lower().endswith(VIDEO_EXTENSIONS)
        and not os.path.splitext(name)[0].endswith(OUTPUT_SUFFIX)
        and os.path.isfile(os.path.join(folder, name)))
    if not videos:
        return print(f"No videos in this folder ({folder}).")

    dates = {name: format_date(recording_date(os.path.join(folder, name)))
             for name in videos}
    print(f"Videos in the folder ({folder}):")
    for number, name in enumerate(videos, 1):
        print(f"  {number}. {name}  [{dates[name]}]")

    print("\nWhich videos? A number (e.g. 2), several (1,3,5),")
    selected = None
    while selected is None:
        answer = input("a range (2-4), X = all, Q = quit: ").strip()
        if answer.lower() in ("q", ""):
            return print("Quit.")
        try:
            selected = parse_selection(answer, len(videos))
        except ValueError as error:
            print(f"Invalid input: {error}. Try again.")

    manual_date = ask_date_mode()
    resolution = ask_resolution()

    text = ask_watermark_text()
    if text is None:
        return print("Cancelled.")

    finished = 0
    for position, index in enumerate(selected, 1):
        name = videos[index]
        source = os.path.join(folder, name)
        target = os.path.join(
            folder, os.path.splitext(name)[0] + OUTPUT_SUFFIX + ".mp4")
        day_text = dates[name] if manual_date is None else manual_date
        full_text = DATE_SEPARATOR.join(part for part in (day_text, text)
                                        if part)
        print(f"\n{name}\n  -> {full_text or '(no watermark, only re-encoding)'}")
        try:
            add_watermark(source, target, full_text,
                          f"[{position}/{len(selected)}] ", resolution)
            finished += 1
        except subprocess.CalledProcessError:
            print("  Error with this video, skipped.")

    # Ask Android to refresh the gallery; never wait more than 10 seconds.
    try:
        subprocess.run(["termux-media-scan", folder], capture_output=True,
                       timeout=10)
    except Exception:
        pass

    print(f"\nDone: {finished}/{len(selected)} videos.")
    print("The new files are next to the originals (name + _watermark).")


if __name__ == "__main__":
    main()
