import base64
import html
import io
import json
import os
import sys
import time
import urllib.parse
import urllib.request

try:
    import readline  # enables Tab completion for file paths, if available
    readline.parse_and_bind("tab: complete")
except ImportError:
    pass

try:
    from PIL import Image, ImageOps
except ImportError:
    print("Pillow is missing. Install it with: pkg install python-pillow")
    sys.exit(1)

DEFAULT_WIDTH = 1080
DEFAULT_QUALITY = 80
IMAGE_EXTENSIONS = (".jpg", ".jpeg", ".png", ".webp", ".bmp", ".gif", ".tif", ".tiff")
ALL_IMAGES = "__ALL__"  # marker returned when the user types X
ALL_OUTPUT_NAME = "all_images_embedded.html"
USER_AGENT = "image2html-script/1.0 (personal use)"  # required by Nominatim's usage policy

_place_cache = {}
_last_request_time = 0.0


def list_images():
    """Return a sorted list of image files in the current folder."""
    files = [
        name for name in os.listdir(".")
        if os.path.isfile(name) and name.lower().endswith(IMAGE_EXTENSIONS)
    ]
    return sorted(files, key=str.lower)


def ask_number(prompt, default):
    value = input(prompt).strip()
    if not value:
        return default
    try:
        return int(value)
    except ValueError:
        print(f"Not a number, using the default: {default}")
        return default


def ask_yes_no(prompt, default):
    hint = "Y/n" if default else "y/N"
    value = input(f"{prompt} [{hint}]: ").strip().lower()
    if not value:
        return default
    return value in ("y", "yes", "i", "igen")


def choose_file():
    images = list_images()

    if images:
        print(f"\nImages in the current folder ({os.getcwd()}):")
        for number, name in enumerate(images, 1):
            print(f"  {number}. {name}")
        print("  X. Put ALL images into ONE HTML file")
        print()
    else:
        print(f"\nNo images found in the current folder ({os.getcwd()}).\n")

    answer = input("Path to the image (number from the list, or X for all): ")
    answer = answer.strip().strip("'\"")

    # X puts every image in the folder into one HTML file
    if answer.lower() == "x" and not os.path.isfile(answer):
        return ALL_IMAGES

    # A number from the list selects that file
    if answer.isdigit() and not os.path.isfile(answer):
        index = int(answer) - 1
        if 0 <= index < len(images):
            return images[index]

    return os.path.expanduser(answer)


def next_free_name(path):
    """Return path with a number added (name_2.html, name_3.html, ...) that does not exist yet."""
    base, ext = os.path.splitext(path)
    counter = 2
    while os.path.exists(f"{base}_{counter}{ext}"):
        counter += 1
    return f"{base}_{counter}{ext}"


def resolve_output_path(path):
    """If the output file exists, ask what to do. Returns the path to use, or None to cancel."""
    if not os.path.exists(path):
        return path

    numbered = next_free_name(path)
    print(f"\nThe output file already exists:\n  {path}")
    print("  1. Save with a new numbered name:")
    print(f"       {numbered}")
    print("  2. Overwrite the existing file")
    print("  3. Cancel")

    while True:
        choice = input("Your choice [Enter = 1]: ").strip()
        if choice in ("", "1"):
            return numbered
        if choice == "2":
            return path
        if choice == "3":
            return None
        print("Please type 1, 2 or 3.")


def ask_settings():
    """Returns (width, quality, meta_opts).
    meta_opts is None (no metadata) or a dict:
      gps      - show the coordinates and altitude
      place    - look up city and country from the GPS position (needs internet)
      fallback - text used when a photo has no GPS or the lookup fails (may be empty)"""
    width = ask_number(
        f"Width in pixels [Enter = {DEFAULT_WIDTH}, 0 = original size]: ",
        DEFAULT_WIDTH)
    quality = ask_number(
        f"JPG quality 1-100 [Enter = {DEFAULT_QUALITY}]: ", DEFAULT_QUALITY)
    quality = max(1, min(100, quality))

    meta_opts = None
    if ask_yes_no("Include metadata (shown when the image is clicked)?", True):
        meta_opts = {"gps": False, "place": False, "fallback": ""}
        meta_opts["gps"] = ask_yes_no(
            "Show GPS coordinates? (visible to anyone who gets the file)", True)
        meta_opts["place"] = ask_yes_no(
            "Add city and country from the GPS position? (needs internet)", True)
        if meta_opts["place"]:
            meta_opts["fallback"] = input(
                "Text to use if a photo has no GPS or the lookup fails\n"
                "(e.g. Amersfoort, Netherlands) [Enter = none]: ").strip()
    return width, quality, meta_opts


# ---------- place lookup ----------

def lookup_place(lat, lon):
    """Reverse geocoding via OpenStreetMap Nominatim. Returns 'City, Country' or None."""
    global _last_request_time

    key = (round(lat, 3), round(lon, 3))  # nearby photos share one lookup
    if key in _place_cache:
        return _place_cache[key]

    # Nominatim allows at most 1 request per second
    wait = 1.1 - (time.time() - _last_request_time)
    if wait > 0:
        time.sleep(wait)

    params = urllib.parse.urlencode({
        "format": "jsonv2",
        "lat": f"{lat:.6f}",
        "lon": f"{lon:.6f}",
        "zoom": 10,
        "accept-language": "en",
    })
    url = f"https://nominatim.openstreetmap.org/reverse?{params}"
    request = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})

    result = None
    try:
        with urllib.request.urlopen(request, timeout=10) as response:
            data = json.loads(response.read().decode("utf-8"))
        address = data.get("address", {})
        city = ""
        for field in ("city", "town", "village", "municipality", "hamlet", "county"):
            if address.get(field):
                city = address[field]
                break
        country = address.get("country", "")
        result = ", ".join(part for part in (city, country) if part) or None
    except Exception as e:
        print(f"    (place lookup failed: {e})")
    finally:
        _last_request_time = time.time()

    _place_cache[key] = result
    return result


# ---------- metadata ----------

def _to_float(value):
    try:
        return float(value)
    except Exception:
        return None


def _clean(value):
    if isinstance(value, bytes):
        value = value.decode("utf-8", errors="replace")
    return str(value).strip().strip("\x00")


def _dms_to_decimal(dms, ref):
    try:
        deg, minutes, seconds = (float(x) for x in dms)
        result = deg + minutes / 60 + seconds / 3600
        if _clean(ref).upper() in ("S", "W"):
            result = -result
        return result
    except Exception:
        return None


def read_metadata(image, meta_opts):
    """Read EXIF data from the ORIGINAL image. Returns a list of text lines."""
    lines = []

    def add(label, value):
        if value not in (None, ""):
            lines.append(f"{label}: {value}")

    try:
        exif = image.getexif()
    except Exception:
        exif = None

    lat = lon = None
    altitude = None

    if exif:
        try:
            exif_ifd = exif.get_ifd(0x8769)
        except Exception:
            exif_ifd = {}

        add("Camera make", _clean(exif.get(271, "")))
        add("Camera model", _clean(exif.get(272, "")))
        add("Lens", _clean(exif_ifd.get(42036, "")))
        add("Software", _clean(exif.get(305, "")))
        add("Date taken", _clean(exif_ifd.get(36867, exif.get(306, ""))))

        exposure = _to_float(exif_ifd.get(33434))
        if exposure:
            add("Exposure", f"1/{round(1 / exposure)} s" if exposure < 1 else f"{exposure:g} s")
        fnumber = _to_float(exif_ifd.get(33437))
        if fnumber:
            add("Aperture", f"f/{fnumber:g}")
        iso = exif_ifd.get(34855)
        if iso:
            add("ISO", _clean(iso))
        focal = _to_float(exif_ifd.get(37386))
        if focal:
            add("Focal length", f"{focal:g} mm")

        try:
            gps = exif.get_ifd(0x8825)
        except Exception:
            gps = {}
        if gps:
            lat = _dms_to_decimal(gps.get(2), gps.get(1)) if gps.get(2) else None
            lon = _dms_to_decimal(gps.get(4), gps.get(3)) if gps.get(4) else None
            altitude = _to_float(gps.get(6))

    # Coordinates are only written into the HTML if the user allowed it
    if meta_opts["gps"]:
        if lat is not None and lon is not None:
            add("GPS", f"{lat:.6f}, {lon:.6f}")
        if altitude is not None:
            add("Altitude", f"{altitude:.1f} m")

    # City and country line (last line)
    if meta_opts["place"]:
        place = None
        if lat is not None and lon is not None:
            place = lookup_place(lat, lon)
        if not place and meta_opts["fallback"]:
            place = meta_opts["fallback"]
        add("Location", place)

    return lines


# ---------- image processing ----------

def encode_image(file_path, width, quality, meta_opts):
    """Resize/compress one image and return it as a base64 JPEG string.
    Returns (b64, meta_lines_or_None, original_size, new_size, original_kb, new_kb)."""
    image = Image.open(file_path)

    # Must be read BEFORE re-saving as JPG, because saving drops the metadata
    meta_lines = None
    if meta_opts:
        meta_lines = read_metadata(image, meta_opts)

    image = ImageOps.exif_transpose(image)  # fix rotation of phone photos
    original_size = image.size

    # Transparent images (PNG) get a white background, since JPG has no transparency
    if image.mode in ("RGBA", "LA", "P"):
        image = image.convert("RGBA")
        background = Image.new("RGB", image.size, (255, 255, 255))
        background.paste(image, mask=image.split()[-1])
        image = background
    else:
        image = image.convert("RGB")

    # Only shrinks, never enlarges
    if width > 0 and image.width > width:
        height = round(image.height * width / image.width)
        image = image.resize((width, height), Image.LANCZOS)

    buffer = io.BytesIO()
    image.save(buffer, format="JPEG", quality=quality, optimize=True)
    data = buffer.getvalue()

    b64 = base64.b64encode(data).decode("ascii")
    original_kb = os.path.getsize(file_path) // 1024
    new_kb = len(data) // 1024
    return b64, meta_lines, original_size, image.size, original_kb, new_kb


def build_html(items):
    """Build one HTML page from a list of (base64_jpeg, meta_lines_or_None) pairs.
    Metadata is hidden and appears below the image when the image is clicked."""
    with_meta = any(meta is not None for _, meta in items)

    parts = [
        '<!DOCTYPE html>\n<html><head><meta charset="utf-8">'
        '<meta name="viewport" content="width=device-width, initial-scale=1">'
        '<style>'
        'body{margin:0;padding:8px;background:#fff}'
        'img{display:block;max-width:100%;height:auto;margin-bottom:8px}'
        '.meta{white-space:pre-wrap;word-break:break-word;background:#f2f2f2;'
        'color:#222;padding:8px;margin:0 0 8px 0;font:13px monospace}'
        '.meta[hidden]{display:none}'
        '</style></head>\n<body>\n'
    ]
    for b64, meta in items:
        parts.append(f'<img src="data:image/jpeg;base64,{b64}" alt="">\n')
        if meta is not None:
            text = "\n".join(meta) if meta else "No metadata found in this image."
            parts.append(f'<pre class="meta" hidden>{html.escape(text)}</pre>\n')

    if with_meta:
        parts.append(
            '<script>'
            'document.querySelectorAll("img").forEach(function(i){'
            'i.addEventListener("click",function(){'
            'var m=i.nextElementSibling;'
            'if(m&&m.classList.contains("meta"))m.hidden=!m.hidden;'
            '});});'
            '</script>\n'
        )
    parts.append("</body></html>\n")
    return "".join(parts)


def convert_all():
    """Put every image of the current folder into ONE HTML file, stacked vertically."""
    images = list_images()
    if not images:
        print("No images found in the current folder.")
        return

    print(f"\n{len(images)} image(s) will be put into one HTML file.")

    output_file = resolve_output_path(ALL_OUTPUT_NAME)
    if output_file is None:
        print("Cancelled. Nothing was changed.")
        return

    width, quality, meta_opts = ask_settings()

    items = []
    failed = 0
    print()
    for number, name in enumerate(images, 1):
        try:
            b64, meta, original_size, new_size, original_kb, new_kb = encode_image(
                name, width, quality, meta_opts)
            items.append((b64, meta))
            print(f"[{number}/{len(images)}] {name}")
            print(f"    {original_size[0]}x{original_size[1]} px, {original_kb} KB"
                  f"  =>  {new_size[0]}x{new_size[1]} px, {new_kb} KB")
            if meta_opts:
                print(f"    metadata lines: {len(meta)}")
        except Exception as e:
            failed += 1
            print(f"[{number}/{len(images)}] {name} -> ERROR: {e}")

    if not items:
        print("\nNo image could be processed. No file was created.")
        return

    with open(output_file, "w", encoding="utf-8") as html_file:
        html_file.write(build_html(items))

    total_kb = os.path.getsize(output_file) // 1024
    print(f"\nFinished. Images in the file: {len(items)}, failed: {failed}")
    print(f"File size: {total_kb} KB")
    print(f"The HTML file was created at:\n{output_file}")


def convert_image():
    print("=== Image -> HTML (base64) converter ===")

    if len(sys.argv) > 1:
        file_path = os.path.expanduser(sys.argv[1].strip().strip("'\""))
    else:
        file_path = choose_file()

    if file_path == ALL_IMAGES:
        convert_all()
        return

    if not os.path.isfile(file_path):
        print(f"File not found: {file_path}")
        return

    output_file = resolve_output_path(file_path.rsplit('.', 1)[0] + "_embedded.html")
    if output_file is None:
        print("Cancelled. Nothing was changed.")
        return

    width, quality, meta_opts = ask_settings()

    try:
        b64, meta, original_size, new_size, original_kb, new_kb = encode_image(
            file_path, width, quality, meta_opts)
        with open(output_file, "w", encoding="utf-8") as html_file:
            html_file.write(build_html([(b64, meta)]))
        print(f"\nOriginal: {original_size[0]}x{original_size[1]} px, {original_kb} KB")
        print(f"New:      {new_size[0]}x{new_size[1]} px, {new_kb} KB (JPG)")
        if meta_opts:
            print(f"Metadata lines found: {len(meta)}")
        print(f"\nSuccess! The HTML file was created at:\n{output_file}")
    except Exception as e:
        print(f"An error occurred during processing: {e}")


if __name__ == "__main__":
    convert_image()
