rm d3host.txt ;
rm blacklist.txt ;
rm adservers.txt ;
rm nlist.txt ;
rm inthost.txt
clear ;

echo ' \n--------------------\nDownloading resource\n--------------------\n' && sleep 1 ;

wget https://raw.githubusercontent.com/d3ward/toolz/master/src/d3host.txt ;

wget https://raw.githubusercontent.com/anudeepND/blacklist/master/adservers.txt ;

wget bymisi.github.io/1TermuxFiles/inthost.txt

sleep 2 ;

cat d3host.txt adservers.txt > 2host.txt

echo ' \n--------------------\nChech for duplicates\n--------------------\n' && sleep 1 ;


sort -nr 2host.txt | uniq -cd

echo ' \n--------------------\nDuplicates found\n--------------------\n' && sleep 1 ;


sleep 1 ;
echo ' \n--------------------\nRemoving duplicates\n--------------------\n' && sleep 1 ;

## or use: sort 2host.txt | uniq -u > host.txt##

awk '!x[$0]++' 2host.txt > $1host.txt

sleep 2 ;

cat host.txt && echo ' \n--------------------\nStart Editing\n--------------------\n' && sleep 2 ;

echo ' \n--------------------\nCreating blacklist.txt\n--------------------\n' && sleep 2 ;

sed '/Ads/,$!d' host.txt >nlist.txt ;

cat nlist.txt;
echo "-------------------------";
echo "1. Deleted everything before (Ads)"
echo "-------------------------";
sleep 1;
## sed -i 's/0.0.0.0 //g' nlist.txt ;

cat nlist.txt;

echo "-------------------------";
echo "2. cleared start of all lines (0.0.0.0.)";
echo "-------------------------";
sleep 1;
sed -i '/#/d' nlist.txt ;

cat nlist.txt;

echo "-------------------------";
echo "3. deleted all lines containing #";
echo "-------------------------";
sleep 1;

sed -i '/^[[:space:]]*$/d' nlist.txt ;

cat nlist.txt ;

echo "-------------------------";
echo "4. removed empty lines"
echo "-------------------------";
sleep 1;

cat inthost.txt nlist.txt > blacklist.txt

echo "--------------------" && awk 'END {print NR}' blacklist.txt && echo "sites from d3ward&misi-c\n--------------------" ;

echo "# original files are d3ward.txt and adservers.txt and inthost.txt" ;

echo "# edited & exported to blacklist.txt" ;
sleep 1;

echo "cat blacklist.txt > ~/../../../../etc/hosts"> adblacklist.sh

echo "-------------------------";
echo "run adblacklist.sh with su"
echo "-------------------------";
sleep 1;


echo -n "# 丂匚尺丨卩ㄒ";sleep 1 && echo " 匚尺乇卂ㄒ乇ᗪ" ;
echo -n " 🇧‌"; sleep 1 && echo -n "🇾‌"; sleep 1 && echo "🇲‌🇮‌🇸‌🇮"; sleep 1;
