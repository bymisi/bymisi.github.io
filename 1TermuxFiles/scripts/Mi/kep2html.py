#!/usr/bin/env python3
"""
kep2html.py - kep kivalasztasa es beagyazasa HTML-be (base64 data URI).

Hasznalat Termuxban:
    python kep2html.py              -> fajlvalaszto nyilik
    python kep2html.py kep.jpg      -> kozvetlenul a megadott kep

Elso alkalommal (egyszer):
    termux-setup-storage            (tarhely-engedely)
    pkg install termux-api          (+ a Termux:API app az F-Droidrol/GitHubrol,
                                     ez kell az Android sajat fajlvalasztojahoz)
    pkg install python-pillow       (opcionalis, a kep kicsinyitesehez)

Ha a termux-api nincs meg, a script magatol atall egy egyszeru
szoveges mappalistara, ami mindenhol mukodik.
"""

import base64
import os
import shutil
import subprocess
import sys
import tempfile
from pathlib import Path

try:
    from PIL import Image  # opcionalis
except Exception:
    Image = None

KEP_KITERJESZTESEK = {".jpg", ".jpeg", ".png", ".gif", ".webp", ".svg"}


# ---------------------------------------------------------------- segedek
def tipus_felismer(adat: bytes):
    """Visszaadja (mime, kiterjesztes) a fajl elso bajtjai alapjan."""
    if adat.startswith(b"\xff\xd8\xff"):
        return "image/jpeg", ".jpg"
    if adat.startswith(b"\x89PNG\r\n\x1a\n"):
        return "image/png", ".png"
    if adat.startswith(b"GIF8"):
        return "image/gif", ".gif"
    if adat[:4] == b"RIFF" and adat[8:12] == b"WEBP":
        return "image/webp", ".webp"
    kezdet = adat[:300].lstrip().lower()
    if kezdet.startswith(b"<svg") or (kezdet.startswith(b"<?xml") and b"<svg" in adat[:2000].lower()):
        return "image/svg+xml", ".svg"
    return None, None


def meret_szep(n: int) -> str:
    if n < 1024:
        return f"{n} B"
    if n < 1024 * 1024:
        return f"{n / 1024:.1f} KB"
    return f"{n / 1024 / 1024:.2f} MB"


def kimeneti_mappa() -> Path:
    for p in (Path.home() / "storage" / "shared" / "Download", Path.home() / "storage" / "downloads"):
        if p.is_dir():
            return p
    return Path.cwd()


# ---------------------------------------------------------------- valaszto
def valaszto_android():
    """Az Android sajat fajlvalasztoja (termux-api). None, ha nem megy."""
    if not shutil.which("termux-storage-get"):
        return None
    tmp = Path(tempfile.gettempdir()) / "kep2html_valasztott.bin"
    if tmp.exists():
        tmp.unlink()
    print("Megnyilik az Android fajlvalasztoja - valaszd ki a kepet...")
    try:
        subprocess.run(["termux-storage-get", str(tmp)], timeout=180)
    except Exception:
        return None
    if tmp.exists() and tmp.stat().st_size > 0:
        return tmp
    return None


def valaszto_szoveges(kezdo: Path):
    """Egyszeru, szoveges mappabongeszo. Mindenhol mukodik."""
    aktualis = kezdo
    while True:
        try:
            elemek = sorted(aktualis.iterdir(), key=lambda x: (not x.is_dir(), x.name.lower()))
        except PermissionError:
            print("Nincs jogosultsag ehhez a mappahoz.")
            aktualis = aktualis.parent
            continue
        mappak = [e for e in elemek if e.is_dir() and not e.name.startswith(".")]
        kepek = [e for e in elemek if e.is_file() and e.suffix.lower() in KEP_KITERJESZTESEK]
        lista = mappak + kepek

        print("\n" + "=" * 50)
        print(f"Mappa: {aktualis}")
        print("=" * 50)
        if not lista:
            print("(ures, vagy nincs benne kep)")
        for i, e in enumerate(lista, 1):
            if e.is_dir():
                print(f"{i:3}. [MAPPA] {e.name}/")
            else:
                print(f"{i:3}.        {e.name}  ({meret_szep(e.stat().st_size)})")
        print("  0. Vissza (felsobb mappa)    q. Kilepes")
        print("  Vagy ird be egy fajl teljes utvonalat.")

        valasz = input("\nValasztas: ").strip().strip("'\"")
        if valasz.lower() in ("q", "quit", "exit"):
            return None
        if valasz == "0":
            if aktualis.parent != aktualis:
                aktualis = aktualis.parent
            continue
        if valasz.isdigit():
            n = int(valasz)
            if 1 <= n <= len(lista):
                valasztott = lista[n - 1]
                if valasztott.is_dir():
                    aktualis = valasztott
                    continue
                return valasztott
            print("Ervenytelen szam.")
            continue
        # beirt utvonal
        p = Path(valasz).expanduser()
        if p.is_file():
            return p
        if p.is_dir():
            aktualis = p
            continue
        print("Nem talalom.")


def kep_kivalaszt():
    if len(sys.argv) > 1:
        p = Path(sys.argv[1]).expanduser()
        if p.is_file():
            return p
        print(f"Nem talalom a fajlt: {p}")
        return None
    p = valaszto_android()
    if p:
        return p
    print("(Az Android-valaszto nem erheto el, szoveges bongeszore valtok.)")
    kezdo = Path.home() / "storage" / "shared"
    if not kezdo.is_dir():
        print("Tipp: futtasd egyszer a  termux-setup-storage  parancsot, hogy lassam a telefon fajljait.")
        kezdo = Path.home()
    return valaszto_szoveges(kezdo)


# ---------------------------------------------------------------- kicsinyites
def kicsinyit(adat: bytes, mime: str, max_px: int):
    """Opcionalis atmeretezes Pillow-val. Ha nem megy, az eredetit adja vissza."""
    if Image is None or mime in ("image/gif", "image/svg+xml"):
        return adat, mime
    import io
    try:
        kep = Image.open(io.BytesIO(adat))
        kep.thumbnail((max_px, max_px))
        ki = io.BytesIO()
        if mime == "image/jpeg":
            kep.convert("RGB").save(ki, "JPEG", quality=85, optimize=True)
        elif mime == "image/png":
            kep.save(ki, "PNG", optimize=True)
        elif mime == "image/webp":
            kep.save(ki, "WEBP", quality=85)
        else:
            return adat, mime
        return ki.getvalue(), mime
    except Exception as hiba:
        print(f"Kicsinyitesi hiba ({hiba}), az eredeti kepet hasznalom.")
        return adat, mime


# ---------------------------------------------------------------- fo resz
def main():
    print("=== Kep -> HTML (base64) konverter ===")
    forras = kep_kivalaszt()
    if not forras:
        print("Nincs kivalasztott kep. Kilepes.")
        return

    adat = Path(forras).read_bytes()
    mime, kit = tipus_felismer(adat)
    if not mime:
        print("Ez nem tunik tamogatott kepnek (JPG, PNG, GIF, WEBP, SVG).")
        return

    print(f"\nKiválasztva: {Path(forras).name}  ({mime}, {meret_szep(len(adat))})")

    if Image is not None and mime not in ("image/gif", "image/svg+xml"):
        v = input("Legnagyobb meret pixelben (pl. 320; Enter = nincs kicsinyites): ").strip()
        if v.isdigit() and int(v) > 0:
            adat, mime = kicsinyit(adat, mime, int(v))
            print(f"Kicsinyitve: {meret_szep(len(adat))}")
    elif len(adat) > 500 * 1024:
        print("Tipp: a kep nagy. A  pkg install python-pillow  utan kicsinyiteni is tudom.")

    b64 = base64.b64encode(adat).decode("ascii")
    data_uri = f"data:{mime};base64,{b64}"

    alap = Path(forras).stem if len(sys.argv) > 1 or str(forras).endswith(tuple(KEP_KITERJESZTESEK)) else "kep"
    nev = input(f"Kimeneti fajl neve kiterjesztes nelkul [{alap}]: ").strip() or alap

    kimenet = kimeneti_mappa()
    snippet = f'<img src="{data_uri}" alt="{nev}">\n'
    oldal = (
        "<!DOCTYPE html>\n"
        '<html lang="hu"><head><meta charset="utf-8">\n'
        '<meta name="viewport" content="width=device-width,initial-scale=1">\n'
        f"<title>{nev}</title></head>\n"
        '<body style="margin:0;background:#f4f4f4;text-align:center">\n'
        f'<img src="{data_uri}" alt="{nev}" style="max-width:100%;height:auto">\n'
        "</body></html>\n"
    )

    f_snippet = kimenet / f"{nev}_snippet.html"
    f_oldal = kimenet / f"{nev}_oldal.html"
    f_snippet.write_text(snippet, encoding="utf-8")
    f_oldal.write_text(oldal, encoding="utf-8")

    print("\nKesz!")
    print(f"  Beillesztheto kod (csak az <img>): {f_snippet}  ({meret_szep(f_snippet.stat().st_size)})")
    print(f"  Onallo HTML oldal:                 {f_oldal}")

    if shutil.which("termux-clipboard-set"):
        try:
            subprocess.run(["termux-clipboard-set"], input=snippet.encode("utf-8"), timeout=10)
            print("  A beillesztheto kod a vágólapra is kerult.")
        except Exception:
            pass
    print("\nA kodot a sajat HTML-edbe ugyanott illeszd be, ahol a kepet szeretned.")


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\nMegszakitva.")
