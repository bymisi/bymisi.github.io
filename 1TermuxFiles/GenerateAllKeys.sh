
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
		${ORANGE}                            Version 2.1
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
 
sleep 1; clear; banner;sleep 2; banner_small;

echo ${BLUE} " installing openssl-tool & openssh ";
apt update ;
echo ${GREEN} " --------------------"
apt satisfy openssl-tool -y ;
echo ${ORANGE} " --------------------"
apt satisfy openssh -y ;

echo ${YELLOW} " Creating files ";

echo "basicConstraints=CA:true" > android_options.txt ;

echo "rm *.crt && rm *.pfx && rm *.pem && rm *.key && rm *.p12 && rm *.pub && rm clean.sh && ls" > clean.sh;

echo ${GREEN} " Creating temp key file ";

echo ${BLUE} && openssl genrsa -out priv_and_pub.key 2048 ;

echo ${GREEN} " Creating CA.pem  ";

openssl req -new -days 9650 -key priv_and_pub.key -out CA.pem ;

echo ${BLUE} " Creating CA.crt ";

openssl x509 -req -days 9650 -in CA.pem -signkey priv_and_pub.key -extfile ./android_options.txt -out CA.crt ;

echo ${GREEN} " Creating CA.der.crt No Warning Certificate "

openssl x509 -inform PEM -outform DER -in CA.crt -out CA_noWarning.der.crt &&

echo ${BLUE} " Creating CA.pfx For signing Pdf Files with OfficeSuite"

openssl pkcs12 -export -out CA_forSigningPdf.pfx -inkey priv_and_pub.key -in CA.crt -name "bymisi" ;

echo ${GREEN} " Creating keyStore.p12 for PacketSniffer "

openssl pkcs12 -export -out keyStore_Sniffer.p12 -inkey priv_and_pub.key -in CA.crt -name "bymisi" ;

echo ${YELLOW} " Creating private and public key pair for GitHub "

ssh-keygen -t rsa -b 4096 -C "misi@dr.com" -f privKey_GitHub.key ;

echo ${GREEN} " Cleaning temp files "

rm android_options.txt

echo ${MAGENTA} " Scripts collected and edited by MiSi-C "

echo ${MAGENTA} " ---Done--- "

sleep 2; clear; banner;banner_small;

ls 
