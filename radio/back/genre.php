<?php
include('header.inc.php');
echo <<<EOL
<p style="clear: both;">
  <a href="./">View Top 500 Again</a>
</p>
EOL;
$genres = simplexml_load_file($base_shoutcast_url . 'newxml.phtml');
echo <<<EOL

<div id="genres">
  <div>
EOL;
$limit = ceil(count($genres->genre)/5);
$count = $limit;
foreach($genres->genre as $genre) {
  if ($count < 1) {
    echo <<<EOL

  </div>
  <div>
EOL;
    $count = $limit;
  }
  $genre['url'] = urlencode($genre['name']);
  $genre['name'] = htmlspecialchars($genre['name']);
  echo <<<EOL

<a href="./?genre={$genre['url']}">{$genre['name']}</a>
EOL;
  $count--;
}
echo <<<EOL

  </div>
</div>

EOL;
include('footer.inc.php');
?>
