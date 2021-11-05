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


echo "<h3><span>Top CStats Online (".$_GET["ids"].")</span></h3>";
echo "<h3><span>FFAC : http://82.232.102.55/FFAC</span></h3>";
$reponsess = mysql_query("SELECT ip FROM servers WHERE id='".$_GET["ids"]."'");
$dataa  = mysql_fetch_array($reponsess);
$ipserv = $dataa['ip'];
$reponsess = mysql_query("SELECT players.*,players.Online,teams.tag FROM players INNER JOIN teams ON players.team = teams.id WHERE Server='".$ipserv."' AND Online=1 ORDER BY csstatscore DESC");
echo "<table border=0 style='background-color:#383838;color: white;font-family:tahoma,arial;font-size:10px;border:#565656 1px solid;}'>";
echo "<tr><td width='42px' bgcolor='#00AAAA'>CStats</td><td width='42px' bgcolor='#00AAAA'>Kills</td><td width='42px' bgcolor='#00AAAA'>Deaths</td><td width='116px' bgcolor='#00AAAA'>Online Time</td><td bgcolor='#00AAAA'>Nickname</td><td bgcolor='#00AAAA'>Team</td></tr>";

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
 if ($donnees['csstatscore']>0)
 {
	echo "<td width='42px' bgcolor='#00AA00'>".$donnees['csstatscore']."</td>";
 }
 if ($donnees['csstatscore']<0)
  {
	echo "<td width='42px' bgcolor='#AA0000'>".$donnees['csstatscore']."</td>";
 }
  if ($donnees['csstatscore']==0)
  {
	echo "<td width='42px' bgcolor='#0000AA'>".$donnees['csstatscore']."</td>";
 }
 
 echo "<td width='42px' bgcolor='#00AA00'>".$donnees['Ckill']."</td>";
 echo "<td width='42px' bgcolor='#AA0000'>".$donnees['Death']."</td>";
		$ConnecTimeM = round((($donnees['ConnecTime'])%3600)/60);
		$ConnecTimeH = round($donnees['ConnecTime'] / 3600);
 echo "<td bgcolor='#0000AA'>".$ConnecTimeH." Hs ".$ConnecTimeM." Ms</td>";
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td class='tcl' bgcolor='#AAAA00'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td class='tcl' bgcolor='#AAAA00'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
if ($donnees['team']==0)
	{
		echo "<td bgcolor='#0000AA' >X</td>";
	}
else
	{
		echo "<td bgcolor='#00AA00'><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['tag']."</a></td>";
	}
echo "</tr>";
}
echo "</table>";
?>

</body>
</html>