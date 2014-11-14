<?php

	include_once '../bootstrap.php';
	include_once 'CDBSession.php';
	include_once 'CDBUser.php';
	
	global $COCKPIT_SYSTEM_DEF;
	
	if(ALLOWED_REFERRER !== ''
		&& (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']),
			strtoupper(ALLOWED_REFERRER)) === false
		)
	){
		die("Internal server error. Please contact system administrator.");
	}
	
	// Only allow employers who are logged in to view this page
	CDBSession::validateUser();
	
	if($_POST['pos'])
	{
		$retVal = CDBUser::setLogDataDashboardPos($_POST['pos'], $COCKPIT_SYSTEM_DEF["user_id"]);
		echo $retVal;
	}
	else
	{
		return false;
	}
?>