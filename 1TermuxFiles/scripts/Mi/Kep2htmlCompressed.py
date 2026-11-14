import base64
import io
import os
import sys

try:
    import readline
    readline.parse_and_bind("tab: complete")
except ImportError:
    pass

try:
    from PIL import Image, ImageOps
except ImportError:
    print("Hiányzik a Pillow. Telepítsd: pkg install python-pillow")
    sys.exit(1)

ALAP_SZELESSEG = 1080
ALAP_MINOSEG = 80


def kerdez_szam(szoveg, alap):
    v = input(szoveg).strip()
    if not v:
        return alap
    try:
        return int(v)
    except ValueError:
        print(f"Nem szám, marad az alapérték: {alap}")
        return alap


def konvertal_kepet():
    print("=== Kep -> HTML (base64) konverter ===")

    if len(sys.argv) > 1:
        fajl_utvonal = sys.argv[1]
    else:
        fajl_utvonal = input("Kep elérési útja: ")

    fajl_utvonal = os.path.expanduser(fajl_utvonal.strip().strip("'\""))

    if not os.path.isfile(fajl_utvonal):
        print(f"Nem található a fájl: {fajl_utvonal}")
        return

    szelesseg = kerdez_szam(
        f"Szélesség pixelben [Enter = {ALAP_SZELESSEG}, 0 = eredeti méret]: ",
        ALAP_SZELESSEG)
    minoseg = kerdez_szam(
        f"JPG minőség 1-100 [Enter = {ALAP_MINOSEG}]: ", ALAP_MINOSEG)
    minoseg = max(1, min(100, minoseg))

    try:
        kep = Image.open(fajl_utvonal)
        kep = ImageOps.exif_transpose(kep)  # telefonos fotók forgatása
        eredeti_meret = kep.size

        # Átlátszó (PNG) képnél fehér háttér, mert a JPG nem tud átlátszóságot
        if kep.mode in ("RGBA", "LA", "P"):
            kep = kep.convert("RGBA")
            hatter = Image.new("RGB", kep.size, (255, 255, 255))
            hatter.paste(kep, mask=kep.split()[-1])
            kep = hatter
        else:
            kep = kep.convert("RGB")

        # Csak kicsinyít, nagyítani nem fog
        if szelesseg > 0 and kep.width > szelesseg:
            magassag = round(kep.height * szelesseg / kep.width)
            kep = kep.resize((szelesseg, magassag), Image.LANCZOS)

        puffer = io.BytesIO()
        kep.save(puffer, format="JPEG", quality=minoseg, optimize=True)
        adat = puffer.getvalue()

        b64 = base64.b64encode(adat).decode("ascii")

        html_tartalom = (
            '<!DOCTYPE html>\n<html><head><meta charset="utf-8">'
            '<meta name="viewport" content="width=device-width, initial-scale=1">'
            '</head>\n'
            f'<body><img src="data:image/jpeg;base64,{b64}" '
            'style="max-width:100%;height:auto"></body></html>\n'
        )

        kimeneti_fajl = fajl_utvonal.rsplit('.', 1)[0] + "_beagyazott.html"

        with open(kimeneti_fajl, "w", encoding="utf-8") as html_file:
            html_file.write(html_tartalom)

        eredeti_kb = os.path.getsize(fajl_utvonal) // 1024
        uj_kb = len(adat) // 1024
        print(f"\nEredeti: {eredeti_meret[0]}x{eredeti_meret[1]} px, {eredeti_kb} KB")
        print(f"Új:      {kep.width}x{kep.height} px, {uj_kb} KB (JPG)")
        print(f"\nSiker! A HTML fájl elkészült ide:\n{kimeneti_fajl}")

    except Exception as e:
        print(f"Hiba történt a feldolgozás során: {e}")


if __name__ == "__main__":
    konvertal_kepet()
