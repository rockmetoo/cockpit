<?php 

	include_once '../bootstrap.php';
	include_once 'CDBHttpTestPlot.php';
	
	$userId			= $argv[1];
	$plotName		= $argv[2];
	
	
	if($userId && $plotName)
	{
		$foo		= CDBHttpTestPlot::getHttpTestPlotByUserIdAndName($userId, $plotName);
		
		if($foo)
		{
			// Start analyze the data
			$analyzedData	= CDBHttpTestPlot::generateHttpTestPlotReportToMongo($foo);
			
			if(!empty($analyzedData))
			{
				$plotReportDb = $foo['plotReportDb'];
				
				// insert analyzed data into mongo
				$foo			= CDBHttpTestPlot::insertAnalyzedHttpDataInMongo($userId, $plotName, $analyzedData);
				
				if($foo)
				{
					// remove raw data from mongo
					//CDBHttpTestPlot::removeRawHttpTestDataFromMongo($userId, $plotName, $plotReportDb);
					
					// XXX: IMPORTANT - execute this func before 'setHttpPlotStatusToDone'. Increment totalHttpPlotExecuted in 'systemInfo' table
					CDBHttpTestPlot::incHttpPlotExecuted($userId, $plotName);
					
					// update plotStatus to 3 which means done
					CDBHttpTestPlot::setHttpPlotStatusToDone($userId, $plotName);
				}
			}
		}
	}
	
	exit;
	