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
    "content" => "install_server_details.ihtml"
  ));

  //Set Blocks
  $tpl->set_block("main", "message", "MESSAGE");
  $tpl->set_block("main", "error", "ERROR");
  $tpl->set_block("tabpane", "tabpaneserverdetails", "DUMMY");
  $tpl->set_block("content", "configfile", "CONFIGFILE");
  $tpl->set_block("content", "configfilelist", "CONFIGFILELIST");

  //general sets an parses
  include("./includes/template_globals.php");

  //Set Variables
  $tpl->set_var(array(
    "ERROR" => "",
    "DUMMY" => "",
    "CONFIGFILELIST" => "",
    "CONFIGFILE" => ""
  ));


  //localserver
  if($config_localserver == 1)
    $tpl->set_var(array(
      "LOCALSERVERYES" => "checked",
      "LOCALSERVERNO" => ""
    ));
  else
    $tpl->set_var(array(
      "LOCALSERVERYES" => "",
      "LOCALSERVERNO" => "checked"
    ));


  //output list
  if(isset($config_file) && sizeof($config_file) > 0)
  {
    while(list($key, $value) = each ($config_file))
    {
      $tpl->set_var(array(
        "NUMBER" => $key,
        "PATH" => $value
      ));
      $tpl->parse("CONFIGFILE", "configfile", true);
    } //while($list($key, $value) each $config_file)
    
    $tpl->parse("CONFIGFILELIST", "configfilelist", false);
  } //if(sizeof($config_file) > 0)


  //parse everything and print it
  $tpl->parse("TABPANE", "tabpaneserverdetails");
  $tpl->parse("CONTENT", "content");
  $tpl->parse("OUT", "main");
  $tpl->p("OUT");

?>