
<?php
mysql_select_db($db_ffac_name);

$reponsess = mysql_query("SELECT players.*,teams.tag,teams.name FROM players INNER JOIN teams ON players.team = teams.id  WHERE players.id='".$_GET["pid"]."'");
$donnees = mysql_fetch_array($reponsess);

$reponsess = mysql_query("SELECT count(*) AS rank FROM players WHERE Skill>".$donnees['Skill']);
$donners = mysql_fetch_array($reponsess);
$reponsess = mysql_query("SELECT count(*) AS rank FROM players WHERE csstatscore>".$donnees['csstatscore']);
$donnerc = mysql_fetch_array($reponsess);

$resultat=mysql_query("SELECT * FROM iptocountry WHERE (IP_FROM<=inet_aton('".$donnees['ip']."') AND IP_TO>=inet_aton('".$donnees['ip']."'))");
$donnees_country = mysql_fetch_array($resultat);
 $ipnum = sprintf("%u", ip2long($donnees['ip']));
$resultat=mysql_query("SELECT * FROM iptocode NATURAL JOIN codetopos WHERE '".$ipnum."' BETWEEN sip AND eid");
$donnees_city = mysql_fetch_array($resultat);

?>		
<?php mysql_select_db("punbb");
$areponsess = mysql_query("SELECT id FROM users WHERE icq='".$donnees['SID']."'");
$adonnees = mysql_fetch_array($areponsess);
$ProfileID= $adonnees['id'];
mysql_select_db($db_ffac_name); ?>	
<table border=0>
<tr><td>
<div class="box">
		<h2><span>Player Info</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<?php
echo "<table height='400px'>";
	echo "<tr>";
		echo "<td>";
		echo "Name";
		echo "</td>";
		echo "<td>";
		echo $donnees['Pseudo'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Steam ID";
		echo "</td>";
		echo "<td>";
		echo $donnees['SID'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Country";
		echo "</td>";
		echo "<td>";
		echo $donnees_country['COUNTRY_NAME'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Country flag";
		echo "</td>";
		echo "<td>";
		echo "<img width='8%' height='8%' src='images/flags/".strtolower($donnees_country['COUNTRY_CODE2']).".png'>";
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "City";
		echo "</td>";
		echo "<td>";
		echo $donnees_city['Ville'];
		echo "</td>";
	echo "</tr>";

	echo "<tr>";
		echo "<td>";
		echo "Team Tag";
		echo "</td>";
		if ($donnees['team']==0)
			{
				echo "<td  width='48px'>X</td>";
			}
		else
			{
				echo "<td  width='48px'><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['tag']."</a></td>";
			}
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Team Name";
		echo "</td>";
		if ($donnees['team']==0)
			{
				echo "<td  width='48px'>No Team</td>";
			}
		else
			{
				echo "<td  width='48px'><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['name']."</a></td>";
			}
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Last Server";
		echo "</td>";
	 if ($donnees['Server']=="192.168.0.69:27015")
	  {
	  echo "<td>";
	 # echo '<td width=""><a href="steam: &quot;-applaunch 10 -game cstrike +connect 82.232.102.55:27015&quot;">82.232.102.55:27015</a>';
	  echo ' <tab>		   <a href=index.php?page=serverinfo&ipserv='.$donnees['Server'].'><img src="images/stats.png"></a></td>';
	  }
  else
	  {
	  echo "<td>";
	 # echo '<td width=""><a href="steam: &quot;-applaunch 10 -game cstrike +connect '.$donnees['Server'].'&quot;">'.$donnees['Server'].'</a>';
	  echo '   <tab>		<a href=index.php?page=serverinfo&ipserv='.$donnees['Server'].'><img src="images/stats.png"></a></td>';
	  }
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Total Connections";
		echo "</td>";
		echo "<td>";
		echo $donnees['NConnect'];
		echo "</td>";
	echo "</tr>";
		echo "<tr>";
		echo "<td>";
		echo "Total Connect Time";
		echo "</td>";
		echo "<td>";
		$ConnecTimeM = round((($donnees['ConnecTime'])%3600)/60);
		$ConnecTimeH = round($donnees['ConnecTime'] / 3600);
		$ConnecTimeHF = $donnees['ConnecTime'] / 3600 ; 
		echo $ConnecTimeH." Hours and ".$ConnecTimeM." Mins";
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Rank by Skill";
		echo "</td>";
		echo "<td>";
		echo $donners['rank'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Rank by CSStats";
		echo "</td>";
		echo "<td>";
		echo $donnerc['rank'];
		echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
		echo "<td>";
		echo "Profile";
		echo "</td>";
		echo "<td>";
		if ($ProfileID=="")
			echo "UnReg.";
		else
			echo "<a href='forum/profile.php?id=".$ProfileID."'>Profile</a>";
		echo "</td>";
	echo "</tr>";
	
	echo "</tr>";
	
echo "</table>";
?>
							</div>
						</div>
			</div></td><td>
	<div class="box">
					<h2><span>Player Stats</span></h2>
						<div id="PlayerComments" class="box">
							<div class="inbox">
<?php
echo "<table height='400px'>";
	echo "<tr>";
		echo "<td>";
		echo "Skill";
		echo "</td>";
		echo "<td>";
		echo $donnees['Skill'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "CStats";
		echo "</td>";
		echo "<td>";
		echo $donnees['csstatscore'];
		echo "</td>";
	echo "</tr>";
 	echo "<tr>";
		echo "<td>";
		echo "Kill";
		echo "</td>";
		echo "<td>";
		echo $donnees['Ckill'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Death";
		echo "</td>";
		echo "<td>";
		echo $donnees['Death'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "HS";
		echo "</td>";
		echo "<td>";
		echo $donnees['CHSG'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "HS take";
		echo "</td>";
		echo "<td>";
		echo $donnees['CHST'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Kills/Hours";
		echo "</td>";
		echo "<td>";
		echo round($donnees['Ckill']/$ConnecTimeHF,2);
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Deaths/Hours";
		echo "</td>";
		echo "<td>";
		echo round($donnees['Death']/$ConnecTimeHF,2);
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Eff./Hours";
		echo "</td>";
		echo "<td>";
		echo round($donnees['csstatscore']/$ConnecTimeHF,2);
		echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
		echo "<td>";
		echo "Kills/Connect";
		echo "</td>";
		echo "<td>";
		echo round($donnees['Ckill']/$donnees['NConnect'],2);
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Deaths/Connect";
		echo "</td>";
		echo "<td>";
		echo round($donnees['Death']/$donnees['NConnect'],2);
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Eff./Connect";
		echo "</td>";
		echo "<td>";
		echo round($donnees['csstatscore']/$donnees['NConnect'],2);
		echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
		echo "<td>";
		echo "Time/Connection";
		echo "</td>";
		echo "<td>";
		echo round(($donnees['ConnecTime']/$donnees['NConnect'])/60);
		echo " Mins</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Spawns";
		echo "</td>";
		echo "<td>";
		echo $donnees['SpawnCount'];
		echo "</td>";
	echo "</tr>";
echo "</table>";
?>

			</div>
			<tr><td colspan='2'>
			
					<h2><span>Player Last Games ( last 7 days, 30 connections )</span></h2>
						<div id="PlayerComments" class="box">
							<div class="inbox">		
	<font size="1.2">				
<?php
echo "<table border=0>";
echo "<tr><td width='150'>Date</td><td  width='150'>Length</td><td>Skill</td><td>Spawn</td><td>CSStats</td><td>Kill</td><td>Death</td><td>Game</td><td>Server</td></tr>";		
$reponsess = mysql_query("SELECT * FROM players_tpl WHERE pid=".$donnees['id']." ORDER BY playdate DESC LIMIT 0,30");
while ($donnees = mysql_fetch_array($reponsess) )
{
echo "<tr>";
		echo "<td width='150'>";
		echo $donnees['playdate'];
		echo "</td>";
		echo "<td  width='150'>";
		$ConnecTimeM = round((($donnees['ConnectTime'])%3600)/60);
		$ConnecTimeH = round($donnees['ConnectTime'] / 3600);
		$ConnecTimeHF = $donnees['ConnectTime'] / 3600 ; 
		echo $ConnecTimeH." Hours and ".$ConnecTimeM." Mins";
		echo "</td>";
		echo "<td>";
		echo $donnees['skill'];
		echo "</td>";
		echo "<td>";
		echo $donnees['SpawnCount'];
		echo "</td>";	
		$csstats=$donnees['CKill']-$donnees['CDeath'];
	 if ($csstats>0){$csscolor="#00AA00";}
	 if ($csstats<0){$csscolor="#AA0000";}
	 if ($csstats==0){$csscolor="#0000AA";}
	 
		echo "<td ";
		echo "bgcolor='";
		echo $csscolor;
		echo "'>";
		echo $csstats;
		echo "</td>";
		echo "<td>";
		echo $donnees['CKill'];
		echo "</td>";
		echo "<td>";
		echo $donnees['CDeath'];
		echo "</td>";
		echo "<td>";
		echo $donnees['gametype'];
		echo "</td>";
		 if ($donnees['Server']=="192.168.0.69:27015")
	  {
	  echo "<td>";
	 # echo '<td width=""><a href="steam: &quot;-applaunch 10 -game cstrike +connect 82.232.102.55:27015&quot;">82.232.102.55:27015</a>';
	  echo ' <tab>		   <a href=index.php?page=serverinfo&servid='.$donnees['Serverid'].'><img src="images/stats.png"></a></td>';
	  }
  else
	  {
	  echo "<td>";
	 # echo '<td width=""><a href="steam: &quot;-applaunch 10 -game cstrike +connect '.$donnees['Server'].'&quot;">'.$donnees['Server'].'</a>';
	  echo '   <tab>		<a href=index.php?page=serverinfo&servid='.$donnees['Serverid'].'><img src="images/stats.png"></a></td>';
	  }
echo "</tr>";

}
echo "</table>";
?>			
</font>			
			</div>
			</div>
</td></tr>			

    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $ggmapkey; ?>"
      type="text/javascript"></script>
	  
	      <script type="text/javascript">

    //<![CDATA[

    function load() {
      if (GBrowserIsCompatible()) {
		var map = new GMap2(document.getElementById("map"));
			map.setCenter(new GLatLng(<?php echo $donnees_city['Latitude']." , ".$donnees_city['Longitude']; ?>), 5);
			map.openInfoWindow(map.getCenter(),
            document.createTextNode("<?php echo $donnees['Pseudo']; ?>"));
      }
    }

    //]]>
    </script>
	</div>
		</div>
	
			</td></tr>
	<tr><td colspan='2'>
						<h2><span>PlayerLocation</span></h2>
						<div id="PlayerComments" class="box">
							<div class="inbox">		
	<body onload="load()" onunload="GUnload()">
	<center>
	 <div id="map" style="width: 500px; height: 300px"></div>
	</center>	
		</body>
		</div></div>
	</td></tr>
			</table>