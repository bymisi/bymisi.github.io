<?php
include "forum/config.php";
mysql_connect($db_ffac_host, $db_ffac_username, $db_ffac_password);
mysql_select_db($db_ffac_name);

   list($ip, $port )= split (":", $_GET["ipserv"], 2);
   if ($ip=="192.168.0.69")
   $ipnum = sprintf("%u", ip2long("82.232.102.55")); 
   else
   $ipnum = sprintf("%u", ip2long($ip)); 
   $resultat=mysql_query("SELECT * FROM iptocode NATURAL JOIN codetopos WHERE '".$ipnum."' BETWEEN sip AND eid");
$servercity = mysql_fetch_array($resultat);
   $reponsess = mysql_query("SELECT * FROM servers WHERE ip='".$_GET["ipserv"]."'");
$serverinfo = mysql_fetch_array($reponsess);
  ?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>FFAC Server google map location !</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $ggmapkey; ?>"
      type="text/javascript"></script>
    <script type="text/javascript">

    //<![CDATA[

    function load() {
      if (GBrowserIsCompatible()) {
	           var icon1 = new GIcon();
               icon1.image = "http://82.232.102.55/FFAC/hlxi/mm_20_blue.png";
               icon1.shadow = "http://82.232.102.55/FFAC/hlxi/mm_20_shadow.png";
               icon1.iconSize = new GSize(12, 20);
               icon1.shadowSize = new GSize(22, 20);
               icon1.iconAnchor = new GPoint(6, 20);
               icon1.infoWindowAnchor = new GPoint(5, 1);
               var icon2 = new GIcon();
               icon2.image = "http://82.232.102.55/FFAC/hlxi/mm_20_blue.png";
               icon2.shadow = "http://82.232.102.55/FFAC/hlxi/mm_20_shadow.png";
               icon2.iconSize = new GSize(13, 22);
               icon2.shadowSize = new GSize(22, 22);
               icon2.iconAnchor = new GPoint(6, 22);
               icon2.infoWindowAnchor = new GPoint(5, 1);
               
               var icon_server1 = new GIcon();
               icon_server1.image = "http://82.232.102.55/FFAC/hlxi/mm_20_red.png";
               icon_server1.shadow = "http://82.232.102.55/FFAC/hlxi/mm_20_shadow.png";
               icon_server1.iconSize = new GSize(12, 20);
               icon_server1.shadowSize = new GSize(22, 20);
               icon_server1.iconAnchor = new GPoint(6, 20);
               icon_server1.infoWindowAnchor = new GPoint(5, 1);
               var icon_server2 = new GIcon();
               icon_server2.image = "http://82.232.102.55/FFAC/hlxi/mm_20_red.png";
               icon_server2.shadow = "http://82.232.102.55/FFAC/hlxi/mm_20_shadow.png";
               icon_server2.iconSize = new GSize(13, 22)
               icon_server2.shadowSize = new GSize(22, 22);
               icon_server2.iconAnchor = new GPoint(6, 22);
               icon_server2.infoWindowAnchor = new GPoint(5, 1);
	  
	  
	  
	  
	  
		var map = new GMap2(document.getElementById("map"));
               map.addControl(new GLargeMapControl());
               map.addControl(new GMapTypeControl());
                map.setCenter(new GLatLng(<?php echo $servercity['Latitude']." , ".$servercity['Longitude']; ?>),     4);map.setMapType(G_HYBRID_TYPE);   				 
               map.enableDoubleClickZoom();
			   
			      
                 
                 function createPlayerMarker(point, player_array) {
                   if (player_array[0]['city'] == "") {
                     var html_text = '<table border=0 style="text-align:left;background: #ffffff">' + 
                                     '<tr><td align="left" style="border-bottom:1px solid black;"><span style="color:blue;font-weight:bold;text-decoration:none;"><small>'+player_array[0]['country']+'</small></span></td></tr>';
                   } else {
                     var html_text = '<table border=0 style="text-align:left;background: #ffffff">' + 
                                     '<tr><td align="left" style="border-bottom:1px solid black;"><span style="color:blue;font-weight:bold;text-decoration:none;"><small>'+player_array[0]['city']+', '+player_array[0]['country']+'</small></span></td></tr>';
                   }                  
                   var i = 0;
                   for(var entry in player_array) {
                     if (i == 0) {
                       html_text     = html_text + '<tr><td style="padding-top:5px;"><a href="hlstats.php?mode=playerinfo&player='+player_array[entry]['player_id']+'" style="text-decoration:none;color:black;"><small style="font-weight:bold;">'+player_array[entry]['name']+'<small></a></td></tr>'+
                                                   '<tr><td><small>Skill:&nbsp;'+player_array[entry]['Skill']+'<small></a></td></tr>'+ 
                                                   '<tr><td><small>Total Online Time:&nbsp;'+player_array[entry]['time']+'<small></a></td></tr>';
                     } else {
                       html_text     = html_text + '<tr><td style="padding-top:5px;border-top:1px #000 solid"><a href="hlstats.php?mode=playerinfo&player='+player_array[entry]['player_id']+'" style="text-decoration:none;color:black;"><small style="font-weight:bold;">'+player_array[entry]['name']+'<small></a></td></tr>'+
                                                   '<tr><td><small>Skill:&nbsp;'+player_array[entry]['Skill']+'<small></a></td></tr>'+ 
                                                   '<tr><td><small>Total Online Time:&nbsp;'+player_array[entry]['time']+'<small></a></td></tr>';
                     }                                                   
                     i = i + 1;
                   }                  

                   html_text     = html_text + '</table>';  
                   if (i > 0) {
                     var marker    = new GMarker(point, icon2);
                   } else {
                     var marker    = new GMarker(point, icon1);
                   }  
                   map.addOverlay(marker);
                   GEvent.addListener(marker, "click", function() {marker.openInfoWindowHtml(html_text);});
                 }


                 function createServerMarker(point, server_array) {
                   if (server_array[0]['city'] == "") {
                     var html_text = '<table border=0 style="text-align:left;background: #ffffff">' + 
                                     '<tr><td align="left" style="border-bottom:1px solid black;"><span style="color:blue;font-weight:bold;text-decoration:none;"><small>'+server_array[0]['country']+'</small></span></td></tr>';
                   } else {
                     var html_text = '<table border=0 style="text-align:left;background: #ffffff">' + 
                                     '<tr><td align="left" style="border-bottom:1px solid black;"><span style="color:blue;font-weight:bold;text-decoration:none;"><small>'+server_array[0]['city']+', '+server_array[0]['country']+'</small></span></td></tr>';
                   }                  
                   var i = 0;
                   for(var entry in server_array) {
                     if (i == 0) {
                       html_text     = html_text + '<tr><td style="padding-top:5px;"><small style="font-weight:bold;">'+server_array[entry]['name']+'<small></td></tr>'+
                                                   '<tr><td><a href="hlsw://'+server_array[entry]['address']+'" style="text-decoration:none;"><small>'+server_array[entry]['address']+'<small></a></td></tr>';
                     } else {
                       html_text     = html_text + '<tr><td style="padding-top:5px;border-top:1px #000 solid"><small style="font-weight:bold;">'+server_array[entry]['name']+'<small></td></tr>'+
                                                   '<tr><td><a href="hlsw://'+server_array[entry]['address']+'" style="text-decoration:none;"><small>'+server_array[entry]['address']+'<small></a></td></tr>';
                     }                
					html_text     = html_text + "<tr><td><?php  echo "<img src='/cgi-bin/server/nukedklan.png?ip=".$ip."&port=".$port."' width='240px'>"; ?></td></tr>"
                     i = i + 1;
                   }                  

                   html_text     = html_text + '</table>';  
                   if (i > 0) {
                     var marker    = new GMarker(point, icon_server2);
                   } else {
                     var marker    = new GMarker(point, icon_server1);
                   }  
                   map.addOverlay(marker);
                   GEvent.addListener(marker, "click", function() {marker.openInfoWindowHtml(html_text);});
                 }
				 
				  var player_data_array = new Array();
<?php				 
$reponsess = mysql_query("SELECT players.*,players.Online,teams.tag FROM players INNER JOIN teams ON players.team = teams.id WHERE Server='".$_GET["ipserv"]."' ORDER BY ConnecTime DESC LIMIT 0,75");
while ($donnees = mysql_fetch_array($reponsess) )
{	
 $ipnump = sprintf("%u", ip2long($donnees['ip'])); 	
   $resultat=mysql_query("SELECT * FROM iptocode NATURAL JOIN codetopos WHERE '".$ipnump."' BETWEEN sip AND eid");
$playerinfo = mysql_fetch_array($resultat);
	$ConnecTimeM = round((($donnees['ConnecTime'])%3600)/60);
		$ConnecTimeH = round($donnees['ConnecTime'] / 3600);
		$ConnecTimeHF = $donnees['ConnecTime'] / 3600 ; 
	if 	($resultat && $playerinfo['Latitude']!="" ) {
?>
                
player_data_array[0] = new Array();
player_data_array[0]['player_id'] = '<?php echo $donnees['SID']; ?>';
player_data_array[0]['name']      = '<?php echo $donnees['Pseudo']; ?>';
player_data_array[0]['Skill']     = '<?php echo $donnees['Skill']; ?>';
player_data_array[0]['time']      = '<?php echo $ConnecTimeH." Hours and ".$ConnecTimeM." Mins"; ?>';
player_data_array[0]['country']      = '<?php echo $playerinfo['Ville']; ?>';
player_data_array[0]['city']   = 'City';
createPlayerMarker(new GLatLng(<?php echo $playerinfo['Latitude']." , ".$playerinfo['Longitude']; ?>), player_data_array);

<?php
}}
?>		 
				 
				 
var server_data_array = new Array();

server_data_array[0] = new Array();
server_data_array[0]['name']    = '<?php echo $serverinfo['hostname']; ?>';
server_data_array[0]['city']    = '<?php echo $servercity['Ville'];?>';
server_data_array[0]['country'] = 'City';
server_data_array[0]['address'] = '<?php echo $ip ?>';
createServerMarker(new GLatLng(<?php echo $servercity['Latitude']." , ".$servercity['Longitude']; ?>), server_data_array);  
				 
 
      }
    }

    //]]>
    </script>
  </head>
  <body onload="load()" onunload="GUnload()">
    <div id="map" style="width: 640px; height: 480px"></div>
  </body>
</html>