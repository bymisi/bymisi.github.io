<?php
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("cache-control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

$base_shoutcast_url = 'http://cp.internet-radio.org.uk:15114';

echo <<<EOL
<table width="100%" border="1" cellspacing="2" cellpadding="1" bgcolor="" bordercolor="#000000">
<TR>
<TD>
<a href="http://bymisi.selfip.com/cantar/uploadcs/guestbook.htm">
<font style="font-size:16px;color:darkblue"><big><b>[Menu]
</a>
<a href="http://bymisi.selfip.com:27015/">
<font style="font-size:16px;color:darkblue"><big><b>[Server]
</a>
<a href="http://bymisi.selfip.com/cantar/uploadcs/admins.htm">
<font style="font-size:16px;color:darkblue"><big><b>[Admins]
</a>
<a href="http://bymisi.selfip.com/radio/csradio.php">
<font style="font-size:16px;color:darkblue"><big><b>[Radio]
</a>
<a href="http://bymisi.selfip.com/cantar/uploadcs/download.htm">
<font style="font-size:16px;color:darkblue"><big><b>[download CS1.6]
</a>
</font>
</TR>
</TD>
</TABLE>
<body background="cs2.jpg">


EOL;
?>