<?php
if (!empty($_COOKIE['laststation'])) {
  if (!empty($_GET['clearls'])) {
    setcookie('laststation','',time()-3600);
  }
  else {
    header('HTTP/1.1 302 Moved Temporarily');
    header('Location: player.php?url=' . $_COOKIE['laststation']);
  }
}

if(!empty($_GET['clear'])) {
  foreach($_COOKIE['streams'] as $key => $value) {
    setcookie('streams[' . $key . '][title]','',time()-3600);
    setcookie('streams[' . $key . '][count]','',time()-3600);
    $_COOKIE = array();
  }
}
include('header.inc.php');
echo <<<EOL
<p>
  <strong style="font-size: 1.2em;">Shoutcast Player and Browser</strong>
  <br />
  <span style="font-size: .75em;">This player uses the Windows Media Player object.  Internet Explorer (and the MOTD browser in Source games) supports this.  Other browsers (including the Steam client as of April 2010) require <a href="http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx">a plugin</a>.</span>
</p>
<ol>
  <li>
    Pick a station below.  By default, the most popular Shoutcast stations are shown.
  </li>
  <li>
    Pick a stream.
  </li>
  <li>
    Enjoy
  </li>
</ol>
EOL;
if (!empty($_COOKIE['streams'])) {
  echo <<<EOL

<hr />
<p>
  <strong>Recent Stations</strong> (Station - Listens):
  <br />
  <a href="./?clear=1" style="font-size: .7em;">Clear the List</a>
</p>
<p>
EOL;
  foreach ($_COOKIE['streams'] as $key => $row) {
    $counts[$key] = $row['count'];
  }
  arsort($counts);
  $sep = '';
  foreach ($counts as $key => $value) {
    echo $sep;
    echo <<<EOL

  <a href="pickstream.php?id={$key}">{$_COOKIE['streams'][$key]['title']}</a> - {$value} times
EOL;
    $sep = "\n  <br />";
  }
  echo <<<EOL

</p>
EOL;
}
echo <<<EOL

<hr />
<p>
  <a href="genre.php">Choose by Genre</a>
</p>
<form action="./" method="get">
  <div>
    <strong>Search for:</strong>
    <br />
    <input name="search" size="25" /> <input type="submit" value="Search" />
    <br />
    <span style="font-size: .8em;">Tip for Source games: Even though you may not see your last letter, it's really there.  Click outside the box to be sure.</span>
  </div>
</form>
<hr />
EOL;
$url = $base_shoutcast_url . 'newxml.phtml?limit=100&';
if (!empty($_GET['genre'])) {
  $url .= 'genre=' . urlencode($_GET['genre']);
}
elseif (!empty($_GET['search'])) {
  $url .= 'search=' . urlencode($_GET['search']);
}
else {
  $url .= 'genre=Top500';
}

$stations = simplexml_load_file($url);
for ($c = 10; empty($stations) and $c > 0; $c--) {
  $stations = simplexml_load_file($url);
  sleep(3);
}
if (empty($stations)) {
  die(<<<EOL

<p>
  The Shoutcast directory server could not be reached at this time.
</p>
EOL
  );
}
echo <<<EOL
<table style="font-size: .85em;">
  <tr>
    <th>
      Station Name
    </th>
    <th>
      KBps
    </th>
    <th>
      Genre
    </th>
    <th>
      Current Song
    </th>
    <th>
      Listeners
    </th>
  </tr>
EOL;
$alt = FALSE;
foreach($stations->station as $station) {
  $station = get_object_vars($station);
  $station_url = urlencode($station['@attributes']['name']);
  $station = array_map('htmlspecialchars',$station['@attributes']);
  echo "\n  <tr";
  if ($alt) {
    echo ' class="alt"';
  }
  echo <<<EOL
>
    <td>
      <a href="pickstream.php?id={$station['id']}&amp;title={$station_url}">{$station['name']}</a>
    </td>
    <td>
      {$station['br']}
    </td>
    <td>
      {$station['genre']}
    </td>
    <td>
      {$station['ct']}
    </td>
    <td>
      {$station['lc']}
    </td>
  </tr>
EOL;
  $alt = !$alt;
}
echo <<<EOL

</table>
EOL;
include('footer.inc.php');
?>
