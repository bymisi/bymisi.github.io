<?php
mysql_select_db($db_ffac_name);


$ipserr= $_GET["ipserv"];
if ($_GET["servid"]!='')
{
	$reponsess = mysql_query("SELECT ip FROM servers WHERE id='".$_GET["servid"]."'");
	$donnees = mysql_fetch_array($reponsess);
	$ipserr=$donnees['ip'];
}


 list($ip, $port )= split (":", $ipserr, 2);

 
$reponsess = mysql_query("SELECT * FROM servers WHERE ip='".$ipserr."'");
$donnees = mysql_fetch_array($reponsess);
 list($ipp, $portp )= split (":", $donnees["ipbind"], 2);
echo "<table border=0>";
echo "<tr>";
 echo "<td width=''><img src='http://82.232.102.55/cgi-bin/server/nukedklan.png?ip=".$ipp."&port=".$port."' width='240px'></td><td>";
?>			
<div class="box">
	<h2><span>Carac.</span></h2>
		<div id="servercarac" class="box">
			<div class="inbox">
<?php
echo "<table border=0>";
echo "<tr><td>HostName</td><td>".$donnees['hostname']."</td></tr>";
echo "<tr><td>Game Type</td><td>".$donnees['gametype']."</td></tr>";
echo "<tr><td>Map</td><td>".$donnees['mapname']."</td></tr>";
echo "<tr><td>Real Maxplayers</td><td>".$donnees['maxplayers']."</td></tr>";
echo "<tr><td>FFAC version</td><td>".$donnees['version']."</td></tr>";
echo "<tr><td>Skill</td><td>".$donnees['Skill']."</td></tr>";
echo "<tr><td>Skill Sum</td><td>".$donnees['skillsum']."</td></tr>";
		$ConnecTimeM = round((($donnees['TTimeOnServer'])%3600)/60);
		$ConnecTimeH = round((($donnees['TTimeOnServer'])%(3600*24)) / 3600);
		$ConnecTimeD = round((($donnees['TTimeOnServer'])) / (3600*24));
echo "<tr><td>Total Time Spend by Players</td><td>".$ConnecTimeD." Days ".$ConnecTimeH." Hours and ".$ConnecTimeM." Mins</td></tr>";
echo "<tr><td>Players connections</td><td>".$donnees['Connections']."</td></tr>";
echo "<tr><td>FFACBans total</td><td>".$donnees['EnforcedBan']."</td></tr>";
echo "<tr><td>Join</td>";
  if ($donnees['ip']=="192.168.0.69:27015")
	  {
	  echo '<td width=""><a href="steam://connect/82.232.102.55:27015">82.232.102.55:27015</a></td>';
	  }
  else
	  {
	  echo '<td width=""><a href="steam://connect/'.$donnees['ipbind'].'">'.$donnees['ipbind'].'</a></td>';
	  }
echo "<tr><td>Real ip</td>";
  if ($donnees['ip']=="192.168.0.69:27015")
	  {
	  echo '<td width="">192.168.0.69:27015</td>';
	  }
  else
	  {
	  echo '<td width="">'.$donnees['ip'].'</td>';
	  }
echo "</tr>";
echo "</table>";
?>
</div></div></div>
<?php
echo "</td></tr>";
echo "<tr><td colspan='2'>";
?>			
<div class="box">
	<h2><span>Players Connections Today</span></h2>
		<div id="pct" class="box">
			<div class="inbox">
<?php
echo "<img src='cgraph.php?ip=".$donnees['ip']."'>";
?>
<?php
echo "<img src='cgrapm.php?ip=".$donnees['ip']."'>";
?>
</div></div></div>
<?php
echo "</td></tr>";


echo "<tr><td colspan='2'>";
?>			
<div class="box">
	<h2><span>Embedded Banner</span></h2>
		<div id="pct" class="box">
			<div class="inbox">
<?php
echo "<img src='".$full_site_url."ffac/urlserver/id/".$donnees['id'].".png'>";

echo "<br>Image link : ".$full_site_url."ffac/urlserver/id/".$donnees['id'].".png</br>";
?>
</div></div></div>
<?php
echo "</td></tr>";



echo "<tr><td>";
?>			
<div class="box">
	<h2><span>Top Skill Online</span></h2>
		<div id="topskillonline" class="box">
			<div class="inbox">
<?php
echo "<table border=0>";
echo "<tr><td width='42px'>Skill</td><td>Nickname</td><td width='48px'>Team</td></tr>";
$reponsess = mysql_query("SELECT players.*,players.Online,teams.tag FROM players INNER JOIN teams ON players.team = teams.id WHERE Server='".$ipserr."' AND Online=1 ORDER BY Skill DESC");
while ($donnees = mysql_fetch_array($reponsess) )
{
$classc = "";
 if ( $donnees['Online'] == 1)
	{
	$classc="online";
	}
else
	{
	$classc="offline";
	}


 echo "<tr>";
 echo "<td width='42px' class='".$classc."'>".$donnees['Skill']."</td>";
 
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
if ($donnees['team']==0)
	{
		echo "<td  width='48px'>X</td>";
	}
else
	{
		echo "<td  width='48px'><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['tag']."</a></td>";
	}
 echo "</tr>";
}
echo "</table>";
?>
</div></div></div>
</td><td>
		<div class="box">
					<h2><span>Top CStats Online</span></h2>
						<div id="topcstatsonline" class="box">
							<div class="inbox">

<?php
$reponsess = mysql_query("SELECT players.*,players.Online,teams.tag FROM players INNER JOIN teams ON players.team = teams.id WHERE Server='".$ipserr."' AND Online=1 ORDER BY csstatscore DESC");
echo "<table border=0>";
echo "<tr><td width='42px'>CStats</td><td>Nickname</td><td width='48px'>Team</td></tr>";

while ($donnees = mysql_fetch_array($reponsess) )
{
$classc = "";
 if ( $donnees['Online'] == 1)
	{
	$classc="online";
	}
else
	{
	$classc="offline";
	}


 echo "<tr>";
 echo "<td width='42px' class='".$classc."'>".$donnees['csstatscore']."</td>";
 
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
if ($donnees['team']==0)
	{
		echo "<td  width='48px'>X</td>";
	}
else
	{
		echo "<td width='48px'><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['tag']."</a></td>";
	}
echo "</tr>";
}
echo "</table>";
echo "</div></div></div>";
echo "</td></tr>";
echo "<tr><td>";
?>
		<div class="box">
					<h2><span>Top 15 Skill</span></h2>
						<div id="topskill" class="box">
							<div class="inbox">

<?php
$reponsess = mysql_query("SELECT players.*,players.Online,teams.tag FROM players INNER JOIN teams ON players.team = teams.id WHERE Server='".$ipserr."' ORDER BY skill DESC LIMIT 0,15");
echo "<table border=0>";
echo "<tr><td width='42px'>Skill</td><td>Nickname</td><td width='48px'>Team</td></tr>";

while ($donnees = mysql_fetch_array($reponsess) )
{
$classc = "";
 if ( $donnees['Online'] == 1)
	{
	$classc="online";
	}
else
	{
	$classc="offline";
	}


 echo "<tr>";
 echo "<td width='42px' class='".$classc."'>".$donnees['Skill']."</td>";
 
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
if ($donnees['team']==0)
	{
		echo "<td  width='48px'>X</td>";
	}
else
	{
		echo "<td  width='48px'><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['tag']."</a></td>";
	}
echo "</tr>";
}
echo "</table>";
echo "</div></div></div>";
echo "</td><td>";
?>
		<div class="box">
					<h2><span>Top 15 CStats</span></h2>
						<div id="topskill" class="box">
							<div class="inbox">

<?php
$reponsess = mysql_query("SELECT players.*,players.Online,teams.tag FROM players INNER JOIN teams ON players.team = teams.id WHERE Server='".$ipserr."' ORDER BY csstatscore DESC LIMIT 0,15");
echo "<table border=0>";
echo "<tr><td width='42px'>CStats</td><td>Nickname</td><td width='48px'>Team</td></tr>";

while ($donnees = mysql_fetch_array($reponsess) )
{
$classc = "";
 if ( $donnees['Online'] == 1)
	{
	$classc="online";
	}
else
	{
	$classc="offline";
	}


 echo "<tr>";
 echo "<td width='42px' class='".$classc."'>".$donnees['csstatscore']."</td>";
 
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
if ($donnees['team']==0)
	{
		echo "<td  width='48px'>X</td>";
	}
else
	{
		echo "<td  width='48px'><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['tag']."</a></td>";
	}
echo "</tr>";
}
echo "</table>";
echo "</div></div></div>";
echo "</td></tr>";
echo "</table>";
?>