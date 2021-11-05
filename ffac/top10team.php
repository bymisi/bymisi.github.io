<?php
mysql_select_db($db_ffac_name);

$reponsess = mysql_query("SELECT Skill,tag, site,name,id FROM teams ORDER BY Skill DESC LIMIT 0,10");

echo "<table border=0 height=364>";
echo "<tr><td width='42px'>Skill</td><td >Team</td><td width='58px'>Tag</td></tr>";
while ($donnees = mysql_fetch_array($reponsess) )
{
 echo "<tr>";
 echo "<td width='42px' >".$donnees['Skill']."</td>";
 echo "<td width='' ><a href='index.php?page=teaminfo&tid=".$donnees['id']."'>".$donnees['name']."</a></td>";

  
 echo "<td width='58px' >".$donnees['tag']."</td>";
 echo "</tr>";
}

echo "</table>";

?>