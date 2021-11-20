<?php
mysql_select_db($db_ffac_name);

$reponsess = mysql_query("SELECT ip,ipbind,skillsum,skill,hostname,mapname,maxplayers,version,gametype FROM servers ORDER BY skill DESC");

echo "<table border=0>";
echo "<tr><td width='38px'>Skill</td><td>Game</td><td width='256px'>Hostname</td><td>Ip</td><td width='124px'>Map</td><td width='38px'>Max Players</td><td width='38px'>Version</td><td>Join</td></tr>";
while ($donnees = mysql_fetch_array($reponsess) )
{
 echo "<tr>";
 echo "<td width='38px' >".$donnees['skill']."</td>";
 echo "<td width='38px' >".$donnees['gametype']."</td>";
 echo "<td width='256px'><a href=index.php?page=serverinfo&ipserv=".$donnees['ip'].">".$donnees['hostname']."</a></td>";
 if ($donnees['ip']=="192.168.0.69:27015")
	 {
	 echo "<td width=''>82.232.102.55:27015</td>";
	 }
 else
	 {
	 echo "<td width=''>".$donnees['ipbind']."</td>";
	 }	
  echo "<td width='124px' >".$donnees['mapname']."</td>";
   echo "<td width='38px' >".$donnees['maxplayers']."</td>";
    echo "<td width='38px' >".$donnees['version']."</td>";
  if ($donnees['ip']=="192.168.0.69:27015")
	  {
	  echo '<td width=""><a href="steam://connect/82.232.102.55:27015">Join</a></td>';
	  }
  else
	  {
	  echo '<td width=""><a href="steam://connect/'.$donnees['ipbind'].'">Join</a></td>';
	  }
 echo "</tr>";
}
echo "</table>";

?>