<?php

	include_once '../bootstrap.php';
	include_once 'CDBSession.php';
	include_once 'CDBUser.php';
	
	global $SMITH_SYSTEM_DEF;
	
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
		$retVal = CDBUser::setMachineDataDashboardPos($_POST['pos'], $SMITH_SYSTEM_DEF["user_id"]);
		echo $retVal;
	}
	else
	{
		return false;
	}
?>