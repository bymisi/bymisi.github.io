STORAGE=~/storage
if [ -d "$STORAGE" ]; then
    echo "$STORAGE exists."
else
    echo "$STORAGE does not exist."
termux-setup-storage&&sleep 10 ;
fi
checkSd(){
cd ~/../../../../../storage && ls -h
echo "imput your sdcard name (ex:9C65-87BD)" ;
echo "(it is listed before)"
read xsd

SD=~/../../../../../storage/${xsd}

if [ -d "$SD" ]; then
    echo "$SD 🇷‌🇪‌🇨‌🇴‌🇬‌🇳‌🇮‌🇿‌🇪‌🇩‌."
else
    echo "🇼‌🇷‌🇴‌🇳‌🇬‌ SD NAME ! CHECK AGAIN"
    checkSd
fi
}
cd ~/../../../../../storage && ls -h
echo "imput your sdcard name (ex:9C65-87BD)" ;
echo "(it is listed before)"
read xsd

SD=~/../../../../../storage/${xsd}

if [ -d "$SD" ]; then
    echo "$SD 🇷‌🇪‌🇨‌🇴‌🇬‌🇳‌🇮‌🇿‌🇪‌🇩‌."
else
    echo "🇼‌🇷‌🇴‌🇳‌🇬‌ SD NAME ! CHECK AGAIN"
    checkSd
fi

DIR=~/../../../../../storage/${xsd}/termuxsd

if [ -d "$DIR" ]; then
    echo "$DIR exists."
else
    echo "$DIR does not exist."
    mkdir ~/../../../../../storage/${xsd}/termuxsd
fi

LINK=~/../../../../../data/data/com.termux/files/home/storage/termuxsd
if [ -h "$LINK" ]; then
    echo "$LINK exists."
else
    echo "$LINK does not exist."
    ln -s ~/../../../../../storage/${xsd}/termuxsd ~/../../../../../data/data/com.termux/files/home/storage/termuxsd

fi

FILE=~/storage/termuxsd/FileOnExtSdCard.txt
if [ -f "$FILE" ]; then
    echo "$FILE exists."
else
    echo "$FILE does not exist."
    echo "FileOnExtSdCard" > ~/storage/termuxsd/FileOnExtSdCard.txt
fi

cd ~/storage/termuxsd && ls 

echo "🇪‌🇽‌🇹‌🇪‌🇷‌🇳‌🇦‌🇱‌ 🇸‌🇩‌ 🇼‌🇦‌🇸‌ 🇱‌🇮‌🇳‌🇰‌🇪‌🇩‌ 🇹‌🇴‌ 🇹‌🇪‌🇷‌🇲‌🇺‌🇽‌"
echo "go to ~/storage/termuxsd"
