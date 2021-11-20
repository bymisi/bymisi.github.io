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

  //include stuff
  include("./includes/session_management.php");
  include("../classes/template.inc");

  //create new template
  $tpl = new Template("./templates", "keep");

  //Set Files
  $tpl->set_file(array(
    "main" => "main.ihtml",
    "tabpane" => "tabpane.ihtml",
    "content" => "install_download.ihtml"
  ));

  //Set Blocks
  $tpl->set_block("main", "message", "MESSAGE");
  $tpl->set_block("main", "error", "ERROR");
  $tpl->set_block("tabpane", "tabpanedownload", "DUMMY");
  $tpl->set_block("content", "description_ok", "DUMMY");
  $tpl->set_block("content", "description_error", "DUMMY");
  $tpl->set_block("content", "error_usernametoshort", "DUMMY");
  $tpl->set_block("content", "error_userpasswordtoshort", "DUMMY");
  $tpl->set_block("content", "error_userpasswordnoconfirm", "DUMMY");
  $tpl->set_block("content", "error_noserverip", "DUMMY");
  $tpl->set_block("content", "error_noserverport", "DUMMY");
  $tpl->set_block("content", "error_noserverpassword", "DUMMY");
  $tpl->set_block("content", "error_mapamounttolow", "DUMMY");
  $tpl->set_block("content", "feedback", "FEEDBACK");
  $tpl->set_block("content", "user", "USER");
  $tpl->set_block("content", "gameserver", "GAMESERVER");
  $tpl->set_block("content", "config_file", "CONFIG_FILE");
  $tpl->set_block("content", "no_config_file", "DUMMY");
  $tpl->set_block("content", "configuration", "DUMMY");

  $tpl->set_var(array(
    "ERROR" => "",
    "DUMMY" => "",
    "FEEDBACK" => "",
    "CONFIGURATION" => ""
  ));

  //general sets an parses
  include("./includes/template_globals.php");


  //error check
  $error = 0;
  $config_user_name = trim($config_user_name);
  if(strlen($config_user_name) < 3)
  {
    $tpl->parse("FEEDBACK", "error_usernametoshort", true);
    $error = 1;
  }
  
  $config_user_password = trim($config_user_password);
  if(strlen($config_user_password) < 3)
  {
    $tpl->parse("FEEDBACK", "error_userpasswordtoshort", true);

    $_SESSION["config_user_password"] = "";
    $_SESSION["config_user_password_confirm"] = "";

    $error = 1;
  }

  $config_user_password_confirm = trim($config_user_password_confirm);
  if($config_user_password != $config_user_password_confirm)
  {
    $tpl->parse("FEEDBACK", "error_userpasswordnoconfirm", true);

    $_SESSION["config_user_password"] = "";
    $_SESSION["config_user_password_confirm"] = "";

    $error = 1;
  }

  $config_server_ip = trim($config_server_ip);
  if($config_server_ip == "")
  {
    $tpl->parse("FEEDBACK", "error_noserverip", true);
    $error = 1;
  }  

  $config_server_port = trim($config_server_port);
  if($config_server_port == "")
  {
    $tpl->parse("FEEDBACK", "error_noserverport", true);
    $error = 1;
  }  

  $config_server_password = trim($config_server_password);
  if($config_server_password == "")
  {
    $tpl->parse("FEEDBACK", "error_noserverpassword", true);
    $error = 1;
  }  

  if($config_map_amount < 1)
  {
    $tpl->parse("FEEDBACK", "error_mapamounttolow", true);
    $error = 1;
  }  

  if($config_localserver != 1)
    $config_localserver = 0;

  if($config_helptext != 1)
    $config_helptext = 0;


  //output
  if($error == 0)
  {
  
    //create output
    $tpl->set_var(array(
      "USER_NAME" => $config_user_name,
      "USER_PASSWORD" => $config_user_password,
      "SERVER_IP" => $config_server_ip,
      "SERVER_PORT" => $config_server_port,
      "SERVER_PASSWORD" => $config_server_password,
      "SERVER_MAP_PAGES" => $config_map_amount,
      "THEME" => $config_theme,
      "HELPTEXT" => $config_helptext,
      "LANGUAGE" => $config_language,
      "LOCALSERVER" => $config_localserver
    ));
    
    $tpl->parse("USER", "user", false);
    $tpl->parse("GAMESERVER", "gameserver", false);
  
    if(!isset($config_file) || sizeof($config_file) < 1)
      $tpl->parse("CONFIG_FILE", "no_config_file", false);
    else
      while(list($key, $value) = each($config_file))
      {
        $tpl->set_var(array(
          "KEY" => $key,
          "VALUE" => $value
        ));
        $tpl->parse("CONFIG_FILE", "config_file", true);
      } //while(list($key, $value) = each($config_file))
  
    $tpl->parse("OUTPUT", "configuration", false);
    $output = $tpl->get_var("OUTPUT");


    //download
    if(isset($_GET["action"]) && $_GET["action"] == "download")
    {
      header("Content-type: application/txt");
      header("Content-Disposition: attachment; filename=config.php");
      echo($output);
      exit;    
    }

    //display config
    else
    {
      $output = str_replace(array(" ", "<", ">", "\n"), array("&nbsp;", "&lt;", "&gt;", "<br>"), $output);

      $tpl->parse("DESCRIPTION", "description_ok", false);
      $tpl->parse("FEEDBACK", "feedback", false);
      $tpl->set_var("CONFIGURATION", $output, false);
      
    } //else
    
  } //if($error == 0)
  
  else
    $tpl->parse("DESCRIPTION", "description_error", false);

    
  //parse everything and print it
  $tpl->parse("TABPANE", "tabpanedownload");
  $tpl->parse("CONTENT", "content");
  $tpl->parse("OUT", "main");
  $tpl->p("OUT");

?>