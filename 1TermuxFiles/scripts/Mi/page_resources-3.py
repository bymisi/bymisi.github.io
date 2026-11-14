#!/usr/bin/env python3
# Page resource lister: which files does a web page load, and from which host?
# Author: by_[MiSi], developed with the help of Claude (Anthropic AI)
#
# Besides the file names it tells what each HOST is for (analytics, ads, CDN, fonts, ...).
#
# Usage:
#   python page_resources.py https://example.com
#   python page_resources.py https://example.com -o result.txt -u
#   python page_resources.py https://example.com --js
#   python page_resources.py https://example.com --big-lists   (+ large threat-intelligence feed)
#   python page_resources.py https://example.com --update-lists (re-download the threat lists now)
#   python page_resources.py https://example.com --no-lookup   (do not identify unknown hosts; faster)
#
# Static analysis only (no JavaScript is executed): the HTML is downloaded, the
# tags (img, script, link, video, iframe, ...) and the stylesheets (url(), @import)
# are parsed. Python standard library only, works in Termux without installs.

import argparse
import concurrent.futures
import datetime
import gzip
import html as htmlmod
import json
import os
import re
import socket
import ssl
import sys
import time
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


# ---------------------------------------------------------------------------
# What is each host for? Built-in list: domain|category|description
# (the longest matching domain suffix wins, so fonts.googleapis.com beats googleapis.com)
# ---------------------------------------------------------------------------
KNOWN_RAW = """
google-analytics.com|Analytics|Google Analytics: visitor statistics
analytics.google.com|Analytics|Google Analytics
googletagmanager.com|Tag manager|Google Tag Manager: loads other tracking/marketing scripts
googletagservices.com|Advertising|Google ad tags
doubleclick.net|Advertising|Google/DoubleClick ad serving and ad tracking
googlesyndication.com|Advertising|Google AdSense ad serving
googleadservices.com|Advertising|Google Ads conversion tracking
2mdn.net|Advertising|Google ad creatives (DoubleClick)
fonts.googleapis.com|Fonts|Google Fonts (CSS)
fonts.gstatic.com|Fonts|Google Fonts (font files)
ajax.googleapis.com|Library CDN|Google-hosted JavaScript libraries (jQuery etc.)
storage.googleapis.com|Hosting|Google Cloud Storage (files of the site or a third party)
maps.googleapis.com|Maps|Google Maps API
maps.gstatic.com|Maps|Google Maps assets
googleapis.com|Google API|Google APIs
gstatic.com|CDN|Google static content (scripts, images for Google services)
googleusercontent.com|Images|Google-hosted user content (Drive, Photos, Blogger images)
ggpht.com|Images|Google-hosted images (Photos, YouTube avatars)
googlevideo.com|Video|YouTube video streaming
youtube.com|Video|YouTube (embedded player)
youtube-nocookie.com|Video|YouTube embeds (privacy-enhanced mode)
ytimg.com|Images|YouTube thumbnails and images
recaptcha.net|Captcha|Google reCAPTCHA (bot protection)
google.com|Google services|Google (search, Maps, reCAPTCHA, login; depends on the path)
connect.facebook.net|Social|Facebook SDK / Meta Pixel (tracking, like buttons)
facebook.net|Social|Facebook scripts (SDK, Meta Pixel)
facebook.com|Social|Facebook (plugins, pixel, login)
fbcdn.net|Social|Facebook/Instagram content delivery
cdninstagram.com|Social|Instagram content delivery
instagram.com|Social|Instagram embeds
ads-twitter.com|Advertising|X/Twitter ads pixel
twitter.com|Social|X/Twitter (embeds, widgets, tracking)
twimg.com|Social|X/Twitter images and media
t.co|Social|X/Twitter link shortener
x.com|Social|X (Twitter)
linkedin.com|Social|LinkedIn (plugins, Insight tag)
licdn.com|Social|LinkedIn content delivery
analytics.tiktok.com|Advertising|TikTok pixel / analytics
tiktok.com|Social|TikTok (embeds, pixel)
tiktokcdn.com|Social|TikTok content delivery
pinterest.com|Social|Pinterest (widgets, tag)
pinimg.com|Social|Pinterest images
sc-static.net|Advertising|Snap Pixel scripts
snapchat.com|Social|Snapchat
reddit.com|Social|Reddit
redditstatic.com|Social|Reddit static content
redditmedia.com|Social|Reddit media
disqus.com|Social|Disqus comments
disquscdn.com|Social|Disqus content delivery
addthis.com|Social|AddThis sharing buttons (tracking)
sharethis.com|Social|ShareThis sharing buttons (tracking)
clarity.ms|Analytics|Microsoft Clarity: session recording and heatmaps
bat.bing.com|Advertising|Microsoft Advertising UET tracking
bing.com|Advertising|Bing (search, Microsoft Ads)
msn.com|Portal|MSN
live.com|Login|Microsoft account login
microsoftonline.com|Login|Microsoft login
microsoft.com|Microsoft|Microsoft services
azureedge.net|CDN|Microsoft Azure CDN
azurefd.net|CDN|Microsoft Azure Front Door CDN
windows.net|Hosting|Microsoft Azure services (storage, web apps)
sharepoint.com|Hosting|Microsoft SharePoint files
cdnjs.cloudflare.com|Library CDN|cdnjs: open-source JavaScript/CSS libraries
cloudflareinsights.com|Analytics|Cloudflare Web Analytics
challenges.cloudflare.com|Captcha|Cloudflare Turnstile (bot protection)
cloudflarestream.com|Video|Cloudflare Stream video
cloudflare.com|CDN|Cloudflare (CDN, security)
workers.dev|Hosting|Cloudflare Workers (site-specific code)
pages.dev|Hosting|Cloudflare Pages (hosted website)
jsdelivr.net|Library CDN|jsDelivr: open-source libraries and GitHub files
unpkg.com|Library CDN|unpkg: npm package files
bootstrapcdn.com|Library CDN|Bootstrap CDN (Bootstrap, Font Awesome)
code.jquery.com|Library CDN|jQuery CDN
jquery.com|Library CDN|jQuery
fontawesome.com|Fonts|Font Awesome icons
polyfill.io|Library CDN|polyfill.io (WARNING: domain was taken over in 2024 and served malicious code; treat as suspicious)
typekit.net|Fonts|Adobe Fonts (Typekit)
fonts.net|Fonts|Fonts.com web fonts
cloudfront.net|CDN|Amazon CloudFront CDN (used by many sites; owner unknown without a lookup)
amazonaws.com|Hosting|Amazon AWS (S3 storage, APIs, servers)
akamaihd.net|CDN|Akamai CDN
akamai.net|CDN|Akamai CDN
akamaized.net|CDN|Akamai CDN
akamaiedge.net|CDN|Akamai CDN
edgekey.net|CDN|Akamai CDN
fastly.net|CDN|Fastly CDN
fastlylb.net|CDN|Fastly CDN
b-cdn.net|CDN|Bunny CDN
stackpathcdn.com|CDN|StackPath CDN
keycdn.com|CDN|KeyCDN
kxcdn.com|CDN|KeyCDN
cdn77.org|CDN|CDN77
imgix.net|Images|imgix image processing/CDN
cloudinary.com|Images|Cloudinary image/video hosting
wp.com|CDN|WordPress.com / Jetpack CDN (images, scripts)
wordpress.com|Platform|WordPress.com
gravatar.com|Images|Gravatar profile images (reveals e-mail hashes of commenters/users)
w.org|Platform|WordPress.org
wixstatic.com|Platform|Wix site images and files
parastorage.com|Platform|Wix static files
wix.com|Platform|Wix website builder
squarespace-cdn.com|Platform|Squarespace CDN
sqspcdn.com|Platform|Squarespace CDN
squarespace.com|Platform|Squarespace
shopifycdn.com|Platform|Shopify CDN
myshopify.com|Platform|Shopify shop
shopify.com|Platform|Shopify
weebly.com|Platform|Weebly
github.io|Hosting|GitHub Pages (hosted website)
githubusercontent.com|Hosting|GitHub raw files and avatars
githubassets.com|Hosting|GitHub static assets
netlify.app|Hosting|Netlify hosted site
vercel.app|Hosting|Vercel hosted site
herokuapp.com|Hosting|Heroku app
firebaseapp.com|Hosting|Firebase hosting
web.app|Hosting|Firebase hosting
firebaseio.com|Hosting|Firebase Realtime Database
hotjar.com|Analytics|Hotjar: heatmaps and session recording
hs-scripts.com|Marketing|HubSpot tracking script
hs-analytics.net|Analytics|HubSpot analytics
hsforms.net|Marketing|HubSpot forms
hubspot.com|Marketing|HubSpot
segment.com|Analytics|Segment: forwards tracking data to other services
segment.io|Analytics|Segment: forwards tracking data to other services
mixpanel.com|Analytics|Mixpanel
amplitude.com|Analytics|Amplitude
heapanalytics.com|Analytics|Heap
fullstory.com|Analytics|FullStory session recording
mouseflow.com|Analytics|Mouseflow session recording
crazyegg.com|Analytics|Crazy Egg heatmaps
luckyorange.com|Analytics|Lucky Orange session recording
optimizely.com|Analytics|Optimizely A/B testing
visualwebsiteoptimizer.com|Analytics|VWO A/B testing
newrelic.com|Monitoring|New Relic performance monitoring
nr-data.net|Monitoring|New Relic data collection
sentry.io|Monitoring|Sentry error reporting
bugsnag.com|Monitoring|Bugsnag error reporting
datadoghq.com|Monitoring|Datadog monitoring
statcounter.com|Analytics|StatCounter
matomo.cloud|Analytics|Matomo analytics
plausible.io|Analytics|Plausible (privacy-friendly analytics)
scorecardresearch.com|Analytics|Comscore audience measurement
quantserve.com|Analytics|Quantcast audience measurement
quantcount.com|Analytics|Quantcast audience measurement
chartbeat.com|Analytics|Chartbeat
chartbeat.net|Analytics|Chartbeat
parsely.com|Analytics|Parse.ly
gemius.pl|Analytics|Gemius audience measurement (common on Hungarian/Polish sites)
adocean-global.com|Advertising|AdOcean ad serving (Gemius)
yandex.ru|Analytics|Yandex (Metrica analytics, search)
baidu.com|Analytics|Baidu (Tongji analytics, search)
demdex.net|Advertising|Adobe Audience Manager (cross-site tracking)
omtrdc.net|Analytics|Adobe Analytics
2o7.net|Analytics|Adobe Analytics (legacy)
everesttech.net|Advertising|Adobe Advertising Cloud tracking
adobedtm.com|Tag manager|Adobe Launch tag manager
adnxs.com|Advertising|Xandr/AppNexus ad exchange
criteo.com|Advertising|Criteo retargeting
criteo.net|Advertising|Criteo retargeting
taboola.com|Advertising|Taboola content recommendation ads
outbrain.com|Advertising|Outbrain content recommendation ads
amazon-adsystem.com|Advertising|Amazon advertising
adsrvr.org|Advertising|The Trade Desk ad platform
rubiconproject.com|Advertising|Magnite/Rubicon ad exchange
pubmatic.com|Advertising|PubMatic ad exchange
openx.net|Advertising|OpenX ad exchange
casalemedia.com|Advertising|Index Exchange ad exchange
smartadserver.com|Advertising|Smart/Equativ ad server
adform.net|Advertising|Adform ad platform
moatads.com|Advertising|Oracle Moat ad measurement
doubleverify.com|Advertising|DoubleVerify ad verification
adsafeprotected.com|Advertising|Integral Ad Science verification
krxd.net|Advertising|Salesforce DMP (Krux) tracking
bluekai.com|Advertising|Oracle BlueKai tracking
agkn.com|Advertising|Neustar/TransUnion tracking
rlcdn.com|Advertising|LiveRamp tracking
3lift.com|Advertising|TripleLift ad exchange
teads.tv|Advertising|Teads video ads
mathtag.com|Advertising|MediaMath tracking
cookiebot.com|Consent|Cookiebot cookie consent banner
onetrust.com|Consent|OneTrust cookie consent
cookielaw.org|Consent|OneTrust cookie consent (CDN)
usercentrics.eu|Consent|Usercentrics cookie consent
trustarc.com|Consent|TrustArc cookie consent
didomi.io|Consent|Didomi cookie consent
consensu.org|Consent|IAB TCF cookie consent vendors
iubenda.com|Consent|iubenda cookie/privacy policies
stripe.com|Payment|Stripe payments
stripe.network|Payment|Stripe payments
paypal.com|Payment|PayPal
paypalobjects.com|Payment|PayPal static files
braintreegateway.com|Payment|Braintree payments
adyen.com|Payment|Adyen payments
klarna.com|Payment|Klarna payments
mollie.com|Payment|Mollie payments
openstreetmap.org|Maps|OpenStreetMap (map tiles, data)
mapbox.com|Maps|Mapbox maps
vimeo.com|Video|Vimeo player
vimeocdn.com|Video|Vimeo video/thumbnail delivery
wistia.com|Video|Wistia video
wistia.net|Video|Wistia video
jwplayer.com|Video|JW Player
jwpcdn.com|Video|JW Player CDN
brightcove.net|Video|Brightcove video
twitch.tv|Video|Twitch embeds
ttvnw.net|Video|Twitch video delivery
dailymotion.com|Video|Dailymotion
indavideo.hu|Video|Indavideo video hosting
spotify.com|Audio|Spotify embeds
scdn.co|Audio|Spotify content delivery
soundcloud.com|Audio|SoundCloud embeds
sndcdn.com|Audio|SoundCloud content delivery
intercom.io|Support chat|Intercom chat widget
intercomcdn.com|Support chat|Intercom chat widget assets
zendesk.com|Support chat|Zendesk support/chat
zdassets.com|Support chat|Zendesk assets
drift.com|Support chat|Drift chat
crisp.chat|Support chat|Crisp chat
tawk.to|Support chat|Tawk.to live chat
livechatinc.com|Support chat|LiveChat
tidio.co|Support chat|Tidio chat
olark.com|Support chat|Olark chat
hcaptcha.com|Captcha|hCaptcha bot protection
arkoselabs.com|Captcha|Arkose Labs bot protection
mailchimp.com|Marketing|Mailchimp
list-manage.com|Marketing|Mailchimp signup forms
chimpstatic.com|Marketing|Mailchimp scripts
klaviyo.com|Marketing|Klaviyo email marketing tracking
sendgrid.net|Marketing|SendGrid email links/tracking
imgur.com|Images|Imgur image hosting
giphy.com|Images|GIPHY animated images
tenor.com|Images|Tenor GIFs
unsplash.com|Images|Unsplash stock photos
pexels.com|Images|Pexels stock photos
staticflickr.com|Images|Flickr images
flickr.com|Images|Flickr
media-amazon.com|Images|Amazon product images
ssl-images-amazon.com|Images|Amazon product images
dropbox.com|Hosting|Dropbox
dropboxusercontent.com|Hosting|Dropbox shared files
box.com|Hosting|Box file sharing
boxcloud.com|Hosting|Box file downloads
"""
KNOWN = {}
for _line in KNOWN_RAW.strip().splitlines():
    _dom, _cat, _desc = _line.split("|", 2)
    KNOWN[_dom] = (_cat, _desc)

# categories that usually mean tracking, advertising or social-network connections
TRACKING_CATS = {"Analytics", "Advertising", "Tag manager", "Marketing", "Social"}

GUESS_RULES = [
    ("tracking/analytics", r"analytic|stats?\b|statistic|metric|telemetry|track|pixel|beacon|collect"),
    ("advertising", r"\bads?\b|adserv|banner|adsys"),
    ("marketing/campaigns", r"campaign|newsletter|\bmail|\bcrm\b|promo"),
    ("CDN / static files", r"cdn|static|assets?|media|\bimg|image|files|content|cache"),
    ("fonts", r"font"),
    ("API / backend", r"\bapi\b|graphql|gateway"),
    ("video / streaming", r"video|vod|stream|player"),
    ("maps / tiles", r"\bmaps?\b|tile"),
    ("login / authentication", r"login|auth|\bsso\b|accounts|\bid\b"),
    ("payments", r"\bpay|checkout"),
]
LABEL_CAT = {
    "tracking/analytics": "Analytics", "advertising": "Advertising", "marketing/campaigns": "Marketing",
    "CDN / static files": "CDN / static", "fonts": "Fonts", "API / backend": "API",
    "video / streaming": "Video", "maps / tiles": "Maps", "login / authentication": "Login",
    "payments": "Payment",
}
PROVIDERS = [
    ("amazonaws.com", "Amazon AWS"), ("cloudfront", "Amazon CloudFront"), ("1e100.net", "Google"),
    ("googleusercontent", "Google"), ("cloudflare", "Cloudflare"), ("akamai", "Akamai"),
    ("fastly", "Fastly"), ("azure", "Microsoft Azure"), ("windows.net", "Microsoft Azure"),
    ("hetzner", "Hetzner"), ("your-server.de", "Hetzner"), ("ovh", "OVH"),
    ("digitalocean", "DigitalOcean"), ("linode", "Linode"), ("scaleway", "Scaleway"),
    ("contabo", "Contabo"), ("vultr", "Vultr"), ("rackforest", "Rackforest (HU hosting)"),
    ("dotroll", "DotRoll (HU hosting)"),
]
KIND_TEXT = {
    "img": "images", "icon": "icons", "font": "fonts", "css": "stylesheets", "script": "scripts",
    "video": "video", "audio": "audio", "media": "media files", "iframe": "an embedded page/widget",
    "embed": "embedded content", "css-url": "files used by CSS", "preload": "preloaded files",
    "connect-hint": "nothing yet (the page only opens an early connection to it)",
    "js-ref?": "nothing confirmed (the address only appears as text in a script)",
    "manifest": "the web app manifest", "track": "subtitles",
}


def describe(host):
    """(category, description) from the built-in list, or None."""
    parts = host.lower().split(".")
    for i in range(len(parts) - 1):
        hit = KNOWN.get(".".join(parts[i:]))
        if hit:
            return hit
    return None


def guess_purpose(host, kinds):
    text = host.lower().replace(".", " ").replace("-", " ")
    hints = [label for label, rx in GUESS_RULES if re.search(rx, text)]
    served = ", ".join(KIND_TEXT.get(k, k) for k in sorted(kinds))
    out = "Not in the built-in list."
    if hints:
        out += " The name suggests: " + ", ".join(hints) + "."
    if served:
        out += " On this page it serves: " + served + "."
    return out


def guess_category(host, kinds):
    """Best guess for a host that is not in the built-in list (from its name and what it serves)."""
    text = host.lower().replace(".", " ").replace("-", " ")
    for label, rx in GUESS_RULES:
        if re.search(rx, text):
            return LABEL_CAT[label] + " (guess)"
    k = set(kinds)
    if k <= {"img", "icon"}:
        return "Image host (guess)"
    if k <= {"font"}:
        return "Fonts (guess)"
    if k <= {"script"}:
        return "Script host (guess)"
    if k <= {"css", "css-url"}:
        return "Stylesheet host (guess)"
    if k <= {"video", "audio", "media"}:
        return "Media host (guess)"
    if k <= {"iframe", "embed"}:
        return "Embedded widget (guess)"
    if k <= {"connect-hint"}:
        return "Connection hint (guess)"
    if k <= {"js-ref?"}:
        return "Mentioned in a script (guess)"
    return "Web / media server (guess)"


def provider_of(*texts):
    t = " ".join(x for x in texts if x).lower()
    for key, name in PROVIDERS:
        if key in t:
            return name
    return ""


def cdn_from_headers(h):
    server = h.get("server", "").lower()
    via = h.get("via", "").lower()
    if "cf-ray" in h or "cloudflare" in server:
        return "Cloudflare"
    if "x-amz-cf-id" in h or "cloudfront" in via:
        return "Amazon CloudFront"
    if "fastly" in via or "cache-" in h.get("x-served-by", ""):
        return "Fastly"
    if any(k.startswith("x-akamai") for k in h) or "akamai" in server:
        return "Akamai"
    if "x-vercel-id" in h:
        return "Vercel"
    if "x-azure-ref" in h:
        return "Microsoft Azure"
    if "x-github-request-id" in h:
        return "GitHub Pages"
    if server in ("gws", "esf", "gse", "ghs") or "google frontend" in server:
        return "Google"
    return ""


def probe(host):
    """Collect facts about a host (needs network): DNS, TLS certificate, what its front page says."""
    f = {"host": host}
    try:
        f["ip"] = socket.gethostbyname(host)
    except OSError:
        f["dns"] = False
        return f
    try:
        f["rdns"] = socket.gethostbyaddr(f["ip"])[0]
    except OSError:
        pass
    try:
        ctx = ssl.create_default_context()
        with socket.create_connection((host, 443), timeout=5) as raw:
            with ctx.wrap_socket(raw, server_hostname=host) as tls:
                cert = tls.getpeercert()
        f["org"] = dict(x[0] for x in cert.get("subject", ())).get("organizationName", "")
        f["issuer"] = dict(x[0] for x in cert.get("issuer", ())).get("organizationName", "")
        f["san"] = [v.lower() for k, v in cert.get("subjectAltName", ()) if k == "DNS"]
    except (OSError, ssl.SSLError, ValueError):
        pass

    body, hdr = "", {}
    req = urllib.request.Request("https://" + host + "/", headers={
        "User-Agent": UA, "Accept": "text/html,*/*", "Accept-Encoding": "identity"})
    try:
        with urllib.request.urlopen(req, timeout=6) as resp:
            body = resp.read(60000).decode("utf-8", errors="replace")
            hdr = {k.lower(): v for k, v in resp.headers.items()}
            f["status"] = resp.status
            f["final_host"] = urllib.parse.urlparse(resp.geturl()).hostname or host
    except urllib.error.HTTPError as e:
        hdr = {k.lower(): v for k, v in e.headers.items()}
        f["status"] = e.code
        try:
            body = e.read(60000).decode("utf-8", errors="replace")
        except Exception:
            pass
    except (urllib.error.URLError, OSError, ValueError):
        pass

    m = re.search(r"<title[^>]*>(.*?)</title>", body, re.I | re.S)
    if m:
        title = " ".join(htmlmod.unescape(m.group(1)).split())[:80]
        if title and not re.search(
                r"^\d{3}\b|forbidden|not found|error|denied|unauthori[sz]ed|just a moment", title, re.I):
            f["title"] = title
    f["server"] = hdr.get("server", "")
    f["cdn"] = cdn_from_headers(hdr)
    f["provider"] = f["cdn"] or provider_of(f.get("rdns"), hdr.get("server"), hdr.get("via"))
    return f


def safe_probe(host):
    try:
        return probe(host)
    except Exception as e:
        return {"host": host, "error": str(e)}


def facts_line(f):
    if f.get("dns") is False:
        return "does not resolve in DNS"
    if f.get("error"):
        return "lookup failed: " + f["error"]
    bits = [f.get("ip", "")]
    if f.get("rdns"):
        bits.append("reverse DNS " + f["rdns"])
    if f.get("provider"):
        bits.append("runs on " + f["provider"])
    if f.get("server"):
        bits.append("server " + f["server"])
    if f.get("status"):
        bits.append("HTTP " + str(f["status"]))
    if f.get("final_host") and f["final_host"] != f["host"]:
        bits.append("redirects to " + f["final_host"])
    if f.get("title"):
        bits.append('front page title "' + f["title"] + '"')
    if f.get("org"):
        bits.append("certificate owner " + f["org"])
    elif f.get("issuer"):
        bits.append("certificate issued by " + f["issuer"])
    return ", ".join(b for b in bits if b)


def refine(host, f, page_base, own_san, current, kinds):
    """Upgrade a guess to a real identification when the lookup found evidence."""
    served = ", ".join(KIND_TEXT.get(k, k) for k in sorted(kinds))
    on_page = f" On this page it serves: {served}." if served else ""
    sans = {base_domain(n.lstrip("*.")) for n in f.get("san", [])}
    own = {base_domain(n.lstrip("*.")) for n in own_san}
    if page_base in sans or base_domain(host) in own:
        return ("Related site", f"Same operator as the page (shared TLS certificate with {page_base})." + on_page)
    if f.get("title"):
        return ("Website", f'Serves a website titled "{f["title"]}".' + on_page)
    cat, desc = current
    if f.get("provider"):
        desc += f" Hosted on {f['provider']}."
    return (cat, desc)


# ---------------------------------------------------------------------------
# Threat / tracker lists (downloaded once, cached for 7 days in ~/.cache/page_resources)
# Tags starting with "!" in the output mean a security-relevant listing.
# A listing is a signal, not proof; "not listed" does not prove a host is safe.
# If a download link stops working, change or add a URL here (they are tried in order).
# ---------------------------------------------------------------------------
CACHE_DIR = os.path.join(os.path.expanduser("~"), ".cache", "page_resources")
LIST_MAX_AGE_DAYS = 7
THREAT_TAGS = {"malware", "phishing", "botnet C2", "unwanted software", "malicious (threat feed)"}

# (name, tag, format, [urls], only with --big-lists)
THREAT_LISTS = [
    ("URLhaus malware domains", "malware", "domains", [
        "https://cdn.jsdelivr.net/gh/curbengh/urlhaus-filter/urlhaus-filter-domains-online.txt",
        "https://malware-filter.gitlab.io/malware-filter/urlhaus-filter-domains-online.txt",
        "https://malware-filter.pages.dev/urlhaus-filter-domains-online.txt"], False),
    ("Phishing URL blocklist", "phishing", "domains", [
        "https://malware-filter.pages.dev/phishing-filter-domains.txt",
        "https://malware-filter.gitlab.io/malware-filter/phishing-filter-domains.txt",
        "https://curbengh.github.io/malware-filter/phishing-filter-domains.txt"], False),
    ("PUP filter", "unwanted software", "domains", [
        "https://malware-filter.pages.dev/pup-filter-domains.txt",
        "https://curben.gitlab.io/malware-filter/pup-filter-domains.txt"], False),
    ("Feodo Tracker botnet C2 IPs", "botnet C2", "ips", [
        "https://feodotracker.abuse.ch/downloads/ipblocklist.txt"], False),
    ("Disconnect tracker list", None, "disconnect", [
        "https://raw.githubusercontent.com/disconnectme/disconnect-tracking-protection/master/services.json"], False),
    ("Peter Lowe ad servers", "advertising", "hosts", [
        "https://pgl.yoyo.org/adservers/serverlist.php?hostformat=hosts&showintro=0&mimetype=plaintext"], False),
    ("HaGeZi threat intelligence feeds", "malicious (threat feed)", "domains", [
        "https://raw.githubusercontent.com/hagezi/dns-blocklists/main/wildcard/tif-onlydomains.txt"], True),
]
# Disconnect category -> (our category, tag)
DC_MAP = {
    "Advertising": ("Advertising", "advertising"),
    "Analytics": ("Analytics", "analytics/tracking"),
    "Social": ("Social", "social tracker"),
    "FingerprintingGeneral": ("Analytics", "fingerprinting"),
    "FingerprintingInvasive": ("Analytics", "fingerprinting (invasive)"),
    "Cryptomining": ("Cryptomining", "!cryptomining"),
}
CAT_TAGS = {
    "Analytics": "analytics/tracking", "Advertising": "advertising", "Tag manager": "tracking",
    "Marketing": "marketing/tracking", "Social": "social tracker", "Cryptomining": "!cryptomining",
}


def suffixes(host):
    parts = host.lower().split(".")
    return [".".join(parts[i:]) for i in range(len(parts) - 1)]


def download_list(url, path, max_bytes=60_000_000):
    req = urllib.request.Request(url, headers={"User-Agent": UA, "Accept-Encoding": "gzip"})
    with urllib.request.urlopen(req, timeout=40) as resp:
        data = resp.read(max_bytes)
        if (resp.headers.get("Content-Encoding") or "").lower() == "gzip":
            data = gzip.decompress(data)
    with open(path + ".part", "wb") as out:
        out.write(data)
    os.replace(path + ".part", path)


def parse_list(fmt, text):
    out = set()
    for line in text.splitlines():
        line = line.strip()
        if not line or line[0] in "#!":
            continue
        parts = line.split()
        tok = parts[1] if fmt == "hosts" and len(parts) > 1 else parts[0]
        tok = tok.lower().strip(".")
        if tok in ("localhost", "0.0.0.0", "127.0.0.1", "::1"):
            continue
        if re.fullmatch(r"[a-z0-9._-]+", tok):
            out.add(tok)
    return out


def load_disconnect(text):
    mapping = {}
    for cat, entries in json.loads(text).get("categories", {}).items():
        if cat not in DC_MAP:
            continue
        our_cat, tag = DC_MAP[cat]
        for entry in entries:
            for company, sub in entry.items():
                if not isinstance(sub, dict):
                    continue
                for domains in sub.values():
                    if isinstance(domains, list):
                        for d in domains:
                            mapping.setdefault(d.lower(), (our_cat, company, tag))
    return mapping


def load_intel(update=False, big=False):
    intel = {"sets": [], "dc": {}, "ok": [], "failed": []}
    try:
        os.makedirs(CACHE_DIR, exist_ok=True)
    except OSError:
        pass
    for name, tag, fmt, urls, is_big in THREAT_LISTS:
        if is_big and not big:
            continue
        path = os.path.join(CACHE_DIR, re.sub(r"\W+", "_", name).strip("_")[:50] + ".txt")
        fresh = os.path.exists(path) and time.time() - os.path.getmtime(path) < LIST_MAX_AGE_DAYS * 86400
        if update or not fresh:
            err = None
            for u in urls:
                try:
                    print(f"    downloading {name} ...")
                    download_list(u, path)
                    err = None
                    break
                except Exception as e:
                    err = e
            if err is not None and not os.path.exists(path):
                intel["failed"].append(f"{name}: {err}")
                continue
        try:
            with open(path, encoding="utf-8", errors="replace") as f:
                text = f.read()
            if fmt == "disconnect":
                intel["dc"] = load_disconnect(text)
                intel["ok"].append(f"{name} ({len(intel['dc'])} domains)")
            else:
                data = parse_list(fmt, text)
                intel["sets"].append((name, tag, "ips" if fmt == "ips" else "domains", data))
                intel["ok"].append(f"{name} ({len(data)} entries)")
        except Exception as e:
            intel["failed"].append(f"{name}: {e}")
    return intel


def resolve_ip(host):
    try:
        return socket.gethostbyname(host)
    except OSError:
        return None


def intel_hits(intel, host, ip):
    """[(tag, list name)] for every threat/ad list that contains the host (or its IP)."""
    out, sfx = [], suffixes(host)
    for name, tag, kind, data in intel["sets"]:
        if kind == "ips":
            hit = bool(ip and ip in data)
        else:
            hit = any(x in data for x in sfx) or bool(ip and ip in data)
        if hit:
            out.append((tag, name))
    return out


def intel_disconnect(intel, host):
    for x in suffixes(host):
        if x in intel["dc"]:
            return intel["dc"][x]
    return None


def host_tags(cat, hit_list, dc):
    tags = []
    t = CAT_TAGS.get(cat.split(" (")[0])
    if t:
        tags.append(t + ("?" if "(guess)" in cat else ""))
    if dc:
        tags.append(dc[2])
    for tag, _name in hit_list:
        tags.append(("!" if tag in THREAT_TAGS else "") + tag)
    seen = []
    for t in tags:
        if t not in seen:
            seen.append(t)
    return seen


def threat_section(hosts_sorted, hits, intel, skipped):
    if skipped:
        return ["Threat lists were skipped (--no-threat)."]
    flagged = []
    for h in hosts_sorted:
        bad = [f"{tag} ({name})" for tag, name in hits[h] if tag in THREAT_TAGS]
        if bad:
            flagged.append((h, bad))
    lines = [f"!! {h}: {', '.join(bad)}" for h, bad in flagged]
    if not flagged:
        lines.append("No host was found on the malware / phishing / botnet lists.")
    lines.append("A listing is a signal, not proof (shared hosting and hacked sites can be listed); "
                 "not being listed does not prove a host is safe.")
    if intel["ok"]:
        lines.append("Lists used: " + "; ".join(intel["ok"]))
    for f in intel["failed"]:
        lines.append("List not available: " + f)
    return lines


def main():
    ap = argparse.ArgumentParser(description="List the resources of a web page, grouped by host.")
    ap.add_argument("url")
    ap.add_argument("-o", "--output", help="output text file (default: <host>_resources.txt)")
    ap.add_argument("-u", "--urls", action="store_true", help="write the full URL after each name")
    ap.add_argument("--no-css", action="store_true", help="do not download stylesheets (faster)")
    ap.add_argument("--js", action="store_true",
                    help="also download scripts and list URLs written in them (not confirmed loads)")
    ap.add_argument("--no-lookup", action="store_true",
                    help="do not connect to unidentified hosts (by default their front page, "
                         "certificate and DNS are checked to find out what they are)")
    ap.add_argument("--no-threat", action="store_true",
                    help="do not use malware/phishing/botnet/tracker lists")
    ap.add_argument("--update-lists", action="store_true", help="re-download the threat lists now")
    ap.add_argument("--big-lists", action="store_true",
                    help="also use the large HaGeZi threat-intelligence feed (more memory and data)")
    ap.add_argument("--lookup", action="store_true", help=argparse.SUPPRESS)  # old option, now the default
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

    intel = {"sets": [], "dc": {}, "ok": [], "failed": []}
    if not args.no_threat:
        print("  loading threat / tracker lists (cached for 7 days) ...")
        intel = load_intel(update=args.update_lists, big=args.big_lists)
        for failed in intel["failed"]:
            print(f"    (list not available: {failed})")
    ips = {}
    if any(kind == "ips" for _, _, kind, _ in intel["sets"]):
        subset = hosts_sorted[:150]
        with concurrent.futures.ThreadPoolExecutor(max_workers=16) as pool:
            ips = dict(zip(subset, pool.map(resolve_ip, subset)))
    hits = {h: intel_hits(intel, h, ips.get(h)) for h in hosts_sorted}
    dc = {h: intel_disconnect(intel, h) for h in hosts_sorted}

    purpose = {}
    unidentified = []
    for h in hosts_sorted:
        kinds_h = {k for k, _ in by_host[h]}
        known_hit = describe(h)
        if h == page_host:
            purpose[h] = ("Own site", "The website's own server (first-party)")
        elif known_hit:
            purpose[h] = known_hit
        elif dc[h]:
            purpose[h] = (dc[h][0], f"{dc[h][1]} (per the Disconnect tracker list)")
        elif party(h) == "own":
            purpose[h] = ("Own site", "Subdomain of the site's own domain. " + guess_purpose(h, kinds_h))
        else:
            purpose[h] = (guess_category(h, kinds_h), guess_purpose(h, kinds_h))
            unidentified.append(h)

    lookups = {}
    if unidentified and not args.no_lookup:
        todo = unidentified[:60]
        print(f"  identifying {len(todo)} host(s) not in the built-in list ...")
        with concurrent.futures.ThreadPoolExecutor(max_workers=8) as pool:
            own_job = pool.submit(safe_probe, page_host)
            jobs = {h: pool.submit(safe_probe, h) for h in todo}
            own_san = set(own_job.result().get("san", []))
            for h, job in jobs.items():
                facts = job.result()
                lookups[h] = facts_line(facts)
                kinds_h = {k for k, _ in by_host[h]}
                purpose[h] = refine(h, facts, page_base, own_san, purpose[h], kinds_h)

    tags = {h: host_tags(purpose[h][0], hits[h], dc[h]) for h in hosts_sorted}
    labels = {h: ", ".join([party(h)] + tags[h]) for h in hosts_sorted}
    threat_lines = threat_section(hosts_sorted, hits, intel, args.no_threat)

    by_cat = defaultdict(list)
    for h in hosts_sorted:
        by_cat[purpose[h][0]].append(h)
    summary_lines = [f"{c} ({len(hs)}): {', '.join(hs)}"
                     for c, hs in sorted(by_cat.items(), key=lambda x: (-len(x[1]), x[0]))]
    tracking = [h for h in hosts_sorted if purpose[h][0].split(" (")[0] in TRACKING_CATS]
    summary_lines += ["", f"Hosts that are typically tracking-, advertising- or social-related: "
                          f"{len(tracking)} of {len(hosts_sorted)}"]

    out = args.output or re.sub(r"[^A-Za-z0-9.-]", "_", page_host or "page") + "_resources.txt"
    lines = [
        f"# Resources of: {page_url}",
        f"# Created: {datetime.datetime.now():%Y-%m-%d %H:%M}",
        "# Static analysis: HTML + stylesheets" + (" + scripts" if args.js else "") + ". "
        "Items loaded later by JavaScript (XHR/fetch, lazy loading, ads) can be missing.",
        "# '?' marks URLs only found as text inside scripts (not confirmed loads).",
        "",
        "=== THREAT FLAGS ===",
    ] + threat_lines + ["", "=== PURPOSE SUMMARY ==="] + summary_lines + ["", "=== HOSTS ==="]
    for h in hosts_sorted:
        lines.append(f"{len(by_host[h]):4d}  {h}  ({labels[h]})  [{purpose[h][0]}] {purpose[h][1]}")
    lines += ["", "=== RESOURCES BY HOST ==="]
    for h in hosts_sorted:
        kinds = Counter(k for k, _ in by_host[h])
        lines += ["", f"## {h}  ({labels[h]}, {len(by_host[h])} items: " +
                  ", ".join(f"{n} {k}" for k, n in kinds.most_common()) + ")",
                  f"   Purpose: [{purpose[h][0]}] {purpose[h][1]}"] + \
                 ([f"   Lookup:  {lookups[h]}"] if h in lookups else [])
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
    print(f"{len(tracking)} host(s) are typically tracking-, advertising- or social-related")
    for line in threat_lines:
        if line.startswith("!!"):
            print(line)
    for h in hosts_sorted[:15]:
        print(f"  {len(by_host[h]):4d}  {h}  [{purpose[h][0]}]" + (" " + " ".join(tags[h]) if tags[h] else ""))
    if len(hosts_sorted) > 15:
        print(f"  ... and {len(hosts_sorted) - 15} more hosts in the file")
    print(f"\nSaved to: {out}")


if __name__ == "__main__":
    main()
