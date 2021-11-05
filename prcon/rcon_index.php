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

  //Includes
  include("./classes/rcon_hl_net.inc");
  include("./classes/template.inc");

  //Get all configs
  include("./config/config.php");

  //login mechanism
  include("./includes/security.php");


  //neues Template erzeugen
  $tpl = new Template("./themes/$config_theme", "keep");

  //Files bekannt machen
  $tpl->set_file(array(
    "main" => "main.ihtml",
    "serverinfo" => "serverinfo.ihtml"
  ));

  //Sprachblöcke setzen
  include("./includes/language.php");


  //Blocks definieren
  $tpl->set_block("serverinfo", "player", "PLAYER");
  $tpl->set_block("serverinfo", "connectingplayer", "CONNECTINGPLAYER");
  $tpl->set_block("serverinfo", "playerlist", "PLAYERLIST");

  //set global output
  $tpl->set_var(array(
    "CONNECTINGPLAYER" => ""
  ));


  //connect to current server
  $server = new Rcon();
  $server->Connect($config_server_ip, $config_server_port, $config_server_password);

  //get server info
  $status = $server->ServerInfo();

  //Not connected error
  if(!$status)
  {
    $tpl->parse("CONTENT", "generalnotconnected", false);
    $tpl->parse("OUT", "main", false);
    $tpl->p("OUT");
    exit;
  }

  //close connection
  $server->Disconnect();

  //Bad rcon password
  if(!is_array($status) && trim($status) == "Bad rcon_password.")
  {
    $tpl->parse("CONTENT", "generalbadrcon", false);
    $tpl->parse("OUT", "main", false);
    $tpl->p("OUT");
    exit;
  }


  //get global server info
  $tpl->set_var(array(
    "HOSTNAME" => $status["name"],
    "VERSION" => $status["mod"],
    "TCPIP" => $status["ip"],
    "MAP" => $status["map"],
    "PLAYERS" => ($status["activeplayers"] . " active / " . $status["maxplayers"] . " max")
  ));


  //get player info
  $row = "";
  for($i = 1; $i <= $status["activeplayers"]; $i++)
  {

    //toggle row color
    if($row == "row_one")
      $row = "row_two";
    else
      $row = "row_one";

    $tpl->set_var(array(
      "ROWSTYLE" => $row,
      "NUMBER" => $i
    ));

    //in case we have a player line ...
    if(isset($status[$i]))
    {
      $tpl->set_var(array(
        "NAME" => $status[$i]["name"],
        "URLNAME" => urlencode($status[$i]["name"]),
        "ID" => $status[$i]["id"],
        "WONID" => $status[$i]["wonid"],
        "FRAG" => $status[$i]["frag"],
        "TIME" => $status[$i]["time"],
        "PING" => $status[$i]["ping"],
        "ADRESS" => $status[$i]["adress"]
      ));

      $tpl->parse("PLAYER", "player", true);
    }
    else
      $tpl->parse("PLAYER", "connectingplayer", true);

  } //for($i = 7; $i < 7 + $active[0]; $i++)


  //check if there are any players online
  if($status["activeplayers"] >= 1)
    $tpl->parse("PLAYERLIST", "playerlist", false);
  else
    $tpl->set_var("PLAYERLIST", "");


  //parse content in main template
  $tpl->parse("CONTENT", "serverinfo", false);

  //parse everything and print it
  $tpl->parse("OUT", "main");
  $tpl->p("OUT");

?>