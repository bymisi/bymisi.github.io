<?php
mysql_select_db($db_ffac_name);

$reponses = mysql_query("SELECT * FROM logmsg WHERE SID='".$SIDp."' ORDER BY id DESC");
echo "<table border=0>";
while ($donnees = mysql_fetch_array($reponses) )
{
echo "<tr>";
echo "<td width='128px'>".$donnees['MsgDate']."</td><td>".$donnees['MsgLog']."</td>";
echo "</tr>";
}
echo "</table>";
?>