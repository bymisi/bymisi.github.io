<?php

require ('class_PQ.php');
require ('./config.php');

$as = str_replace (':', '_', $_GET ['server']);

$cache = ($cachelifetime > 0);

if ($cache) {

    if (substr($cachedir, -1) != '/') $cachedir .= '/';
    $cachefile = $cachedir . 'motd_server_' . str_replace(':', '_', $as) . '.png';
    
    if (file_exists ($cachefile) && (time () - filemtime ($cachefile) < $cachelifetime)) {
    
        Header ('Content-type: image/png');
        readfile ($cachefile);
        
        exit;
        
    }
    
}

$x = $xstart;
$y = $ystart;

$img = imagecreatefrompng ($image);
    
imagealphablending ($img, true);
imagesavealpha ($img, true);

foreach ($servers as $server) {
    
    $pq = PQ::create(array(
    	'ip' 		=> $server['ip'],
    	'port'		=> $server['port'],
    	'querytype'	=> 'halflife',
    	'timeout' 	=> $timeout, 
    	'retries' 	=> $retries,
    ));
    
    $pqinfo = @$pq->query(array('info'));
    
    $placeholders = array ('#name#' => $server ['name'],
                           '#ip#'   => $server ['ip'],
                           '#port#' => $server ['port']
                          );
                              
    $placeholders ['#map#'] = (isset ($pqinfo ['map'])) ? $pqinfo ['map'] : ''; 
    $placeholders ['#players#'] = (isset ($pqinfo ['totalplayers'])) ? $pqinfo ['totalplayers'] : '';
    $placeholders ['#maxplayers#'] = (isset ($pqinfo ['maxplayers'])) ? $pqinfo ['maxplayers'] : '';
    
    if ($nextmap != '') {
        
        $res = @$pq -> rcon ($nextmap, $server ['pass']);
        
        if (preg_match ('/"[^"]+" = "([^"]+)"/', $res, $arr))
            $placeholders ['#nextmap#'] = $arr [1];
        else
            $placeholders ['#nextmap#'] = '';
        
    }
    
    if (isset ($timeleft) && is_array ($timeleft)) {
        
        $res = @$pq -> rcon ($timeleft ['command'], $server ['pass']);
        
        $match = false;
        
        foreach ($timeleft ['outputs'] as $regexp => $out) {
            
            if (preg_match ('@' . $regexp . '@' , $res, $arr)) {
                
                $trtable = array ();
                
                for ($i = 0, $j = count ($arr); $i < $j; $i++) {
                    
                    $trtable ['#' . $i . '#'] = $arr [$i];
                    
                }
                
                $placeholders ['#timeleft#'] = strtr ($out, $trtable);
                
                $match = true;
                break;
                
            }
            
        }
        
        if (!$match) {
            
            $placeholders ['#timeleft#'] = $timeleft ['default'];
            
        }
        
    }
    
    if (count ($pqinfo) == 0) {
        
        if (isset ($templates_down))
            $templates = &$templates_down;
        else
            continue;
        
    } else if ($server['ip'] . '_' . $server['port'] == $as && isset ($templates_active))    
        $templates = &$templates_active;
    else
        $templates = &$templates_default;
    
    
    foreach ($templates as $template) {
        
        $c = explode (' ', $template ['color']); 
        
        imagettftext ($img,
                      $template ['size'], 0,
                      $x + $template ['x'], $y + $template ['y'],
                      imagecolorallocate ($img, $c [0], $c [1], $c [2]),
                      $template ['font'],
                      strtr ($template ['text'], $placeholders)
                     );
        
    }
    
    
    $x += $xstep;
    $y += $ystep;
    
}

if ($cache)
    imagepng ($img, $cachefile);

Header ('Cache-Control: no-cache');
Header ('Content-type: image/png');
imagepng ($img);

?>
