<?php
/*******************************************************************************
*  Title: PHP graphical hit counter (GCount)
*  Version: 1.3.1 from 4th January 2015
*  Author: Klemen Stirn
*  Website: http://www.phpjunkyard.com
********************************************************************************
*  COPYRIGHT NOTICE
*  Copyright 2004-2015 Klemen Stirn. All Rights Reserved.

*  This script may be used and modified free of charge by anyone
*  AS LONG AS COPYRIGHT NOTICES AND ALL THE COMMENTS REMAIN INTACT.
*  By using this code you agree to indemnify Klemen Stirn from any
*  liability that might arise from it's use.

*  Selling the code for this program, in part or full, without prior
*  written consent is expressly forbidden.

*  Using this code, in part or full, to create derivate work,
*  new scripts or products is expressly forbidden. Obtain permission
*  before redistributing this software over the Internet or in
*  any other medium. In all cases copyright and header must remain intact.
*  This Copyright is in full effect in any country that has International
*  Trade Agreements with the United States of America or
*  with the European Union.
********************************************************************************

ACKNOWLEDGEMENT

Please support future script development by linking to us:
http://www.phpjunkyard.com/about/link2us.php

Or by sending a small donation:
http://www.phpjunkyard.com/about/donate.php

*******************************************************************************/

////////////////////////////////////////////////////////////////////////////////
// SETTINGS
////////////////////////////////////////////////////////////////////////////////

// URL of the folder where script is installed. INCLUDE a "/" at the end!
$base_url = 'http://www.yourwebsite.com/gcount/';

// Default image style (font)
$default_style = 'web1';

// Default counter image extension
$default_ext = 'gif';

// Count unique visitors? 1 = YES, 0 = NO
$count_unique = 0;

// Number of hours a visitor is considered as "unique"
$unique_hours = 24;

// Minimum number of digits shown (zero-padding). Set to 0 to disable.
$min_digits = 0;

////////////////////////////////////////////////////////////////////////////////
// DO NOT EDIT BELOW
////////////////////////////////////////////////////////////////////////////////

// Turn error notices off
error_reporting(E_ALL ^ E_NOTICE);

// Set the correct MIME type
header("Content-type: text/javascript");

// Tell browsers not to cache the file output so we can count all hits
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Is page ID set?
if ( ! isset($_GET['page']) )
{
	die('ERROR: The gcount.php file must be called with a <b>?page=PAGEID</b> parameter, for example <b>gcount.php?page=test</b>');
}

// Remove any illegal chars from the page ID
$page = preg_replace('/[^a-zA-Z0-9\-_\.]/','',$_GET['page']);

// Stop if $page is not valid
if ( ! strlen($page) )
{
	die('ERROR: Page ID is missing or contains only invalid chars. Please use only these chars for the page ID: a-z, A-Z, 0-9, &quot;.&quot;, &quot;-&quot; and &quot;_&quot;');
}

// Get style and extension information
$style = isset($_GET['style']) ? preg_replace('/[^a-zA-Z0-9\-_\.]/','',$_GET['style']) : $default_style;
$ext   = isset($_GET['ext']) ? preg_replace('/[^a-zA-Z0-9\-_\.]/','',$_GET['ext']) : $default_ext;

// Set values for cookie, log file and style flye names
$cname     = 'gcount_unique_'.$page;
$logfile   = 'logs/' . $page . '.txt';
$style_dir = 'styles/' . $style . '/';

// Does the log file exist?
if ( ! file_exists($logfile) )
{
	die('ERROR: Log file not found. Make sure there is a file called <b>' . $page . '.txt</b> inside your <b>logs</b> folder. On most servers file names are CaSe SeNSiTiVe!');
}

// Open log file for reading and writing
if ($fp = @fopen($logfile, 'r+'))
{
	// Lock log file from other scripts
	$locked = flock($fp, LOCK_EX);

	// Lock successful?
	if ($locked)
	{
		// Let's read current count
		$count = intval( trim( fread($fp, filesize($logfile) ) ) );

		// If counting unique hits is enabled make sure it's a unique hit
		if ( $count_unique == 0 || ! isset($_COOKIE[$cname]) )
		{
			// Update count by 1 and write the new value to the log file
			$count = $count + 1;
			rewind($fp);
			fwrite($fp, $count);

			// Print the Cookie and P3P compact privacy policy
			header('P3P: CP="NOI NID"');
			setcookie($cname, 1, time()+60*60*$unique_hours);
		}
	}
	else
	{
		// Lock not successful. Better to ignore than to damage the log file
		$count = 1;
	}

	// Release file lock and close file handle
	flock($fp, LOCK_UN);
	fclose($fp);
}
else
{
	die("ERROR: Can't read/write to the log file ($logfile). Make sure this file is writable by PHP scripts. On UNIX servers, CHMOD the log file to 666 (rw-rw-rw-).");
}

// Is zero-padding enabled? If yes, add zeros if required
if ($min_digits)
{
	$count = sprintf('%0'.$min_digits.'s', $count);
}

// Print out Javascript code and exit
$len = strlen($count);
for ($i=0; $i<$len; $i++)
{
	echo 'document.write(\'<img src="'.$base_url . $style_dir . substr($count,$i,1) . '.' . $ext .'" border="0" />\');';
}

exit();
?>
