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
	
	$status = CDBHttpTestPlot::getHttpDLTestPlotStatusByName($plotName);
	
	if($status == 0)
	{
		echo '{"val": ' . $status . ', message": "Not Testing"}';
		exit;
	}
	else if($status == 1)
	{
		echo '{"val": ' . $status . ', "message": "Accepted"}';
		exit;
	}
	else if($status == 2)
	{
		echo '{"val": ' . $status . ', "message": "Testing"}';
		exit;
	}
	else if($status == 3)
	{
		echo '{"val": ' . $status . ', "message": "Completed"}';
		exit;
	}
	else if($status == 4)
	{
		$fileName	= md5($SMITH_SYSTEM_DEF['userId'] . CSettings::$HOST_IP_OWNERSHIP_FILENAME_SALT) . ".html";
		
		$jsonData = array(
			"val" => $status,
			"message" => "<img src='../images/warning.png'></img>&nbsp;Host ownership not confirmed&nbsp;<a href='httpDLHostOwnerShipFile.php?fileName=" . $fileName . "&amp;plotName=" . $plotName . "' target='_blank'>[Upload this file]</a>"
		);
		
		$jsonData["message"] .= "&nbsp;<a href='#' class='checkHostOwnership' id='" . $plotName . "|http'>[Check Ownership]</a>";
		
		echo json_encode($jsonData);
		exit;
	}
?>