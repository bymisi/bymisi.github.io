<?php
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("cache-control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

$base_shoutcast_url = 'http://yp.shoutcast.com/sbin/';

echo <<<EOL
<html>
  <head profile="http://www.w3.org/2005/11/profile">
    <title>Source Engine In-Game Shoutcast Player</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="main.css" type="text/css" />
  </head>
  <body>


EOL;
?>