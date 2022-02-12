rm d3host.txt ;
rm blacklist.txt ;
clear ;

echo ' \n--------------------\nDownloading resource\n--------------------\n' && sleep 1 ;

wget https://raw.githubusercontent.com/d3ward/toolz/master/src/d3host.txt ;

sleep 2 ;

cat d3host.txt && echo ' \n--------------------\nStart Editing\n--------------------\n' && sleep 2 ;

echo ' \n--------------------\nCreating blacklist.txt\n--------------------\n' && sleep 2 ;

sed '/Ads/,$!d' d3host.txt >blacklist.txt ;

sed -i 's/0.0.0.0 //g' blacklist.txt ;

sed -i '/#/d' blacklist.txt ;

sed -i '/^[[:space:]]*$/d' blacklist.txt ;

cat blacklist.txt ;

echo "--------------------" && awk 'END {print NR}' blacklist.txt && echo "sites from d3ward\n--------------------" ;

echo "# original file is d3ward.txt" ;

echo "# edited & exported to blacklist.txt" ;

echo "# 丂匚尺丨 卩ㄒ 匚尺乇卂ㄒ乇ᗪ 🇧‌🇾‌🇲‌🇮‌🇸‌🇮" ;
