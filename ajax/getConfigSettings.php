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
			$res = CDBHttpTestPlot::getHttpTestPlotByName($plotInfo[0]);
			
			if($res)
			{
				$type				= $res['type'];
				$method				= $res['method'];
				$contentType		= $res['contentType'];
				$accept				= $res['accept'];
				$charset			= $res['charset'];
				
				$res['type']		= $HTTP_TYPE["$type"];
				$res['method']		= $HTTP_METHODS["$method"];
				$res['contentType'] = $HTTP_CONTENT_TYPE["$contentType"];
				$res['accept']		= $HTTP_ACCEPT["$accept"];
				$res['charset']		= $HTTP_CHARSET[$charset];
				
				try
				{
					$res['queryData']	= json_decode($res['queryData']);
				}
				catch(Exception $e)
				{
					$res['queryData']	= $res['queryData'];
				}
				
				unset($res['plotStatus'], $res['plotReportDb']);
				
				if($res['authType'] == 0)
				{
					$res['authType'] = 'None';
					unset(
						$res['authUser'], $res['authPassword'],
						$res['consumerKey'], $res['consumerSecret'], $res['token'],
						$res['tokenSecret'], $res['getTokenAndSecretFromHere']
					);
				}
				else if($res['authType'] == 1)
				{
					$res['authType'] = 'Http Auth';
					unset(
						$res['consumerKey'], $res['consumerSecret'], $res['token'],
						$res['tokenSecret'], $res['getTokenAndSecretFromHere']
					);
				}
				else if($res['authType'] == 2)
				{
					$res['authType'] = 'Oauth';
					unset(
						$res['authUser'], $res['authPassword']
					);
				}
				
				// XXX: IMPORTANT - unset host owner ship code and status
				unset($res['hostIpOwnership'], $res['hostIpOwnershipCode'], $res['isDeleted']);
				
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