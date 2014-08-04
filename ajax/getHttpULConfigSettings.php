<?php

	include_once '../bootstrap.php';
	include_once 'CDBSession.php';
	include_once 'CDBHttpTestPlot.php';
	include_once 'formValues' . $SMITH_SYSTEM_DEF['lang'] . '.php';
	
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

	$plotInfo = $_POST['plotInfo'];
	
	$plotInfo = explode('|', $plotInfo);
	
	if(is_array($plotInfo))
	{
		if($plotInfo[1] == 'http')
		{
			$res = CDBHttpTestPlot::getHttpULTestPlotByName($plotInfo[0]);
			
			if($res)
			{
				$type				= $res['type'];
				$res['type']		= $HTTP_TYPE["$type"];
				
				unset($res['plotStatus'], $res['plotReportDb'], $res['isDeleted']);
				
				// XXX: IMPORTANT - unset host owner ship code and status
				unset($res['hostIpOwnership'], $res['hostIpOwnershipCode']);
				
				echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			}
			else
			{
				echo 'Error: request plot config';
			}
		}
	}
	else
	{
		echo 'Error: request plot config';
	}

	exit;