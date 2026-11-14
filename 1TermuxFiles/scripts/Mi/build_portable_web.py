#!/usr/bin/env python3
# Portable single-file site builder
# Author: by_[MiSi], developed with the help of Claude (Anthropic AI)
#
# Usage: python build.py <site_root> [output.html]
# Merges index.html, galery/galery.html and galery/about.html (with all CSS,
# JS, fonts and images) into ONE self-contained HTML file.

import base64
import io
import json
import mimetypes
import os
import re
import sys

from PIL import Image, ImageOps

ROOT = os.path.abspath(sys.argv[1] if len(sys.argv) > 1 else ".")
OUT = sys.argv[2] if len(sys.argv) > 2 else os.path.join(ROOT, "elektrikus_hordozhato.html")

IMG_WIDTH = 1100      # max width of embedded photos (px)
IMG_QUALITY = 72      # JPEG quality

# page name -> file (relative to ROOT)
PAGES = {
    "index": "index.html",
    "galery": "galery/galery.html",
    "about": "galery/about.html",
}
START_PAGE = "index"

# pages that only redirect to an external site: link straight to the target
REDIRECT_PAGES = ["box/index.html", "gdrive/index.html"]

_img_cache = {}


def read_text(path):
    with open(path, encoding="utf-8", errors="replace") as f:
        return f.read()


def find_file(base_dir, rel):
    rel = rel.split("?")[0].split("#")[0].strip()
    if not rel:
        return None
    p = os.path.normpath(os.path.join(ROOT if rel.startswith("/") else base_dir,
                                      rel.lstrip("/")))
    return p if os.path.isfile(p) else None


def is_external(u):
    return re.match(r"^(https?:|//|data:|#|mailto:|tel:|javascript:)", u.strip(), re.I) is not None


def data_uri_raw(path):
    mime = mimetypes.guess_type(path)[0] or "application/octet-stream"
    if path.lower().endswith(".woff"):
        mime = "font/woff"
    elif path.lower().endswith(".ttf"):
        mime = "font/ttf"
    with open(path, "rb") as f:
        return f"data:{mime};base64," + base64.b64encode(f.read()).decode()


def data_uri_image(path):
    """Photos are resized + recompressed to JPEG; tiny/other files are embedded as-is."""
    if path in _img_cache:
        return _img_cache[path]
    ext = os.path.splitext(path)[1].lower()
    if ext in (".jpg", ".jpeg", ".png", ".webp") and os.path.getsize(path) > 20000:
        img = ImageOps.exif_transpose(Image.open(path))
        if img.mode in ("RGBA", "LA", "P"):
            img = img.convert("RGBA")
            bg = Image.new("RGB", img.size, (255, 255, 255))
            bg.paste(img, mask=img.split()[-1])
            img = bg
        else:
            img = img.convert("RGB")
        if img.width > IMG_WIDTH:
            h = round(img.height * IMG_WIDTH / img.width)
            img = img.resize((IMG_WIDTH, h), Image.LANCZOS)
        buf = io.BytesIO()
        img.save(buf, format="JPEG", quality=IMG_QUALITY, optimize=True)
        if len(buf.getvalue()) >= os.path.getsize(path):
            uri = data_uri_raw(path)  # already small: keep the original
            _img_cache[path] = uri
            return uri
        uri = "data:image/jpeg;base64," + base64.b64encode(buf.getvalue()).decode()
        print(f"  image {os.path.relpath(path, ROOT)}: "
              f"{os.path.getsize(path)//1024} KB -> {len(buf.getvalue())//1024} KB")
    else:
        uri = data_uri_raw(path)
    _img_cache[path] = uri
    return uri


_font_cache = {}


def font_uri(path):
    """Embed a font. Single-file ttf fonts are subset to Latin + Latin Extended-A
    (covers Hungarian) when fontTools is available, which makes them much smaller."""
    if path in _font_cache:
        return _font_cache[path]
    ext = os.path.splitext(path)[1].lower()
    uri = None
    if ext == ".ttf" and os.path.getsize(path) > 150000:
        try:
            from fontTools import subset
            from fontTools.ttLib import TTFont
            opts = subset.Options()
            opts.flavor = "woff"
            opts.layout_features = ["*"]
            font = TTFont(path)
            sub = subset.Subsetter(opts)
            sub.populate(unicodes=list(range(0x20, 0x250)) + [0x2013, 0x2014, 0x2018, 0x2019,
                                                              0x201C, 0x201D, 0x2026, 0x20AC])
            sub.subset(font)
            font.flavor = "woff"
            buf = io.BytesIO()
            font.save(buf)
            uri = "data:font/woff;base64," + base64.b64encode(buf.getvalue()).decode()
            print(f"  font {os.path.relpath(path, ROOT)}: "
                  f"{os.path.getsize(path)//1024} KB -> {len(buf.getvalue())//1024} KB (subset)")
        except Exception as e:
            print(f"  (font subset skipped for {os.path.basename(path)}: {e})")
    if uri is None:
        uri = data_uri_raw(path)
    _font_cache[path] = uri
    return uri


def inline_css(css, base_dir):
    """Embed url(...) in CSS. In every @font-face only ONE font file is kept
    (woff preferred), the old eot/svg/ttf duplicates are dropped."""

    def face_repl(m):
        block = m.group(0)
        urls = re.findall(r"url\(\s*['\"]?([^)'\"]+?)['\"]?\s*\)", block)
        files = []
        for u in urls:
            if is_external(u):
                continue
            p = find_file(base_dir, u)
            if p:
                files.append(p)
        if not files:
            return block
        pick = None
        for pref in (".woff2", ".woff", ".ttf", ".otf"):
            pick = next((f for f in files if f.lower().endswith(pref)), None)
            if pick:
                break
        if not pick:
            return block
        fmt = {".woff2": "woff2", ".woff": "woff", ".ttf": "truetype", ".otf": "opentype"}[
            os.path.splitext(pick)[1].lower()]
        body = re.sub(r"src\s*:[^;}]*;?", "", block[block.index("{") + 1:block.rindex("}")])
        uri = font_uri(pick)
        if uri.startswith("data:font/woff;") and fmt == "truetype":
            fmt = "woff"
        return (block[:block.index("{") + 1] + body +
                f'src:url("{uri}") format("{fmt}");' + "}")

    css = re.sub(r"@font-face\s*\{[^}]*\}", face_repl, css, flags=re.I)

    def url_repl(m):
        url = m.group(2).strip()
        if is_external(url):
            return m.group(0)
        p = find_file(base_dir, url)
        if not p:
            return m.group(0)
        if os.path.splitext(p)[1].lower() in (".jpg", ".jpeg", ".png", ".gif", ".webp"):
            return f'url("{data_uri_image(p)}")'
        return m.group(0) if url.startswith("data:") else f'url("{data_uri_raw(p)}")'

    return re.sub(r"url\((['\"]?)([^)'\"]+)\1\)", url_repl, css)


def get_attr(tag, name):
    m = re.search(name + r"\s*=\s*([\"'])(.*?)\1", tag, re.I | re.S)
    return m.group(2) if m else None


def build_page(name, rel_path, page_files):
    path = os.path.join(ROOT, rel_path)
    base = os.path.dirname(path)
    html = read_text(path)
    print(f"[{name}] {rel_path}")

    # stylesheets
    def link_repl(m):
        tag = m.group(0)
        rel = (get_attr(tag, "rel") or "").lower()
        href = get_attr(tag, "href")
        if not href or is_external(href):
            return tag
        p = find_file(base, href)
        if not p:
            print("  MISSING:", href)
            return tag
        if "stylesheet" in rel:
            return "<style>\n" + inline_css(read_text(p), os.path.dirname(p)) + "\n</style>"
        return tag

    html = re.sub(r"<link\b[^>]*>", link_repl, html, flags=re.I)

    # local scripts
    def script_repl(m):
        tag = m.group(1)
        src = get_attr(tag, "src")
        if not src or is_external(src):
            return m.group(0)
        p = find_file(base, src)
        if not p:
            print("  MISSING:", src)
            return m.group(0)
        tag2 = re.sub(r"\s+src\s*=\s*([\"']).*?\1", "", tag, flags=re.I)
        code = read_text(p).replace("</script", "<\\/script")
        return f"{tag2}\n{code}\n</script>"

    html = re.sub(r"(<script\b[^>]*>)\s*</script>", script_repl, html, flags=re.I)

    # images
    def img_repl(m):
        prefix, url = m.group(1), m.group(3)
        if is_external(url):
            return m.group(0)
        p = find_file(base, url)
        if not p:
            print("  MISSING:", url)
            return m.group(0)
        return f'{prefix}"{data_uri_image(p)}"'

    html = re.sub(r"""(<img\b[^>]*?[\s"']src\s*=\s*)(["'])([^"']+)\2""", img_repl, html, flags=re.I)

    # inline style="...url(...)" attributes
    html = re.sub(r'style="([^"]*url\([^"]*)"',
                  lambda m: 'style="' + inline_css(m.group(1), base) + '"', html, flags=re.I)

    # internal page links -> in-app navigation
    def a_repl(m):
        tag = m.group(0)
        href = get_attr(tag, "href")
        if not href or is_external(href):
            return tag
        target = os.path.normpath(os.path.join(base, href.split("#")[0]))
        rel_target = os.path.relpath(target, ROOT).replace(os.sep, "/")
        # redirect pages: link directly to the external address
        if rel_target in REDIRECT_PAGES:
            ext = re.search(r"url=['\"]?([^'\">]+)", read_text(target), re.I)
            if ext:
                new = re.sub(r"href\s*=\s*([\"']).*?\1", f'href="{ext.group(1)}"', tag, count=1)
                return new.replace("<a ", '<a target="_blank" rel="noopener" ', 1)
        for pname, pfile in page_files.items():
            if os.path.normpath(os.path.join(ROOT, pfile)) == target:
                new = re.sub(r"href\s*=\s*([\"']).*?\1", f'href="#{pname}"', tag, count=1)
                return new.replace("<a ", f'<a onclick="parent.go(\'{pname}\');return false;" ', 1)
        return tag

    html = re.sub(r"<a\b[^>]*>", a_repl, html, flags=re.I)
    return html


def main():
    print("Building from:", ROOT)
    pages = {n: build_page(n, f, PAGES) for n, f in PAGES.items()}

    # JSON with every '<' escaped, so it is safe inside a <script> tag
    payload = json.dumps(pages, ensure_ascii=False).replace("<", "\\u003c")

    shell = f"""<!DOCTYPE html>
<html lang="hu"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>elektrikus</title>
<style>html,body{{margin:0;height:100%;background:#000}}
iframe{{border:0;width:100%;height:100%;display:block}}</style>
</head><body>
<iframe id="view" title="elektrikus"></iframe>
<script id="pages" type="application/json">{payload}</script>
<script>
var PAGES = JSON.parse(document.getElementById("pages").textContent);
var START = "{START_PAGE}";
function go(name) {{
  if (!PAGES[name]) name = START;
  document.getElementById("view").srcdoc = PAGES[name];
  try {{ history.replaceState(null, "", "#" + name); }} catch (e) {{}}
}}
window.addEventListener("hashchange", function () {{ go(location.hash.slice(1)); }});
go(location.hash.slice(1) || START);
</script>
</body></html>
"""
    with open(OUT, "w", encoding="utf-8") as f:
        f.write(shell)
    print(f"\nDone: {OUT} ({os.path.getsize(OUT)//1024} KB)")


if __name__ == "__main__":
    main()
