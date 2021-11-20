<?php
if (isset($_GET['volume'])){
  setcookie('volume', $_GET['volume'], time()+60*60*24*365);
}
elseif (!isset($_COOKIE['volume'])) {
  setcookie('volume', 100, time()+60*60*24*365);
  $_GET['volume'] = 100;
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
<a href="http://bymisi.selfip.com/cantar/uploadcs/wmp11.exe">[MediaPlayer]</a><br><br><br>
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


<a href="http://bymisi.selfip.com/radio/player.php?url=http://www.savetofile.net:7000/">
<font style="font-size:12px;color:cyan"><big><b>

[Radio Face] </a>

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
