rm d3host.txt ;
rm blacklist.txt ;
clear ;

echo ' \n--------------------\nDownloading resource\n--------------------\n' && sleep 1 ;

wget https://raw.githubusercontent.com/d3ward/toolz/master/src/d3host.txt ;

sleep 2 ;

cat d3host.txt && echo ' \n--------------------\nStart Editing\n--------------------\n' && sleep 2 ;

echo ' \n--------------------\nCreating blacklist.txt\n--------------------\n' && sleep 2 ;

sed '/Ads/,$!d' d3host.txt >blacklist.txt ;

cat blacklist.txt;
echo "-------------------------";
echo "1. Deleted everything before (Ads)"
echo "-------------------------";
sleep 1;
sed -i 's/0.0.0.0 //g' blacklist.txt ;

cat blacklist.txt;

echo "-------------------------";
echo "2. cleared start of all lines (0.0.0.0.)";
echo "-------------------------";
sleep 1;
sed -i '/#/d' blacklist.txt ;

cat blacklist.txt;

echo "-------------------------";
echo "3. deleted all lines containing #";
echo "-------------------------";
sleep 1;
sed -i '/^[[:space:]]*$/d' blacklist.txt ;

cat blacklist.txt ;

echo "-------------------------";
echo "4. removed empty lines"
echo "-------------------------";
sleep 1;
echo "--------------------" && awk 'END {print NR}' blacklist.txt && echo "sites from d3ward\n--------------------" ;

echo "# original file is d3ward.txt" ;

echo "# edited & exported to blacklist.txt" ;
sleep 1;
echo -n "# 丂匚尺丨卩ㄒ";sleep 1 && echo " 匚尺乇卂ㄒ乇ᗪ" ;
echo -n " 🇧‌"; sleep 1 && echo -n "🇾‌"; sleep 1 && echo "🇲‌🇮‌🇸‌🇮"; sleep 1;
