#!/usr/bin/env python3
"""Vízjel videóra Termuxban - számozott listából választással.

Előfeltétel:  pkg install python ffmpeg ttf-dejavu
(A felugró szövegablakhoz kell még: pkg install termux-api + Termux:API app,
 de enélkül is működik, akkor a terminálban kérdezi a szöveget.)

Használat:
  python vizjel.py            -> az aktuális mappa videóit listázza
  python vizjel.py /mappa/út  -> megadott mappa videóit listázza
"""
import json, os, subprocess, sys, tempfile

FONT = "/data/data/com.termux/files/usr/share/fonts/TTF/DejaVuSans.ttf"
ALAP_SZOVEG = "[by Misi] Sindelfingen, Germany"
KIMENET_ALMAPPA = "vizjeles"
KITERJESZTES = (".mp4", ".mov", ".mkv", ".3gp", ".webm", ".avi")


def szoveg_bekeres():
    """Felugró ablak; ha nem megy, terminálos kérdés."""
    try:
        r = subprocess.run(
            ["termux-dialog", "text", "-t", "Vízjel szövege", "-i", ALAP_SZOVEG],
            capture_output=True, text=True, timeout=120)
        adat = json.loads(r.stdout)
        if adat.get("code") != -1:
            return None  # megszakította
        return adat.get("text", "").strip() or None
    except (FileNotFoundError, json.JSONDecodeError, subprocess.TimeoutExpired):
        s = input(f"Vízjel szövege [{ALAP_SZOVEG}]: ").strip()
        return s or ALAP_SZOVEG


def valasztas_ertelmez(bevitel, db):
    """'1,3,5' / '2-4' / 'x' -> indexek listája (0-tól)."""
    bevitel = bevitel.strip().lower()
    if bevitel in ("x", "mind", "all"):
        return list(range(db))
    kijeloltek = []
    for resz in bevitel.replace(" ", "").split(","):
        if not resz:
            continue
        if "-" in resz:
            a, b = resz.split("-", 1)
            if not (a.isdigit() and b.isdigit()):
                raise ValueError(resz)
            kijeloltek.extend(range(int(a), int(b) + 1))
        elif resz.isdigit():
            kijeloltek.append(int(resz))
        else:
            raise ValueError(resz)
    for n in kijeloltek:
        if not 1 <= n <= db:
            raise ValueError(str(n))
    return [n - 1 for n in kijeloltek]


def vizjelez(be, ki, szoveg):
    """Szöveg fájlon át megy az ffmpegnek, így nincs escape-elés."""
    with tempfile.NamedTemporaryFile("w", suffix=".txt", delete=False,
                                     encoding="utf-8") as f:
        f.write(szoveg)
        szovegfajl = f.name
    szuro = (
        f"drawtext=textfile={szovegfajl}:expansion=none:"
        f"fontfile={FONT}:fontsize=h/25:fontcolor=white:"
        "box=1:boxcolor=black@0.5:boxborderw=8:x=20:y=h-th-20"
    )
    try:
        subprocess.run(
            ["ffmpeg", "-y", "-loglevel", "error", "-stats", "-i", be,
             "-vf", szuro, "-preset", "veryfast", "-c:a", "copy", ki],
            check=True)
    finally:
        os.remove(szovegfajl)


def main():
    mappa = os.path.abspath(sys.argv[1]) if len(sys.argv) > 1 else os.getcwd()
    if not os.path.isdir(mappa):
        return print("Nincs ilyen mappa:", mappa)

    print("\n=== Videó vízjelező ===\n")
    videok = sorted(f for f in os.listdir(mappa)
                    if f.lower().endswith(KITERJESZTES)
                    and os.path.isfile(os.path.join(mappa, f)))
    if not videok:
        return print(f"Nincs videó ebben a mappában ({mappa}).")

    print(f"Videók a mappában ({mappa}):")
    for i, v in enumerate(videok, 1):
        print(f"  {i}. {v}")

    print("\nMit vízjelezzek? Szám (pl. 2), több (1,3,5), tartomány (2-4),")
    kijeloltek = None
    while kijeloltek is None:
        bevitel = input("X = mind, Q = kilépés: ").strip()
        if bevitel.lower() in ("q", ""):
            return print("Kilépés.")
        try:
            kijeloltek = valasztas_ertelmez(bevitel, len(videok))
        except ValueError as e:
            print(f"Érvénytelen: {e}. Próbáld újra.")

    szoveg = szoveg_bekeres()
    if not szoveg:
        return print("Megszakítva.")

    kimappa = os.path.join(mappa, KIMENET_ALMAPPA)
    os.makedirs(kimappa, exist_ok=True)

    kesz = 0
    for sorszam, idx in enumerate(kijeloltek, 1):
        nev = videok[idx]
        be = os.path.join(mappa, nev)
        ki = os.path.join(kimappa, os.path.splitext(nev)[0] + ".mp4")
        print(f"\n[{sorszam}/{len(kijeloltek)}] {nev}")
        try:
            vizjelez(be, ki, szoveg)
            kesz += 1
        except subprocess.CalledProcessError:
            print("  Hiba ennél a videónál, kihagyva.")

    subprocess.run(["termux-media-scan", kimappa], capture_output=True)
    print(f"\nKész: {kesz}/{len(kijeloltek)} videó.")
    print("Mappa:", kimappa)


if __name__ == "__main__":
    main()
