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
        <title>PHPrcon - Test rcon</title>
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

  //get server info
  $status = $server->ServerInfo();

  //close connection
  $server->Disconnect();

  //Bad rcon password
  if(!is_array($status) && trim($status) == "Bad rcon_password.")
  {
    echo("<h1>Bad rcon password</h1>");
    exit;
  }

  //server settings are OK
  echo("<h1>Gameserver Settings OK</h1>");
  
?>
