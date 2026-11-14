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


def ask_number(prompt, default):
    value = input(prompt).strip()
    if not value:
        return default
    try:
        return int(value)
    except ValueError:
        print(f"Not a number, using the default: {default}")
        return default


def convert_image():
    print("=== Image -> HTML (base64) converter ===")

    if len(sys.argv) > 1:
        file_path = sys.argv[1]
    else:
        file_path = input("Path to the image: ")

    file_path = os.path.expanduser(file_path.strip().strip("'\""))

    if not os.path.isfile(file_path):
        print(f"File not found: {file_path}")
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

        output_file = file_path.rsplit('.', 1)[0] + "_embedded.html"

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
