<html>
<HEAD>
<TITLE>
by_[MiSi]
</TITLE>
<table width="100%" border="1" cellspacing="2" cellpadding="1" bgcolor="" bordercolor="#000000">
<TR>
<TD>
<a href="http://bymisi.dyndns.org/cantar/uploadcs/guestbook.htm">
<font style="font-size:16px;color:yellow"><big><b>[Menu]
</a>
<a href="http://bymisi.dyndns.org:27015/">
<font style="font-size:16px;color:blue"><big><b>[Server]
</a>
<a href="http://bymisi.dyndns.org/cantar/uploadcs/admins.htm">
<font style="font-size:16px;color:cyan"><big><b>[Admins]
</a>
<a href="http://bymisi.dyndns.org/radio/csradio.php">
<font style="font-size:16px;color:green"><big><b>[Radio]
</a>
<a href="http://bymisi.dyndns.org/cantar/uploadcs/download.htm">
<font style="font-size:16px;color:red"><big><b>[download]
</a>
</font>
</TR>
</TD>
</TABLE>
<h1> Your Ip Address is: <strong>
<?php
        function getIP($type) { 
                 if (getenv("HTTP_CLIENT_IP") 
                     && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
                     $ip = getenv("HTTP_CLIENT_IP"); 
                 else if (getenv("REMOTE_ADDR") 
                     && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
                     $ip = getenv("REMOTE_ADDR"); 
                 else if (getenv("HTTP_X_FORWARDED_FOR") 
                     && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
                     $ip = getenv("HTTP_X_FORWARDED_FOR"); 
                 else if (isset($_SERVER['REMOTE_ADDR']) 
                     && $_SERVER['REMOTE_ADDR'] 
                     && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
                     $ip = $_SERVER['REMOTE_ADDR']; 
                 else { 
                     $ip = "unknown"; 
                 return $ip; 
                     } 
                 if ($type==1) {return md5($ip);} 
                 if ($type==0) {return $ip;} 
                 } 

echo getIP(0); 
?>
</h1>

</HEAD>

<body style="background-color:008888">

<br>

<iframe src="http://cache.www.gametracker.com/components/html0/?host=78.131.57.13:27015&bgColor=333333&fontColor=CCCCCC&titleBgColor=222222&titleColor=FF9900&borderColor=555555&linkColor=FFCC00&borderLinkColor=222222&showMap=1&currentPlayersHeight=400&showCurrPlayers=1&topPlayersHeight=100&showTopPlayers=0&showBlogs=0&width=240" frameborder="0" scrolling="no" width="240" height="800"></iframe>


</body>
</html>