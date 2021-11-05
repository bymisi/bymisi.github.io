

<?php

include "forum/config.php";
mysql_connect($db_ffac_host, $db_ffac_username, $db_ffac_password);
mysql_select_db($db_ffac_name);
$sorttype="DESC";

$rankby="SumCSSTATS";



$reponsess = mysql_query("SELECT players.Pseudo,players.id,players.SID,players.Online, sum(players_tpl.ConnectTime) AS SumConnectTime, sum(players_tpl.CKill) AS SumKill, sum(players_tpl.CDeath) AS SumDeath, sum(players_tpl.CKill-players_tpl.CDeath) AS SumCSSTATS FROM players_tpl INNER JOIN players ON players_tpl.pid = players.id WHERE players_tpl.Serverid='".$_GET["ids"]."' GROUP BY players_tpl.pid ORDER BY ".$rankby." ".$sorttype." LIMIT 0,".$_GET["nb"]."");

echo "<table border=0 height=364>";
echo "<tr><td width='42px'>CStats</td><td>Nickname</td></tr>";

while ($donnees = mysql_fetch_array($reponsess) )
{
$backcolor = "";
 if ( $donnees['Online'] == 1)
	{
	$backcolor="#009900";
	$classc="online";
	}
else
	{
	$backcolor="#990000";
	$classc="offline";
	}



 echo "<tr>";
 if ($donnees['SumCSSTATS']>0)
 {
	echo "<td width='42px' bgcolor='#00AA00'>".$donnees['SumCSSTATS']."</td>";
 }
 if ($donnees['SumCSSTATS']<0)
  {
	echo "<td width='42px' bgcolor='#AA0000'>".$donnees['SumCSSTATS']."</td>";
 }
  if ($donnees['SumCSSTATS']==0)
  {
	echo "<td width='42px' bgcolor='#0000AA'>".$donnees['SumCSSTATS']."</td>";
 }	
 
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
	

 
echo "</tr>";
}
echo "</table>";
?>
