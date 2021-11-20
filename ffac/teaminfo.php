<?php mysql_select_db($db_name);
$reponsess = mysql_query("SELECT icq FROM users WHERE id='".$pun_user['id']."'");
$donnees = mysql_fetch_array($reponsess);
$SteamID= $donnees['icq'];
mysql_select_db($db_ffac_name);
$reponsess = mysql_query("SELECT id FROM teams WHERE teammanager='".$pun_user['id']."' AND id='".$_GET["tid"]."'");
$isteamowner=mysql_fetch_array($reponsess);
 ?>
<?php
mysql_select_db($db_ffac_name);


$editable=0;
if ($isteamowner && $_GET["edit"]) $editable=1;

if ($isteamowner && $_GET["Update"]=="Update")
{
mysql_query("UPDATE teams SET name='".$_GET["teamname"]."' WHERE id=".$_GET["tid"]."");
mysql_query("UPDATE teams SET tag='".$_GET["tag"]."' WHERE id=".$_GET["tid"]."");
mysql_query("UPDATE teams SET site='".$_GET["website"]."' WHERE id=".$_GET["tid"]."");
mysql_query("UPDATE teams SET leader='".$_GET["contact"]."' WHERE id=".$_GET["tid"]."");
}
$reponsess = mysql_query("SELECT * FROM teams WHERE id='".$_GET["tid"]."'");
$reponsesss = mysql_query("SELECT * FROM players WHERE team='".$_GET["tid"]."' ORDER BY Skill DESC LIMIT 0,20");
$donnees = mysql_fetch_array($reponsess);

?>			
			<table border=0>
			<tr><td>
			<div class="box">
					<h2><span>Team Info</span></h2>
						<div id="teaminfo" class="box">
							<div class="inbox">
<?php
if ($editable)
{
echo "<FORM>";
echo '<INPUT TYPE=hidden NAME="page" VALUE="teaminfo">';
echo '<INPUT TYPE=hidden NAME="tid" VALUE="'.$_GET["tid"].'">';
}


echo "<table>";
	echo "<tr>";
		echo "<td>";
		echo "Name";
		echo "</td>";
		echo "<td>";
		if ($editable)
			echo '<INPUT TYPE=text NAME="teamname" VALUE="'.$donnees['name'].'">';
		else
			echo $donnees['name'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Tag";
		echo "</td>";
		echo "<td>";
		if ($editable)
			echo '<INPUT TYPE=text NAME="tag" VALUE="'.$donnees['tag'].'">';
		else
			echo $donnees['tag'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Website";
		echo "</td>";
		echo "<td>";
		if ($editable)
			echo '<INPUT TYPE=text NAME="website" VALUE="'.$donnees['site'].'">';
		else
			echo "<a href='".$donnees['site']."'>".$donnees['site']."</a>";
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Contact";
		echo "</td>";
		echo "<td>";
		if ($editable)
			echo '<INPUT TYPE=text NAME="contact" VALUE="'.$donnees['leader'].'">';
		else
			echo $donnees['leader'];
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>";
		echo "Skill";
		echo "</td>";
		echo "<td>";
		echo $donnees['Skill'];
		echo "</td>";
	echo "</tr>";
if ($editable)
{
echo "<tr><td colspan=2>";
echo "<center><INPUT TYPE=submit NAME=Update VALUE='Update'></center>";
echo "</td></tr>";
//echo "</FORM>";
}
echo "</table>";
?>
							</div>
						</div>
			</div></td><td>
	<div class="box">
					<h2><span>Team Players</span></h2>
						<div id="infoplayers" class="box">
							<div class="inbox">
<?php


echo "<table border=0>";
echo "<tr>";
echo "<td width='42px'>Skill</td>";
echo "<td>Nickname</td>";
echo "<td width='42px'>Kill</td>";
echo "<td width='42px'>Death</td>";
echo "<td width='78px'>Last Server</td>";
echo "<td width='42px'>Status</td>";
echo "</tr>";
$compte=0;
while ($donnees = mysql_fetch_array($reponsesss) )
{
if ($compte<5)
	$classc="online";
else
	$classc="offline";
$compte=$compte+1;
 
 echo "<tr>";
 echo "<td width='42px' class='".$classc."'>".$donnees['Skill']."</td>";
 
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td ><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td ><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
	 echo "<td width=''>".$donnees['Ckill']."</td>";
	 echo "<td width=''>".$donnees['Death']."</td>";

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
	
	
if ($donnees['online']==0)
	{
		echo "<td width='28px' class='offline'>Offline</td>";
	}
else
	{
		echo "<td width='28px' class='online'>Online</td>";
	}	
	
 echo "</tr>";
}


echo "</table>";

 ?>
 
 </div>
						</div>
			</div>

			</td></tr>
			</table>