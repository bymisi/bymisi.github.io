rm pkglist.sh ;
clear ;

pkg list-installed >pkglist.sh ;

cat pkglist.sh ;

echo ' \n-----------------------\n List installed Packages  \n-----------------------\n' ;
sleep 3;

echo "--------------------";
echo " 1. Remove first line from file (Listing ...)";
echo "--------------------";
sleep 2;
gawk -i inplace 'NR>1' pkglist.sh ;
cat pkglist.sh;

echo "--------------------";
echo " 2. Remove content from all lines after /  " ;
echo "--------------------";
sleep 2;
sed -i 's/\/.*//' pkglist.sh ;
cat pkglist.sh ;
echo ' \n---------------------\nExtracted pkg names\n---------------------\n' ;

sleep 3 ;
echo "--------------------";
echo "3. add to begining of all lines (;apt satisfy -y) "
echo "--------------------";
sleep 2;
sed -i 's/^/;apt satisfy -y /' pkglist.sh ;
cat pkglist.sh;

echo "--------------------";
echo "4. add numbering to all lines front "
echo "--------------------";
sleep 2;
gawk -i inplace '{print NR, $0}' pkglist.sh ;
cat pkglist.sh ;

echo "--------------------";
echo "5. add content to begining of all lines (# ✓ )";
echo "--------------------";
sleep 2;
sed -i 's/^/# ✓ /' pkglist.sh ;
cat pkglist.sh;

echo "--------------------";
echo "6. add split to all lines before match (apt sat) "
echo "--------------------";
sleep 2;
sed -i 's/apt sat/\n&/g' pkglist.sh ;
cat pkglist.sh;

echo "--------------------";
echo "7. add 1.st line to file to exclude error (apt update) "
echo "--------------------";
sleep 2;
sed -i '1s;^;apt update\n;' pkglist.sh ;

echo "# --------------------------">>pkglist.sh ;
echo "# autoinstall: sh pkglist.sh">>pkglist.sh ;
echo "# --------------------------">>pkglist.sh ;

cat pkglist.sh

echo ' \n-------------------------\nCreated autoinstall file\n-------------------------\n' ;
sleep 1;
echo -n "# 丂匚尺丨卩ㄒ";sleep 1 && echo " 匚尺乇卂ㄒ乇ᗪ" ;
echo -n " 🇧‌"; sleep 1 && echo -n "🇾‌"; sleep 1 && echo " 🇲‌🇮‌🇸‌🇮"; sleep 1;
