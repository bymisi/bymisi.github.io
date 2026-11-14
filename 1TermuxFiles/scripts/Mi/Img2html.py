import base64
import io
import os
import sys

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


def choose_file():
    images = list_images()

    if images:
        print(f"\nImages in the current folder ({os.getcwd()}):")
        for number, name in enumerate(images, 1):
            print(f"  {number}. {name}")
        print()
    else:
        print(f"\nNo images found in the current folder ({os.getcwd()}).\n")

    answer = input("Path to the image (or its number from the list): ")
    answer = answer.strip().strip("'\"")

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


def convert_image():
    print("=== Image -> HTML (base64) converter ===")

    if len(sys.argv) > 1:
        file_path = os.path.expanduser(sys.argv[1].strip().strip("'\""))
    else:
        file_path = choose_file()

    if not os.path.isfile(file_path):
        print(f"File not found: {file_path}")
        return

    output_file = resolve_output_path(file_path.rsplit('.', 1)[0] + "_embedded.html")
    if output_file is None:
        print("Cancelled. Nothing was changed.")
        return

    width = ask_number(
        f"Width in pixels [Enter = {DEFAULT_WIDTH}, 0 = original size]: ",
        DEFAULT_WIDTH)
    quality = ask_number(
        f"JPG quality 1-100 [Enter = {DEFAULT_QUALITY}]: ", DEFAULT_QUALITY)
    quality = max(1, min(100, quality))

    try:
        image = Image.open(file_path)
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

        html_content = (
            '<!DOCTYPE html>\n<html><head><meta charset="utf-8">'
            '<meta name="viewport" content="width=device-width, initial-scale=1">'
            '</head>\n'
            f'<body><img src="data:image/jpeg;base64,{b64}" '
            'style="max-width:100%;height:auto"></body></html>\n'
        )

        with open(output_file, "w", encoding="utf-8") as html_file:
            html_file.write(html_content)

        original_kb = os.path.getsize(file_path) // 1024
        new_kb = len(data) // 1024
        print(f"\nOriginal: {original_size[0]}x{original_size[1]} px, {original_kb} KB")
        print(f"New:      {image.width}x{image.height} px, {new_kb} KB (JPG)")
        print(f"\nSuccess! The HTML file was created at:\n{output_file}")

    except Exception as e:
        print(f"An error occurred during processing: {e}")


if __name__ == "__main__":
    convert_image()
