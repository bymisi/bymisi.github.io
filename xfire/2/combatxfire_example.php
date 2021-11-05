<?php

error_reporting( E_ERROR );

$user = $_GET["u"] or "phpj0ck";

include "xfireparser.class.php";
$x = new xfire_data;
$x->SetUsername( $user );

$profile = $x->GetProfile();
$live = $x->GetLive();
$gametime = $x->GetGamedata();
$rig = $x->GetGamerig();

?>

<script src="sorttable.js" type="text/javascript"></script>

<style type="text/css">
.xbody {
	background-color: rgb( 51, 47, 17);
	padding: 10px;
	color: #FFFFFF !important;
}
.xwindow a {
	color: rgb( 43, 146, 206 );
}
.xwindow {
	//width: 471px;
	border: 1px solid #FFFFFF;
	background-color: rgb( 73,73,73 );
	color: #FFFFFF;
	font-family: Tahoma, Verdana, Arial, sans-serif;
	font-size: 8pt;
	margin-bottom: 15px;
}
.xtop {
	background-image: url( "combat_c2_windowheader.png" );
	border-bottom: 1px solid #FFFFFF;
	padding: 3px;
	text-align: center;
	font-weight: bold;
}
.xprofile {
	padding: 7px;
}
.xproside {
	border: 1px solid #FFFFFF;
	float: right;
	background-color: rgb( 58, 58, 58 );
	width: 100px;
	padding: 5px;
}
table.xproinfo {
	color: #FFFFFF;
	font-size: 8pt;
}
td.xprofield {
	font-weight: bold;
	text-align: right;
	width: 80px;
}
.xproside a {
	color: rgb( 169, 108, 30 );
}
.xgametime {
	color: #FFFFFF;
	font-size: 8pt;
	width: 100%;
}
.xgame_02
{
	background-color: rgb( 58, 58, 58 );
}
.xgamegame {
	vertical-align: center;
	text-align: left;
}
.xgameweek, .xgameallt {
	text-align: center;
	width: 115px;
}
.xgamegame a, .xgameweek a, .xgameallt a {
	color: rgb( 43, 146, 206 ) !important;
	font-weight: bold;
}
tfoot.xoverall {
	border: 1px solid #FFFFFF;
	background-color: rgb( 58, 58, 58 );
}
tfoot.xoverall tr td {
	padding: 5px;
	font-size: 14pt;
}
.xoverlab {
	font-weight: bold;
}
.xoverweek, .xoverallt {
	text-align: center;
}
.xlive {
	text-align: center;
	font-size: 14pt;
	font-weight: bold;
	padding: 15px;
}
td.xrigcell {
	padding: 2px;
}
td.xrigleft {
	font-weight: bold;
	width: 150px;
}
.xgamerd {
	padding: 10px;
}
</style>

THIS IS TOTALLY INDEPEDANT TO YOUR SITE'S STYLE!
<div class="xbody">
	<div class="xwindow">
		<div class="xtop">
			PROFILE
		</div>
		
		<div class="xprofile">
			<img src="<?php echo $profile["avatar" ]; ?>" alt="Avatar" style="float: left; padding-right: 5px;" />
			<div class="xproside">
				<b>Status</b><br />
				<?php echo $live["status"]; ?><br />
				
				<b>Member Since</b><br />
				<?php echo $profile["joindate" ]; ?><br />
				
				<a href="#"><?php echo $profile["friends_count" ]; ?> Friends</a>
			
			</div>
			
			<table class="xproinfo">
				<tr class="xprorow">
					<td class="xprofield">
						Username:
					</td>
					<td class="xprovalue">
						<a href="http://xfire.com/profile/<?php echo $profile["username"]; ?>/"><?php echo $profile["username"]; ?></a>
					</td>
				</tr>
				<tr class="xprorow">
					<td class="xprofield">
						Nickname:
					</td>
					<td class="xprovalue">
						<a href="http://xfire.com/profile/<?php echo $profile["username"]; ?>/"><?php echo $profile["nickname"]; ?></a>
					</td>
				</tr>
				<tr class="xprorow">
					<td class="xprofield">
						Location:
					</td>
					<td class="xprovalue">
						<img src="flags/<?php echo $profile["country"]; ?>.png" alt="<?php echo $profile["country"]; ?>" /> <?php echo $profile["location"]; ?>
					</td>
				</tr>
				<tr class="xprorow">
					<td class="xprofield">
						Age:
					</td>
					<td class="xprovalue">
						<?php echo $profile["age"]; ?>
					</td>
				</tr>
				<tr class="xprorow">
					<td class="xprofield">
						Gender:
					</td>
					<td class="xprovalue">
						<?php if( $profile["gender"] == "f" ) { echo "Female"; } else { echo "Male"; } ; ?>
					</td>
				</tr>
				<tr class="xprorow">
					<td class="xprofield">
						Gaming Style:
					</td>
					<td class="xprovalue">
						<?php echo $profile["gaming_style"]; ?>
					</td>
				</tr>
			</table>
		</div>
	</div>

<?php if( $live["gameid"] != 0 ) { ?>
	<div class="xwindow">
		<div class="xtop">
			IN-GAME STATUS
		</div>
		
		<div class="xprofile xlive">
			<?php echo htmlentities( $live["nickname"] ); ?> is currently playing  <span style="color: #00AA00"><?php echo $live["gameid"]; ?></span><?php if( $live["ip"] != "0.0.0.0" ) { ?> on <span style="color: #00AA00"><?php echo $live["ip"] . ":" . $live["port"] ; } ?></span>
		</div>
	</div>
<?php } ?>

	<div class="xwindow">
		<div class="xtop">
			GAMEPLAY HOURS
		</div>
		
		<div class="xprofile">
				<table class="sortable xgametime">
					<thead>
						<tr class="xgame_01">
							<th class="xgamegame"><a href="#">Game</a></td>
							<th class="sorttable_numeric xgameweek"><a href="#">Last 7 Days</a></td>
							<th class="sorttable_numeric xgameallt"><a href="#">All Time</a></td>
						</tr>
					</thead>
					<tbody>
						
			<?php
			
			$xgame = 1;
			foreach( $gametime as $k => $v )
			{
			?>
					<tr class="xgame_tr">
						<td class="xgamegame xgame_02"><img src="http://xfireimg.com/xfire/xf/images/icons/<?php echo $v["shortname"]; ?>.gif" alt="" height="16" /> <?php echo $v["longname" ];?></td>
						<td class="xgameweek xgame_02"><?php 
						
						$weektime = $v["weektime"];
						$rweekhrs = round( $v["weektime"] / 3600 );
						
						$totalw += $weektime;
				
						if( $weektime == 0 )
						{
							echo "-";
						}
						elseif( $rweekhrs < 1 )
						{
							echo "<span style=\"color: #AA0000\">&lt; 1 hour</span>";
						}
						else
						{
							echo $rweekhrs . " hours";
						}
						?>
						</td>
						<td class="xgameallt xgame_02"><?php 
						
						$alltime = $v["alltime"];
						$allhrs = round( $v["alltime"] / 3600 );
						
						$totala += $alltime;
				
						if( $alltime == 0 )
						{
							echo "-";
						}
						elseif( $allhrs < 1 )
						{
							echo "<span style=\"color: #AA0000\">&lt; 1 hour</span>";
						}
						else
						{
							echo $allhrs . " hours";
						}
						?>
						</td>
					</tr>
			<?php
			
				if( $xgame == 1 )
				{
					$xgame = 2;
				}
				else
				{
					$xgame = 1;
				}
			}
			?>
				</tbody>
				<tfoot class="xoverall">
					<tr>
						<td class="xoverlab">Overall Hours:</td>
						<td class="xoverweek"><?php 
				
						$totalwhrs = round( $totalw / 3600 );
						
						if( $totalw == 0 )
						{
							echo "-";
						}
						elseif( $totalwhrs < 1 )
						{
							echo "<span style=\"color: #AA0000\">&lt; 1 hour</span>";
						}
						else
						{
							echo $totalwhrs . " hours";
						}
						?></td>
						<td class="xoverallt"><?php 
				
						$totalahrs = round( $totala / 3600 );
						
						if( $totala == 0 )
						{
							echo "-";
						}
						elseif( $totalahrs < 1 )
						{
							echo "<span style=\"color: #AA0000\">&lt; 1 hour</span>";
						}
						else
						{
							echo $totalahrs . " hours";
						}
						?></td>
					</tr>
				</tfoot>
				</table>
		</div>
	</div>
	
	<div class="xwindow">
		<div class="xtop">
			GAMING RIG
		</div>
		
		<div class="xgamerd">
			<table class="xgamerig xproinfo">
				<?php foreach( $rig as $rigk => $rigv ) { ?>
				<tr class="xrigrow">
					<td class="xrigcell xrigleft">
						<?php echo $rigk; ?>:
					</td>
					<td class="xrigcell xrightright">
						<?php echo $rigv; ?>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
</div>
