
## ANSI colors (FG & BG)
RED="$(printf '\033[31m')"  GREEN="$(printf '\033[32m')"  ORANGE="$(printf '\033[33m')"  BLUE="$(printf '\033[34m')"
MAGENTA="$(printf '\033[35m')"  CYAN="$(printf '\033[36m')"  WHITE="$(printf '\033[37m')" BLACK="$(printf '\033[30m')"
REDBG="$(printf '\033[41m')"  GREENBG="$(printf '\033[42m')"  ORANGEBG="$(printf '\033[43m')"  BLUEBG="$(printf '\033[44m')"
MAGENTABG="$(printf '\033[45m')"  CYANBG="$(printf '\033[46m')"  WHITEBG="$(printf '\033[47m')" BLACKBG="$(printf '\033[40m')"
RESETBG="$(printf '\e[0m\n')"


## Script termination

## Reset terminal colors
reset_color() {
	tput sgr0   # reset attributes
	tput op     # reset color
    return
}

## Banner
banner() {
	cat <<- EOF
		${MAGENTA}╔╗╔═╗╔═══╗╔╗──╔╗────╔═══╗╔═══╗╔═╗─╔╗
		${MAGENTA}║║║╔╝║╔══╝║╚╗╔╝║────║╔═╗║║╔══╝║║╚╗║║
		${MAGENTA}║╚╝╝─║╚══╗╚╗╚╝╔╝────║║─╚╝║╚══╗║╔╗╚╝║
		${MAGENTA}║╔╗║─║╔══╝─╚╗╔╝─────║║╔═╗║╔══╝║║╚╗║║
		${MAGENTA}║║║╚╗║╚══╗──║║──────║╚╩═║║╚══╗║║─║║║
		${MAGENTA}╚╝╚═╝╚═══╝──╚╝──────╚═══╝╚═══╝╚╝─╚═╝
		${ORANGE}                            Version 2.2
		${ORANGE}
	EOF
}

## Small Banner
banner_small() {
	cat <<- EOF
		${BLUE}     
 		${CYAN} █▀▄ ▀▄░▄▀░░░░ █▄░▄█ ▀ ▄▀▀ ▀
 		${CYAN} █▀█ ░░█░░░░░░ █░█░█ █ ░▀▄ █
 		${CYAN} ▀▀░ ░░▀░░░░░░ ▀░░░▀ ▀ ▀▀░ ▀
		${BLUE}     
		${ORANGE}                     misi@email.com
		${BLUE}     
	EOF
}
##aptupt
aptupt(){
echo "🇨‌🇷‌🇪‌🇦‌🇹‌🇮‌🇳‌🇬‌  🇫‌🇮‌🇱‌🇪‌🇸‌"
echo ${BLUE} " installing openssl-tool & openssh "
apt update && apt satisfy openssl-tool -y && apt satisfy openssh -y 
}
##CACRT
cacrt(){
echo "basicConstraints=CA:true" > android_options.txt ;

##echo "rm *.crt && rm *.pfx && rm *.pem && rm *.key && rm *.p12 && rm *.pub && rm clean.sh && ls" > clean.sh;

echo ${GREEN} " Creating temp key file ";

echo ${BLUE} && openssl genrsa -out priv_and_pub.key 2048 ;

echo ${GREEN} " Creating CA.pem  ";

openssl req -new -key priv_and_pub.key -out CA.pem ;

echo ${BLUE} " Creating CA.crt ";

openssl x509 -req -days 9650 -in CA.pem -signkey priv_and_pub.key -extfile ./android_options.txt -out CA.crt ;
}

##PDF
pdf_inst(){
echo ${BLUE} " Creating CA.pfx For 🆂🅸🅶🅽🅸🅽🅶   🅿🅳🅵 Files with OfficeSuite"
openssl pkcs12 -export -out CA_forSigningPdf.pfx -inkey priv_and_pub.key -in CA.crt -name "bymisi" ;
}

##Sniffer
sniffer_inst(){
echo ${GREEN} " Creating keyStore.p12 for 🄿🄰🄲🄺🄴🅃 🄲🄰🄿🅃🅄🅁🄴"
openssl pkcs12 -export -out Packet_Capture.p12 -inkey priv_and_pub.key -in CA.crt -name "bymisi" ;
}

##Der
der_inst(){
echo ${GREEN} " Creating CA.der.crt ⓝⓞ   ⓦⓐⓡⓝⓘⓝⓖ Certificate "
openssl x509 -inform PEM -outform DER -in CA.crt -out CA_noWarning.der 
}

##Github
github_inst(){
echo ${YELLOW} " Creating private and public key pair for 𝔾𝕚𝕥ℍ𝕦𝕓 "
echo ${RED}"▄▀█ ▀█▀ ▀█▀ █▀▀ █▄░█ ▀█▀ █ █▀█ █▄░█ ░░█ ░░"
echo ${RED}"█▀█ ░█░ ░█░ ██▄ █░▀█ ░█░ █ █▄█ █░▀█ ░░▄ ░░";
sleep 2;
echo ${GREEN} " input your 🅴🅼🅰🅸🅻 for 🇬‌🇮‌🇹‌🇭‌🇺‌🇧‌ key file ! " 
read x
ssh-keygen -t rsa -b 4096 -C ${x} -f GitHub.key ;
}
##Done
doned(){
echo ${GREEN} " Cleaning temp files "
rm android_options.txt ; rm priv_and_pub.key; 
echo ${MAGENTA} " Scripts collected and edited by ⓂⓘⓈⓘ-ⓒ"
echo ${MAGENTA} " ---𝕯𝖔𝖓𝖊--- "
sleep 3; clear; banner;banner_small;
ls 
}

##Repeat
repeat(){
echo "🇼‌🇷‌🇴‌🇳‌🇬‌ 🇰‌🇪‌🇾‌  Try 1 ..... 5 !!!"
sleep 3; clear; banner;sleep 2; banner_small;
echo "🄶🄸🅃🄷🅄🄱  🄻🄾🄶🄸🄽 --1"
echo "🅿🅳🅵 🆂🅸🅶🅽🅸🅽🅶 &ᎷϴᎡᎬ--2"
echo "🄿🄰🄲🄺🄴🅃 🄲🄰🄿🅃🅄🅁🄴 --3"
echo "🅽🅾 🆆🅰🆁🅽🅸🅽🅶 ᗞᗴᖇ 🅲🅴🆁🆃. --4"
echo "🅝🅞🅝🅔 --5"

read -p "Chose Key to Generate: " usk
 
if [ $usk -eq 1 ]
then
    aptupt && github_inst && doned
 
elif [ $usk -eq 2 ]
then
    aptupt && cacrt && pdf_inst && doned
 
elif [ $usk -eq 3 ]
then
    aptupt && cacrt && sniffer_inst && doned

elif [ $usk -eq 4 ]
then
    aptupt && cacrt && der_inst && doned

elif [ $usk -eq 5 ]
then
    echo "none" && doned
else
    repeat
fi


}

sleep 1; clear; banner;sleep 2; banner_small;
echo "🄶🄸🅃🄷🅄🄱  🄻🄾🄶🄸🄽 --1"
echo "🅿🅳🅵 🆂🅸🅶🅽🅸🅽🅶 &ᎷϴᎡᎬ--2"
echo "🄿🄰🄲🄺🄴🅃 🄲🄰🄿🅃🅄🅁🄴 --3"
echo "🅽🅾 🆆🅰🆁🅽🅸🅽🅶 ᗞᗴᖇ 🅲🅴🆁🆃. --4"
echo "🅝🅞🅝🅔 --5"

read -p "Chose Key to Generate: " usk
 
if [ $usk -eq 1 ]
then
    aptupt && github_inst && doned
 
elif [ $usk -eq 2 ]
then
    aptupt && cacrt && pdf_inst && doned
 
elif [ $usk -eq 3 ]
then
    aptupt && cacrt && sniffer_inst && doned

elif [ $usk -eq 4 ]
then
    aptupt && cacrt && der_inst && doned

elif [ $usk -eq 5 ]
then
    echo "none" && doned
else
    repeat
fi

