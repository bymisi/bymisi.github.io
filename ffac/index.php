<?php


define('PUN_ROOT', './forum/');
require PUN_ROOT.'include/common.php';


if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);


// Load the index.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/index.php';

$page_title = pun_htmlspecialchars($pun_config['o_board_title']);
define('PUN_ALLOW_INDEX', 1);
require 'header.php';

?>
<div id="mainpage" class="block2col">
	<div id="mainpagemenu" class="blockmenu">
		<h2><span>Menu</span></h2>
		<div class="box">

			<div class="inbox">
				<ul>
					<li><a href="index.php">Index</a></li>
					<li><a href="index.php?page=playerrank">Player Rank</a></li>
					<li><a href="index.php?page=teamrank">Team Rank</a></li>
					<li><a href="index.php?page=serverrank">Server Rank</a></li>
					<li><a href="index.php?page=groups">Groups</a></li>
					<li><a href="index.php?page=globalstats">Global Stats</a></li>
					<li><a href="index.php?page=ffacbans">FFACBans</a></li>
					<li><a href="index.php?page=irc">IRC</a></li>
				</ul>
			</div>
		</div>
		<h2 class="block2"><span>User</span></h2>
		<div class="box">

			<div class="inbox">
				<ul>
					<?php if ($pun_user['is_guest'])
					{
						echo '<li id="navregister"><a href="forum/register.php">'.$lang_common['Register'].'</a>';
						echo '<li id="navlogin"><a href="forum/login.php">'.$lang_common['Login'].'</a>';
					} 
					else
					{
						echo '<li id="navlogout"><a href="forum/login.php?action=out&amp;id='.$pun_user['id'].'">'.$lang_common['Logout'].'</a>';
						echo '<li id="navprofile"><a href="forum/profile.php?id='.$pun_user['id'].'">'.$lang_common['Profile'].'</a>';
						$reponsess = mysql_query("SELECT icq FROM users WHERE id='".$pun_user['id']."'");
						$donnees = mysql_fetch_array($reponsess) ;
						$SIDp=$donnees['icq'];
						mysql_select_db($db_ffac_name); 
						$reponsess = mysql_query("SELECT id FROM players WHERE SID='".$SIDp."'");
						$donnees = mysql_fetch_array($reponsess) ;
						$playerrid=$donnees['id'];
						mysql_select_db("punbb");
						echo '<li id="navprofile"><a href="index.php?page=playerinfo&pid='.$playerrid.'">My Stats</a>';
						echo '<li><a href="index.php?page=mylogs">My Logs</a></li>';
						echo '<li><a href="index.php?page=team"> My Team</a></li>';
						echo '<li><a href="index.php?page=groupsse">My Groups</a></li>';
					}
					
					?>

				</ul>
			</div>
		</div>
<?php if($_GET['page']=="") { ?>
		<h2 class="block2"><span>FFAC Stats</span></h2>
		<div class="box">

			<div class="inbox">
				<ul>
				<?php mysql_select_db($db_ffac_name); 
				$reponsess = mysql_query("SELECT count(*) AS SPlayer, sum(Online) AS SOnline ,sum(NConnect) AS SConnect FROM players");
				$reponses = mysql_query("SELECT sum(maxplayers) AS SMPlayers FROM servers");
				$donneess = mysql_fetch_array($reponsess) ;
				$donnees = mysql_fetch_array($reponses) ;
				echo '<li id="statss">'.$donneess['SOnline']."/".$donnees['SMPlayers']." Players Online";
				echo '</li">';
				echo '<li id="statss">'.$donneess['SPlayer']." Players in DB";
				echo '</li">';
				echo '<li id="statss">'.$donneess['SConnect']." Connections";
				echo '</li">';
				mysql_select_db("punbb"); ?>
				</ul>
			</div>
		</div>
		<?php } ?>
		<h2><span>Server</span></h2>
		<div class="box">

			<div class="inbox">
				<ul>
					<li><a href="/FFAChmp">Plugin Download</a></li>
					<li><a href="index.php?page=doc">Documentation</a></li>
					<li><a href="index.php?page=doc">How to</a></li>
				</ul>
			</div>
		</div>
		
		
	</div>

<?php if ($_GET['page']=="") { ?>
	<div class="block">
	<?php  if ($pun_user['is_guest']) { ?>
			<?php mysql_select_db($db_ffac_name); 
			$reponses = mysql_query("SELECT * FROM players WHERE ip='".get_remote_address()."'");
			$donnees = mysql_fetch_array($reponses) ;
			if ($donnees['Pseudo']!=""){?>
	    
		<h2><strong>Welcome unregistred User :) This is your stats</strong> </h2>
		<div id="adintro" class="box">
			<div class="inbox">
			<?php
			echo "<table>";
				echo "<tr>";
					echo "<td class='tcl'>";
					echo "<strong>Name</strong>";
					echo "</td>";
					echo "<td class='tcl'>";
					echo $donnees['Pseudo'];
					echo "</td>";
					echo "<td class='tcl'>";
					echo "<strong>Total Connect Time</strong>";
					echo "</td>";
					echo "<td class='tcl'>";
					$ConnecTimeM = round((($donnees['ConnecTime'])%3600)/60);
					$ConnecTimeH = round($donnees['ConnecTime'] / 3600);
					$ConnecTimeHF = $donnees['ConnecTime'] / 3600 ; 
					echo $ConnecTimeH." Hours and ".$ConnecTimeM." Mins";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='tcl'>";
					echo "<strong>Skill</strong>";
					echo "</td>";
					echo "<td class='tcl'>";
					echo $donnees['Skill'];
					echo "</td>";
					echo "<td class='tcl'>";
					echo "<strong>CStats</strong>";
					echo "</td>";
					echo "<td class='tcl'>";
					echo $donnees['csstatscore'];
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='tcl'>";
					echo "<strong>Kills</strong>";
					echo "</td>";
					echo "<td class='tcl'>";
					echo $donnees['Ckill'];
					echo "</td>";
					echo "<td class='tcl'>";
					echo "<strong>Deaths</strong>";
					echo "</td>";
					echo "<td class='tcl'>";
					echo $donnees['Death'];
					echo "</td>";
				echo "</tr>";
			echo "</table>";
			 ?>
			</div>

		</div>
	
					<br></br><br></br>
	    <?php mysql_select_db("punbb"); }} ?>
		<h2>News</h2>
		<div id="adintro" class="box">
			<div class="inbox">
			<?php //include('news.php'); 
			?>

			</div>

		</div>
	
		<br></br><br></br>
		<h2 class=""><span>Statistics ( Last 7 Days )</span></h2>
		<font size="1.5">
			<div class="box">			
			<table border=0>
			<tr>
			<td>
			<div class="box">
					<h2><span>Top CSStats Players</span></h2>
						<div id="rankplayer" class="box">
							<div class="inbox">
							<?php include('wtop15.php'); ?>						
							</div>
						</div>
			</div></td>
			<td>
			<div class="box">
					<h2><span>Top Online Time</span></h2>
						<div id="rankplayer" class="box">
							<div class="inbox">
							<?php include('wtop15t.php'); ?>						
							</div>
						</div>
			</div></td>
			<td>
			<div class="box">
					<h2><span>Top Kill</span></h2>
						<div id="rankplayer" class="box">
							<div class="inbox">
							<?php include('wtop15k.php'); ?>						
							</div>
						</div>
			</div></td>
			</tr>
			</table>
			</div>
		</font>
		
		
		<h2 class=""><span>Statistics ( All Time )</span></h2>
		<font size="1.5">
			<div class="box">			
			<table border=0>
			<tr><td>
			<div class="box">
					<h2><span>Top Skill Players</span></h2>
						<div id="rankplayer" class="box">
							<div class="inbox">
							<?php include('top10player.php'); ?>
							
							</div>
						</div>
			</div></td><td>
			<div class="box">
					<h2><span>Top Teams</span></h2>
						<div id="rankteam" class="box">
							<div class="inbox">
							<?php include('top10team.php'); ?>
							</div>
						</div>
			</div>
			</div></td><td>
			<div class="box">
					<h2><span>Top Servers</span></h2>
						<div id="rankplayer" class="box">
							<div class="inbox">
							<?php include('top10server.php'); ?>
							</div>
						</div>
			</div>
	

			</td></tr>
			<tr><td>
			<div class="box">
					<h2><span>Top CStats Players</span></h2>
						<div id="rankplayer" class="box">
							<div class="inbox">
							<?php include('top10playercsstats.php'); ?>
							
							</div>
						</div>
			</div></td><td>
			<div class="box">
					<h2><span>Top Skill Players Online</span></h2>
						<div id="rankteam" class="box">
							<div class="inbox">
							<?php include('top10playero.php'); ?>
							</div>
						</div>
			</div>
			</div></td><td>
			<div class="box">
					<h2><span>Top CStats Players Online</span></h2>
						<div id="rankplayer" class="box">
							<div class="inbox">
							<?php include('top10playercsstatso.php'); ?>
							</div>
						</div>
			</div>
	

			</td></tr>
			</table>
			</div>
			


		</font>
	</div>
	<div class="clearer"></div>
</div>

<?php } ?>

<?php if ($_GET['page']=="playerrank") { ?>
	<div class="block">
		<h2>Players rank</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('players.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="teamrank") { ?>
	<div class="block">
		<h2>Players rank</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('teams.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="serverrank") { ?>
	<div class="block">
		<h2>Servers rank</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('servers.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="ffacbans") { ?>
	<div class="block">
		<h2>FFACBans</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('ffacbans.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="serverinfo") { ?>
	<div class="block">
		<h2>Server Info</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('serverinfo.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="playerinfo") { ?>
	<div class="block">
		<h2>Player Info</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('playerinfo.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="teaminfo") { ?>
	<div class="block">
		<h2>Team Info</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('teaminfo.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="groups") { ?>
	<div class="block">
		<h2>Groups</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('groupslist.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="groupsse") { ?>
	<div class="block">
		<h2>Team Info</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('groups.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>


<?php if ($_GET['page']=="globalstats") { ?>
	<div class="block">
		<h2>Global Stats</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('globalstats.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="team") { ?>
	<div class="block">
		<h2>Team</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('team.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="mylogs") { ?>
	<div class="block">
		<h2>My Logs</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('mylogs.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="irc") { ?>
	<div class="block">
		<h2>IRC</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('irc.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php if ($_GET['page']=="doc") { ?>
	<div class="block">
		<h2>Documentation</h2>
		<div id="adintro" class="box">
			<div class="inbox">
<?php	include('docffac.php'); ?>
			</div>
		</div>
		<br></br><br></br>
</div>
<?php }?>

<?php
mysql_select_db($db_name);

// Collect some statistics from the database
$result = $db->query('SELECT COUNT(id)-1 FROM '.$db->prefix.'users') or error('Unable to fetch total user count', __FILE__, __LINE__, $db->error());
$stats['total_users'] = $db->result($result);

$result = $db->query('SELECT id, username FROM '.$db->prefix.'users ORDER BY registered DESC LIMIT 1') or error('Unable to fetch newest registered user', __FILE__, __LINE__, $db->error());
$stats['last_user'] = $db->fetch_assoc($result);

$result = $db->query('SELECT SUM(num_topics), SUM(num_posts) FROM '.$db->prefix.'forums') or error('Unable to fetch topic/post count', __FILE__, __LINE__, $db->error());
list($stats['total_topics'], $stats['total_posts']) = $db->fetch_row($result);

?>
<div id="brdstats" class="block">
	<h2><span><?php echo $lang_index['Board info'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<dl class="conr">
				<dt><strong><?php echo $lang_index['Board stats'] ?></strong></dt>
				<dd><?php echo $lang_index['No of users'].': <strong>'. $stats['total_users'] ?></strong></dd>
				<dd><?php echo $lang_index['No of topics'].': <strong>'.$stats['total_topics'] ?></strong></dd>
				<dd><?php echo $lang_index['No of posts'].': <strong>'.$stats['total_posts'] ?></strong></dd>
			</dl>
			<dl class="conl">
				<dt><strong><?php echo $lang_index['User info'] ?></strong></dt>
				<dd><?php echo $lang_index['Newest user'] ?>: <a href="forum/profile.php?id=<?php echo $stats['last_user']['id'] ?>"><?php echo pun_htmlspecialchars($stats['last_user']['username']) ?></a></dd>
<?php

if ($pun_config['o_users_online'] == '1')
{
	// Fetch users online info and generate strings for output
	$num_guests = 0;
	$users = array();
	$result = $db->query('SELECT user_id, ident FROM '.$db->prefix.'online WHERE idle=0 ORDER BY ident', true) or error('Unable to fetch online list', __FILE__, __LINE__, $db->error());

	while ($pun_user_online = $db->fetch_assoc($result))
	{
		if ($pun_user_online['user_id'] > 1)
			$users[] = "\n\t\t\t\t".'<dd><a href="forum/profile.php?id='.$pun_user_online['user_id'].'">'.pun_htmlspecialchars($pun_user_online['ident']).'</a>';
		else
			++$num_guests;
	}

	$num_users = count($users);
	echo "\t\t\t\t".'<dd>'. $lang_index['Users online'].': <strong>'.$num_users.'</strong></dd>'."\n\t\t\t\t".'<dd>'.$lang_index['Guests online'].': <strong>'.$num_guests.'</strong></dd>'."\n\t\t\t".'</dl>'."\n";


	if ($num_users > 0)
		echo "\t\t\t".'<dl id="onlinelist" class= "clearb">'."\n\t\t\t\t".'<dt><strong>'.$lang_index['Online'].':&nbsp;</strong></dt>'."\t\t\t\t".implode(',</dd> ', $users).'</dd>'."\n\t\t\t".'</dl>'."\n";
	else
		echo "\t\t\t".'<div class="clearer"></div>'."\n";

}
else
	echo "\t\t".'</dl>'."\n\t\t\t".'<div class="clearer"></div>'."\n";


?>
		</div>
	</div>
</div>
<?php

$footer_style = 'index';
require 'footer.php';

