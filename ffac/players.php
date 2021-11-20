<div class="box">
		<h2><span>Player Search</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<center>
<form method="get" action="index.php">
<input type="hidden" name="page" value="playerrank">
Nickname
<input type="text" name="RName">
SID
<input type="text" name="RSID">
<INPUT TYPE=submit NAME=search VALUE="search">
</form>
</center>
</div></div></div>				


<div class="box">
		<h2><span>Player List</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">

<?php
mysql_select_db($db_ffac_name);

$pagenum=$_GET["pagenum"];
$classement=$_GET["classem"];
if ($classement=="") $classement="skill";

if ($pagenum=='')
{
$pagenum=0;
}
/*if ($_GET["classem"]=="") 
{
$reponsess = mysql_query("SELECT players.*, teams.tag FROM players INNER JOIN teams ON players.team = teams.id ORDER BY players.Skill DESC LIMIT ".($pagenum*100).",100 ");
$reponsesss = mysql_query("SELECT * FROM players");
}
else*/
{
$reponsess = mysql_query("SELECT players.*, teams.tag FROM players INNER JOIN teams ON players.team = teams.id WHERE players.Pseudo LIKE '%".$_GET["RName"]."%' AND players.SID LIKE '%".$_GET["RSID"]."%' ORDER BY players.".$classement." DESC LIMIT ".($pagenum*100).",100 ");
$reponsesss = mysql_query("SELECT count(*) AS PCount FROM players");
}
echo "<table border=0>";
echo "<tr>";
echo "<td width='42px'><a href='index.php?page=playerrank&classem=Skill'>Skill</a></td>";
echo "<td><a href='index.php?page=playerrank&classem=Pseudo'>Nickname</a></td>";
echo "<td width='42px'><a href='index.php?page=playerrank&classem=Ckill'>Kill</a></td>";
echo "<td width='42px'><a href='index.php?page=playerrank&classem=Death'>Death</a></td>";
echo "<td width='42px'><a href='index.php?page=playerrank&classem=csstatscore'>Cstats</a></td>";
echo "<td width='78px'><a href='index.php?page=playerrank&classem=server'>Last Server</a></td>";
echo "<td width='48px'><a href='index.php?page=playerrank&classem=team'>Team</a></td>";
echo "<td width='42px'><a href='index.php?page=playerrank&classem=online'>Status</a></td>";
echo "</tr>";
while ($donnees = mysql_fetch_array($reponsess) )
{
 echo "<tr>";
 echo "<td width='42px'>".$donnees['Skill']."</td>";
 
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td  bgcolor='".$backcolor."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td  bgcolor='".$backcolor."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
	 echo "<td width=''>".$donnees['Ckill']."</td>";
	 echo "<td width=''>".$donnees['Death']."</td>";
	 echo "<td width=''>".$donnees['csstatscore']."</td>";

if ($donnees['Server']=="192.168.0.69:27015")
{
echo '<td width="78px"><a href="steam: &quot;-applaunch 10 -game cstrike +connect 82.232.102.55:27015&quot;">Join</a> :: ';	
echo "<a href=index.php?page=serverinfo&ipserv=".$donnees['Server'].">Info</a></td>";
}
else
{
echo '<td width=""><a href="steam: &quot;-applaunch 10 -game cstrike +connect '.$donnees['Server'].'&quot;">Join</a> :: ';	
echo "<a href=index.php?page=serverinfo&ipserv=".$donnees['Server'].">Info</a></td>";	
}
	
if ($donnees['team']==0)
	{
		echo "<td  width='48px'>X</td>";
	}
else
	{
		echo "<td  width='48px'><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['tag']."</a></td>";
	}
	
if ($donnees['online']==0)
	{
		echo "<td  width='28px' class='offline'>Offline</td>";
	}
else
	{
		echo "<td   width='28px' class='online'>Online</td>";
	}	
	
 echo "</tr>";
}


echo "</table>";
$pagecount = $reponsesss['PCount']/100;


if ($pagenum>0)
{
echo "<a href=index.php?page=playerrank&pagenum=".($pagenum-1).">Previous</a>  ";
}
if ($pagenum<$pagecount)
{
echo "<a href=index.php?page=playerrank&pagenum=".($pagenum+1).">Next</a>  ";
}
echo "Page : ";
echo  $pagenum." / ".$pagecount;
?>
</div></div></div>