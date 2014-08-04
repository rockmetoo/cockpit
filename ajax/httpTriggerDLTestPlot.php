<?php

	include_once '../bootstrap.php';
	include_once 'CDBSession.php';
	include_once 'CDBHttpTestPlot.php';
	
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
	
	$plotName = $_POST['plotName'];
	
	$isExists = CDBHttpTestPlot::isHttpDLTestPlotExists($plotName);
	
	if(!$isExists)
	{
		echo '{"error": 1, "message": "Please select a valid download test plot"}';
		exit;
	}
	
	$isStatusOkToRun = CDBHttpTestPlot::isHttpDLTestPlotAvailableByStatus($plotName);
	
	if(!$isStatusOkToRun)
	{
		echo '{"error": 1, "message": "Test is not completed yet. Please be patience"}';
		exit;
	}
	
	// XXX: IMPORTANT - is this host own by you?
	$isIpOwnershipConfirmed = CDBHttpTestPlot::isHttpDLTestPlotHostIpOwnership($plotName);
	
	if(!$isIpOwnershipConfirmed)
	{
		$fileNameHash = md5($SMITH_SYSTEM_DEF['userId'] . CSettings::$HOST_IP_OWNERSHIP_FILENAME_SALT);
		
		// XXX: file name will be same always
		$fileName = $fileNameHash . ".html";
		
		echo '{"error": 2, "message": "Please confirm your host ownership by uploading <a href=\"httpDLHostOwnerShipFile.php?fileName=' . $fileName . '&plotName=' . $plotName . '\" target=\"_blank\">THIS</a> file"}';
		exit;
	}
	
	$status = CDBHttpTestPlot::triggerHttpDLTestPlotRunRequest($_POST);
	
	if($status['status'] == 1)
	{
		echo '{"error": 0, "message": "Test plot run request successfully received for testing"}';
		exit;
	}
	else if($status['status'] == -1)
	{
		echo '{"error": 1, "message": "Due to some technical reason request not received!"}';
		exit;
	}
	else
	{
		echo '{"error": 1, "message": "Unknown Error Occured"}';
		exit;
	}
	
	
	
	
	
?>