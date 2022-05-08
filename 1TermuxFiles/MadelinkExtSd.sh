ls ~/../../../../../storage > storage.txt;
##sed -n '/-/p' storage.txt > sd.txt;
sed -i -n '/-/p' storage.txt;
file="storage.txt"
xsd=$(cat "$file")

DIR=~/../../../../../storage/$xsd/termuxsd
if [ -d "$DIR" ]; then
    echo "termuxsd exists."
else
    echo "Creating Dir"
    mkdir ~/../../../../../storage/$xsd/termuxsd
fi

LINK=~/../../../../../data/data/com.termux/files/home/storage/termuxsd
if [ -h "$LINK" ]; then
    echo "LINK exists."
else
    echo "Creating Link"
    ln -s ~/../../../../../storage/$xsd/termuxsd ~/../../../../../data/data/com.termux/files/home/storage/termuxsd
fi
FILE=~/storage/termuxsd/FileOnExtSdCard.txt
if [ -f "$FILE" ]; then
    echo "1.st FILE exists."
else
    echo "Creating 1.st File"
    echo "FileOnExtSdCard" > ~/storage/termuxsd/FileOnExtSdCard.txt
fi
rm storage.txt ;
cd ~/storage/termuxsd && ls 

echo "🇪‌🇽‌🇹‌🇪‌🇷‌🇳‌🇦‌🇱‌ 🇸‌🇩‌ 🇼‌🇦‌🇸‌ 🇱‌🇮‌🇳‌🇰‌🇪‌🇩‌ 🇹‌🇴‌ 🇹‌🇪‌🇷‌🇲‌🇺‌🇽‌"
echo "For External Sd Folder :"
echo "type in : "
echo "cd ~/storage/termuxsd"
