<?php
mysql_select_db($db_ffac_name);

$reponsess = mysql_query("SELECT ip,ipbind,hostname,skill FROM servers ORDER BY skill DESC LIMIT 0,10");

echo "<table border=0 height=364>";
echo "<tr><td width='38px'>Skill</td><td>Name</td><td width='26px'>Join</td></tr>";
while ($donnees = mysql_fetch_array($reponsess) )
{
 echo "<tr>";
 echo "<td width='38px'>".$donnees['skill']."</td>";
  // if ($donnees['ip']=="192.168.0.69:27015")
	//  {
 //echo "<td class='tcl'>82.232.102.55:27015</td>";
	//  }
	//  else
	//  {
 //echo "<td class='tcl'>".$donnees['ip']."</td>";
	//  }
 //echo "<td width=''>".$donnees['ip']."</td>";
 echo "<td width=''><a href=index.php?page=serverinfo&ipserv=".$donnees['ip'].">".$donnees['hostname']."</td></a>";
  if ($donnees['ip']=="192.168.0.69:27015")
	  {
	  echo '<td  width=""><a href="steam: &quot;-applaunch 10 -game cstrike +connect 82.232.102.55:27015&quot;">Join</a>';
	 // echo "<a href=index.php?page=serverinfo&ipserv=".$donnees['ip'].">Info</a></td>";	
	  }
  else
	  {
	  echo '<td width=""><a href="steam: &quot;-applaunch 10 -game cstrike +connect '.$donnees['ipbind'].'&quot;">Join</a>';
	 // echo "<a href=index.php?page=serverinfo&ipserv=".$donnees['ip'].">Info</a></td>";	
	  }
 echo "</tr>";
}
echo "</table>";

?>