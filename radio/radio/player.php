<?php
if (isset($_GET['volume'])){
  setcookie('volume', $_GET['volume'], time()+60*60*24*365);
}
elseif (!isset($_COOKIE['volume'])) {
  setcookie('volume', 80, time()+60*60*24*365);
  $_GET['volume'] = 80;
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
  <span style="font-size: .75em; margin-left: 20px; display: block;">This player uses the Windows Media Player object.  Internet Explorer (and the MOTD browser in Source games) supports this.  Other browsers (including the Steam client as of April 2010) require <a href="http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx">a plugin</a>.</span>
</div>
<p>
  <a href="./?clearls=1">Change Station</a>
</p>
<p>
  Volume (currently {$_GET['volume']}):
  <br />
  <a href="player.php?volume=100&amp;url={$_GET['url']}">100</a>
  <br />
  <a href="player.php?volume=90&amp;url={$_GET['url']}">90</a>
  <br />
  <a href="player.php?volume=80&amp;url={$_GET['url']}">80</a>
  <br />
  <a href="player.php?volume=70&amp;url={$_GET['url']}">70</a>
  <br />
  <a href="player.php?volume=60&amp;url={$_GET['url']}">60</a>
  <br />
  <a href="player.php?volume=50&amp;url={$_GET['url']}">50</a>
  <br />
  <a href="player.php?volume=40&amp;url={$_GET['url']}">40</a>
  <br />
  <a href="player.php?volume=30&amp;url={$_GET['url']}">30</a>
  <br />
  <a href="player.php?volume=20&amp;url={$_GET['url']}">20</a>
  <br />
  <a href="player.php?volume=10&amp;url={$_GET['url']}">10</a>
  <br />
  <a href="player.php?volume=5&amp;url={$_GET['url']}">5</a>
  <br />
  <a href="player.php?volume=0&amp;url={$_GET['url']}">Mute</a>
</p>
EOL;
include('footer.inc.php');
?>
