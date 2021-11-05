<?php

$debug = false;
$hostname = isset($_GET['ip']) ? $_GET['ip'] : 'war3.freakz.ro';
$port = isset($_GET['port']) ? $_GET['port'] : 27015;

include ('Includes/LiveStats.class.php');
include ('Includes/Smarty.class.php');

$smarty = new Smarty;

$smarty->compile_check = true;
$smarty->debugging = false;
$smarty->template_dir = './Templates/';
$smarty->compile_dir = './Compiled/';
$smarty->config_dir = './Configs/';
$smarty->cache_dir = './Cache/';

$smarty->assign('PATH_TO_TEMPLATE', 'Templates');
$smarty->assign('PAGE_TITLE', 'FREAKZ.RO');
$smarty->assign('FORM_ACTION', '');
$smarty->assign('FORM_METHOD', 'GET');
$smarty->assign('HOST_NAME', 'ip');
$smarty->assign('HOST_VALUE', $hostname);
$smarty->assign('PORT_NAME', 'port');
$smarty->assign('PORT_VALUE', $port);

try {
	$stats = new LiveStats($hostname, $port);
	$server = $stats->GetServer();
	
	$smarty->assign('HOST_DOWN', false);
	$smarty->assign('GAME_TYPE', $server->Type);
	$smarty->assign('SERVER_HOSTNAME', $server->Hostname);
	$smarty->assign('SERVER_IP', $server->Address);
	$smarty->assign('SERVER_PAROLA', $server->PasswordProtected ? 'Da' : 'Nu');
	$smarty->assign('SERVER_PLAYERS', $server->PlayerCount . ' / ' . $server->MaxPlayers);
	$smarty->assign('SERVER_MAP', $server->Map);
	if ($server->Type == 1) {
		$smarty->assign('SERVER_MOD', $server->Description);
	} else {
		$smarty->assign('SERVER_MOD', $server->GameMode);
	}
	$smarty->assign('SERVER_PLAYER_STATS', $server->Players);
} catch (LSError $e) {
	$smarty->assign('SERVER_DOWN', true);
	$smarty->assign('STATS_DEBUG', $debug);
	$smarty->assign('STATS_ERROR', "Error [{$e->ErrorCode}]: {$e->ErrorMessage}");
}

$smarty->display('index.html');

?>