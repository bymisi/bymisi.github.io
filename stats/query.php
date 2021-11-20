<?php

require ('config.php');
require ('class_PQ.php');

if (file_exists ($cachefile) && (time () - filemtime ($cachefile) < $cachelifetime)) {
    
    readfile ($cachefile);    
    exit;
    
}

$out = '';

foreach ($servers as $server) {
    
    $pq = PQ::create(array(
    	'ip' 		=> $server['ip'],
    	'port'		=> $server['port'],
    	'querytype'	=> 'halflife',
    	'timeout' 	=> 1,
    	'retries' 	=> 1,
    ));
    
    $pqinfo = $pq->query(array('info'));
    
    $arr = array ('#name#' => $server ['name'],
                  '#ip#'   => $server ['ip'],
                  '#port#' => $server ['port'],
                  
                  '#map#'        => $pqinfo ['map'],
                  '#players#'    => $pqinfo ['totalplayers'],
                  '#maxplayers#' => $pqinfo ['maxplayers']);
    
    $out .= strtr ($output, $arr);
    
}

if ($cachelifetime != 0) {

    $fp = fopen ($cachefile, 'w');
    fwrite ($fp, $out);
    fclose ($fp);
    
}

echo $out;

?>
