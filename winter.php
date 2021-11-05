<?php
/*
Author: ricocheting.com
Creation Date: 5/11/2004
Last Update: 1/13/2008
Current Version: 1.2.1

Copyright 2004 ricocheting.com
*/


/*
This script is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/



// max image width or height allowed for thumbnail
$config['size'] = 320;

// jpeg thumbnail image quality
$config['imagequality'] = 70;

// rows of images per page
$config['rows'] = 2;

// columns of images per page
$config['cols'] = 3;

// max page numbers to show at once (so if you have 100 pages, it won't show links to all 100 at once)
$config['maxShow'] = 10;

// folder where full size images are stored (include trailing slash)
$config['fulls'] = "vfolders/winter/";

// folder where thumbnails are to be created (include trailing slash)
$config['thumbs'] = "thumbs/";




#-#############################################
# desc: prints out html for the table and the images found in directory
function PrintThumbs(){
	global $config;


// full directory error check
	if (!file_exists($config['fulls'])) { 
		Oops("Fullsize image directory <b>$config[fulls]</b> does not exist.");
	}

// thumb directory error check
	if (!file_exists($config['thumbs'])) { 
		if (!@mkdir($config['thumbs'], 0755)) {
			Oops("Thumbnail image directory <b>$config[thumbs]</b> does not exist and can not be created. Check directory write permissions.");
		}
	}

// get the list of all the fullsize images
   $imagelist = GetFileList($config['fulls']);

// number of and which images on current page
	$config['start'] = ($config['page']*$config['cols']*$config['rows']);
	$config['max'] = ( ($config['page']*$config['cols']*$config['rows']) + ($config['cols']*$config['rows']) );
	if($config['max'] > count($imagelist)){$config['max']=count($imagelist);}
	if($config['start'] > count($imagelist)){$config['start']=0;}


// start table
	echo '<table border="0" cellpadding="0" cellspacing="0" align="center" class="gallery">';
	echo "<tr>\n<td colspan=\"$config[cols]\" class=\"entries\">";
	
// "showing results"
	if ($config['max'] == "0"){echo "Showing results <b>0 - 0</b> of <b>0</b></td></tr>\n";}
	else{echo "Showing results <b>".($config['start']+1)." - $config[max]</b> of <b>".count($imagelist)."</b> entries</td>\n</tr>\n\n";}


	$column_counter = 1;

// start row
	echo "<tr>\n";

// for the images on the page
	for($i=$config['start']; $i<$config['max']; $i++){

		$thumb_image = $config['thumbs'].$imagelist[$i];
		$thumb_exists = file_exists($thumb_image);

		// create thumb if not exist
		if(!$thumb_exists){
			set_time_limit(30);
			if(!($thumb_exists = ResizeImageUsingGD("$config[fulls]$imagelist[$i]", $thumb_image, $config['size']))){
				Oops("Creating Thumbnail image of <strong>$config[fulls]$imagelist[$i]</strong> failed. Possible directory write permission errors.");
			}
		}

		$imagelist[$i] = rawurlencode($imagelist[$i]);


	#########################################################
	# print out the image and link to the page
	#########################################################
		echo '<td>';
			echo '<a href="'. $config['fulls'].$imagelist[$i] .'" title="'. $imagelist[$i] .'" target="_blank">'; 
			echo '<img src="'. $config['thumbs'].$imagelist[$i] .'" alt="'. $imagelist[$i] .'">';
			echo '</a>';
		echo '</td>'."\n";
	#########################################################


		//if the max columns is reached, start new col
		if(($column_counter == $config['cols']) && ($i+1 != $config['max'])){
			echo "</tr>\n\n<tr><td colspan=\"$config[cols]\" class=\"spacer\"></td></tr>\n\n<tr>\n";
			$column_counter=0;
		}
		$column_counter++;
	}//for(images on the page)


// if there are no images found
	if($config['start'] == $config['max']){
		echo "<td colspan=\"$config[cols]\" class=\"entries\">No Entries found</td>\n";
	}

// if there are empty "boxes" at the end of the row (ie; last page)
	elseif($column_counter != $config['cols']+1){
		echo "<td colspan=\"".($config['cols']-$column_counter+1)."\">&nbsp;</td>\n";
	}

// end row
	echo "</tr>\n\n";

// print out page number list
	echo "<tr>\n<td colspan=\"$config[cols]\" class=\"pagenumbers\">\n";
		GetPageNumbers(count($imagelist));
	echo "</td>\n</tr>\n\n";

// end table
	echo "</td></tr></table>\n";

}#-#PrintThumbs()


#-#############################################
# desc: gets the list of image files in the directory
# param: [optional] directory to look through
# returns: array with alphabetically sorted list of images
function GetFileList($dirname="."){
	global $config;
	$list = array(); 

	if ($handle = opendir($dirname)) {
		while (false !== ($file = readdir($handle))) {
			//this matches what type of files to get. jpeg, jpg, gif, png (ignoring case)
			if (preg_match("/\.(jpe?g|gif|png)$/i",$file)) { 
				$list[] = $file;
			} 
		}
		closedir($handle); 
	}
	sort($list);

	return $list;
}#-#GetFileList()


#-#############################################
# desc: resizes image using GD library
# param: ($fullFilename) filename of full size image ($thumbFilename) filename to save thumbnail as ($size) max width or height to resize to
# returns: true if image created, false if failed
function ResizeImageUsingGD($fullFilename, $thumbFilename, $size) {

	list ($width,$height,$type) = GetImageSize($fullFilename);

	if($im = ReadImageFromFile($fullFilename,$type)){
		//if image is smaller than the $size, show the original
		if($height <= $size && $width <= $size){
			$newheight=$height;
			$newwidth=$width;
		}
		//if image height is larger, height=$size, then calc width
		else if($height > $width){
			$newheight=$size;
			$newwidth=round($width / ($height/$size));
		}
		//if image width is larger, width=$size, then calc height
		else{
			$newwidth=$size;
			$newheight=round($height / ($width/$size));
		}

		$im2=ImageCreateTrueColor($newwidth,$newheight);
		ImageCopyResampled($im2,$im,0,0,0,0,$newwidth,$newheight,$width,$height);

		return WriteImageToFile($im2,$thumbFilename,$type);
	}

	return false;
}#-#ResizeImageUsingGD()


#-#############################################
# desc: reads the image from the server into memory depending on type
# param: ($filename) filename of image to create ($type) int of type. 1=gif,2=jpeg,3=png
# returns: image resource
function ReadImageFromFile($filename, $type) {
	$imagetypes = ImageTypes();

	switch ($type) {
		case 1 :
			if ($imagetypes & IMG_GIF){
				return ImageCreateFromGIF($filename);
			}
			else{Oops("File type <b>.gif</b> not supported by GD version on server");}
		break;

		case 2 :
			if ($imagetypes & IMG_JPEG){
				return ImageCreateFromJPEG($filename);
			}
			else{Oops("File type <b>.jpg</b> not supported by GD version on server");}
		break;

		case 3 :
			if ($imagetypes & IMG_PNG){
				return ImageCreateFromPNG($filename);
			}
			else{Oops("File type <b>.png</b> not supported by GD version on server");}
		break;

		default:
			Oops("Unknown file type passed to ReadImageFromFile");
		return 0;
	}
}#-#ReadImageFromFile()


#-#############################################
# desc: creates the new thumbnail image depending on type
# param: ($im) image resource ($filename) filename of image to create ($type) int of type. 1=gif,2=jpeg,3=png
# returns: true if created, false if failed
function WriteImageToFile($im, $filename, $type) {
	global $config;

	switch ($type) {
		case 1 :
			return ImageGIF($im, $filename);
		case 2 :
			return ImageJpeg($im, $filename, $config['imagequality']);
		case 3 :
			return ImagePNG($im, $filename);
		default:
			return false;
	}
}#-#WriteImageToFile()


#-#############################################
# desc: prints out the limited space page numbers
# param: number of entries
# returns: prints out text
function GetPageNumbers($entries) {
	global $config;

	$prev = "&laquo;Prev";
	$next = "Next&raquo;";

	$config['totalPages']=Ceil(($entries)/($config['cols']*$config['rows']));

// calculate how and what numbers to print
	$start=0; // starting image number
	$end=$config['totalPages']-1; // ending image number (total / number image on page)

	// cutoff size < page. or . page != last page (otherwise keep above values)
	if($config['maxShow'] < $config['page'] || (($config['cols']*$config['rows']*$config['maxShow'])< $entries) ){
		// if page >= cutoff size+1 -> start at page - cutoff size
		if($config['page'] >= ($config['maxShow']+1) && $config['page'] < $end-$config['maxShow']){ $start = $config['page']-$config['maxShow'];}
		elseif($end < $config['page']+$config['maxShow']+1 && $config['totalPages']-1 >= $config['maxShow']*2+1){$start = $config['totalPages']-1-$config['maxShow']*2;}
		else{$start=0;} // else start at 0
		
		// if page+cutoff+1 > number of pages total -> end= number of pages total
		if( $config['page']+$config['maxShow']+1 > $config['totalPages']-1 ){$end = $entries/($config['cols']*$config['rows']);}
		elseif($start == 0 && $end > $config['maxShow']*2){$end = $config['maxShow']*2;}
		elseif($start == 0 && $config['totalPages'] <= $config['maxShow']*2){$end = $config['totalPages']-1;}
		else{$end = ($config['page']+$config['maxShow']);} //end = page+cutoff+1
	}


// number of pages
	echo "Page ($config[totalPages]): \n";

// PREV
	if(($config['page']-1) >= 0){echo "<a href=\"$_SERVER[SCRIPT_NAME]?page=".($config['page']-1)."\">$prev</a>\n";}
	else{echo "$prev\n";}

// divide marker
	if($start > 0){echo " ... ";}
	else{echo " - ";}

// each of the actual numbers
	for($i=$start; $i<=$end ; $i++){
		if($config['page']==$i){echo "[".($i+1)."] \n";}
		else{echo "<a href=\"$_SERVER[SCRIPT_NAME]?page=$i\">".($i+1)."</a>\n";}
	}

// divide marker
	if(Ceil($end) < $config['totalPages']-1){echo " ... ";}
	else{echo " - ";}

// NEXT
	if(($config['page']+1) <= $config['totalPages']-1){echo "<a href=\"$_SERVER[SCRIPT_NAME]?page=".($config['page']+1)."\">$next</a>\n";}
	else{echo "$next\n";}

}#-#end GetPageNumbers()


#-#############################################
# desc: throw an error message
# param: [optional] any custom error to display
# returns: nothing, but exits and stops script
function Oops($msg) {
?>
<div style="width:450px;">
	<h3 style="margin:0px;">Error</h3>
	<?php echo $msg; ?>

	<hr style="height:1px;width:80%">
	Please hit the <a href="javaScript:history.back();"><b>back button</b></a> on your browser to try again.
</div>
<?php
exit;
}#-#Oops()


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<title> Photo Gallery </title>

<style type="text/css">

/* overall gallery table */
table.gallery{
	border-collapse: collapse;
	}

/* images in the gallery */
table.gallery img {
	border:0px;
	}

/* table cells in gallery */
table.gallery td {
	border:1px black solid;
	font-size:8pt;
	font-family:verdana;
	}

/*  "Showing results X - Y of Z entries" entry row */
table.gallery td.entries {
	text-align:right;
	padding:3px;
	}

/* spacer between each row of images */
table.gallery td.spacer {
	background-color:#E2E2E2;
	height:16px;
	}

/*  "Page (5): <<Prev  - [1] 2 3 4 5  - Next>>" pagenumber */
table.gallery td.pagenumbers {
	text-align:center;
	padding:3px;
	font-weight:bold;
	}

/* page number links */
table.gallery td.pagenumbers a {
	text-decoration:none;
	}

/* page number links:hover */
table.gallery td.pagenumbers a:hover {
	color:#3399FF;
	}

</style>

</head>
<body>


<h3 style="text-align:center;">winter 2010 by_[MiSi]</h3>
<div style="text-align:center;font-style:italic;">(click thumbnail to open in new window)</div>

<?php

// do not change any of these. used for internal purposes
$config['start']=0;
$config['max']=0;
$config['page']=isset($_GET['page'])?$_GET['page']:"0";

//#############################################
// print out the page with all the thumbnails
PrintThumbs();
//#############################################

?>


</body>
</html>
