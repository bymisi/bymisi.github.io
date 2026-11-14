#!/usr/bin/env python3
# Page resource lister: which files does a web page load, and from which host?
# Author: by_[MiSi], developed with the help of Claude (Anthropic AI)
#
# Usage:
#   python page_resources.py https://example.com
#   python page_resources.py https://example.com -o result.txt -u
#   python page_resources.py https://example.com --js
#
# Static analysis only (no JavaScript is executed): the HTML is downloaded, the
# tags (img, script, link, video, iframe, ...) and the stylesheets (url(), @import)
# are parsed. Python standard library only, works in Termux without installs.

import argparse
import datetime
import gzip
import os
import re
import sys
import urllib.error
import urllib.parse
import urllib.request
import zlib
from collections import Counter, defaultdict
from html.parser import HTMLParser

UA = ("Mozilla/5.0 (Linux; Android 14; Mobile) AppleWebKit/537.36 "
      "(KHTML, like Gecko) Chrome/124.0 Mobile Safari/537.36")
SKIP_PREFIXES = ("data:", "javascript:", "mailto:", "tel:", "#", "about:", "blob:", "sms:")
MAX_FILES = 60  # max number of CSS/JS files that are downloaded and scanned

EXT_KINDS = {
    "img": ("png", "jpg", "jpeg", "gif", "webp", "avif", "svg", "ico", "bmp"),
    "font": ("woff", "woff2", "ttf", "otf", "eot"),
    "video": ("mp4", "webm", "ogv", "m3u8", "mov", "mpd", "ts"),
    "audio": ("mp3", "ogg", "wav", "m4a", "aac", "flac"),
    "css": ("css",),
    "script": ("js", "mjs"),
}


def fetch(url, timeout=20, max_bytes=6_000_000):
    """Download a URL. Returns (final_url, text)."""
    req = urllib.request.Request(url, headers={
        "User-Agent": UA, "Accept": "*/*", "Accept-Encoding": "gzip, deflate"})
    with urllib.request.urlopen(req, timeout=timeout) as resp:
        data = resp.read(max_bytes)
        enc = (resp.headers.get("Content-Encoding") or "").lower()
        if enc == "gzip":
            data = gzip.decompress(data)
        elif enc == "deflate":
            try:
                data = zlib.decompress(data)
            except zlib.error:
                data = zlib.decompress(data, -zlib.MAX_WBITS)
        charset = resp.headers.get_content_charset() or "utf-8"
        return resp.geturl(), data.decode(charset, errors="replace")


def classify(url, default):
    ext = os.path.splitext(urllib.parse.urlparse(url).path)[1].lower().lstrip(".")
    for kind, exts in EXT_KINDS.items():
        if ext in exts:
            return kind
    return default


def clean_url(base, raw):
    raw = (raw or "").strip().strip("\"'")
    if not raw or raw.lower().startswith(SKIP_PREFIXES):
        return None
    full = urllib.parse.urljoin(base, raw)
    if not full.lower().startswith(("http://", "https://")):
        return None
    return full.split("#")[0]


def css_refs(text, base):
    """URLs inside CSS: url(...) and @import."""
    found = []
    for m in re.finditer(r"url\(\s*([\"']?)(.*?)\1\s*\)", text, re.S):
        u = clean_url(base, m.group(2))
        if u:
            found.append((classify(u, "css-url"), u))
    for m in re.finditer(r"@import\s+(?:url\(\s*)?[\"']?([^\"')\s;]+)", text):
        u = clean_url(base, m.group(1))
        if u:
            found.append(("css", u))
    return found


def js_refs(text, base):
    """Absolute URLs written inside JavaScript (not proof that they are really loaded)."""
    found = []
    for m in re.finditer(r"(?:https?:)?//[A-Za-z0-9.-]+\.[A-Za-z]{2,}[^\s\"'`<>\\)]*", text):
        raw = m.group(0)
        if raw.startswith("//"):
            raw = "https:" + raw
        u = clean_url(base, raw)
        if u:
            found.append(("js-ref?", u))
    return found


def parse_srcset(value):
    urls = []
    for tok in (value or "").split():
        if re.fullmatch(r"[\d.]+[wx],?", tok):
            continue
        urls.append(tok.rstrip(","))
    return urls


class Collector(HTMLParser):
    def __init__(self, base):
        super().__init__(convert_charrefs=True)
        self.base = base
        self.found = []          # (kind, url)
        self._mode = None        # "style" or "script" while inside such a tag
        self._buf = []

    def add(self, kind, raw):
        u = clean_url(self.base, raw)
        if u:
            self.found.append((kind, u))

    def handle_starttag(self, tag, attrs):
        a = {k: (v or "") for k, v in attrs}
        add = self.add

        if tag == "base" and a.get("href"):
            self.base = urllib.parse.urljoin(self.base, a["href"])
        elif tag == "img":
            for key in ("src", "data-src", "data-lazy-src", "data-original"):
                add("img", a.get(key))
            for key in ("srcset", "data-srcset"):
                for u in parse_srcset(a.get(key)):
                    add("img", u)
        elif tag == "source":
            add("media", a.get("src"))
            for u in parse_srcset(a.get("srcset")):
                add("img", u)
        elif tag == "video":
            add("video", a.get("src"))
            add("img", a.get("poster"))
        elif tag == "audio":
            add("audio", a.get("src"))
        elif tag == "track":
            add("track", a.get("src"))
        elif tag in ("iframe", "frame"):
            add("iframe", a.get("src"))
        elif tag == "embed":
            add("embed", a.get("src"))
        elif tag == "object":
            add("embed", a.get("data"))
        elif tag == "script":
            if a.get("src"):
                add("script", a["src"])
            else:
                kind = (a.get("type") or "").lower()
                if "json" not in kind and "template" not in kind:
                    self._mode, self._buf = "script", []
        elif tag == "style":
            self._mode, self._buf = "style", []
        elif tag == "link":
            rel = a.get("rel", "").lower()
            href = a.get("href")
            if "stylesheet" in rel:
                add("css", href)
            elif "icon" in rel:
                add("icon", href)
            elif any(x in rel for x in ("preload", "prefetch", "modulepreload")):
                add(classify(urllib.parse.urljoin(self.base, href or ""), "preload"), href)
            elif any(x in rel for x in ("preconnect", "dns-prefetch")):
                add("connect-hint", href)
            elif "manifest" in rel:
                add("manifest", href)

        style_attr = a.get("style", "")
        if "url(" in style_attr:
            self.found.extend(css_refs(style_attr, self.base))

    def handle_data(self, data):
        if self._mode:
            self._buf.append(data)

    def handle_endtag(self, tag):
        if tag in ("style", "script") and self._mode == tag:
            text = "".join(self._buf)
            self.found.extend(css_refs(text, self.base) if tag == "style" else js_refs(text, self.base))
            self._mode, self._buf = None, []


def base_domain(host):
    parts = host.split(".")
    if len(parts) <= 2:
        return host
    if len(parts[-1]) == 2 and parts[-2] in ("co", "com", "org", "net", "gov", "ac", "edu"):
        return ".".join(parts[-3:])
    return ".".join(parts[-2:])


def short_name(url):
    path = urllib.parse.unquote(urllib.parse.urlparse(url).path)
    name = path.rstrip("/").split("/")[-1] or "/"
    return name if len(name) <= 90 else name[:87] + "..."


def main():
    ap = argparse.ArgumentParser(description="List the resources of a web page, grouped by host.")
    ap.add_argument("url")
    ap.add_argument("-o", "--output", help="output text file (default: <host>_resources.txt)")
    ap.add_argument("-u", "--urls", action="store_true", help="write the full URL after each name")
    ap.add_argument("--no-css", action="store_true", help="do not download stylesheets (faster)")
    ap.add_argument("--js", action="store_true",
                    help="also download scripts and list URLs written in them (not confirmed loads)")
    args = ap.parse_args()

    url = args.url if "://" in args.url else "https://" + args.url
    print(f"Downloading: {url}")
    try:
        page_url, html = fetch(url)
    except Exception as e:
        print(f"Could not download the page: {e}")
        sys.exit(1)

    parser = Collector(page_url)
    parser.feed(html)
    items = list(parser.found)
    warnings = []

    # download and scan stylesheets (and optionally scripts) one level deeper
    scanned = set()
    queue = [(k, u) for k, u in items if k == "css" or (args.js and k == "script")]
    while queue and len(scanned) < MAX_FILES:
        kind, u = queue.pop(0)
        if u in scanned or (kind == "css" and args.no_css):
            continue
        scanned.add(u)
        print(f"  scanning {kind}: {short_name(u)}")
        try:
            final, text = fetch(u, max_bytes=3_000_000)
        except Exception as e:
            warnings.append(f"could not read {u}: {e}")
            continue
        new = css_refs(text, final) if kind == "css" else js_refs(text, final)
        items.extend(new)
        queue.extend((k, x) for k, x in new if k == "css")

    # de-duplicate
    seen, unique = set(), []
    for kind, u in items:
        if (kind, u) not in seen:
            seen.add((kind, u))
            unique.append((kind, u))

    page_host = urllib.parse.urlparse(page_url).hostname or ""
    page_base = base_domain(page_host)
    by_host = defaultdict(list)
    for kind, u in unique:
        host = urllib.parse.urlparse(u).hostname or "?"
        by_host[host].append((kind, u))

    def party(host):
        return "own" if base_domain(host) == page_base else "3rd-party"

    hosts_sorted = sorted(by_host, key=lambda h: (party(h) != "own", -len(by_host[h]), h))

    out = args.output or re.sub(r"[^A-Za-z0-9.-]", "_", page_host or "page") + "_resources.txt"
    lines = [
        f"# Resources of: {page_url}",
        f"# Created: {datetime.datetime.now():%Y-%m-%d %H:%M}",
        "# Static analysis: HTML + stylesheets" + (" + scripts" if args.js else "") + ". "
        "Items loaded later by JavaScript (XHR/fetch, lazy loading, ads) can be missing.",
        "# '?' marks URLs only found as text inside scripts (not confirmed loads).",
        "",
        "=== HOSTS ===",
    ]
    for h in hosts_sorted:
        lines.append(f"{len(by_host[h]):4d}  {h}  ({party(h)})")
    lines += ["", "=== RESOURCES BY HOST ==="]
    for h in hosts_sorted:
        kinds = Counter(k for k, _ in by_host[h])
        lines += ["", f"## {h}  ({party(h)}, {len(by_host[h])} items: " +
                  ", ".join(f"{n} {k}" for k, n in kinds.most_common()) + ")"]
        for kind, u in sorted(by_host[h], key=lambda x: (x[0], short_name(x[1]).lower())):
            line = f"   {kind:<12} {short_name(u)}"
            if args.urls:
                line += f"   <- {u}"
            lines.append(line)
    if warnings:
        lines += ["", "=== WARNINGS ==="] + warnings

    with open(out, "w", encoding="utf-8") as f:
        f.write("\n".join(lines) + "\n")

    third = [h for h in hosts_sorted if party(h) != "own"]
    print(f"\n{len(unique)} resources, {len(hosts_sorted)} hosts ({len(third)} third-party)")
    for h in hosts_sorted[:35]:
        print(f"  {len(by_host[h]):4d}  {h}  ({party(h)})")
    if len(hosts_sorted) > 35:
        print(f"  ... and {len(hosts_sorted) - 35} more hosts in the file")
    print(f"\nSaved to: {out}")


if __name__ == "__main__":
    main()
