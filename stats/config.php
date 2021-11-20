<?php

$servers = array (array ('name' => 'Server #1 [TF2 - All maps]',
                         'ip' => '85.17.60.96',
                         'port' => 27025),
                  array ('name' => 'Server #2 [TF2 - All maps]',
                         'ip' => '85.17.60.96',
                         'port' => 27035),
                  array ('name' => 'Server #3 [TF2 - All maps]',
                         'ip' => '85.17.60.102',
                         'port' => 27015),
                  array ('name' => 'Server #4 [TF2 - Dustbowl]',
                         'ip' => '80.193.86.252',
                         'port' => 27015));

$cachelifetime = 30;
$cachefile = './cache/servers.html';

$output = '<a href ="steam://connect/#ip#:#port#">#name#</a><br />#map# #players#/#maxplayers#<br /><br />';

?>
