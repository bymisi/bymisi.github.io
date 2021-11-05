<html>
<body style="background-color:008888">
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
<br>
<a href="http://bymisi.selfip.com/cantar/uploadcs/guestbook.htm"><img border="0" src="http://tools.ip2location.com/ip2locationbig.png"/></a>


<!-- Start ip2location.com Link --> <table width="239" border="0" cellspacing="0" cellpadding="0"><tr><td><a href="http://www.ip2location.com"><img src="http://www.ip2location.com/images/searchboxtitle.gif" width="239" height="14" border="0"></a></td></tr><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td background="http://www.ip2location.com/images/searchboxbg.gif" align="center" valign="middle" width="108" height="112" ><table width="108" height="112" border="0" cellspacing="0" cellpadding="0"><tr><td  align="center" valign="middle"><form name="form1" method="post" action="http://www.ip2location.com/demo.aspx"><textarea name="ipaddresses" cols="15" rows="5" wrap="ON" style="font-family: Arial, Helvetica, sans-serif;font-size: 9px;color: #333333;text-decoration: none; border: 1px solid #A1D2FE;"></textarea><br><input type="image" src="http://www.ip2location.com/images/searchboxbutton.gif" name="submit" width="76" height="16"></form></td></tr></table></td><td width="131" height="112" valign="top"><a href="http://bymisi.selfip.com/cantar/uploadcs/guestbook.htm"><img src="http://www.ip2location.com/images/searchboxinfo.gif" width="131" height="112" border="0"></a></td></tr></table></td></tr></table><!-- End ip2location.com Link -->


<iframe src="http://cache.www.gametracker.com/components/html0/?host=78.131.57.13:27015&bgColor=333333&fontColor=CCCCCC&titleBgColor=222222&titleColor=FF9900&borderColor=555555&linkColor=FFCC00&borderLinkColor=222222&showMap=1&currentPlayersHeight=100&showCurrPlayers=1&topPlayersHeight=100&showTopPlayers=1&showBlogs=0&width=240" frameborder="0" scrolling="no" width="240" height="536"></iframe>
</html>