<?php
/***********************************************************************

  Copyright (C) 2005  Terrell Russell (punbb@terrellrussell.com)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/



// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);



// --------------------------------------------------------------------

// Confirm Page

if (isset($_POST['confirm']))
{
	// Make sure message body was entered
	if (trim($_POST['message_body']) == '')
		message('You didn\'t enter a message body!');

	// Make sure message subject was entered
	if (trim($_POST['message_subject']) == '')
		message('You didn\'t enter a subject!');

	// Display the admin navigation menu
	generate_admin_menu($plugin);
	
	$preview_message_body = nl2br(pun_htmlspecialchars($_POST['message_body']));
	
	$sql = "SELECT count(*) AS usercount
				FROM ".$db->prefix."users
				WHERE username != 'Guest'
				ORDER BY username";	
	$result = $db->query($sql) or error('Could not get user count from database', __FILE__, __LINE__, $db->error());
   	$row = $db->fetch_assoc($result);

?>
	<div id="exampleplugin" class="blockform">
		<h2><span>Broadcast Email - Confirm</span></h2>
		<div class="box">
			<div class="inbox">
				<p>Please confirm your message below.<br /><br />If something is not correct, please <a href="javascript: history.go(-1)">Go Back</a>.</p>
			</div>
		</div>

		<h2 class="block2"><span>Confirm Message</span></h2>
		<div class="box">
			<form id="broadcastemail" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<div class="inform">
					<input type="hidden" name="message_subject" value="<?php echo pun_htmlspecialchars($_POST['message_subject']) ?>" />
					<input type="hidden" name="message_body" value="<?php echo pun_htmlspecialchars($_POST['message_body']) ?>" />
					<fieldset>
						<legend>Message Recipients</legend>
						<div class="infldset">
							[ <strong><?php echo $row['usercount'] ?></strong> ] Registered Users will receive this message (including the Administrator).
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend>Message Contents</legend>
						<div class="infldset">
							<table class="aligntop" cellspacing="0">
								<tr>
									<th scope="row">Subject</th>
									<td>
										<?php echo pun_htmlspecialchars($_POST['message_subject']) ?>
									</td>
								</tr>
								<tr>
									<th scope="row">Body</th>
									<td>
										<?php echo $preview_message_body ?>
									</td>
								</tr>
							</table>
							<div class="fsetsubmit"><input type="submit" name="send_message" value="Confirmed - Send It." tabindex="3" /></div>
							<p class="topspace">Please hit this button only once. Patience is key.</p>
						</div>
					</fieldset>
				</div>
			</form>
		</div>
	</div>
<?php

}

// --------------------------------------------------------------------

// Send the Message

else if (isset($_POST['send_message']))
{

	require_once PUN_ROOT.'include/email.php';

	// Display the admin navigation menu
	generate_admin_menu($plugin);

	$sql = "SELECT username, email
				FROM ".$db->prefix."users
				WHERE username != 'Guest'
				ORDER BY username";	
	$result = $db->query($sql) or error('Could not get users from the database', __FILE__, __LINE__, $db->error());
   	while($row = $db->fetch_assoc($result))
   	{
   		$addresses[$row['username']] = $row['email'];
   	}

	$usercount = count($addresses);

	foreach ($addresses as $recipientname => $recipientemail)
	{
	
		$mail_to        = $recipientname." <".$recipientemail.">";
		$mail_subject   = pun_htmlspecialchars($_POST['message_subject']);
		$mail_message   = pun_htmlspecialchars($_POST['message_body']);

		pun_mail($mail_to, $mail_subject, $mail_message);

	}


	
?>
	<div class="block">
		<h2><span>Broadcast Email - Message Sent</span></h2>
		<div class="box">
			<div class="inbox">
				<p>The message was sent to [ <strong><?php echo $usercount ?></strong> ] Registered Users.</p>
				<p>You should receive the Administrator's copy in a few moments.</p>
				<p>Please use the Administrator's copy as a record of this event.</p>
			</div>
		</div>
	</div>
<?php

}

// --------------------------------------------------------------------

// Display the Main Page

else
{
	// Display the admin navigation menu
	generate_admin_menu($plugin);

?>
	<div id="exampleplugin" class="blockform">
		<h2><span>Broadcast Email</span></h2>
		<div class="box">
			<div class="inbox">
				<p>This plugin allows the Administrator to send a Broadcast Email to all registered users.</p>
				<p>There will be a confirmation page after this one - to make sure you have not made any mistakes.</p>
			</div>
		</div>

		<h2 class="block2"><span>Compose Message</span></h2>
		<div class="box">
			<form id="broadcastemail" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<div class="inform">
					<fieldset>
						<legend>Message Contents</legend>
						<div class="infldset">
							<table class="aligntop" cellspacing="0">
								<tr>
									<th scope="row">Subject</th>
									<td>
										<input type="text" name="message_subject" size="50" tabindex="1" />
									</td>
								</tr>
								<tr>
									<th scope="row">Body</th>
									<td>
										<textarea name="message_body" rows="14" cols="48" tabindex="2"></textarea>
									</td>
								</tr>
							</table>
							<div class="fsetsubmit"><input type="submit" name="confirm" value="Continue to Confirmation" tabindex="3" /></div>
						</div>
					</fieldset>
				</div>
			</form>
		</div>
	</div>
<?php

}

// --------------------------------------------------------------------

// Note that the script just ends here. The footer will be included by admin_loader.php.
