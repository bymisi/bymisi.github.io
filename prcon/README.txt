PHPrcon V0.6.3 for Halflife Server
--------------------------------

PHPrcon is a collection of PHP scripts that makes you able to remotely administrate a Halflife or HalflifeMod Server through a webinterface. This little project has its origin in a project I had to do for my studies at the University of Paderborn. The original project was bigger and more comfortabel, with a complete commando list, multiuser and multiserver management. I decided to derive a smaller version of this project, so that you just have to unpack a bunch of scripts, do a little configuration and give it a GO.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License (LGPL) as published by the Free Software Foundation. (http://www.gnu.org/copyleft/lesser.html). A copy of the LGPL licence is part of the PHPrcon zip.


Systemrequirements
------------------

Server side:
- Any Webserver (PHP as pure CGI may cause problems)
- PHP V4.2.0 >=
(It has been developed and tested with Xitami V3.2.2 and PHP V4.2.3 as webserver module)

Client side:
- Any Webbrowser


Installation Instructions
-------------------------

Unpack all files in a directory on your Webserver. Create a link on your Website to "rcon_index.php" to use the rcon part or to "info_index.php" to use the public part of PHPrcon. To configure PHPrcon open "./install/install.php" in your webbrowser. If you want to do configuration stuff by yourself modify "./config.php" in your main PHPrcon folder to fit your Game- and Webserver. The use of "./install/install.php" is recommended.

config.php:

<?php 
  //User 
  //Name 
  $config_user_name = "rcon"; 
  //Password 
  $config_user_password = "rcon"; 
   
   
  //Gameserver 
  //IP or Domain Name 
  $config_server_ip = "gameserver.ip.here"; 
  //Port 
  $config_server_port = "27015"; 
  //rcon password 
  $config_server_password = "password"; 
  //Number of pages of maps that are sent from the server 
  $config_map_amount = 1; 
   
   
  //relative path to the selected theme, from PHPrcon root dir 
  $config_theme = "basic"; 

  //1 = a small helptext will be displayed on top of every page; 0 = no helptext 
  $config_helptext = 1; 

  //language of PHPrcon 
  $config_language = "english"; 

  //1 = PHPrcon is running on the same server as your Gameserver, 0 = PHPrcon is running anywhere 
  $config_localserver = 1; 

  //List of Config Files 
  //absolute path to the xxxx.cfg that should be editable in PHPrcon (if $localserver = 0 this has no effect at all) 
  $config_file[1] = "/usr/local/hl/server.cfg";
  $config_file[2] = "/usr/local/hl/config.cfg";
   
?> 


- $config_user_name and $config_user_pwd are the authentication information PHPrcon askes from, when your are logging in.

- $config_server_ip, $config_server_port and $config_server_password specify your Halflifeserver. $config_server_ip may be a pure IP or a domainname. See "Troubleshooting" for information.

- $config_map_amount is the number of pages of maps that are sent from the gameserver

- $config_theme specifies the theme you want to use. You may build your own theme. For instructions how to build your own themes see http://www.phprcon.net templates section

- $config_language specifies the language of PHPrcon. For a list of available languages see http://www.phprcon.net download section

- $config_helptext = 1 displayes a small helptext for every page of PHPrcon. Set this to 0 and no helptext will be displayed.

- if $config_localserver is set to 1, you may edit config files from your gameserver. The config files are set in $config_file[1], $config_file[2] .... increase the number in $config_file[ ] by 1 each time you add a new file. Use only absolute paths. If you want to disable this feature set $config_localserver to 0.


Troubleshooting
---------------

- If your IP is valid, but there is no Halflife Server listening on your PORT, PHPrcon will not be able to cast a proper error message. A fatal error will be displayed after the "max_execution_time". The "max_execution_time" is specified in your Webserver config.

- If $config_map_amount is greater than the number of pages being sent from the gameserver, your browser will not stop loading the page until the PHP max_execution_time exceeded.

- If your webserver has PHP installed as CGI, you will not be able to download the "config.php". Please Copy&Paste the displayed "config.php" into a "config.php" in the "./config/" folder, where "." is your PHPrcon root directory.


Contact
-------

Henrik Beige
henrik.beige@phprcon.net
http://www.phprcon.net


Languages
---------

PHPrcon is available in several languages. This is only possible by the help of some guys who offered me there help in translating the project. With great thanks and best regards ...

  · Eriksen, Robert		Norwegian translation
  · Haggren, Thomas		Danish translation
  · Jonas aka Oakim		Swedish translation
  · Kapi			French translation
  · Kosojevic, Radomir		Serbian translation
  · de Lera, Fernando		Spanish translation
  · Peng, Sun			Simplified Chinese translation
  · Pires, Fabiano "peixe" 	Portuguese (Brazilian) translation
  · Shacall			Polish translation
  · Wilco van Duinkerken	Dutch translation
  · Woudenberg, Sebastiaan	Dutch translation
  · [;-(] COOL_ZERO		Russian translation


Change Log
----------
V0.6.3
- New install script
- News page added. News are provided by http://www.phprcon.net
- config_map_amount added for more than 62 maps on the gameserver
- Login changed from HTTP Authentication to Session based solution
- "HOSTNAME", "MAP", "TCPIP" and "PLAYERS" template variables available on all sites
- ServerRules are displayed in alpha/numerical order
- Internal changes for PHP4.2.3 compatibility
- Fixed "Server Config" Save bugs. Now configs should not be rewritten to 0 byte
- Multipackage receipt from HL or HLMod servers fixed
- Themes "basic2" and "paradroid" removed from PHPRcon
- Language independeny fixed
- Several new languages
  · Chinese, Simplified
  · Danish
  · Dutch
  · French
  · Norwegian
  · Polish
  · Portuguese (Brazilian)
  · Russian
  · Serbian
  · Spanish
  · Swedish

V0.6
- Fixed "Server Map" Bug. Now odd looking directory names should be no problem any more
- "Kick" / "Ban" Quickcommand at "Server Info" set to proper command
- Multilanguage support. English and German as language available
- Additional setting if PHPrcon is running on same machine as the Gameserver. Config files may be edited through webinterface
- PHP install script, with which PHPrcon can be configured easily by webinterface as well
- Improved "Server Banlist" usability
- Improved "Server Map" appearance
- Improved usability of rcon_net, due to new intern structure

V0.5
- Serverinfo
- Information about all banned players. Easy banning and ban remoiving
- Information about all maps on the gameserver and easy change of the current map
- Information and modification of server variables
- Small help section