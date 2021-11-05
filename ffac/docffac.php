<div id="post_message_432680" style="width: 100%; display: block;">
                				<b><u><font size="4"><font color="Red">FFAC/Tools Features !</font></font></u></b><br />
<font size="3"><font color="Orange"><b><br />
Statisticals part<br />
<br />

</b></font></font><font color="Olive"><font size="2"><u><b>Players stats<br />
Team stats<br />
Server stats<br />
Ingame stats, website<br />
Death,kill,spawn, .. counter, rank by csstats,skill, ...<br />
Other misc function to display stats ( like display skill on aim ! )<br />
<br />
</b></u></font></font><font size="3"><font color="Orange"><b> Tools<br />
<br />

</b></font></font><font color="Olive"><font size="2"><u><b>MSN Bot ( a player on the server can contact several admin on msn ! )<br />
</b></u></font></font><font color="Olive"><font size="2"><u><b>Banlist ( about 12000 cheater banned ... )<br />
</b></u></font></font><font size="3"><font color="Orange"><b><u><font size="2"><font color="Olive"><font color="Olive">Package manager ( install plugin like amxmatchdeluxe with one command line, without ftp access, ... )<br />
</font></font></font></u></b></font></font><font color="Olive"><font size="2"><u><b>Auto update ( The plugin automaticaly download and install the lastest version ! )<br />
Map Download ( You wan some new map on your server, there is a function to download map from ffac server to your server ! )<br />
</b></u></font></font><br />
<br />
<!-- Code block -->
<div style="margin:20px; margin-top:5px">
	<div class="smallfont" style="margin-bottom:2px">Code:</div>

	<pre class="alt2" dir="ltr" style="margin: 0px;padding: 6px;border: 1px inset;width: 640px; height: 499px; text-align: left; overflow: auto">/*********************************************************************************************
*                        FFAC server side plugin v1.3
*
**********************************************************************************************
/*********************************************************************************************
*                        FFAC server side plugin v1.4
**********************************************************************************************
FFAChmp V1.4
hackziner : hackziner@gmail.com
FFAC Website : http://82.232.102.55/FFAC


Licence : CeCILL FREE SOFTWARE LICENSE AGREEMENT, there is a copy of the licence at the end of this file.

Use :
 Only run the plugin on your server. After the first player connection the server will be added to the server list
 No subscription require !

     Features, ... :

    Global players stats  ( based on skill ) !
    FFA Team rank ( create a team with ffa player )
    Global CStats like rank ( based on Kill - Death ... )
    Servers stats ( graphs with connections by hours/day, total frag, ... )
    Show current skill,rank on a server
    Show top players of the serveur in motd
    Show your stats in motd
    Global Banlist
    Sentence log
    User can contact an admin on MSN through server/ffacmsnbot
    Map download from FFAC server 
    Package Manager 
    Package choice menu 
    Autoupdate
    Show if an admin is online, and explain how to contact througth msn if nobody is online
    No subscription require !

More informations, support, improvements, .... :
http://ufb.free.fr or http://82.232.102.55/FFAC/forum

    Cvar:
    ffac_showinfo ( default 1 ) : Show information ingame, like player skill ... ( commands are skill,ffac,score,allskill,ffac version )
    ffac_hudinfo ( default 0 ) : Show informations ( like skill ... ) in a hud message
    ffac_showpub ( default 1 ) : Show a little pub message for ffac every 180 secs
    ffac_ffacban ( default 1 ) : Use the FFAC banned steamid database
    ffac_skill_on_aim ( default 0 ) : show player level on aim ( eg Low/Middle/High )
    ffac_ban ( default 30 ) : Duration of a FFACBan in Min
    ffac_plog ( default 1 ) : Log sentence function ( when you say something with &quot;log&quot; inside, the sentence is logged and you can read it on the ffac website )
    ffac_msn_contact ( default &quot;&quot; ) : Set the msn adress for the ffac msn bot !
    ffac_ip_bind ( default &quot;&quot; ) : Set your public ip ( if you server is behind someting ... )
    ffac_autoupdate ( default 1 ) : enable autoupdate
    ffac_hpt ( default 1 ) : enable the packet manager
    
    Technicals Cvar :
    ffac_packetsize ( default 1024 ) :  Size of packets
    ffac_master_server ( default &quot;82.232.102.55&quot; ) : FFAC master server address
    
    admin commands :
    
    amx_ffac_ban &lt;user id or steam id&gt; : ban a player for amx_ffac_ffacban minuts, and repport the ban to the ffac server
    amx_ffac_map_download &lt;map&gt; : download a map from the ffac server on your server ! 
    amx_ffac_map_list : show the list of downloable maps
    amx_ffac_hpt_install &lt;package&gt; : Install a package on your server !
    amx_ffac_hpt_remove &lt;package&gt; : Remove a package 
    amx_ffac_hpt_menu : Show a menu with all packages available
    
    Players commands : ffac version,ffac changelog,skill,rank,topplayers,mystats,serverconnections,!log &lt;sentence&gt;,admin,!admin &lt;message&gt;,set nickname

    ffac version : display the ffac version
    ffac changelog : display ffac changelog
    serverconnections : show server connections stats in motd ( day &amp; month )
    skill : show your skill
    /me : display your level
    admin : show number of admin online. If 0 and msn_contact set, explain the !admin command
    topplayers : show kill,deaths ... of all players in game
    mystats : show your stats ( kill, death, connections, online time, ... )
    rank : show your rank by skill and csstats ( global and server rank )
    !admin &lt;message&gt; : send a message to the msn contact ( on msn ^^ ) by the ffac msn bot
    !log &lt;sentence&gt; : Log a sentence ( you can view all your logged sentences on the ffac website )
    
    
    FFACBan :
    Ban player banned in the FFAC Database for 30 minute ! Not unlimited ban !
    
    
    Package manager syntax : 
    
    
    [Version]  // Check the require amx version 
    1.7
    [Download] // Download a file
    configs/hpt/remove_smallfun.hpt
    [Install] // Download and install a plugin
    plugins/spawnprotection.amxx
    spawnprotection.amxx

    
    Package : 
    smallfun ( amx_ffac_hpt_install smallfun ) ( this package contains, spawnprotection, bunnyhop and lastmanbets )
    sanksound ( amx_ffac_hpt_install sanksound ) ( this package contains sanksound plugin with sounds )
    matchdeluxe ( amx_ffac_hpt_install matchdeluxe ) ( this package contains amx_match_deluxe )
    ...

    ToDo: 
    -stats improvement
    -few commands like online time, etcs
    -msn to server messages
    -autoupdate for all plugins
    -trombi/annuaire
    ....
    
    Changelog:
    
    [21/4/2007] 1.4
    -allow multiple msn in msn_contact ( use  : ffac_msn_contact &quot;msn@msn.com;msn2@msn.com&quot; )
    -Extend max msn_contact size from 30 to 128 chars
    -Fix command display bug
    -Replace valve id lan or steam id lan by ip in the msn contact message
    -new natives for db_sys
    -indentation problems fix
    -multi language support
    -auto download language file if not found !
    -integration of the package manager in the amxmodmenu !
    -new say command : /me to display your level
    
    
    
    [21/4/2007] 1.3
    -amx_ffac_hpt_menu //Menu for install new plugin, autodownload the plugin list, ...
    -new comands to show server connections stats, say &quot;serverconnections&quot;

    -new commands to show changelog, say &quot;ffac changelog&quot;
    -new function to display player level on aim : set ffac_skill_on_aim 1
    -Some fix with the proxy

    [7/4/2007]    1.2
    -security fix ( package manager )
    -dynamic master server
    -new package matchdeluxe
    -now package manager and autoupdate are activate by default
    -spawn count fixed
    

*********************************************************************************************/
    

*********************************************************************************************/</pre>
</div>
<!-- /Code block --> The purpose of the plugin is to provided a kind of &quot;global&quot; ranking and admins tools on several server at the same time.<br />
<br />
For example you go on a server A, you get a skill of 54000. You leave the server and go on an other one. Your skill will load on the other server, ...<br />

<br />
<b> <br />
<u><font size="4"><font color="Red">FFAC/Tools</font></font></u></b><br />
<font size="3"><font color="Orange"><b><br />
Statisticals part<br />
<br />
</b></font></font><font color="Olive"><font size="2"><u><b>Players stats<br />
</b></u></font></font><br />
<b>Description:<br />
</b>Players stats are available ingame and on the ffac website. FFAC provides several player stats :<ul><li>Connection count</li><li>Online Time</li><li>Kill</li><li>Death</li><li>HS</li><li>HS taken</li><li>Spawns</li><li>Server rank</li><li>FFAC rank</li><li>Skill</li><li>Country</li></ul><font color="Olive"><font size="2"><b><font color="Black">Commands:<br />

</font></b></font></font><ul><li>skill (say ): show your skill</li><li>mystats ( say ): display your full stats in motd</li><li>/me ( say ) : display your level in chat (low/middle/... )</li><li>rank ( say ) : display your rank on the server and on all server ( by skill and csstats )</li></ul><font color="Olive"><font size="2"><b><font color="Black"><img src="http://82.232.102.55/public/images/divers/skill.JPG" border="0" alt="" /><br />
<br />
<br />
</font></b><u><b><br />
Team stats<br />
<br />
</b></u></font></font><b>Description:<br />
</b>Team stats are only available on the FFAC Website for now ( <a href="http://82.232.102.55/FFAC" target="_blank">http://82.232.102.55/FFAC</a> ). Team are ranked by skill only.<br />

<br />
<b>FAQ :<br />
</b>Join a team ?<br />
Play on a server with the FFAC plugin, register on the FFAC website ( <a href="http://82.232.102.55/FFAC" target="_blank">http://82.232.102.55/FFAC</a> ), and go to the team list.<br />
<font color="Olive"><font size="2"><u><b><br />
Server stats<br />
<br />
</b></u></font></font><b>Description:<br />
</b>FFAC provides server connections stats by hours and day. Stats are displayed on the FFAC website and in motd<br />

<br />
<font color="Olive"><font size="2"><b><font color="Black">Commands:<br />
</font></b></font></font><ul><li>Server connections ( say ) : display connections of today by hours and connections of this month</li><li> topplayers ( say ) : <a href="http://forums.alliedmods.net/attachment.php?attachmentid=13799&amp;stc=1&amp;d=1178033838" target="_blank">display a top15 player like /top15 command with more informations/stats</a></li></ul><font color="Olive"><font size="2"><b><font color="Black">CVARS:<br />
</font></b></font></font><ul><li><font color="Olive"><font size="2"><font color="Black"> ffac_showinfo (default 1) : Show information ingame, like player skill,... <br />
</font></font></font></li><li><font color="Olive"><font size="2"><font color="Black">ffac_hudinfo (default 0) : Show informations (like skill) in hud message</font></font></font></li><li><font color="Olive"><font size="2"><font color="Black">ffac_showpub (default 1) : Show a little ads message with commands for ffac every 180 secs</font></font></font></li></ul><img src="http://82.232.102.55/public/images/divers/igstats.JPG" border="0" alt="" /><br />
<font color="Olive"><font size="2"><u><b><img src="http://82.232.102.55/public/images/divers/topplayers.JPG" border="0" alt="" /><br />
Misc<br />

<br />
</b></u></font></font><b>Level on aim : </b>display player level on aim ( &quot;low&quot;, &quot;middle&quot;, ... )<br />
<b>Sentence log : </b>log sentences on the FFAC website<br />
<font color="Olive"><font size="2"><b><font color="Black">Commands:<br />
</font></b></font></font><ul><li>ffac version ( say ) : displays FFAC version</li><li>ffac changelog ( say ) : displays the ffac changelog</li><li>admin (say ) : displays how many admin are online</li><li>!log &lt;sentence&gt; ( say ) : log a sentence on the ffac website, you can see all your logged sentences on the ffac website</li></ul><font color="Olive"><font size="2"><b><font color="Black">CVARS:<br />

</font></b></font></font><ul><li><font color="Olive"><font size="2"><font color="Black">ffac_plog (default 1) : Enable log sentence</font></font></font></li><li><font color="Olive"><font size="2"><font color="Black">ffac_skill_on_aim (default 0) : show player level on aim <br />
</font></font></font></li><li><font color="Olive"><font size="2"><font color="Black">ffac_ip_bind (default &quot;&quot;) : Set your public ip ( if you server is behind something ... )<br />
</font></font></font></li></ul><font size="3"><font color="Orange"><b><br />
<br />
Tools<br />
<br />
</b></font></font><font color="Olive"><font size="2"><u><b>MSN Bot<br />
<br />
</b></u></font></font><b>Description:</b><br />
This tool allows someone on your server to send a message to an admin on MSN<br />

<br />
<b>Sample:<br />
</b>Your are playing on a server, there is a cheater. You type &quot;!admin CheaterPlayer is cheating&quot;<br />
The admin will receive on msn something like :<br />
<i><font color="#000000"><font face="Arial"><i>!admin from  '-=UFB=-hackziner' (STEAM_0:0:186639) say : </i></font></font>!admin CheaterPlayer is cheating<br />
<br />
<img src="http://82.232.102.55/public/images/divers/msnbot.JPG" border="0" alt="" /><br />
<br />
</i><font color="Olive"><font size="2"><b><font color="Black">Command:</font></b></font></font><ul><li>!admin &lt;message&gt; : send the &lt;message&gt; to server msn contact with the ffac msn bot</li></ul><font color="Olive"><font size="2"><b><font color="Black">CVARS:<br />

</font></b></font></font><ul><li><font color="Olive"><font size="2"><font color="Black">ffac_msn_contact (default &quot;&quot;) : Set msn adress for the ffac msn bot ! ( you can set severals address with ';' . Like &quot;msn@msn.com;msn2@hotmail.com&quot; )<br />
</font></font></font></li></ul><font color="Olive"><font size="2"><b><font color="Black"><br />
</font></b></font></font><font color="Olive"><font size="2"><u><b>Banlist<br />
<br />
</b></u></font></font><b>Description:</b><br />
<font color="Olive"><font size="2"><font color="black">FFAC provides a global banlist like steambans or radars. <br />
<br />
</font></font></font><font color="Olive"><font size="2"><font color="Black"><b>CVARS:</b><br />

</font></font></font><ul><li><font color="Olive"><font size="2"><font color="Black">ffac_ffacban (default 1) : Use the FFAC banned steamid database</font></font></font></li><li><font color="Olive"><font size="2"><font color="Black">ffac_ban (default 30) : Duration of a FFACBan in Min</font></font></font></li></ul><font color="Olive"><font size="2"><font color="black"><br />
</font></font></font> <font size="3"><font color="Orange"><b> <u><font size="2"><font color="Olive"><font color="Olive">Package manager</font><br />
</font></font></u><font size="2"><font color="black"><br />
Description:<br />
<br />
</font></font></b><font size="2"><font color="black">FFAC include a package manager like apt/dpkg on debian ... So a package is pack with several files and installation instruction. The package manager will download and install every files from FFAC server and configures everything automaticaly. For now there are about 10 packages. <br />
<br />
new(v1.4) : You can access to the hpt_menu with the amxmodmenu command !<br />
<br />

<b>Sample: </b><br />
You want to install amxmatchdeluxe for your server.<br />
You have two solution :<br />
</font></font></font></font><ul><li><font size="3"><font color="Orange"><font size="2"><font color="black">in console : &quot;amx_ffac_hpt_install matchdeluxe&quot;</font></font></font></font></li><li><font size="3"><font color="Orange"><font size="2"><font color="black">in console : &quot;amx_ffac_hpt_menu&quot; and select &quot;amx matchdeluxe&quot;</font></font></font></font></li></ul><font size="3"><font color="Orange"><font size="2"><font color="black">The package manager will download all plugin file from FFAC server and add all config automaticaly ( With one line you can install a plugin ! )<br />
<br />
<img src="http://82.232.102.55/public/images/divers/ighptmenu.JPG" border="0" alt="" /><br />

</font></font><b><u><font size="2"><font color="Olive"> <br />
</font></font></u></b></font></font><b>Available packages<br />
</b>    smallfun  ( this package contains, spawnprotection, bunnyhop and lastmanbets )<br />
    sanksound  ( this package contains sanksound plugin with sounds )<br />
    matchdeluxe ( this package contains amx_match_deluxe )<br />
more and more ... use amx_ffac_hpt_menu to display all packages<br />
<br />
<font color="Olive"><font size="2"><b><font color="Black">CVARS:<br />

</font></b></font></font><ul><li><font color="Olive"><font size="2"><font color="Black">ffac_hpt (default 1) : Enable the packet manager</font></font></font></li></ul><b>Admins commands:<br />
</b><ul><li>amx_ffac_hpt_install &lt;package&gt; : Install a package on your server!</li><li>amx_ffac_hpt_remove &lt;package&gt; : Remove a package</li><li>amx_ffac_hpt_menu : Show a menu with all packages available</li></ul><font color="Olive"><font size="2"><u><b>Map download system<br />
<br />
</b></u></font></font><font size="3"><font color="Orange"><b><font size="2"><font color="black"> Description:<br />

</font></font></b><font size="2"><font color="black">Allow to download map from FFAC server to your server.<br />
<br />
</font></font></font></font><b>Sample:<br />
</b>You don't have fy_pool_day, your admin don't have a ftp access to the server and he wants put fy_pool_day.<br />
<br />
He can download with a command on the server :<br />
amx_ffac_map_download fy_pool_day<br />
The server'll download the map and he will be able to changemap to fy_pool_day <img src="images/smilies/smile.gif" border="0" alt="" title="Smile" class="inlineimg" /><br />
<br />
<img src="http://82.232.102.55/public/images/divers/mapdownload.JPG" border="0" alt="" /><br />
<br />

<b>Admins commands:<br />
</b><ul><li>amx_ffac_map_download &lt;map&gt; : download a map from the ffac server on your server !</li><li>amx_ffac_map_list : show the list of downloable maps</li></ul><font size="3"><font color="Orange"><b><u><font size="2"><font color="Olive"><br />
</font></font></u></b></font></font><font color="Olive"><font size="2"><u><b>Auto update</b></u></font></font><br />
<br />
<font size="3"><font color="Orange"><b><font size="2"><font color="black"> Description:<br />
</font></font></b><font size="2"><font color="black">Automaticaly download and install the lastest version of FFAC.<br />
</font></font></font></font> <br />

<br />
<b> admin :</b><br />
<br />
    *amx_ffac_ban &lt;user id or steam id&gt; : ban a player for amx_ffac_ffacban minuts, and repport the ban to the ffac server<br />
    *amx_ffac_map_download &lt;map&gt; : download a map from the ffac server on your server ! <br />

    *amx_ffac_map_list : show the list of downloable maps<br />
    *amx_ffac_hpt_install &lt;package&gt; : Install a package on your server !<br />
    *amx_ffac_hpt_remove &lt;package&gt; : Remove a package <br />
    *amx_ffac_hpt_menu : Show a menu with all packages available<br />

<br />
<font color="Olive"><font size="2"><b><font color="Black">CVARS:</font></b></font></font><ul><li>ffac_autoupdate ( default 1 ) : enable autoupdate</li></ul><font color="Olive"><font size="2"><u><b><br />
MISC<br />
<br />
</b></u></font></font> <font color="Olive"><font size="2"><b><font color="Black">CVARS:<br />
</font></b></font></font><ul><li><font color="Olive"><font size="2"><font color="Black">ffac_packetsize ( default 1024 ) :  Size of packets</font></font></font></li><li><font color="Olive"><font size="2"><font color="Black">ffac_master_server ( default &quot;82.232.102.55&quot; ) : FFAC master server address</font></font></font></li></ul><b><font color="Red">Installation :</font><br />

</b>Only install the plugin on your server. The server will  be automaticaly add to the server list.<br />
&quot;No incoming port open required&quot;<br />
<b><br />
<font color="Red"> Front end :</font><br />
</b><a href="http://82.232.102.55/FFAC" target="_blank">http://82.232.102.55/FFAC</a><br />
<br />
<font color="Red"><b>Require : </b></font><br />
Socket<br />
<br />
Some stats :<br />

<br />
FFAC Stats ( 11 feb )<br />
<br />
<font face="Arial"><font size="1">Total Players</font></font><font face="Arial"><font size="1"> : 8840</font></font><font face="Arial"><font size="1"><br />
Total Players : Online</font></font><font face="Arial"><font size="1">111</font></font><font face="Arial"><font size="1"><br />
Total Kills : </font></font><font face="Arial"><font size="1">253918</font></font><font face="Arial"><font size="1"><br />
Total Deaths : </font></font><font face="Arial"><font size="1">248854<br />

</font></font><font face="Arial"><font size="1">Total Connections : </font></font><font face="Arial"><font size="1">26140</font></font><font face="Arial"><font size="1"><br />
Total Servers : </font></font><font face="Arial"><font size="1">30</font></font><font face="Arial"><font size="1"><br />
Total Teams : </font></font><font face="Arial"><font size="1">7<br />
</font></font><font face="Arial"><font size="1">Players Online/Capacity</font></font><font face="Arial"><font size="1"> : 111/431<br />
</font></font><font face="Arial"><font size="1">Total Players Online Time</font></font><font face="Arial"><font size="1"> : 188 Days 21 Hours and 17 Mins<br />

<br />
</font></font> FFAC Stats ( 1 may )<br />
<br />
<font size="1">Total Players    92404<br />
Total Players Online    226<br />
Total Kills    4268608<br />
Total Deaths    4265948<br />
Total Connections    412119<br />
Total Servers    85<br />

Total Teams    27<br />
Players Online/Capacity    226/1620<br />
Total Players Online Time    2824 Days 11 Hours and 37 Mins<br />
</font> <br />
Connections of all server on FFAC by day<br />
<img src="http://82.232.102.55/FFAC/cgrapm.php" border="0" alt="" /><br />
 Connections of all server on FFAC by hours<br />
<img src="http://82.232.102.55/FFAC/cgraph.php" border="0" alt="" /><br />
<br />
<br />

If you have any questions or suggestions, please ask <img src="images/smilies/smile.gif" border="0" alt="" title="Smile" class="inlineimg" /> <br />
Even thought you think it's impossible ^^<br />
<br />
<a href="http://82.232.102.55/FFAC" target="_blank">http://82.232.102.55/FFAC/forum</a><br />
<br />
<br />
Thks to Alka for testing new functions in beta ^^<br />
<br />
<br />
    Changelog:<br />

<br />
<b>[6/5/2007] 1.4</b><br />
-allow multiple msn in msn_contact ( use  : ffac_msn_contact &quot;msn@msn.com;msn2@msn.com&quot; )<br />
-Extend max msn_contact size from 30 to 128 chars<br />
-Fix command display bug<br />
-Replace valve id lan or steam id lan by ip in the msn contact message<br />
-new natives for db_sys<br />
-indentation problems fix<br />

-multi language support ( set ffac_multi_lang to 1 to test )<br />
-auto download language file if not found !<br />
-integration of the package manager in the amxmodmenu !<br />
 -new say command : /me to display your level<br />
<b><br />
 [21/4/2007] 1.3</b><br />
    -amx_ffac_hpt_menu //Menu for install new plugin, autodownload the plugin list, ...<br />
    -new comands to show server connections stats, say &quot;serverconnections&quot;<br />

    -new commands to show changelog, say &quot;ffac changelog&quot;<br />
    -new function to display player level on aim : set ffac_skill_on_aim 1<br />
    -Some fix with the proxy<br />
<br />
<br />
<b>     [7/4/2007]    1.2</b><br />
    -security fix ( package manager )<br />

    -dynamic master server<br />
    -new package matchdeluxe<br />
    -now package manager and autoupdate are activate by default<br />
    -spawn count fixed
                				</div>