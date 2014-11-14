<?php

	include_once 'CMQC.php';
	include_once 'CDBQuery.php';
	include_once 'CDBMongo.php';
	include_once 'COAuthProvider.php';
	include_once 'CSettings.php';
	include_once 'CUnirest.php';
	
	class CDBCPUMonita extends CDBQuery
	{
		public static function createCPUMonita($cpuMonita, $graphTitle)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			$date = date("Y-m-d H:i:s");
				
			$query	= $db->quoteInto('SELECT null FROM cockpitCpuMonita WHERE userId=%s AND monitaName=%s', array($COCKPIT_SYSTEM_DEF['userId'], $cpuMonita));
			$res	= $db->queryOther('cockpit', $query);
				
			if($res->num_rows) return false;
			
			$oauthProvider	= new COAuthProvider('CPU');
			$accessToken	= $oauthProvider->generateAccessToken($COCKPIT_SYSTEM_DEF['userId'] . $cpuMonita);
			
			$db->insertAndReturnConn('cockpit', 'cockpitCpuMonita',
				array(
					'userId'			=> $COCKPIT_SYSTEM_DEF['userId'],
					'monitaName'		=> $cpuMonita,
					'graphTitle'		=> $graphTitle,
					'ident'				=> $accessToken[0],
					'cycleInSeconds'	=> CSettings::$GRAPH_STEP_SIZE,
					'dateCreated'		=> $date,
					'dateUpdated'		=> $date
				)
			);
			
			// XXX: IMPORTANT - affected_rows because there is no auto_increment field in "cockpitCpuMonita" table
			$conn = $db->getConnection('cockpit');
			return mysqli_affected_rows($conn);
		}
	}