<?php
// ************************************************************************
//PHPrcon - PHP script collection to remotely administrate and configure Halflife and HalflifeMod Servers through a webinterface
//Copyright (C) 2002  Henrik Beige
//
//This library is free software; you can redistribute it and/or
//modify it under the terms of the GNU Lesser General Public
//License as published by the Free Software Foundation; either
//version 2.1 of the License, or (at your option) any later version.
//
//This library is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
//Lesser General Public License for more details.
//
//You should have received a copy of the GNU Lesser General Public
//License along with this library; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// ************************************************************************

  include("../classes/rcon_hl_net.inc");

  echo ("
    <html>
      <head>
        <title>PHPrcon - Test Map Packages</title>
      </head>
      
      <body>
        <center>
   ");
   
   
  //connect to current server
  $server = new Rcon();
  
  //Not connected error
  $server->Connect($_GET["ip"], $_GET["port"], $_GET["rconpassword"]);
    
  if(!$server->IsConnected())
  {
    echo("<h1>Server not available</h1>");
    exit;
  }

  //get maps side XXX
  if(isset($_GET["side"]))
    $side = $_GET["side"];
  else
    $side = 0;
    
  $status = $server->RconCommand("maps *", $side);

  //close connection
  $server->Disconnect();

  //Bad rcon password
  if(!is_array($status) && trim($status) == "Bad rcon_password.")
  {
    echo("<h1>Bad rcon password</h1>");
    exit;
  }

  //server settings are OK
  echo("<h1>Page $side OK</h1>");
  echo("<a name='next' href='./testmaps.php?ip={$_GET["ip"]}&port={$_GET["port"]}&rconpassword={$_GET["rconpassword"]}&side=" . ($side + 1) . "'>Next page</a>");
  echo("
  <script type='text/javascript'>
  <!--
    parent.document.getElementsByName('config_map_amount')[0].value = '$side';
    document.getElementsByName('next')[0].click();
  -->
  </script>
  </body>
  </html>
  ");
  
?>
