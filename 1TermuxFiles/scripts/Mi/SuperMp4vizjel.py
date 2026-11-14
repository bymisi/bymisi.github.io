#!/usr/bin/env python3
"""Vízjel videóra Termuxban - számozott listából választással.

Előfeltétel:  pkg install python ffmpeg ttf-dejavu
(A felugró szövegablakhoz kell még: pkg install termux-api + Termux:API app,
 de enélkül is működik, akkor a terminálban kérdezi a szöveget.)

Használat:
  python vizjel.py            -> az aktuális mappa videóit listázza
  python vizjel.py /mappa/út  -> megadott mappa videóit listázza
"""
import json, os, shutil, subprocess, sys, tempfile, time

FONT = "/data/data/com.termux/files/usr/share/fonts/TTF/DejaVuSans.ttf"
ALAP_SZOVEG = "[by Misi] Sindelfingen, Germany"
UTOTAG = "_watermark"
FELUGRO_ABLAK = False  # True: termux-dialog felugró ablak (Termux:API kell)
KITERJESZTES = (".mp4", ".mov", ".mkv", ".3gp", ".webm", ".avi")


def szoveg_bekeres():
    """Vízjel szövegének bekérése a terminálban (az alapszöveg szerkeszthető)."""
    if FELUGRO_ABLAK:
        try:
            r = subprocess.run(
                ["termux-dialog", "text", "-t", "Vízjel szövege",
                 "-i", ALAP_SZOVEG],
                capture_output=True, text=True, timeout=30)
            adat = json.loads(r.stdout)
            if adat.get("code") != -1:
                return None  # megszakította
            return adat.get("text", "").strip() or None
        except Exception:
            print("(A felugró ablak nem jelent meg, kérdezem itt.)")

    print("\nVízjel szövege (az alap szöveget átírhatod, Enter = rendben):")
    try:
        import readline
        readline.set_startup_hook(lambda: readline.insert_text(ALAP_SZOVEG))
        elozetes = ""
    except ImportError:
        readline = None
        elozetes = f" [{ALAP_SZOVEG}]"
    try:
        szoveg = input("> " + elozetes).strip()
    except EOFError:
        return None
    finally:
        if readline:
            readline.set_startup_hook()
    if not szoveg and readline is None:
        szoveg = ALAP_SZOVEG
    return szoveg or None


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


def idotartam(fajl):
    """Videó hossza másodpercben (ffprobe), vagy None."""
    try:
        r = subprocess.run(
            ["ffprobe", "-v", "error", "-show_entries", "format=duration",
             "-of", "default=nw=1:nk=1", fajl],
            capture_output=True, text=True)
        return float(r.stdout.strip())
    except (ValueError, FileNotFoundError):
        return None


def videomeret(fajl):
    """(szélesség, magasság) úgy, ahogy a kép megjelenik (forgatással)."""
    try:
        r = subprocess.run(
            ["ffprobe", "-v", "error", "-select_streams", "v:0",
             "-show_entries",
             "stream=width,height:stream_tags=rotate:stream_side_data=rotation",
             "-of", "json", fajl],
            capture_output=True, text=True)
        adat = json.loads(r.stdout)["streams"][0]
        w, h = int(adat["width"]), int(adat["height"])
        forgatas = 0
        for sd in adat.get("side_data_list", []):
            if "rotation" in sd:
                forgatas = int(float(sd["rotation"]))
        if not forgatas:
            forgatas = int(adat.get("tags", {}).get("rotate", 0))
        if abs(forgatas) % 180 == 90:
            w, h = h, w
        return w, h
    except Exception:
        return None


def elrendezes(szoveg, w, h):
    """Betűméret úgy, hogy a szöveg biztosan kiférjen; ha kell, két sorba törik."""
    def merete(sorok):
        leghosszabb = max(len(x) for x in sorok)
        return min(h / 25, (w * 0.9) / (0.62 * leghosszabb))

    sorok = szoveg.split("\n")
    meret = merete(sorok)
    szokozok = [i for i, c in enumerate(szoveg) if c == " "]
    if meret < h / 40 and len(sorok) == 1 and szokozok:
        i = min(szokozok, key=lambda k: abs(k - len(szoveg) // 2))
        sorok = [szoveg[:i].strip(), szoveg[i + 1:].strip()]
        meret = merete(sorok)
    return "\n".join(sorok), max(int(meret), 10)


def ido(mp):
    mp = int(max(mp, 0))
    return f"{mp // 3600}:{mp % 3600 // 60:02d}:{mp % 60:02d}"


def vizjelez(be, ki, szoveg, elotag=""):
    """Szöveg fájlon át megy az ffmpegnek, haladásjelzéssel."""
    hossz = idotartam(be)
    meretek = videomeret(be)
    if meretek:
        w, h = meretek
        szoveg, fs = elrendezes(szoveg, w, h)
        keret = max(4, fs // 5)
        pozicio = f"x={int(w * 0.04)}:y=h-th-{int(h * 0.02)}"
        meret_resz = f"fontsize={fs}:boxborderw={keret}"
    else:  # ha az ffprobe nem ad méretet, régi módszer
        pozicio = "x=20:y=h-th-20"
        meret_resz = "fontsize=h/30:boxborderw=8"
    with tempfile.NamedTemporaryFile("w", suffix=".txt", delete=False,
                                     encoding="utf-8") as f:
        f.write(szoveg)
        szovegfajl = f.name
    szuro = (
        f"drawtext=textfile={szovegfajl}:expansion=none:"
        f"fontfile={FONT}:{meret_resz}:fontcolor=white:"
        f"box=1:boxcolor=black@0.5:line_spacing=4:{pozicio}"
    )
    parancs = ["ffmpeg", "-y", "-loglevel", "error", "-nostats",
               "-progress", "pipe:1", "-i", be,
               "-vf", szuro, "-preset", "veryfast", "-c:a", "copy", ki]
    kezdet = time.time()
    hibak = []
    utolso = ""
    try:
        p = subprocess.Popen(parancs, stdout=subprocess.PIPE,
                             stderr=subprocess.STDOUT, text=True)
        for sor in p.stdout:
            sor = sor.strip()
            if sor.startswith("out_time_us=") or sor.startswith("out_time_ms="):
                try:
                    kesz_mp = int(sor.split("=", 1)[1]) / 1_000_000
                except ValueError:
                    continue
                eltelt = time.time() - kezdet
                if hossz:
                    szazalek = max(0.0, min(kesz_mp / hossz, 1.0))
                    tele = int(szazalek * 15)
                    sav = "#" * tele + "-" * (15 - tele)
                    hatra = (eltelt / szazalek * (1 - szazalek)
                             if szazalek > 0.01 else None)
                    szoveg_sor = (f"{elotag}[{sav}] {szazalek * 100:3.0f}% "
                                  f"~{ido(hatra) if hatra is not None else '?'}")
                else:
                    szoveg_sor = f"{elotag}{ido(kesz_mp)} kész"
                oszlop = shutil.get_terminal_size((50, 20)).columns
                szoveg_sor = szoveg_sor[:oszlop - 1]
                if szoveg_sor != utolso:
                    sys.stdout.write("\r" + szoveg_sor + "\033[K")
                    sys.stdout.flush()
                    utolso = szoveg_sor
            elif "=" not in sor and sor:
                hibak.append(sor)  # valódi hibaüzenet
        p.wait()
        sys.stdout.write("\n")
        if p.returncode != 0:
            for h in hibak[-5:]:
                print("  ", h)
            raise subprocess.CalledProcessError(p.returncode, parancs)
    finally:
        os.remove(szovegfajl)


def main():
    mappa = os.path.abspath(sys.argv[1]) if len(sys.argv) > 1 else os.getcwd()
    if not os.path.isdir(mappa):
        return print("Nincs ilyen mappa:", mappa)

    print("\n=== Videó vízjelező ===\n")
    videok = sorted(f for f in os.listdir(mappa)
                    if f.lower().endswith(KITERJESZTES)
                    and not os.path.splitext(f)[0].endswith(UTOTAG)
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

    kesz = 0
    for sorszam, idx in enumerate(kijeloltek, 1):
        nev = videok[idx]
        be = os.path.join(mappa, nev)
        ki = os.path.join(mappa, os.path.splitext(nev)[0] + UTOTAG + ".mp4")
        print(f"\n{nev}")
        try:
            vizjelez(be, ki, szoveg, f"[{sorszam}/{len(kijeloltek)}] ")
            kesz += 1
        except subprocess.CalledProcessError:
            print("  Hiba ennél a videónál, kihagyva.")

    try:
        subprocess.run(["termux-media-scan", mappa], capture_output=True,
                       timeout=10)
    except Exception:
        pass
    print(f"\nKész: {kesz}/{len(kijeloltek)} videó.")
    print("Az új fájlok a videók mellett vannak (név + _watermark).")


if __name__ == "__main__":
    main()
