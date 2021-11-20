<html>
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

<body style="background-color:008888">
<a href="http://bymisi.dyndns.org/ip"><img border="0" src="http://tools.ip2location.com/ip2locationbig.png"/></a>
<br>
<iframe src="http://baremetal.com/cgi-bin/dnsip">
</iframe>

<br>

<iframe src="http://cache.www.gametracker.com/components/html0/?host=78.131.57.13:27015&bgColor=333333&fontColor=CCCCCC&titleBgColor=222222&titleColor=FF9900&borderColor=555555&linkColor=FFCC00&borderLinkColor=222222&showMap=1&currentPlayersHeight=200&showCurrPlayers=1&topPlayersHeight=100&showTopPlayers=0&showBlogs=0&width=240" frameborder="0" scrolling="no" width="240" height="470"></iframe>

</body>
</html>