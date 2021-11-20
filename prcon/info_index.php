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


  //neues Template erzeugen
  $tpl = new Template("./themes/$config_theme", "keep");

  //Files bekannt machen
  $tpl->set_file(array(
    "publicinfo" => "publicinfo.ihtml",
  ));

  //Sprachblöcke setzen
  include("./includes/language.php");

  //Blocks definieren
  $tpl->set_block("publicinfo", "player", "PLAYER");
  $tpl->set_block("publicinfo", "connectingplayer", "CONNECTINGPLAYER");
  $tpl->set_block("publicinfo", "playerlist", "PLAYERLIST");

  //set global output
  $tpl->set_var(array(
    "PLAYER" => "",
    "CONNECTINGPLAYER" => ""
  ));


  //connect to current server
  $server = new Rcon();

  //Not connected error
  $server->Connect($config_server_ip, $config_server_port);

  //Get Server Info
  $info = $server->Info();

  //Get Player Info
  $players = $server->Players();

  //Not connected error
  if(!$info && !$player)
  {
    $tpl->parse("CONTENT", "generalnotconnected", false);
    $tpl->parse("OUT", "main", false);
    $tpl->p("OUT");
    exit;
  }

  //close connection
  $server->Disconnect();


  //display global server info
  $tpl->set_var(array(
    "TCPIP" => $info["ip"] ,
    "HOSTNAME" => $info["name"],
    "MAP" => $info["map"],
    "PLAYERS" => ($info["activeplayers"] . " active  / " . $info["maxplayers"] . " max")
  ));


  //display player info
  $row = "";
  for($i = 1; is_array($players) && $i <= $info["activeplayers"]; $i++)
  {
    //toggle row color
    if($row == "row_one")
      $row = "row_two";
    else
      $row = "row_one";

    //parse connecting players
    $tpl->set_var(array(
      "ROWSTYLE" => $row,
      "NUMBER" => $i
    ));

    if($i < count($players))
    {
      //parse player info
      $tpl->set_var(array(
        "NAME" => $players[$i]["name"],
        "FRAG" => $players[$i]["frag"],
        "TIME" => $players[$i]["time"]
      ));
      $tpl->parse("PLAYER", "player", true);
    }

    else
      $tpl->parse("CONNECTINGPLAYER", "connectingplayer", true);

  } //for($i = 1; $i <= $line[0]; $i++)

  //parse Playerlist
  if($info["activeplayers"] > 0)
    $tpl->parse("PLAYERLIST", "playerlist");
  else
    $tpl->set_var("PLAYERLIST", "");

  //parse everything and print it
  $tpl->parse("OUT", "publicinfo");
  $tpl->p("OUT");

?>