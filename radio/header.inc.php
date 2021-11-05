<?php
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("cache-control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
echo <<<EOL
<style type="text/css">
body {margin:0px; color:#fff; font-weight:bold;}
#bg_image {
width: 100%;
height: 100%;
left: 0px;
top: 0px;
position: absolute;
z-index: 0;
}
#contents {
z-index: 1;
position: absolute;
}
</style>

<div id="bg_image"> 
<body bgcolor="#000000" style="width: 100%; height: 100%;"> 
</div>

<div id="contents"> 

<table width="100%" border="1" cellspacing="2" cellpadding="1" bgcolor="" bordercolor="#000000">
<TR>
<TD>
<a href="http://bymisi.dyndns.org/cantar/uploadcs/guestbook.htm">
<font style="font-size:16px;color:red"><big><b>[Menu]
</a>
<a href="http://bymisi.dyndns.org/shared/cs/index.html">
<font style="font-size:16px;color:yellow"><big><b>[Server]
</a>
<a href="http://bymisi.dyndns.org/cantar/uploadcs/admins.htm">
<font style="font-size:16px;color:green"><big><b>[Admins]
</a>
<a href="http://bymisi.dyndns.org/radio/csradio.php">
<font style="font-size:16px;color:blue"><big><b>[Radio]
</a>
<a href="http://bymisi.dyndns.org/cantar/uploadcs/download.htm">
<font style="font-size:16px;color:white"><big><b>[download CS1.6]
</a>
</font>
</TR>
</TD>
</TABLE>
<body>
EOL;
?>