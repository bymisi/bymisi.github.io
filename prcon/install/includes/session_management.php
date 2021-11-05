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

session_start();

//Get settings custom or default
$config = "../config/config.php";
if(file_exists($config))
  include($config);
else
{
  $config_user_name = "rcon";
  $config_user_password = "";
  $config_theme = "basic";
  $config_helptext = 1;
  $config_language = "english";
  $config_server_ip = "your.serverip.here";
  $config_server_port = "27015";
  $config_server_password = "";
  $config_map_amount = 1;
  $config_localserver = 0;
  $config_file = array();
}


//set session variables

//user_name
if(isset($_POST["config_user_name"]))
  $_SESSION["config_user_name"] = $_POST["config_user_name"];
else if(!isset($_SESSION["config_user_name"]))
  $_SESSION["config_user_name"] = $config_user_name;
$config_user_name = $_SESSION["config_user_name"];


//user_password
if(isset($_POST["config_user_password"]))
  $_SESSION["config_user_password"] = $_POST["config_user_password"];
else if(!isset($_SESSION["config_user_password"]))
  $_SESSION["config_user_password"] = "";
$config_user_password = $_SESSION["config_user_password"];


//user_password_confirm
if(isset($_POST["config_user_password_confirm"]))
  $_SESSION["config_user_password_confirm"] = $_POST["config_user_password_confirm"];
else if(!isset($_SESSION["config_user_password_confirm"]))
  $_SESSION["config_user_password_confirm"] = "";
$config_user_password_confirm = $_SESSION["config_user_password_confirm"];


//theme
if(isset($_POST["config_theme"]))
  $_SESSION["config_theme"] = $_POST["config_theme"];
else if(!isset($_SESSION["config_theme"]))
  $_SESSION["config_theme"] = $config_theme;
$config_theme = $_SESSION["config_theme"];


//helptext
if(isset($_POST["config_helptext"]))
  $_SESSION["config_helptext"] = $_POST["config_helptext"];
else if(!isset($_SESSION["config_helptext"]))
  $_SESSION["config_helptext"] = $config_helptext;
$config_helptext = $_SESSION["config_helptext"];


//language
if(isset($_POST["config_language"]))
  $_SESSION["config_language"] = $_POST["config_language"];
else if(!isset($_SESSION["config_language"]))
  $_SESSION["config_language"] = $config_language;
$config_language = $_SESSION["config_language"];


//server_ip
if(isset($_POST["config_server_ip"]))
  $_SESSION["config_server_ip"] = $_POST["config_server_ip"];
else if(!isset($_SESSION["config_server_ip"]))
  $_SESSION["config_server_ip"] = $config_server_ip;
$config_server_ip = $_SESSION["config_server_ip"];


//server_port
if(isset($_POST["config_server_port"]))
  $_SESSION["config_server_port"] = $_POST["config_server_port"];
else if(!isset($_SESSION["config_server_port"]))
  $_SESSION["config_server_port"] = $config_server_port;
$config_server_port = $_SESSION["config_server_port"];


//server_password
if(isset($_POST["config_server_password"]))
  $_SESSION["config_server_password"] = $_POST["config_server_password"];
else if(!isset($_SESSION["config_server_password"]))
  $_SESSION["config_server_password"] = "";
$config_server_password = $_SESSION["config_server_password"];


//map_amount
if(isset($_POST["config_map_amount"]))
  $_SESSION["config_map_amount"] = $_POST["config_map_amount"];
else if(!isset($_SESSION["config_map_amount"]))
  $_SESSION["config_map_amount"] = $config_map_amount;
$config_map_amount = $_SESSION["config_map_amount"];


//localserver
if(isset($_POST["config_localserver"]))
  $_SESSION["config_localserver"] = ($_POST["config_localserver"] == 1)?1:0;
else if(!isset($_SESSION["config_localserver"]))
  $_SESSION["config_localserver"] = $config_localserver;
$config_localserver = $_SESSION["config_localserver"];


//config_file
if(isset($_POST["config_file_new"]) && isset($_GET["action"]))
{
  if($_GET["action"] == "addconfig")
  {
    if(isset($_SESSION["config_file"]))
    {
      $config_file = unserialize($_SESSION["config_file"]);
      $config_file[sizeof($config_file)] = $_POST["config_file_new"];
    }
    else
      $config_file[0] = $_POST["config_file_new"];

    $_SESSION["config_file"] = serialize($config_file);
  }
  else if($_GET["action"] == "removeconfig")
  {
    $config_file = unserialize($_SESSION["config_file"]);
    $size = sizeof($config_file) - 1;
    for($i = $_GET["key"]; $i < $size; $i++)
    {
      $config_file[$i] = $config_file[$i + 1];
    }
    unset($config_file[$size]);
    $_SESSION["config_file"] = serialize($config_file);
  }
}
else if(!isset($_SESSION["config_file"]))
{
  if(isset($config_file))
    $_SESSION["config_file"] = serialize($config_file);
}
else if(isset($_SESSION["config_file"]))
  $config_file = unserialize($_SESSION["config_file"]);

?>