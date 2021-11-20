<?php

Header ('Cache-Control: no-cache');

if (!isset ($_GET ['server']))
    $_GET ['server'] = 'global';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
html, body {
height: 100%;
margin: 0;
padding: 0;
}
img#bg {
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
}
#content {
position:relative;
width:100%;
height:100%;
z-index:1;
}
#content img {
margin-left: auto;
margin-right: auto;
display: block;
}
</style>
</head>

<body>
<img src="blackboard.png" id="bg" />
<div id="content"><img src="motdpic.php?server=<?php echo $_GET ['server']; ?>" /></div>
</body>
</html>
