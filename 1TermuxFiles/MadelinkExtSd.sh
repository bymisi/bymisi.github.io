RD="../../../../.."
WD="data/data/com.termux/files/home"
TD="storage/termuxsd"
TX="FileOnExtSdCard.txt"
ls ~/$RD/storage > storage.txt;
sed -i -n '/-/p' storage.txt;
sed -i '/^[[:space:]]*$/d' storage.txt
file="storage.txt"
xsd=$(cat "$file")
echo "----------"
MicroSD="storage.txt"
if [ -s "$MicroSD" ]; then
   echo "Detected MicroSd:  $xsd"
   sleep 1;
else
   echo "MicroSd Card Not Detected"
   sleep 4 && exit
fi

DIR=~/$RD/storage/$xsd/termuxsd
if [ -d "$DIR" ]; then
   echo "termuxsd exists."
else
   echo "Creating Dir"
   mkdir ~/$RD/storage/$xsd/termuxsd
fi

FLINK=~/$RD/$WD/$TD
if [ -h "$FLINK" ]; then
   echo "Folder LINK exists."
else
   echo "Creating Folder Link"
  ln -s ~/$RD/storage/$xsd/termuxsd ~/$RD/$WD/$TD

fi


DLINK=~/$RD/$WD/$xsd
if [ -h "$DLINK" ]; then
   echo "DISK LINK exists."
else
   echo "Creating DISK Link"

  ln -s ~/$RD/storage/$xsd ~/$RD/$WD/$xsd


fi


FILE=~/$TD/$TX
if [ -f "$FILE" ]; then
   echo "1.st FILE exists."
else
   echo "Creating 1.st File"
   echo "1.st" > ~/$TD/$TX
fi
rm storage.txt ;
cd ~/$TD && ls 

echo "๐ชโ๐ฝโ๐นโ๐ชโ๐ทโ๐ณโ๐ฆโ๐ฑโ ๐ธโ๐ฉโ"
ls -a ~/$xsd
echo "๐ฑโ๐ฎโ๐ณโ๐ฐโ๐ชโ๐ฉโ ๐นโ๐ดโ ๐นโ๐ชโ๐ทโ๐ฒโ๐บโ๐ฝโ"
echo "For External Sd  :"
echo "type in : "
echo "cd ~/$xsd"
