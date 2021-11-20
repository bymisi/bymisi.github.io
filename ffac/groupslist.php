<?php
mysql_select_db($db_ffac_name);

$reponsess = mysql_query("SELECT * FROM groups WHERE Link=0");

echo "<table border=0>";
echo "<tr><td width='102px'>Name</td><td width='48px'>Skill</td><td width='48px'>Players</td><td width='48px'>Teams</td><td width='48px'>Servers</td><td width='48px'>Groups</td><td width='102px'>Join AS</td></tr>";
while ($donnees = mysql_fetch_array($reponsess) )
{
 echo "<tr>";
 echo "<td width='102px' >".$donnees['Name']."</td>";
 echo "<td width='42px' >".$donnees['Skill']."</td>";
 echo "<td width='42px' >".$donnees['id_player']."</td>";
 echo "<td width='42px' >".$donnees['id_team']."</td>";
 echo "<td width='42px' >".$donnees['id_server']."</td>";
 echo "<td width='102px' >".$donnees['id_group']."</td>";
 echo "<td width='' ><a href='#'>Player</a> ;<a href='#'>Team</a> ; <a href='#'>Server</a> ;<a href='#'> Group</a></td>";
 echo "</tr>";
}

echo "</table>";

?>