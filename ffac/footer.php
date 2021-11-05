<?php
// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<pun_main>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_main>

// START SUBST - <pun_footer>
ob_start();

?>
<div id="brdfooter" class="block">
	<h2><span><?php echo $lang_common['Board footer'] ?></span></h2>
	<div class="box">
		<div class="inbox">
<?php

// If no footer style has been specified, we use the default (only copyright/debug info)
$footer_style = isset($footer_style) ? $footer_style : NULL;


// Display debug info (if enabled/defined)

	if ($pun_user['is_guest'])
		$tpl_temp = '<div id="brdwelcome" class="inbox">'."".'<p>'.$lang_common['Not logged in'].'</p>'."".'</div>';
	else
	{
		$tpl_temp = '<div id="brdwelcome" class="inbox">'."".'<ul class="conl">'."".''.$lang_common['Logged in as'].' <strong>'.pun_htmlspecialchars($pun_user['username']).'</strong>'."".'	::	'.$lang_common['Last visit'].': '.format_time($pun_user['last_visit']).'';

		if ($pun_user['g_id'] < PUN_GUEST)
		{
			$result_header = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'reports WHERE zapped IS NULL') or error('Unable to fetch reports info', __FILE__, __LINE__, $db->error());


			if ($pun_config['o_maintenance'] == '1')
				$tpl_temp .= "\n\t\t\t\t".'<li class="maintenancelink"><strong><a href="admin_options.php#maintenance">Maintenance mode is enabled!</a></strong></li>';
		}


		$tpl_temp .= "\n\t\t".'</ul>'."\n\t\t".'<div class="clearer"></div>'."\n\t\t".'</div>';
	}
	echo $tpl_temp	;
	
	
	
	list($usec, $sec) = explode(' ', microtime());
	$time_diff = sprintf('%.3f', ((float)$usec + (float)$sec) - $pun_start);
	echo "\t\t\t".'<p class="conr">[ Generated in '.$time_diff.' seconds, '.$db->get_num_queries().' queries executed ]</p>'."\n";


?>
			<div class="clearer"></div>
		</div>
	</div>
</div>
<?php


// End the transaction
$db->end_transaction();

// Display executed queries (if enabled)
if (defined('PUN_SHOW_QUERIES'))
	display_saved_queries();

$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<pun_footer>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <pun_footer>


// START SUBST - <pun_include "*">
while (preg_match('#<pun_include "([^/\\\\]*?)">#', $tpl_main, $cur_include))
{
	if (!file_exists(PUN_ROOT.'include/user/'.$cur_include[1]))
		error('Unable to process user include &lt;pun_include "'.htmlspecialchars($cur_include[1]).'"&gt; from template main.tpl. There is no such file in folder /include/user/');

	ob_start();
	include PUN_ROOT.'include/user/'.$cur_include[1];
	$tpl_temp = ob_get_contents();
	$tpl_main = str_replace($cur_include[0], $tpl_temp, $tpl_main);
    ob_end_clean();
}
// END SUBST - <pun_include "*">


// Close the db connection (and free up any result data)
$db->close();

// Spit out the page
exit($tpl_main);
