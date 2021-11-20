<?php
mysql_select_db($db_ffac_name);

$reponses = mysql_query("SELECT count(*) AS SPlayer, sum(Online) AS SOnline ,sum(CKill) AS SKill,sum(Death) AS SDeath,sum(NConnect) AS SConnect , Sum(ConnecTime) AS SConnectT FROM players");
$donnees = mysql_fetch_array($reponses) ;
$reponsess = mysql_query("SELECT sum(maxplayers) AS SMPlayers FROM servers");
$donneess = mysql_fetch_array($reponsess) ;
?>
<table>
<tr>
<td>
<div class="box">
		<h2><span>Stats Counters</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">

<?php

echo "<table border=0>";
echo "<tr>";
echo "<td width='196px'>Total Players</td><td width='64px'>".$donnees['SPlayer']."</td>";
echo "</tr><tr>";
echo "<td width='196px'>Total Players Online</td width='64px'><td>".$donnees['SOnline']."</td>";
echo "</tr><tr>";
echo "<td width='196px'>Total Kills</td><td width='64px'>".$donnees['SKill']."</td>";
echo "</tr><tr>";
echo "<td width='196px'>Total Deaths</td><td width='64px'>".$donnees['SDeath']."</td>";
echo "</tr><tr>";
echo "<td width='196px'>Total Connections</td><td width='64px'>".$donnees['SConnect']."</td>";
echo "</tr><tr>";

$reponsesss = mysql_query("SELECT count(*) AS SServers FROM servers");
$donneesss = mysql_fetch_array($reponsesss) ;
echo "<td width='196px'>Total Servers</td><td width='64px'>".$donneesss['SServers']."</td>";
echo "</tr><tr>";
$reponsesss = mysql_query("SELECT count(*) AS STeams FROM teams");
$donneesss = mysql_fetch_array($reponsesss) ;
echo "<td width='196px'>Total Teams</td><td width='64px'>".$donneesss['STeams']."</td>";
echo "</tr><tr>";
echo "<td width='196px'>Players Online/Capacity</td><td width='64px'>".$donnees['SOnline']."/".$donneess['SMPlayers']."</td>";
echo "</tr><tr>";
echo "</tr><tr>";
		$ConnecTimeM = round((($donnees['SConnectT'])%3600)/60);
		$ConnecTimeH = round((($donnees['SConnectT'])%(3600*24)) / 3600);
		$ConnecTimeD = round((($donnees['SConnectT'])) / (3600*24));
echo "<td width='196px'>Total Players Online Time</td><td width='64px'>".$ConnecTimeD." Days ".$ConnecTimeH." Hours and ".$ConnecTimeM." Mins</td>";
echo "</tr><tr>";
echo "</table>";
?>
</div>
</div>
</td>
</tr>
<tr>
<td>
<div class="box">
	<h2><span>Players Connections Today/Month</span></h2>
		<div id="pct" class="box">
			<div class="inbox">
<?php
echo "<img src='cgraph.php'>";
?>
<?php
echo "<img src='cgrapm.php'>";
?>
<?php

?>
</div>
</div>
</td>
</tr>
</table>