<?php
mysql_select_db($db_ffac_name);
?>
<table>
<tr>
<td>
<div class="box">
		<h2><span>Bans counters</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">

<?php
$reponsess = mysql_query("SELECT count(*) AS SBan FROM banned");
$donnees = mysql_fetch_array($reponsess) ;
echo "<table border=0>";
echo "<tr>";
echo "<td width='196px'>Total Players Banned</td><td width='64px'>".$donnees['SBan']."</td>";
echo "</tr>";
$reponsess = mysql_query("SELECT sum(EnforcedBan) AS SEB FROM servers");
$donnees = mysql_fetch_array($reponsess) ;
echo "<tr>";
echo "<td width='196px'>Players Banned By FFACBans</td><td width='64px'>".$donnees['SEB']."</td>";
echo "</tr>";
echo "</table>";
?>
</div>
</div>
</td>
</tr>
<tr>
<td>
<div class="box">
		<h2><span>Top bans servers</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<?php
mysql_select_db($db_ffac_name);

$reponsess = mysql_query("SELECT ip,hostname,EnforcedBan FROM servers ORDER BY EnforcedBan DESC LIMIT 15");
echo "<table border=0>";
echo "<tr><td>Name</td><td width='76px'>EnforcedBan</td></tr>";
while ($donnees = mysql_fetch_array($reponsess) )
{
 echo "<tr>";

 echo "<td width=''><a href=index.php?page=serverinfo&ipserv=".$donnees['ip'].">".$donnees['hostname']."</td></a>";
 echo "<td width=''>".$donnees['EnforcedBan']."</td>";
 echo "</tr>";
}
echo "</table>";

mysql_select_db($db_ffac_name);
?>
</div>
</div>
</td>
</tr>
<tr>
<td>
<div class="box">
		<h2><span>Top Player Enforced Bans</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<?php
$reponsess = mysql_query("SELECT * FROM banned ORDER BY Enforce DESC LIMIT 30");
echo "<table border=0>";
echo "<tr><td>Steam ID</td><td width='76px'>Enforced Ban</td></tr>";
while ($donnees = mysql_fetch_array($reponsess) )
{
 echo "<tr>";
echo "<td width=''>".$donnees['SID']."</td>";
 echo "<td width=''>".$donnees['Enforce']."</td>";
 echo "</tr>";
}
echo "</table>";
?>
</div>
</div>
</td>
</tr>
</table>