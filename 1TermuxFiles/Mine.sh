
## ANSI colors (FG & BG)
RED="$(printf '\033[31m')"  GREEN="$(printf '\033[32m')"  ORANGE="$(printf '\033[33m')"  BLUE="$(printf '\033[34m')"
MAGENTA="$(printf '\033[35m')"  CYAN="$(printf '\033[36m')"  WHITE="$(printf '\033[37m')" BLACK="$(printf '\033[30m')"
REDBG="$(printf '\033[41m')"  GREENBG="$(printf '\033[42m')"  ORANGEBG="$(printf '\033[43m')"  BLUEBG="$(printf '\033[44m')"
MAGENTABG="$(printf '\033[45m')"  CYANBG="$(printf '\033[46m')"  WHITEBG="$(printf '\033[47m')" BLACKBG="$(printf '\033[40m')"
RESETBG="$(printf '\e[0m\n')"

if [ $(dpkg-query -W -f='${Status}' nano 2>/dev/null | grep -c "ok installed") -eq 0 ];
then
  apt-get install nano;
fi
## Script termination

## Banner
banner() {
	cat <<- EOF
${ORANGE}╔═══╗───╔╗─╔╗╔══╗─╔╗─────╔═╗╔═╗──────────
${ORANGE}║╔═╗║───║║─║║║╔╗║╔╝╚╗────║║╚╝║║──────────
${ORANGE}║║─╚╝╔═╗║╚═╝║║╚╝║╚╗╔╝╔══╗║╔╗╔╗║╔╗╔═╗─╔══╗
${ORANGE}║║─╔╗║╔╝╚═╗╔╝║╔═╝─║║─║╔╗║║║║║║║╠╣║╔╗╗║║═╣
${ORANGE}║╚═╝║║║─╔═╝║─║║───║╚╗║╚╝║║║║║║║║║║║║║║║═╣
${ORANGE}╚═══╝╚╝─╚══╝─╚╝───╚═╝╚══╝╚╝╚╝╚╝╚╝╚╝╚╝╚══╝ v2.2
	EOF
}

mine1() {
	cat <<- EOF
${BLUE}███╗░░░███╗██╗███╗░░██╗███████╗░░░░░░░███╗░░Raven
${BLUE}████╗░████║██║████╗░██║██╔════╝░░░░░░████║░░
${BLUE}██╔████╔██║██║██╔██╗██║█████╗░░░░░░░██╔██║░░Cryptonote
${BLUE}██║╚██╔╝██║██║██║╚████║██╔══╝░░░░░░░╚═╝██║░░
${BLUE}██║░╚═╝░██║██║██║░╚███║███████╗░░░░░███████╗TTR
${BLUE}╚═╝░░░░░╚═╝╚═╝╚═╝░░╚══╝╚══════╝░░░░░╚══════╝
	EOF
}

mine2() {
	cat <<- EOF
${RED}███╗░░░███╗██╗███╗░░██╗███████╗░░░░░██████╗░Bc
${RED}████╗░████║██║████╗░██║██╔════╝░░░░░╚════██╗
${RED}██╔████╔██║██║██╔██╗██║█████╗░░░░░░░░░███╔═╝Supxmr
${RED}██║╚██╔╝██║██║██║╚████║██╔══╝░░░░░░░██╔══╝░░
${RED}██║░╚═╝░██║██║██║░╚███║███████╗░░░░░███████╗
${RED}╚═╝░░░░░╚═╝╚═╝╚═╝░░╚══╝╚══════╝░░░░░╚══════╝TURTLE
	EOF
}

mine3() {
	cat <<- EOF
${ORANGE}███╗░░░███╗██╗███╗░░██╗███████╗░░░░░██████╗░Tcoin
${ORANGE}████╗░████║██║████╗░██║██╔════╝░░░░░╚════██╗
${ORANGE}██╔████╔██║██║██╔██╗██║█████╗░░░░░░░░█████╔╝Cryp-p
${ORANGE}██║╚██╔╝██║██║██║╚████║██╔══╝░░░░░░░░╚═══██╗
${ORANGE}██║░╚═╝░██║██║██║░╚███║███████╗░░░░░██████╔╝
${ORANGE}╚═╝░░░░░╚═╝╚═╝╚═╝░░╚══╝╚══════╝░░░░░╚═════╝░ETI
	EOF
}

mine4() {
	cat <<- EOF
${GREEN}███╗░░░███╗██╗███╗░░██╗███████╗░░░░░░░██╗██╗MONERO
${GREEN}████╗░████║██║████╗░██║██╔════╝░░░░░░██╔╝██║
${GREEN}██╔████╔██║██║██╔██╗██║█████╗░░░░░░░██╔╝░██║Gulf.mon
${GREEN}██║╚██╔╝██║██║██║╚████║██╔══╝░░░░░░░███████║
${GREEN}██║░╚═╝░██║██║██║░╚███║███████╗░░░░░╚════██║XMR
${GREEN}╚═╝░░░░░╚═╝╚═╝╚═╝░░╚══╝╚══════╝░░░░░░░░░░╚═╝HASH+
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

##Done
doned(){
echo ${MAGENTA} " Scripts collected and edited by ⓂⓘⓈⓘ-ⓒ"
echo ${MAGENTA} " ---𝕯𝖔𝖓𝖊--- "
sleep 3; clear; banner_small;
echo "🇲‌🇮‌🇳‌🇪‌ 🇨‌🇱‌🇴‌🇸‌🇪‌🇩‌ !"
}

##Repeat
repeat(){
echo "🇼‌🇷‌🇴‌🇳‌🇬‌ 🇰‌🇪‌🇾‌  Try 1 ..... 5 !!!"
sleep 3; clear; banner;sleep 2; 
mine1;sleep 1;mine2;sleep 1;mine3;
sleep 1;mine4;sleep 2;banner_small;

read -p "🇨‌🇭‌🇴‌🇴‌🇸‌🇪‌ 🇾‌🇴‌🇺‌🇷‌ 🇲‌🇮‌🇳‌🇪‌ !: " usk
 
if [ $usk -eq 1 ]
then
cd ~/xmrig/build
./misi

elif [ $usk -eq 2 ]
then
cd ~/xmrig/build
./2misi

elif [ $usk -eq 3 ]
then
cd ~/xmrig/build
./3misi

elif [ $usk -eq 4 ]
then
cd ~/xmrig/build
./4misi

elif [ $usk -eq 5 ]
then
    echo "none" && doned
else
    repeat
fi
}

sleep 1; clear; banner;sleep 2; 
mine1;sleep 1;mine2;sleep 1;mine3;
sleep 1;mine4;sleep 2;banner_small;

read -p "🇨‌🇭‌🇴‌🇴‌🇸‌🇪‌ 🇾‌🇴‌🇺‌🇷‌ 🇲‌🇮‌🇳‌🇪‌ !: " usk
 
if [ $usk -eq 1 ]
then
cd ~/xmrig/build
./misi

elif [ $usk -eq 2 ]
then
cd ~/xmrig/build
./2misi

elif [ $usk -eq 3 ]
then
cd ~/xmrig/build
./3misi

elif [ $usk -eq 4 ]
then
cd ~/xmrig/build
./4misi

elif [ $usk -eq 5 ]
then
    echo "none" && doned
else
    repeat
fi

