<?php

	include_once '../bootstrap.php';
	include_once 'CDBSession.php';
	include_once 'CDBHttpTestPlot.php';
	include_once 'CHelperFunctions.php';
	
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
	
	$plotInfo = $_POST['plotInfo'];
	
	$plotInfo = explode('|', $plotInfo);
	
	if(is_array($plotInfo))
	{
		if($plotInfo[1] == 'http')
		{
			$plotName = $plotInfo[0];
			
			$isExists = CDBHttpTestPlot::isHttpULTestPlotExists($plotName);
			
			if(!$isExists)
			{
				echo '{"error": 1, "message": "Error: Please select a valid http test plot"}';
				exit;
			}
			
			$foo		= CDBHttpTestPlot::getHttpULTestPlotByName($plotName);
			$urlParts	= parse_url($foo['baseAddress']);
			$fileName	= md5($COCKPIT_SYSTEM_DEF['userId'] . CSettings::$HOST_IP_OWNERSHIP_FILENAME_SALT) . ".html";
			
			$host		= isset($urlParts['host']) ? $urlParts['host'] : $foo['baseAddress'];
			
			$hostIpOwnershipCode = CHelperFunctions::getHostOwnershipFileContent($host, $fileName);
			
			if(strlen($hostIpOwnershipCode) < 32 || strlen($foo['hostIpOwnershipCode']) < 32)
			{
				echo '{"error": 2, "message": "Please upload valid ownership file in your server"}';
				exit;
			}
			
			if($hostIpOwnershipCode == $foo['hostIpOwnershipCode'])
			{
				$query = CDBHttpTestPlot::setHttpULHostIpOwnershipConfirmed($host);
				
				echo '{"error": 0, "message": "Host ip ownership confirmed"}';
			}
			else
			{
				echo '{"error": 3, "message": "Error: code doesn\'t match"}';
			}
			
			exit;
		}
	}
	else
	{
		echo '{"error": 2, "message": "Error: Invalid plot information"}';
		exit;
	}