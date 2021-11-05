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

//Start session
session_start();

//Is this a new login
if (isset($_POST["name"]) && isset($_POST["password"]))
{

  //Has the correct name and password been sent
  if($_POST["name"] == $config_user_name && $_POST["password"] == $config_user_password)
  {
    $_SESSION["name"] = $_POST["name"];
    $_SESSION["password"] = $_POST["password"];
    $_SESSION["time"] = time();
  }

  //No correct name and password
  else
  {
    Authenticate($_POST["name"], $_POST["password"], true);
    exit;
  }
}

//Is this an existing login
else if (isset($_SESSION['name']) && isset($_SESSION['password']))
{

  //Have the Session variables the correct name and password and has the last page call happened within the last 10 minutes
  if($_SESSION["name"] == $config_user_name && $_SESSION["password"] == $config_user_password && (time() - $_SESSION["time"]) < 3600)
    $_SESSION["time"] = time();

  //No correct Session variables
  else
  {
    Authenticate();
    exit;
  }
}

//Any other case
else
{
  Authenticate();
  exit;
}


function Authenticate($name = "", $password = "", $error = false)
{

  //Alte Daten loeschen
  $_SESSION["name"] = "";
  $_SESSION["password"] = "";
  $_SESSION["time"] = 0;

  //configs holen
  include("./config/config.php");

  //neues Template erzeugen
  $tpl = new Template("./themes/$config_theme", "keep");

  //Files bekannt machen
  $tpl->set_file(array(
    "main" => "main.ihtml",
    "login" => "login.ihtml",
  ));

  //Sprachblöcke setzen
  include("./includes/language.php");

  //Variablen setzen
  $tpl->set_var(array(
    "LOGIN_CURRENTURL" => $_SERVER["SCRIPT_NAME"],
    "LOGIN_NAME" => $name,
    "LOGIN_PASSWORD" => $password,
    "FEEDBACK" => ""
  ));

  //Sachen einparsen
  if($error)
    $tpl->parse("FEEDBACK", "loginerror", false);

  //alles raus parsen
  $tpl->parse("CONTENT", "login", false);
  $tpl->parse("OUT", "main", false);
  $tpl->p("OUT");

  //CU l8er
  exit;
}

?> 