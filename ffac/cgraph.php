<?php

include "forum/config.php";
mysql_connect($db_ffac_host, $db_ffac_username, $db_ffac_password);
mysql_select_db($db_ffac_name);

$ip = $_GET["ip"];
$dday = $_GET["day"];
$dmonth = $_GET["month"];
$dyear= $_GET["year"];

$id = $_GET["id"];
if ($id!="")
{
$reponsess = mysql_query("SELECT * FROM servers WHERE id='".$id."'");
$donnees = mysql_fetch_array($reponsess);
$ip = $donnees['ip'];
}

if ($dday=="")
	$dday=date("j");
if ($dmonth=="")
	$dmonth=date("n");
if ($dyear=="")
	$dyear=date("Y");
// This array of values is just here for the example.

    //$values = array("23","32","35","57","12",
                  //  "3","36","54","32","15",
                   // "43","24","30");
if ($ip=="")
$reponsess = mysql_query("SELECT SUM(connections) AS CENT,heures FROM servers_tpl WHERE annee='".$dyear."' AND mois='".$dmonth."' AND jours='".$dday."' GROUP BY annee, mois, jours, heures");
else
$reponsess = mysql_query("SELECT connections AS CENT,heures FROM servers_tpl WHERE annee='".$dyear."' AND mois='".$dmonth."' AND jours='".$dday."' AND ip='".$ip."'");
$i=0;
while ($donnees = mysql_fetch_array($reponsess) )
{
$i = $donnees["heures"];
$values[$i]=$donnees["CENT"];
}

    $columns  = 24;//count($values);
// Get the height and width of the final image

    $width = 480;
    $height = 300;

// Set the amount of space between each column

    $padding = 5;

// Get the width of 1 column

    $column_width = $width / $columns ;

// Generate the image variables

    $im        = imagecreate($width,$height);
    $gray      = imagecolorallocate ($im,0xcc,0xcc,0xcc);
    $gray_lite = imagecolorallocate ($im,0xee,0xee,0xee);
    $gray_dark = imagecolorallocate ($im,0x7f,0x7f,0x7f);
    $white     = imagecolorallocate ($im,0xff,0xff,0xff);
	$red	     = imagecolorallocate ($im,0xff,0x00,0x00);
    
// Fill in the background of the image

    imagefilledrectangle($im,0,0,$width,$height,$white);
    
    $maxv = 0;

// Calculate the maximum value we are going to plot

    for($i=0;$i<$columns;$i++)$maxv = max($values[$i],$maxv);
$maxv=$maxv+16;
// Now plot each column
        
    for($i=0;$i<$columns;$i++)
    {
        $column_height = (($height-32) / 100) * (( $values[$i] / $maxv) *100);

        $x1 = $i*$column_width;
        $y1 = $height-$column_height;
        $x2 = (($i+1)*$column_width)-$padding;
        $y2 = $height-32;

        imagefilledrectangle($im,$x1,$y1,$x2,$y2,$gray);
		if ($i==date("G"))
			imagefilledrectangle($im,$x1,$y1,$x2,$y2,$red);

// This part is just for 3D effect

		imagestring($im, 2, $x1, $y2+4,$i, $gray_dark); 
		imagestring($im, 2, $x1, $y1-16,$values[$i], $gray_dark); 
		
    }
imagestring($im, 2, 1, $height-14,"Connection graph powered by http://82.232.102.55/FFAC", $gray_dark);
imagestring($im, 2, 1, 1,"Connection of ".$ip. " day ".$dday." month ".$dmonth. " year ".$dyear, $gray_dark);
ImageColorTransparent ($im, $white);
// Send the PNG header information. Replace for JPEG or GIF or whatever

    header ("Content-type: image/png");
    imagepng($im);
?> 