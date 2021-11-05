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
    "servermaps" => "servermaps.ihtml"
  ));

  //Sprachblöcke setzen
  include("./includes/language.php");

  //Blocks definieren
  $tpl->set_block("servermaps", "map", "DUMMY");
  $tpl->set_block("servermaps", "dir", "DUMMY");
  $tpl->set_block("servermaps", "servertype", "SERVERTYPE");

  //parse helptext
  $tpl->set_var(array(
    "DUMMY" => "",
    "SERVERTYPE" => ""
  ));


  //connect to current server
  $server = new Rcon();
  $server->Connect($config_server_ip, $config_server_port, $config_server_password);

  //Get Server Info
  $info = $server->Info();

  //Get Server maps
  $maps = $server->ServerMaps($config_map_amount);

  //Not connected error
  if(!$maps)
  {
    $tpl->parse("CONTENT", "generalnotconnected", false);
    $tpl->parse("OUT", "main", false);
    $tpl->p("OUT");
    exit;
  }

  //send mapchange command
  if(isset($_GET["action"]) && $_GET["action"] == "change")
  {
    //send command
    $result  = $server->RconCommand("changelevel " . $_GET["mapname"]);

    //specify feedback
    if(strstr($result, "failed"))
      $feedback = "servermapsnochange";
    else
      $feedback = "servermapschange";
  }
  else
    $feedback = "";

  //close connection
  $server->Disconnect();

  //Bad rcon password
  if(!is_array($maps) && trim($maps) == "Bad rcon_password.")
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


  //get all servertypes
  $servertypes = array_keys($maps);

  //output maps by servertypes
  for($i = 0; $i < sizeof($servertypes); $i++)
  {
    //get all maps for one servertype
    $servermaps = $maps[$servertypes[$i]];

    //output directory name
    $tpl->set_var("DIRECTORY" , $servertypes[$i]);
    $tpl->parse("ROWS", "dir");

    //sort maps
    sort($servermaps);

    //output all maps
    $row = "";
    for($j = 0; $j < sizeof($servermaps); $j++)
    {

      //toggle row color
      if($row == "row_one")
        $row = "row_two";
      else
        $row = "row_one";

      //fill template
      $tpl->set_var(array(
        "ROWSTYLE" => $row,
        "MAPNAME" => $servermaps[$j],
      ));
      $tpl->parse("ROWS", "map", true);

    } //for($j = 0; $j < sizeof($servermaps); $j++)

    $tpl->parse("SERVERTYPE", "servertype", true);

  } //for($i = 0; $i < sizeof($servertypes); $i++)


  //parse content in main template
  $tpl->parse("FEEDBACK", $feedback, false);

  //parse content in main template
  $tpl->parse("CONTENT", "servermaps", false);

  //parse everything and print it
  $tpl->parse("OUT", "main", false);
  $tpl->p("OUT");

?>
