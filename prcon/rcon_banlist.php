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
    "serverbanlist" => "serverbanlist.ihtml"
  ));

  //Sprachblöcke setzen
  include("./includes/language.php");


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

  //und Action
  $feedback = "";
  if(isset($_GET["action"]))
  {
    //Ban ID
    if($_GET["action"] == "banid")
    {
      //If banid or timeframe is not valid => error
      $banid = $_POST["id"];
      $tmpbanid = $banid;
      settype($banid, "integer");
      settype($banid, "string");

      $timeframe = $_POST["timeframe"];
      $tmptimeframe = $timeframe;
      settype($timeframe, "integer");
      settype($timeframe, "string");

      if($tmpbanid != $banid || $tmptimeframe != $timeframe)
      {
        $banid = "";
        $timeframe = "";
      }

      //if kick is not "kick", set kick to ""
      if(!(isset($_GET["kick"]) && $_GET["kick"] != "kick"))
        $kick = "";
      else
        $kick = "kick";

      //send "banid" command to server
      $result = $server->RconCommand("banid $timeframe $banid $kick");

      //Not connected error
      if(!$result && $result != "")
      {
        $tpl->parse("CONTENT", "generalnotconnected", false);
        $tpl->parse("OUT", "main", false);
        $tpl->p("OUT");
        exit;
      }

      //Bad rcon password
      if(!is_array($result) && trim($result) == "Bad rcon_password.")
      {
        $tpl->parse("CONTENT", "generalbadrcon", false);
        $tpl->parse("OUT", "main", false);
        $tpl->p("OUT");
        exit;
      }


      //specify feedback
      if($result == "")
        $feedback = "serverbanlistban";
      else
        $feedback = "serverbanlistnoban";
    }

    //UnBan ID
    else if($_GET["action"] == "removeid")
    {
      //If banid is not valid => error
      $banid = $_POST["id"];
      $tmpbanid = $banid;
      settype($banid, "integer");
      settype($banid, "string");

      if($tmpbanid != $banid)
        $banid = "";

      //send "banid" command to server
      $result = $server->RconCommand("removeid $banid");

      //Not connected error
      if(!$result)
      {
        $tpl->parse("CONTENT", "generalnotconnected", false);
        $tpl->parse("OUT", "main", false);
        $tpl->p("OUT");
        exit;
      }

      //Bad rcon password
      if(!is_array($result) && trim($result) == "Bad rcon_password.")
      {
        $tpl->parse("CONTENT", "generalbadrcon", false);
        $tpl->parse("OUT", "main", false);
        $tpl->p("OUT");
        exit;
      }


      //specify feedback
      if(strstr($result, "removed"))
        $feedback = "serverbanlistunban";
      else
        $feedback = "serverbanlistnounban";
    }

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


  //parse feedback in main template
  $tpl->parse("FEEDBACK", $feedback, false);

  //parse all in main template
  $tpl->parse("CONTENT", "serverbanlist", false);

  //parse everything and print it
  $tpl->parse("OUT", "main", false);
  $tpl->p("OUT");

?>
