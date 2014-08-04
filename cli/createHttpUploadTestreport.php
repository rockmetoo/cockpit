<?php 

	include_once '../bootstrap.php';
	include_once 'CDBHttpTestPlot.php';
	
	$userId			= $argv[1];
	$plotName		= $argv[2];
	
	
	if($userId && $plotName)
	{
		$foo = CDBHttpTestPlot::getHttpULTestPlotByUserIdAndName($userId, $plotName);
		
		if($foo)
		{
			// Start analyze the data
			$analyzedData = CDBHttpTestPlot::generateHttpULTestPlotReportToMongo($foo);
			
			if(!empty($analyzedData))
			{
				$plotReportDb = $foo['plotReportDb'];
				
				// insert analyzed data into mongo
				$foo = CDBHttpTestPlot::insertAnalyzedHttpULDataInMongo($userId, $plotName, $analyzedData);
				
				if($foo)
				{
					// remove raw data from mongo
					//CDBHttpTestPlot::removeRawHttpULTestDataFromMongo($userId, $plotName, $plotReportDb);
					
					// XXX: IMPORTANT - execute this func before 'setHttpULPlotStatusToDone'. Increment totalHttpUploadPlotExecuted in 'systemInfo' table
					CDBHttpTestPlot::incHttpULPlotExecuted($userId, $plotName);
					
					// update plotStatus to 3 which means done
					CDBHttpTestPlot::setHttpULPlotStatusToDone($userId, $plotName);
				}
			}
		}
	}
	
	exit;
	