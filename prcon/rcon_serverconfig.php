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


  function FormatFilename($file)
  {
    $slashpos = 0;

    //test on slashes
    $tmppos = strrpos(substr($file, 0, strrpos($file, "/")), "/");
    if($tmppos > 0)
      $slashpos = $tmppos;

    //test on backslashes
    $tmppos2 = strrpos(substr($file, 0, strrpos($file, "\\")), "\\");
    if($tmppos2 > 0)
      $slashpos = $tmppos2;

    //return formatted string
    return ("... " . substr($file, $slashpos));
  }


  //neues Template erzeugen
  $tpl = new Template("./themes/$config_theme", "keep");

  //Files bekannt machen
  $tpl->set_file(array(
    "main" => "main.ihtml",
    "serverconfig" => "serverconfig.ihtml"
  ));

  //Sprachblöcke setzen
  include("./includes/language.php");

  //Blocks definieren
  $tpl->set_block("serverconfig", "row", "ROW");
  $tpl->set_block("serverconfig", "norow", "NOROW");
  $tpl->set_block("serverconfig", "list", "LIST");
  $tpl->set_block("serverconfig", "edit", "EDIT");

  //parse helptext
  $tpl->set_var(array(
    "FEEDBACK" => "<br>",
    "LIST" => "",
    "EDIT" => "",
    "ROW" => "",
    "NOROW" => ""
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

  //Close connection
  $server->Disconnect();


  //display global server info
  $tpl->set_var(array(
    "TCPIP" => $info["ip"] ,
    "HOSTNAME" => $info["name"],
    "MAP" => $info["map"],
    "PLAYERS" => ($info["activeplayers"] . " active  / " . $info["maxplayers"] . " max")
  ));


  //display feedback if PHPrcon and Gameserver are not running on same machine. $localserver option enabled.
  if($config_localserver != 1)
    $tpl->parse("LIST", "serverconfignolocalserver", false);


  //edit file
  else if(isset($_GET["mod"]))
  {

    //Check if key exists
    if(!(isset($_GET["key"]) && isset($config_file[$_GET["key"]]) && file_exists($config_file[$_GET["key"]])))
    {
      //parse feedback
      $tpl->parse("EDIT", "serverconfignofileexist", false);
    } //if(!(isset($_GET["key"]) && isset($cfg_file[$_GET["key"]]) && file_exists($config_file[$_GET["key"]])))


    //edit stuff
    else if($_GET["mod"] == "edit")
    {
      //get filename
      $file = $config_file[$_GET["key"]];

      //get fiel contents
      $filecontent = "";
      $fp = fopen ($file, "r");
      while (!feof($fp))
      {
        $filecontent .= fread($fp, 4096);
      }
      fclose($fp);
      $filecontent = str_replace(array("<", ">"), array("&lt;", "&gt;"), $filecontent);
 
      //display all
      $tpl->set_var(array(
        "TEXT" => $filecontent,
        "KEY" => $_GET["key"],
        "PATH" => $file,
        "CFG" => FormatFilename($file)
      ));
      $tpl->parse("EDIT", "edit", false);

    } //else if($_GET["mod"] == "edit")


    //save stuff
    else if($_GET["mod"] == "save")
    {
      //get filename
      $file = $config_file[$_GET["key"]];

      //get configfile content
      $filecontent = $_POST["configfile"];

      //format configfile
      $filecontent = stripslashes($filecontent);
      $filecontent = str_replace("\n\r", "\n", $filecontent);

      //copy old "config file" in "config file.bak"
      @unlink($file . ".bak");
      $result = copy($file, $file . ".bak");
      chmod($file . ".bak", octdec(666));


      //save changes
      $fp = fopen($file, "w");
      if($fp && $result)
      {
        //save
        fwrite($fp, $filecontent, strlen($filecontent));
        fclose($fp);
        $feedback = "serverconfigfilesaved";

      } //if($fp && $result)
      else
        $feedback = "serverconfignopermission";


      //set output
      $filecontent = str_replace(array("<", ">"), array("&lt;", "&gt;"), $filecontent);

      //display all
      $tpl->set_var(array(
        "TEXT" => $filecontent,
        "KEY" => $_GET["key"],
        "PATH" => $file,
        "CFG" => FormatFilename($file)
      ));

      $tpl->parse("FEEDBACK", $feedback, false);
      $tpl->parse("EDIT", "edit", false);

    } //else if($_GET["mod"] == "save")

  } //else if(isset($_GET["mod"]))


  //display file list
  else if(isset($config_file) && sizeof($config_file) > 0)
  {
    //sort the array keys naturally
    $keys = array_keys($config_file);
    natsort($keys);

    //display list
    $row = "";
    $keys_count = sizeof($keys);
    for($i = 0; $i < $keys_count; $i++)
    {
      //toggle row color
      if($row == "row_one")
        $row = "row_two";
      else
        $row = "row_one";

      //check on status
      $file = $config_file[$keys[$i]];
      if(!file_exists($file))
        $status = "serverconfignofileexist";
      else
      {
        $fileperms = fileperms($file);
      	if(!($fileperms & 2 && $fileperms & 4))
      	  $status = "serverconfignopermission";
        else
          $status = "serverconfigok";
      }

      //parse cfg file
      $tpl->set_var(array(
        "ROWSTYLE" => $row,
        "KEY" => $keys[$i],
        "CFG" => FormatFilename($file)
      ));

      $tpl->parse("STATUS", $status, false);
      if($status == "serverconfigok")
        $tpl->parse("ROW", "row", true);
      else
        $tpl->parse("ROW", "norow", true);

    } //for($i = 0; $i < $keys_count; $i++)

    //parse cfg filelist out
    $tpl->parse("LIST", "list", false);

  } //else if(sizeof($config_file) > 0)


  //parse content in main template
  $tpl->parse("CONTENT", "serverconfig", false);

  //parse everything and print it
  $tpl->parse("OUT", "main", false);
  $tpl->p("OUT");

?>
