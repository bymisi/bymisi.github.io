<?php
if (!empty($_GET['url'])) {
  header('HTTP/1.1 302 Moved Temporarily');
  header('Location: http://bymisi.selfip.com/radio/player.php?url=http://78.159.104.196:80' . $_SERVER['HTTP_HOST'] . '/radio/?clear=1');
  die;
}
if (empty($_GET['retry'])) {
  if (empty($_COOKIE['streams'][$_GET['id']])) {
    setcookie('streams[' . $_GET['id'] . '][title]', $_GET['title'], time()+60*60*24*365);
    setcookie('streams[' . $_GET['id'] . '][count]', 1, time()+60*60*24*365);
  }
  else {
    setcookie('streams[' . $_GET['id'] . '][count]', $_COOKIE['streams'][$_GET['id']]['count'] + 1, time()+60*60*24*365);
  }
}
include('header.inc.php');
echo <<<EOL
<h1>
  Pick a Stream
</h1>
EOL;
$pls = file_get_contents($base_shoutcast_url . 'tunein-station.pls?id=' . $_GET['id']);
preg_match_all('/File[0-9]+=(http:.*)/',$pls,$streams);
for ($c = 20; empty($streams[1]) and $c > 0; $c--) {
  $pls = file_get_contents($base_shoutcast_url . 'tunein-station.pls?id=' . $_GET['id']);
  preg_match_all('/File[0-9]+=(http:.*)/',$pls,$streams);
  sleep(3);
}
if (empty($streams[1])) {
  echo <<<EOL

<p style="color: #f00;">
  This station didn't return any streams.  Sometimes the cheaper servers will do that if they're busy.
  <br />
  <a href="./">Go back to the main page</a>.
</p>
EOL;
}
else {
  echo <<<EOL

<p>
  If the first one doesn't work, try another.
</p>
<p>
EOL;
  $sep = '';
  foreach ($streams[1] as $stream) {
    $stream = preg_replace('/(:[0-9]+)$/','$1/',$stream);
    $stream_url = urlencode($stream);
    $stream = htmlspecialchars($stream);
    echo $sep;
    echo <<<EOL

  <a href="player.php?url={$stream}">{$stream}</a>
EOL;
    $sep = "\n  <br />";
  }
  echo <<<EOL

</p>
EOL;
}
include('footer.inc.php');
?>
