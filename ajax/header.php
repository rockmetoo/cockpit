<?php

	require_once('../bootstrap.php');
	require_once('Net/UserAgent/Detect.php');

	mb_internal_encoding('UTF-8');
	if(!isset($content_type)) $content_type='text/html';
	header("Content-Type: $content_type; charset=utf-8");

	require_once('CDBSession.php');
	global $SMITH_SYSTEM_DEF;
	
	require_once('CMail.php');
	require_once('CFormValidator.php');
	require_once('CHelperFunctions.php');
	require_once('form_values_' . $SMITH_SYSTEM_DEF['lang'] . '.php');

	$is_std_browser = (Net_UserAgent_Detect::getBrowser(
		array('firefox','opera','safari','gecko','konq'))
	) ? true : false;

	//Set content types depending on browser standard compliance. This will generate more reliable output in
	//none IE browsers.
	$is_std_browser = '';
	$content_type = array(
		'script' => ($is_std_browser) ? 'application/javascript' : 'text/javascript',
		'html' => ($is_std_browser) ?  'application/xhtml+xml' : 'text/html'
	);