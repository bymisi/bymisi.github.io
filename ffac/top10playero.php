<?php
mysql_select_db($db_ffac_name);

$reponsess = mysql_query("SELECT players.*,players.Online,teams.tag FROM players INNER JOIN teams ON players.team = teams.id WHERE Online='1' ORDER BY players.Skill DESC LIMIT 0,10");

echo "<table border=0 height=364>";
echo "<tr><td width='42px'>Skill</td><td>Nickname</td><td width='48px'>Team</td></tr>";

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
 echo "<td width='42px' class='".$classc."'>".$donnees['Skill']."</td>";
 
 if ( $donnees['Pseudo'] == '')
	{ 
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['SID']."</a></td>";
	}
 else
	{
	 echo "<td  class='".$classc."'><a href='index.php?page=playerinfo&pid=".$donnees['id']."'>".$donnees['Pseudo']."</a></td>";
	}
if ($donnees['team']==0)
			{
				echo "<td  width='48px'>X</td>";
			}
		else
			{
				echo "<td width='48px'><a href = 'index.php?page=teaminfo&tid=".$donnees['team']."'>".$donnees['tag']."</a></td>";
			}
 echo "</tr>";
}

echo "</table>";

?>