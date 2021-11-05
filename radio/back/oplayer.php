<?php
if (isset($_GET['volume'])){
  setcookie('volume', $_GET['volume'], time()+60*60*24*365);
}
elseif (!isset($_COOKIE['volume'])) {
  setcookie('volume', 60, time()+60*60*24*365);
  $_GET['volume'] = 60;
}
else {
  $_GET['volume'] = $_COOKIE['volume'];
}

setcookie('laststation', $_GET['url'], time()+60*60*24*365);

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
  <span style="font-size: .75em; margin-left: 8px; display: block;">if radio does not working download and install:
<a href="http://bymisi.selfip.com/shared/wmp11.exe">MediaPlayer</a><br><br><br>
</span>

<span style="font-size: .95em; color: black; margin-left: 8px; display: block;"><big><b>

<a href="http://bymisi.selfip.com/radio/player.php?url=http://95.169.188.110:8000/">
<font style="font-size:18px;color:red"><big><b>

[RADIO_1]</a>

<a href="http://bymisi.selfip.com/radio/player.php?url=http://78.159.104.178:80/">
<font style="font-size:18px;color:green"><big><b>
[RADIO_2]</a>

<a href="http://bymisi.selfip.com/radio/player.php?url=http://listen.technobase.fm/tunein-dsl-pls">
<font style="font-size:18px;color:blue"><big><b>
[RADIO_3]</a>

</span> <br><br>

<span style="font-size: .95em; color: black; margin-left: 22px; display: block;"><big><b>
<a href="http://bymisi.selfip.com/cantar/uploadcs/guestbook.htm">
<font style="font-size:18px;color:black"><big><b>
[[[STOP RADIO]]]</a>
</span>
</div>
EOL;
include('footer.inc.php');
?>
