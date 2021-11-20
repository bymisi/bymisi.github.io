<?php
if (isset($_GET['volume'])){
  setcookie('volume', $_GET['volume'], time()+60*60*24*365);
}
elseif (!isset($_COOKIE['volume'])) {
  setcookie('volume', 50, time()+60*60*24*365);
  $_GET['volume'] = 50;
}
else {
  $_GET['volume'] = $_COOKIE['volume'];
}

include('header.inc.php');

if (stristr($_SERVER['HTTP_USER_AGENT'], "MSIE")) $classinfo = <<<EOL
classid="clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6" type="application/x-oleobject"
EOL;
else $classinfo = <<<EOL
type="application/x-ms-wmp"
EOL;

echo <<<EOL
<div>
  <object style="width: 100%; height: 63px;" {$classinfo}>
    <param name="URL" value="{$_GET['url']}" />
    <param name="AutoStart" value="True" />
    <param name="volume" value="{$_GET['volume']}">
  </object>
  <span style="font-size: .75em; color: cyan; margin-left: 10px; display: block;">if radio does not working download and install:
<a href="http://bymisi.selfip.com:3333/uploadcs/wmp11.exe">[MediaPlayer]</a><br><br><br>
</span>

<span style="font-size: .75em; color: black; margin-left: 8px; display: block;"><big><b>


<a href="http://bymisi.selfip.com/radio/player.php?url=http://cp2.internet-radio.org.uk:30115/">
<font style="font-size:12px;color:cyan"><big><b>


[HOT 100]</a>


<a href="http://bymisi.selfip.com/radio/player.php?url=http://listen.technobase.fm/tunein-dsl-pls/">
<font style="font-size:12px;color:cyan"><big><b>


[Technobase]</a>


<a href="http://bymisi.selfip.com/radio/player.php?url=http://79.172.201.167:8100/">
<font style="font-size:12px;color:cyan"><big><b>


[V.I.P HU]</a>

<a href="http://bymisi.selfip.com/radio/player.php?url=http://cp.internet-radio.org.uk:15114/">
<font style="font-size:12px;color:cyan"><big><b>


[Maxdance] </a>

</span>

 <br><br>

<span style="font-size: .95em; color: black; margin-left: 22px; display: block;"><big><b>
<a href="http://bymisi.selfip.com/cantar/uploadcs/guestbook.htm">
<font style="font-size:18px;color:red"><big><b>
[[[STOP RADIO]]]</font></a><br><br><br><br>
</span>
<span style="font-size: .30em; color: cyan; margin-left: 10px; display: block;"><big><b>
<a href="http://bymisi.selfip.com/radio/radio_list.htm">
<font style="font-size:10px;color:cyan"><big><b>
[CHANNELS_LIST] </a> <br>

<script language="Javascript" src="http://bymisi.selfip.com/shared/graphcount.php?page=PAGENAME"><!--

//--></script>

</div>

EOL;
?>
Your ip is :
<?php
function getIP($type) { 
                 if (getenv("HTTP_CLIENT_IP") 
                     && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
                     $ip = getenv("HTTP_CLIENT_IP"); 
                 else if (getenv("REMOTE_ADDR") 
                     && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
                     $ip = getenv("REMOTE_ADDR"); 
                 else if (getenv("HTTP_X_FORWARDED_FOR") 
                     && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
                     $ip = getenv("HTTP_X_FORWARDED_FOR"); 
                 else if (isset($_SERVER['REMOTE_ADDR']) 
                     && $_SERVER['REMOTE_ADDR'] 
                     && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
                     $ip = $_SERVER['REMOTE_ADDR']; 
                 else { 
                     $ip = "unknown"; 
                 return $ip; 
                     } 
                 if ($type==1) {return md5($ip);} 
                 if ($type==0) {return $ip;} 
                 } 

echo getIP(0); 
?>