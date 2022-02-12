rm pkglist.sh ;
clear ;
echo ' \n-----------------------\nGetting installed List \n-----------------------\n' && sleep 2 ;

pkg list-installed >pkglist.sh ;

cat pkglist.sh && sleep 3 ;

echo ' \n---------------------\nExtracting pkg names\n---------------------\n' && sleep 2 ;

gawk -i inplace 'NR>1' pkglist.sh ;

sed -i 's/\/.*//' pkglist.sh ;

cat pkglist.sh && sleep 3 ;

echo ' \n-------------------------\nCreating autoinstall file\n-------------------------\n' && sleep 2 ;


sed -i 's/^/;apt satisfy -y /' pkglist.sh ;

gawk -i inplace '{print NR, $0}' pkglist.sh ;

sed -i 's/^/# ✓ /' pkglist.sh ;

sed -i 's/apt sat/\n&/g' pkglist.sh ;

echo "# --------------------------">>pkglist.sh ;

echo "# autoinstall: sh pkglist.sh">>pkglist.sh ;

echo "# --------------------------">>pkglist.sh ;

echo "# 丂匚尺丨 卩ㄒ 匚尺乇卂ㄒ乇ᗪ 🇧‌🇾‌🇲‌🇮‌🇸‌🇮">>pkglist.sh ;

sed -i '1s;^;apt update\n;' pkglist.sh ;

cat pkglist.sh
