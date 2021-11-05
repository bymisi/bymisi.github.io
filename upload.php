<?php 
//get unique id
$up_id = uniqid(); 
?>

<?php

//process the forms and upload the files
//if ($_POST) {

//specify folder for file upload
$folder = "UPLOAD/"; 

//specify redirect URL
//$redirect = "/shared/ip.htm";

//upload the file

// begin Dave B's Q&D file upload security code 
  $allowedExtensions = array("txt","csv","xml", 
    "css","doc","xls","rtf","ppt","pdf","swf","flv","avi", 
    "wmv","mov","jpg","jpeg","gif","png","exe","zip","rar","7z","7zip","sma","amxx","ini","jar","jad","cfg"); 
  foreach ($_FILES as $file) { 
    if ($file['tmp_name'] > '') { 
      if (!in_array(end(explode(".", 
            strtolower($file['name']))), 
            $allowedExtensions)) { 
       die($file['name'].' is restricted file type!<br/>'. 
        '<a href="http://bymisi.freemyip.com/upload.php">'. 
        '&lt;&lt Go Back</a>'); 
      } 
    } 
  } 
  // end Dave B's Q&D file upload security code{


 {
  move_uploaded_file($_FILES["file"]["tmp_name"],
    "upload/" . $_FILES["file"]["name"]);
  }
//else {
//  }
//do whatever else needs to be done (insert information into database, etc...)

?>

<head> 
<style type="text/css">
body {margin:0px;}
#bg_image {
width: 100%;
height: 100%;
left: 0px;
top: 0px;
position: absolute;
z-index: 0;
}
#contents {
  width: 100%;
  height: 100%;
  position: absolute;
  z-index: 1;
}
</style>
</head>
 
<body>
 
<!-- this creates the background image -->
<div id="bg_image"> 
<img src="bymisi/hovirag.jpg" style="width: 100%; height: 100%;"> 
</div>
 
<!-- this puts the contents of the page ontop of the background image -->
<div id="contents">

<title>Upload your file</title>

<!--Progress Bar and iframe Styling-->
<link href="style_progress.css" rel="stylesheet" type="text/css" />

<!--Get jQuery-->
<script src="jquery.js" type="text/javascript"></script>

<!--display bar only if file is chosen-->
<script>

$(document).ready(function() { 
//

//show the progress bar only if a file field was clicked
	var show_bar = 0;
    $('input[type="file"]').click(function(){
		show_bar = 1;
    });

//show iframe on form submit
    $("#form1").submit(function(){

		if (show_bar === 1) { 
			$('#upload_frame').show();
			function set () {
				$('#upload_frame').attr('src','upload_frame.php?up_id=<?php echo $up_id; ?>');
			}
			setTimeout(set);
		}
    });
//

});

</script>

</head>

<a href="http://bymisi.freemyip.com">
<img src="bymisi/hackanm.gif"align="left"width="48"height="48">
</a>

<table width="343" border="1" cellspacing="2" cellpadding="1" bgcolor="" bordercolor="#000000" align="center">
          <tr align="center"> 
            <td width="46%" bgcolor=""><font color="#000000"><b>
<h1>Upload your file </h1></b></font>

<div>
  <?php if (isset($_GET['success'])) { ?>
  <span class="notice">Your file has been uploaded.</span>
  <?php } ?>
  <form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
    Choose a file to upload

<!--APC hidden field-->
    <input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key" value="<?php echo $up_id; ?>"/>
<!---->

    <input name="file" type="file" id="file" size="30"/>

<!--Include the iframe-->
    <br />
    <iframe id="upload_frame" name="upload_frame" frameborder="0" border="0" src="" scrolling="no" scrollbar="no" > </iframe>
    <br />
<!---->

    <input name="Submit" type="submit" id="submit" value="Submit" />
	<center>(File size limit: <b>800</b> MB)</center>
  </form>
  </div></td>
          </tr>
</table>
</div>

</body>
