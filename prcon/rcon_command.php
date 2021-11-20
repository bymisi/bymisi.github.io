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
    "servercommand" => "servercommand.ihtml"
  ));


  //Sprachblöcke setzen
  include("./includes/language.php");


  //get command
  if(isset($_POST["command"]))
    $command = $_POST["command"];
  else if(isset($_GET["command"]))
    $command = $_GET["command"];
  else
    $command = "";
    
  //parse helptext
  $command = trim(urldecode($command));
  $tpl->set_var(array(
    "CALLTYPE" => "",
    "COMMANDOUTPUT" => "",
    "COMMAND" => $command
  ));


  //connect to current server
  $server = new Rcon();

  //build connection to Gameserver
  $server->Connect($config_server_ip, $config_server_port, $config_server_password);

  //Get Server Info
  $info = $server->Info();

  //Not connected error
  if(!$info)
  {
    $tpl->parse("CONTENT", "generalnotconnected", false);
    $tpl->parse("OUT", "main", false);
    $tpl->p("OUT");
    exit;
  }

  //Action
  $result = "";

  if(isset($_POST["action"]) && $_POST["action"] == 1 || isset($_GET["action"]) && $_GET["action"] == 1 )
  {
    //set rcon command
    $result = $server->RconCommand($command);

    //Not connected error
    if(!$server->IsConnected())
    {
      $tpl->parse("CONTENT", "generalnotconnected", false);
      $tpl->parse("OUT", "main", false);
      $tpl->p("OUT");
      exit;
    }

    //put out output :)
    if(isset($_POST["commandoutput"]))
      $output = $_POST["commandoutput"];
    else
      $output = "";

    $output = StripSlashes("$output\nCommando: $command\n$result\n______________________________________________________________\n\n");
    $tpl->set_var("COMMANDOUTPUT", $output);

  }

  else if(isset($_GET["command"]) && trim($_GET["command"]) != "")
  {
    $tpl->set_var("COMMAND", $_GET["command"]);
    $tpl->parse("CALLTYPE", "rconcommandnotexecuted", false);
  }

  //close connection
  $server->Disconnect();

  //Bad rcon password
  if(trim($result) == "Bad rcon_password.")
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


  //parse content in main template
  $tpl->parse("CONTENT", "servercommand", false);

  //parse everything and print it
  $tpl->parse("OUT", "main", false);
  $tpl->p("OUT");

?>