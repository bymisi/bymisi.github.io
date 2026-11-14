#!/usr/bin/env python3
"""Vízjel videóra Termuxban - felugró ablakokkal.

Előfeltétel:
  pkg install python ffmpeg termux-api ttf-dejavu
  termux-setup-storage
  + Termux:API app (F-Droid)
"""
import json, os, subprocess, sys, tempfile, time

FONT = "/data/data/com.termux/files/usr/share/fonts/TTF/DejaVuSans.ttf"
ALAP_SZOVEG = "[by Misi] Sindelfingen, Germany"
ALAP_MAPPA = "/sdcard/DCIM/Camera"
KIMENET = "/sdcard/Movies/Vizjeles"
KITERJESZTES = (".mp4", ".mov", ".mkv", ".3gp", ".webm")


def dialog(tipus, *args):
    """termux-dialog hívása, visszaadja a JSON-t."""
    r = subprocess.run(["termux-dialog", tipus, *args],
                       capture_output=True, text=True)
    try:
        return json.loads(r.stdout)
    except json.JSONDecodeError:
        return {"code": -2}


def uzenet(szoveg):
    print(szoveg)
    subprocess.run(["termux-toast", szoveg], capture_output=True)


def vizjelez(be, ki, szoveg):
    """Egy videó vízjelezése. A szöveg fájlon át megy, nincs escape."""
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
            ["ffmpeg", "-y", "-loglevel", "error", "-i", be,
             "-vf", szuro, "-preset", "veryfast", "-c:a", "copy", ki],
            check=True)
    finally:
        os.remove(szovegfajl)


def main():
    # 1. Mit dolgozzunk fel?
    v = dialog("radio", "-t", "Mit szeretnél vízjelezni?",
               "-v", "Egy videó kiválasztása,Egy mappa összes videója")
    if v.get("code") != -1:
        return print("Megszakítva.")
    egy_video = v.get("index") == 0

    feladatok = []  # (bemenet, kimenet)
    os.makedirs(KIMENET, exist_ok=True)

    if egy_video:
        # Androidos fájlválasztó
        ideiglenes = os.path.join(tempfile.gettempdir(), "valasztott_video")
        if os.path.exists(ideiglenes):
            os.remove(ideiglenes)
        subprocess.run(["termux-storage-get", ideiglenes])
        if not os.path.exists(ideiglenes) or os.path.getsize(ideiglenes) == 0:
            return print("Nem választottál videót.")
        nev = "vizjeles_" + time.strftime("%Y%m%d_%H%M%S") + ".mp4"
        feladatok.append((ideiglenes, os.path.join(KIMENET, nev)))
    else:
        m = dialog("text", "-t", "Mappa elérési útja", "-i", ALAP_MAPPA)
        if m.get("code") != -1:
            return print("Megszakítva.")
        mappa = m.get("text", "").strip() or ALAP_MAPPA
        if not os.path.isdir(mappa):
            return uzenet("Nincs ilyen mappa: " + mappa)
        for fajl in sorted(os.listdir(mappa)):
            if fajl.lower().endswith(KITERJESZTES):
                ki = os.path.join(KIMENET, fajl.rsplit(".", 1)[0] + ".mp4")
                if not os.path.exists(ki):  # már kész, kihagyjuk
                    feladatok.append((os.path.join(mappa, fajl), ki))
        if not feladatok:
            return uzenet("Nincs feldolgozandó videó a mappában.")

    # 2. Vízjel szövege
    t = dialog("text", "-t", "Vízjel szövege (%d videó)" % len(feladatok),
               "-i", ALAP_SZOVEG)
    szoveg = t.get("text", "").strip()
    if t.get("code") != -1 or not szoveg:
        return print("Megszakítva.")

    # 3. Feldolgozás
    kesz = 0
    for i, (be, ki) in enumerate(feladatok, 1):
        print(f"[{i}/{len(feladatok)}] {os.path.basename(be)}")
        try:
            vizjelez(be, ki, szoveg)
            kesz += 1
        except subprocess.CalledProcessError:
            print("  Hiba ennél a videónál, kihagyva.")

    if egy_video and os.path.exists(feladatok[0][0]):
        os.remove(feladatok[0][0])

    # Hogy a galéria is lássa az új fájlokat
    subprocess.run(["termux-media-scan", KIMENET], capture_output=True)
    uzenet(f"Kész: {kesz}/{len(feladatok)} videó. Mappa: {KIMENET}")


if __name__ == "__main__":
    main()
