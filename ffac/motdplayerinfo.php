<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>FFAC : Free For All CS Championship</title>
</head>
<body  bgcolor='#2A2A2A'>
<?php
echo "<h3><span>FFAC playerinfo</span></h3>";
echo "<h3><span>FFAC : http://82.232.102.55/FFAC</span></h3>";


include "forum/config.php";
mysql_connect($db_ffac_host, $db_ffac_username, $db_ffac_password);
mysql_select_db($db_ffac_name);

$reponsess = mysql_query("SELECT players.*,teams.tag,teams.name FROM players INNER JOIN teams ON players.team = teams.id  WHERE players.SID='".$_GET["SID"]."'");
$donnees = mysql_fetch_array($reponsess);
$resultat=mysql_query("SELECT * FROM iptocountry WHERE (IP_FROM<=inet_aton('".$donnees['ip']."') AND IP_TO>=inet_aton('".$donnees['ip']."'))");
$donnees_country = mysql_fetch_array($resultat);
?>		
<?php mysql_select_db("punbb");
$areponsess = mysql_query("SELECT id FROM users WHERE icq='".$donnees['SID']."'");
$adonnees = mysql_fetch_array($areponsess);
$ProfileID= $adonnees['id'];
mysql_select_db($db_ffac_name); ?>	
<table border='0' style='background-color:#686868;color: white;font-family:tahoma,arial;font-size:10px;border:#565656 1px solid;}'>
<tr><td>
<div class="box">
		<h3><span>Player Info</span></h3>
			<div id="playerinfo" class="box">
<?php
echo "<table>";
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
		echo "<img width='10%' height='60%' src='images/flags/".strtolower($donnees_country['COUNTRY_CODE2']).".png'>";
		echo "</td>";
	echo "</tr>";

	echo "<tr>";
		echo "<td>";
		echo "Team Tag";
		echo "</td>";
		if ($donnees['team']==0)
			{
				echo "<td>X</td>";
			}
		else
			{
				echo "<td><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['tag']."</a></td>";
			}
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Team Name";
		echo "</td>";
		if ($donnees['team']==0)
			{
				echo "<td>No Team</td>";
			}
		else
			{
				echo "<td><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['name']."</a></td>";
			}
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Last Server";
		echo "</td>";
	 if ($donnees['Server']=="192.168.0.69:27015")
	  {
	  echo '<td width=""><a href="steam: &quot;-applaunch 10 -game cstrike +connect 82.232.102.55:27015&quot;">82.232.102.55:27015</a>';
	  echo ' <tab>		   <a href=index.php?page=serverinfo&ipserv='.$donnees['Server'].'><img src="images/stats.png"></a></td>';
	  }
  else
	  {
	  echo '<td width=""><a href="steam: &quot;-applaunch 10 -game cstrike +connect '.$donnees['Server'].'&quot;">'.$donnees['Server'].'</a>';
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
			</div></td><td>
	<div class="box">
					<h3><span>Player Stats</span></h3>
						<div id="PlayerComments" class="box">
<?php
echo "<table>";
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
echo "</table>";
?>
			</div>
			</div>


			</td></tr>
			</table>
			
</body>
</html>