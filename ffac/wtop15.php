<?php
mysql_select_db($db_ffac_name);
$sorttype="DESC";
if ($_GET["sorttype"] == "flop" )
{
$sorttype="ASC";
}

$rankby="SumCSSTATS";
if ($_GET["rankby"]=="kill")$rankby="SumKill";
if ($_GET["rankby"]=="death")$rankby="SumDeath";
if ($_GET["rankby"]=="time")$rankby="SumConnectTime";


echo "<h3><span>FFAC</span></h3>";
$reponsess = mysql_query("SELECT players.Pseudo,players.id,players.SID,players.Online, sum(players_tpl.ConnectTime) AS SumConnectTime, sum(players_tpl.CKill) AS SumKill, sum(players_tpl.CDeath) AS SumDeath, sum(players_tpl.CKill-players_tpl.CDeath) AS SumCSSTATS FROM players_tpl INNER JOIN players ON players_tpl.pid = players.id WHERE 1=1 GROUP BY players_tpl.pid ORDER BY ".$rankby." ".$sorttype." LIMIT 0,10");

echo "<table border=0>";
echo "<tr><td width='42px'>Pseudo</td><td width='42px'>CSStats</td></tr>";

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
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
	
 if ($donnees['SumCSSTATS']>0)
 {
	echo "<td width='42px'>".$donnees['SumCSSTATS']."</td>";
 }
 if ($donnees['SumCSSTATS']<0)
  {
	echo "<td width='42px'>".$donnees['SumCSSTATS']."</td>";
 }
  if ($donnees['SumCSSTATS']==0)
  {
	echo "<td width='42px'>".$donnees['SumCSSTATS']."</td>";
 }	
	
 

 
echo "</tr>";
}
echo "</table>";
?>
