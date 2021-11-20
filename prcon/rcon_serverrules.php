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
    "serverrules" => "serverrules.ihtml"
  ));

  //Sprachblöcke setzen
  include("./includes/language.php");

  //Blocks definieren
  $tpl->set_block("serverrules", "row", "ROWS");


  //connect to current server
  $server = new Rcon();

  //Not connected error
  $server->Connect($config_server_ip, $config_server_port, $config_server_password);

  //get server info
  $rules = $server->ServerRules();

  //Get Server Info
  $info = $server->Info();

  //Not connected error
  if(!$rules)
  {
    $tpl->parse("CONTENT", "generalnotconnected", false);
    $tpl->parse("OUT", "main", false);
    $tpl->p("OUT");
    exit;
  }

  //close connection
  $server->Disconnect();

  //Bad rcon password
  if(!is_array($rules) && trim($rules) == "Bad rcon_password.")
  {
    $tpl->parse("CONTENT", "generalbadrcon", false);
    $tpl->parse("OUT", "main", false);
    $tpl->p("OUT");
    exit;
  }


  //display global server info
  $tpl->set_var(array(
    "TCPIP" => $info["ip"] ,
    "HOSTNAME" => $info["name"],
    "MAP" => $info["map"],
    "PLAYERS" => ($info["activeplayers"] . " active  / " . $info["maxplayers"] . " max")
  ));


  //Bad rcon password
  if(!is_array($rules) && trim($rules) == "Bad rcon_password.")
  {
    $tpl->parse("CONTENT", "generalbadrcon", false);
    $tpl->parse("OUT", "main", false);
    $tpl->p("OUT");
    exit;
  }


  //Do all output stuff
  $row = "";

  while(list($rule, $value) = each($rules))
  {
    //toggle row color
    if($row == "row_one")
      $row = "row_two";
    else
      $row = "row_one";

    //parse rule
    $tpl->set_var(array(
      "ROWSTYLE" => $row,
      "COMMAND" => urlencode("$rule $value"),
      "RULE" => $rule,
      "VALUE" => $value
    ));

    $tpl->parse("ROWS", "row", true);
  }



/*
  $rule_count = sizeof($rules);
  for($i = 0; $i < $rule_count; $i++)
  {
    //toggle row color
    if($row == "row_one")
      $row = "row_two";
    else
      $row = "row_one";

    //parse rule
    $tpl->set_var(array(
      "ROWSTYLE" => $row,
      "COMMAND" => urlencode($rules[$i]["name"] . " " . trim($rules[$i]["value"])),
      "RULE" => $rules[$i]["name"],
      "VALUE" => $rules[$i]["value"]
    ));

    $tpl->parse("ROWS", "row", true);

  } //for($i = 1; $i <= $count; $i++)
*/

  //parse content in main template
  $tpl->parse("CONTENT", "serverrules", false);

  //parse everything and print it
  $tpl->parse("OUT", "main", false);
  $tpl->p("OUT");

?>
