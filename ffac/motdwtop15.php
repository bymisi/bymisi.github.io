<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>FFAC : Free For All CS Championship</title>
</head>
<body bgcolor='#2A2A2A'>

<?php

include "forum/config.php";
mysql_connect($db_ffac_host, $db_ffac_username, $db_ffac_password);
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
$reponsess = mysql_query("SELECT players.Pseudo,players.id,players.SID,players.Online, sum(players_tpl.ConnectTime) AS SumConnectTime, sum(players_tpl.CKill) AS SumKill, sum(players_tpl.CDeath) AS SumDeath, sum(players_tpl.CKill-players_tpl.CDeath) AS SumCSSTATS FROM players_tpl INNER JOIN players ON players_tpl.pid = players.id WHERE players_tpl.Serverid='".$_GET["ids"]."' GROUP BY players_tpl.pid ORDER BY ".$rankby." ".$sorttype." LIMIT 0,".$_GET["nb"]."");

echo "<table border=0 style='background-color:#383838;color: white;font-family:tahoma,arial;font-size:10px;border:#565656 1px solid;}'>";
echo "<tr><td width='42px' bgcolor='#00AAAA'>Pseudo</td><td width='42px' bgcolor='#00AAAA'>CSStats</td><td width='42px' bgcolor='#00AAAA'>Kills</td><td width='42px' bgcolor='#00AAAA'>Deaths</td><td width='128px' bgcolor='#00AAAA'>Online Time</td></tr>";

while ($donnees = mysql_fetch_array($reponsess) )
{
$backcolor = "";
 if ( $donnees['Online'] == 1)
	{
	$backcolor="#00AA00";
	}
else
	{
	$backcolor="#AA0000";
	}



 echo "<tr>";
if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td class='tcl' bgcolor='".$backcolor."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td class='tcl' bgcolor='".$backcolor."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
	
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
	
 echo "<td width='42px' bgcolor='#00AA00'>".$donnees['SumKill']."</td>";
 echo "<td width='42px' bgcolor='#AA0000'>".$donnees['SumDeath']."</td>";
 
		$ConnecTimeM = round((($donnees['SumConnectTime'])%3600)/60);
		$ConnecTimeH = round($donnees['SumConnectTime'] / 3600);
 echo "<td bgcolor='#0000AA'>".$ConnecTimeH." Hs ".$ConnecTimeM." Ms</td>"; 
 
echo "</tr>";
}
echo "</table>";
?>

</body>
</html>