<?php 

	include_once '../bootstrap.php';
	include_once 'CDBHttpTestPlot.php';
	
	$userId			= $argv[1];
	$plotName		= $argv[2];
	
	
	if($userId && $plotName)
	{
		$foo = CDBHttpTestPlot::getHttpDLTestPlotByUserIdAndName($userId, $plotName);
		
		if($foo)
		{
			// Start analyze the data
			$analyzedData = CDBHttpTestPlot::generateHttpDLTestPlotReportToMongo($foo);
			
			if(!empty($analyzedData))
			{
				$plotReportDb = $foo['plotReportDb'];
				
				// insert analyzed data into mongo
				$foo = CDBHttpTestPlot::insertAnalyzedHttpDLDataInMongo($userId, $plotName, $analyzedData);
				
				if($foo)
				{
					// remove raw data from mongo
					//CDBHttpTestPlot::removeRawHttpDLTestDataFromMongo($userId, $plotName, $plotReportDb);
					
					// XXX: IMPORTANT - execute this func before 'setHttpDLPlotStatusToDone'. Increment totalHttpDownloadPlotExecuted in 'systemInfo' table
					CDBHttpTestPlot::incHttpDLPlotExecuted($userId, $plotName);
					
					// update plotStatus to 3 which means done
					CDBHttpTestPlot::setHttpDLPlotStatusToDone($userId, $plotName);
				}
			}
		}
	}
	
	exit;
	