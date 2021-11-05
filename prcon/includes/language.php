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


  //Sprachen holen
  $tpl->set_root("./language/$config_language");
  $tpl->set_file(array(
    "helptext" => "helptext.ihtml",
    "messages" => "messages.ihtml"
  ));


  //Allgemeine Variablen setzen
  $tpl->set_var(array(
    "TEMPLATEDIR" => "./themes/$config_theme"
  ));


  //generell
  $tpl->set_block("messages", "generalnotconnected", "DUMMY");
  $tpl->set_block("messages", "generalnotresponding", "DUMMY");
  $tpl->set_block("messages", "generalbadrcon", "DUMMY");
  $tpl->set_block("messages", "generalrefreshserver", "DUMMY");
  $tpl->parse("GENERALNOTCONNECTED", "generalnotconnected", false);
  $tpl->parse("GENERALNOTRESPONDING", "generalnotresponding", false);
  $tpl->parse("GENERALBADRCON", "generalbadrcon", false);
  $tpl->parse("GENERALREFRESHSERVER", "generalrefreshserver", false);

  //hilfe texte
  if($config_helptext == 1)
  {
    $tpl->set_block("helptext", "helpserverinfo", "DUMMY");
    $tpl->set_block("helptext", "helprconcommand", "DUMMY");
    $tpl->set_block("helptext", "helpserverbanlist", "DUMMY");
    $tpl->set_block("helptext", "helpservermaps", "DUMMY");
    $tpl->set_block("helptext", "helpserverrules", "DUMMY");
    $tpl->set_block("helptext", "helpserverconfig", "DUMMY");
    $tpl->set_block("helptext", "helpnews", "DUMMY");
    $tpl->parse("HELPSERVERINFO", "helpserverinfo", false);
    $tpl->parse("HELPRCONCOMMAND", "helprconcommand", false);
    $tpl->parse("HELPSERVERRULES", "helpserverrules", false);
    $tpl->parse("HELPSERVERBANLIST", "helpserverbanlist", false);
    $tpl->parse("HELPSERVERMAPS", "helpservermaps", false);
    $tpl->parse("HELPSERVERCONFIG", "helpserverconfig", false);
    $tpl->parse("HELPNEWS", "helpnews", false);
  }
  else
  {
    $tpl->set_var(array(
      "HELPSERVERINFO" => "",
      "HELPRCONCOMMAND" => "",
      "HELPSERVERRULES" => "",
      "HELPSERVERBANLIST" => "",
      "HELPSERVERMAPS" => "",
      "HELPSERVERCONFIG" => "",
      "HELPNEWS" => ""
    ));
  }


  //Sachen die immer da sind

  //überschriften
  $tpl->set_block("messages", "topicserverinfo", "DUMMY");
  $tpl->set_block("messages", "topicrconcommand", "DUMMY");
  $tpl->set_block("messages", "topicserverrules", "DUMMY");
  $tpl->set_block("messages", "topicserverbanlist", "DUMMY");
  $tpl->set_block("messages", "topicservermaps", "DUMMY");
  $tpl->set_block("messages", "topicserverconfig", "DUMMY");
  $tpl->set_block("messages", "topichelp", "DUMMY");
  $tpl->set_block("messages", "topicnews", "DUMMY");
  $tpl->set_block("messages", "topicpublicinfo", "DUMMY");
  $tpl->set_block("messages", "topicmultiindex", "DUMMY");
  $tpl->parse("TOPICSERVERINFO", "topicserverinfo", false);
  $tpl->parse("TOPICRCONCOMMAND", "topicrconcommand", false);
  $tpl->parse("TOPICSERVERRULES", "topicserverrules", false);
  $tpl->parse("TOPICSERVERBANLIST", "topicserverbanlist", false);
  $tpl->parse("TOPICSERVERMAPS", "topicservermaps", false);
  $tpl->parse("TOPICSERVERCONFIG", "topicserverconfig", false);
  $tpl->parse("TOPICHELP", "topichelp", false);
  $tpl->parse("TOPICNEWS", "topicnews", false);
  $tpl->parse("TOPICPUBLICINFO", "topicpublicinfo", false);
  $tpl->parse("TOPICMULTIINDEX", "topicmultiindex", false);

  //listenüberschriften
  $tpl->set_block("messages", "titlehostname", "DUMMY");
  $tpl->set_block("messages", "titleversion", "DUMMY");
  $tpl->set_block("messages", "titletcpip", "DUMMY");
  $tpl->set_block("messages", "titlemap", "DUMMY");
  $tpl->set_block("messages", "titleplayers", "DUMMY");
  $tpl->set_block("messages", "titlename", "DUMMY");
  $tpl->set_block("messages", "titleid", "DUMMY");
  $tpl->set_block("messages", "titlewonid", "DUMMY");
  $tpl->set_block("messages", "titlefrag", "DUMMY");
  $tpl->set_block("messages", "titletime", "DUMMY");
  $tpl->set_block("messages", "titleping", "DUMMY");
  $tpl->set_block("messages", "titleadress", "DUMMY");
  $tpl->set_block("messages", "titlerule", "DUMMY");
  $tpl->set_block("messages", "titlevalue", "DUMMY");
  $tpl->set_block("messages", "titlebanid", "DUMMY");
  $tpl->set_block("messages", "titletimeframe", "DUMMY");
  $tpl->set_block("messages", "titlekick", "DUMMY");
  $tpl->set_block("messages", "titleunbanid", "DUMMY");
  $tpl->set_block("messages", "titledir", "DUMMY");
  $tpl->set_block("messages", "titleconfigfiles", "DUMMY");
  $tpl->set_block("messages", "titleport", "DUMMY");
  $tpl->set_block("messages", "titlekey", "DUMMY");
  $tpl->set_block("messages", "titleconnectingplayer", "DUMMY");
  $tpl->parse("TITLEHOSTNAME", "titlehostname", false);
  $tpl->parse("TITLEVERSION", "titleversion", false);
  $tpl->parse("TITLETCPIP", "titletcpip", false);
  $tpl->parse("TITLEMAP", "titlemap", false);
  $tpl->parse("TITLEPLAYERS", "titleplayers", false);
  $tpl->parse("TITLENAME", "titlename", false);
  $tpl->parse("TITLEID", "titleid", false);
  $tpl->parse("TITLEWONID", "titlewonid", false);
  $tpl->parse("TITLEFRAG", "titlefrag", false);
  $tpl->parse("TITLETIME", "titletime", false);
  $tpl->parse("TITLEPING", "titleping", false);
  $tpl->parse("TITLEADRESS", "titleadress", false);
  $tpl->parse("TITLERULE", "titlerule", false);
  $tpl->parse("TITLEVALUE", "titlevalue", false);
  $tpl->parse("TITLEBANID", "titlebanid", false);
  $tpl->parse("TITLETIMEFRAME", "titletimeframe", false);
  $tpl->parse("TITLEKICK", "titlekick", false);
  $tpl->parse("TITLEUNBANID", "titleunbanid", false);
  $tpl->parse("TITLEDIR", "titledir", false);
  $tpl->parse("TITLECONFIGFILES", "titleconfigfiles", false);
  $tpl->parse("TITLEPORT", "titleport", false);
  $tpl->parse("TITLEKEY", "titlekey", false);
  $tpl->parse("TITLECONNECTINGPLAYER", "titleconnectingplayer", false);

  //buttons
  $tpl->set_block("messages", "buttonsubmit", "DUMMY");
  $tpl->set_block("messages", "buttonclearoutput", "DUMMY");
  $tpl->set_block("messages", "buttonbanid", "DUMMY");
  $tpl->set_block("messages", "buttonunbanid", "DUMMY");
  $tpl->set_block("messages", "buttonsave", "DUMMY");
  $tpl->set_block("messages", "buttonreset", "DUMMY");
  $tpl->set_block("messages", "buttonlogin", "DUMMY");
  $tpl->parse("BUTTONSUBMIT", "buttonsubmit", false);
  $tpl->parse("BUTTONCLEAROUTPUT", "buttonclearoutput", false);
  $tpl->parse("BUTTONBANID", "buttonbanid", false);
  $tpl->parse("BUTTONUNBANID", "buttonunbanid", false);
  $tpl->parse("BUTTONSAVE", "buttonsave", false);
  $tpl->parse("BUTTONRESET", "buttonreset", false);
  $tpl->parse("BUTTONLOGIN", "buttonlogin", false);


  //login
  $tpl->set_block("messages", "loginusername", "DUMMY");
  $tpl->set_block("messages", "loginpwd", "DUMMY");
  $tpl->parse("LOGINUSERNAME", "loginusername", false);
  $tpl->parse("LOGINPWD", "loginpwd", false);

  //server info
  $tpl->set_block("messages", "serverinfocommanddescr", "DUMMY");
  $tpl->parse("SERVERINFOCOMMANDDESCR", "serverinfocommanddescr", false);

  //rcon Command
  $tpl->set_block("messages", "rconcommanddescr", "DUMMY");
  $tpl->parse("RCONCOMMANDDESCR", "rconcommanddescr", false);

  //server rules
  $tpl->set_block("messages", "serverruleschange", "DUMMY");
  $tpl->parse("SERVERRULESCHANGE", "serverruleschange", false);

  //server banlist
  $tpl->set_block("messages", "serverbanlistpermanent", "DUMMY");
  $tpl->parse("SERVERBANLISTPERMANENT", "serverbanlistpermanent", false);

  //servermaps
  $tpl->set_block("messages", "servermapschangedescr", "DUMMY");
  $tpl->parse("SERVERMAPSCHANGEDESCR", "servermapschangedescr", false);

  //serverconfig
  $tpl->set_block("messages", "serverconfigstatus", "DUMMY");
  $tpl->set_block("messages", "serverconfigeditdescr", "DUMMY");
  $tpl->set_block("messages", "serverconfigeditdescr2", "DUMMY");
  $tpl->parse("SERVERCONFIGSTATUS", "serverconfigstatus", false);
  $tpl->parse("SERVERCONFIGEDITDESCR", "serverconfigeditdescr", false);
  $tpl->parse("SERVERCONFIGEDITDESCR2", "serverconfigeditdescr2", false);

  //help
  $tpl->set_block("messages", "helplink2page", "DUMMY");
  $tpl->set_block("messages", "helpnoiframe", "DUMMY");
  $tpl->set_block("messages", "titletopic", "DUMMY");
  $tpl->parse("HELPLINK2PAGE", "helplink2page", false);
  $tpl->parse("HELPNOIFRAME", "helpnoiframe", false);
  $tpl->parse("TITLETOPIC", "titletopic", false);



  //Messages

  //login
  $tpl->set_block("messages", "loginerror", "DUMMY");

  //rcon Command
  $tpl->set_block("messages", "rconcommandnotexecuted", "DUMMY");

  //server banlist
  $tpl->set_block("messages", "serverbanlistban", "DUMMY");
  $tpl->set_block("messages", "serverbanlistnoban", "DUMMY");
  $tpl->set_block("messages", "serverbanlistunban", "DUMMY");
  $tpl->set_block("messages", "serverbanlistnounban", "DUMMY");

  //servermaps
  $tpl->set_block("messages", "servermapschange", "DUMMY");
  $tpl->set_block("messages", "servermapsnochange", "DUMMY");

  //serverconfig
  $tpl->set_block("messages", "serverconfignolocalserver", "DUMMY");
  $tpl->set_block("messages", "serverconfigok", "DUMMY");
  $tpl->set_block("messages", "serverconfigfilesaved", "DUMMY");
  $tpl->set_block("messages", "serverconfignopermission", "DUMMY");
  $tpl->set_block("messages", "serverconfignofileexist", "DUMMY");

?>