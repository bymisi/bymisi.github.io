<?php mysql_select_db($db_name);
$reponsess = mysql_query("SELECT icq FROM users WHERE id='".$pun_user['id']."'");
$donnees = mysql_fetch_array($reponsess);
$SteamID= $donnees['icq'];
mysql_select_db($db_ffac_name); ?>
<div class="box">
		<h2><span>Join a Group </span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<br></br>
<a href="index.php?page=groups"><B>Join an existing group</B></a>
<br></br>
</div>
</div>
<div class="box">
		<h2><span>Manage your Groups</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<br></br>
<?php
echo "<table>";
 echo "<tr>";
 echo "<td>Name</td>";
echo "<td>Action</td>";
 echo "</tr>";
$reponsess = mysql_query("SELECT * FROM groups WHERE owner='".$pun_user['id']."'");
while ($donnees = mysql_fetch_array($reponsess) )
{
 echo "<tr>";
 echo "<td>".$donnees['Name']."</td>";
 echo "<td>Delete</td>";
 echo "</tr>";
}


 echo "</table>";
 ?>
<br></br>
<br></br>
</div>
</div>
<div class="box">
		<h2><span>Create a group</span></h2>
			<div id="playerinfo" class="box">
				<div class="inbox">
<?php if ($pun_user['is_guest']) { } else {?>
<?php
if ($_GET["actiongp"]=="join")
{
$reponsess = mysql_query("INSERT INTO groups( id, name, Link, owner,".$_GET["targetlink"]." ) VALUES( '', 'Link', '".$_GET["targetid"]."', '".$pun_user['id']."', '".$_GET["targetlinid"]."' )");
echo "<strong> Groupe link".$_GET["targetlinid"]." created ! </strong>";



}
?>
<?php if ($_GET["groupname"]=="") { ?>
<FORM>
<table>
<tr>
<INPUT TYPE=hidden NAME="page" VALUE="groupsse">
<td><B>Team Name</B></td>
<td><INPUT TYPE=text NAME="groupname" VALUE="" SIZE=40></td>
</tr>
</table>
<center><INPUT TYPE=submit NAME=Create VALUE="Create"></center>
<?php } 
else
	{
	$reponsess = mysql_query("INSERT INTO groups( id, name, Link, owner) VALUES( '', '".$_GET["groupname"]."', '0', '".$pun_user['id']."' )");
	echo "<strong> Groupe ".$_GET["groupname"]." created ! </strong>";
	} ?>
<?php } ?>
</div>
</div>