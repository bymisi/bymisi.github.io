<?php mysql_select_db($db_name);
$reponsess = mysql_query("SELECT icq FROM users WHERE id='".$pun_user['id']."'");
$donnees = mysql_fetch_array($reponsess);
$SteamID= $donnees['icq'];
mysql_select_db($db_ffac_name); ?>
<div class="box">
		<h2><span>Join a Team </span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<br></br>
<a href="index.php?page=teamrank"><B>Join an existing Team</B></a>
<br></br>
</div>
</div>
<div class="box">
		<h2><span>Manage your Team</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<br></br>
<?php
if ($_GET["teamaddid"]!="" && $SteamID!="")
{
if ($_GET["teamaddid"]==0)
{
	echo "<strong>You have leave a Team</strong>";
	mysql_query("UPDATE players SET team='0' WHERE SID='".$SteamID."'");
}
else
{
	echo "<strong>You have join a new team !!</strong>";
	mysql_query("UPDATE players SET team='".$_GET["teamaddid"]."' WHERE SID='".$SteamID."'");
}
}

echo "<table>";
 echo "<tr>";
 echo "<td>ID</td>";
 echo "<td>Status</td>";
 echo "<td>Tag</td>";
 echo "<td>Name</td>";
 echo "<td>Skill</td>";
 echo "<td>Actions</td>";
 echo "</tr>";
$reponsess = mysql_query("SELECT * FROM teams WHERE teammanager='".$pun_user['id']."'");
while ($donnees = mysql_fetch_array($reponsess) )
{
 echo "<tr>";
 echo "<td>".$donnees['id']."</td>";
 echo "<td  bgcolor='#0000AA'>Owner</td>";
 echo "<td>".$donnees['tag']."</td>";
 echo "<td>".$donnees['name']."</td>";
 echo "<td>".$donnees['Skill']."</td>";
 echo "<td><a href='index.php?page=teaminfo&tid=".$donnees['id']."&edit=1'>Info & Manage</td>";
 echo "</tr>";
}
$reponsess = mysql_query("SELECT * FROM players WHERE SID='".$SteamID."'");
$donnees = mysql_fetch_array($reponsess) ;
$reponsess = mysql_query("SELECT * FROM teams WHERE id='".$donnees['team']."'");
$donneess = mysql_fetch_array($reponsess) ;
 echo "<tr>";
 echo "<td>".$donnees['team']."</td>";
 echo "<td bgcolor='#00AA00'>Player</td>";
 echo "<td>".$donneess['tag']."</td>";
 echo "<td>".$donneess['name']."</td>";
 echo "<td>".$donneess['Skill']."</td>";
 echo "<td><a href='index.php?page=teaminfo&tid=".$donnees['team']."'>Info</a> :: <a href=index.php?page=team&teamaddid=0>Leave</a></td>";
 echo "</tr>";


echo "</table>";
?>
<br></br>
<br></br>
</div>
</div>
<div class="box">
		<h2><span>Create a Team</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<?php if ($pun_user['is_guest']) { } else {?>
<br></br>
<?php if ($_GET["teamname"]=="") { ?>
<FORM>
<table>
<tr>
<INPUT TYPE=hidden NAME="page" VALUE="team">
<td><B>Team Name</B></td>
<td><INPUT TYPE=text NAME="teamname" VALUE="" SIZE=40></td>
</tr>
<tr>
<td><B>Tag</B></td>
<td><INPUT TYPE=text NAME="tag" VALUE="" SIZE=40></td>
<tr>
</tr>
<tr>
<td><B>Website</B></td>
<td><INPUT TYPE=text NAME="website" VALUE="" SIZE=40></td>
</tr>
<tr>
<td><B>Team Public contact</B></td>
<td><INPUT TYPE=text NAME="contact" VALUE="" SIZE=40></td>
</tr>
</table>
<center><INPUT TYPE=submit NAME=Create VALUE="Create"></center>
<br></br>
<?php }
else
	{
	$reponsess = mysql_query("INSERT INTO teams( id, tag, name, site, leader, teammanager) VALUES( '', '".$_GET["tag"]."', '".$_GET["teamname"]."', '".$_GET["website"]."', '".$_GET["contact"]."', '".$pun_user['id']."' )");
	echo "<strong> Team ".$_GET["teamname"]." created ! </strong>";
	} ?>

<?php } ?>
</div>
</div>