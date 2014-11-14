<?php

	include_once 'bootstrap.php';
	include_once 'CDBSession.php';
	include_once 'CDBUser.php';
	include_once 'CDBHttpTestPlot.php';
	include_once 'CHelperFunctions.php';
	include_once 'CSettings.php';
	
	global $COCKPIT_SYSTEM_DEF;
	
	
	if(ALLOWED_REFERRER !== '' && (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']), strtoupper(ALLOWED_REFERRER)) === false))
	{
		echo "Invalid file access!";
		exit;
	}
	
	// Only allow user who are logged in to view this page
	CDBSession::validateUser();

	$COCKPIT_USER_DEF	= CDBUser::getUserDetails($COCKPIT_SYSTEM_DEF['userId']);
	
	$fileName		= $_REQUEST['fileName'];
	$plotName		= $_REQUEST['plotName'];
	
	$compareName	= md5($COCKPIT_SYSTEM_DEF['userId'] . CSettings::$HOST_IP_OWNERSHIP_FILENAME_SALT) . ".html";
	
	if($fileName != $compareName)
	{
		echo 'Invalid file access!';
		exit;
	}
	
	header("Content-type: text/plain");
	header('Content-Disposition: attachment; filename="' . $fileName . '"');
	
	$hostIpOwnershipCode = CHelperFunctions::createPseudoSecretHash($COCKPIT_SYSTEM_DEF['userId'], CSettings::$HOST_IP_OWNERSHIP_SALT);
	
	CDBHttpTestPlot::setHttpDLPlotHostIpOwnershipCode($hostIpOwnershipCode, $plotName);
	
	print $hostIpOwnershipCode;