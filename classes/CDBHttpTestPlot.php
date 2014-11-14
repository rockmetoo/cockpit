<?php

	include_once 'CMQC.php';
	include_once 'CDBQuery.php';
	include_once 'CDBMongo.php';
	include_once 'CSettings.php';
	include_once 'CUnirest.php';
	
	class CDBHttpTestPlot extends CDBQuery
	{
		public static function getAllHttpTestPlotNameByUserId()
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$query = $db->quoteInto("SELECT plotName, plotStatus FROM httpTestPlot WHERE userId=%s AND isDeleted=0", array($COCKPIT_SYSTEM_DEF['userId']));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$data = array();
				
				while($foo = $res->fetch_assoc())
				{
					$data[$foo['plotName']] = $foo['plotName'];
				}
				
				return $data;
			}
			else
			{
				return array();
			}
		}
		
		public static function getAllHttpDLTestPlotNameByUserId()
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT plotName, plotStatus FROM httpDownloadTestPlot WHERE userId=%s AND isDeleted=0", array($COCKPIT_SYSTEM_DEF['userId']));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$data = array();
		
				while($foo = $res->fetch_assoc())
				{
					$data[$foo['plotName']] = $foo['plotName'];
				}
		
				return $data;
			}
			else
			{
				return array();
			}
		}
		
		public static function getAllHttpTestPlotHtmlNameByUserId($pageStart=0, $pageEnd=100)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query	= $db->quoteInto(
				"SELECT plotName, plotStatus, hostIpOwnership FROM httpTestPlot WHERE userId=%s AND isDeleted=0 LIMIT $pageStart, $pageEnd",
				array($COCKPIT_SYSTEM_DEF['userId'])
			);
			
			$res	= $db->queryOther('cockpit', $query);
			
			if($res->num_rows)
			{
				$data = "<tr><th width='5%'></th><th>Plot Name</th><th>Status</th><th>Options</th></tr>";
		
				while($foo = $res->fetch_assoc())
				{
					$plotStatus = "Not Tested Yet";
					if($foo['plotStatus'] == 1)			$plotStatus = 'Accepted';
					else if($foo['plotStatus'] == 2)	$plotStatus = "Testing &nbsp;<img src='../images/busy.gif'></img>";
					else if($foo['plotStatus'] == 3)	$plotStatus = 'Completed';
					
					// XXX: IMPORTANT - if 'hostIpOwnership' is not 1 then we must display that the plot host ip is not confirmed
					if($foo['hostIpOwnership'] == 0)
					{
						$fileName	= md5($COCKPIT_SYSTEM_DEF['userId'] . CSettings::$HOST_IP_OWNERSHIP_FILENAME_SALT) . ".html";
						$confirmOwnerShip = "&nbsp;<a href='#' class='checkHostOwnership' id='" . $foo['plotName'] . "|http'>[Check Ownership]</a>";
						$plotStatus	= "<img src='../images/warning.png'></img>&nbsp;Host ownership not confirmed&nbsp;<a href='hostOwnerShipFile.php?fileName=" . $fileName . "&amp;plotName=" . $foo['plotName'] . "' target='_blank'>[Upload this file]</a>";
						$plotStatus	.= $confirmOwnerShip;
					}

					// XXX: IMPORTANT - remove all spaces and dots from plot name
					$filteredPlotName = str_replace(array(' ', '.'), array('space', 'dot'), $foo['plotName']);
					
					$data .= "
					<tr>
						<td><input type='radio' value='" . $foo['plotName'] . "' name='plotName' /></td>
						<td>" . $foo['plotName'] . "</td>
						<td><div id='" . $filteredPlotName . "Status'>$plotStatus</div></td>
						<td>
							<a href='#configContent' rel='leanModal' class='plotConfig displayTableCell' id='" . $foo['plotName'] . "|http'>
								<div>
									<img src='../images/config.png'></img>
								</div>
							</a>
							<a href='#editContent' rel='plotEditModal' class='plotEdit displayTableCell' id='" . $foo['plotName'] . "'>
								<div>
									<img src='../images/edit.png'></img>
								</div>
							</a>
							<a href='#deleteContent' class='plotDelete displayTableCell' id='" . $foo['plotName'] . "'>
								<div>
									<img src='../images/closeBtn.png' height='16'></img>
								</div>
							</a>
						</td>
					</tr>";
				}
		
				return $data;
			}
			else
			{
				return '<tr><td colspan="4">No test plot exist</td></tr>';
			}
		}
		
		/**
		 * List up httpDownloadTestPlot
		 * @param number $pageStart
		 * @param number $pageEnd
		 * @return string
		 */
		public static function getAllHttpDLTestPlotHtmlNameByUserId($pageStart=0, $pageEnd=100)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query	= $db->quoteInto(
				"SELECT plotName, plotStatus, hostIpOwnership FROM httpDownloadTestPlot WHERE userId=%s AND isDeleted=0 LIMIT $pageStart, $pageEnd",
				array($COCKPIT_SYSTEM_DEF['userId'])
			);
				
			$res	= $db->queryOther('cockpit', $query);
				
			if($res->num_rows)
			{
				$data = "<tr><th width='5%'></th><th>Plot Name</th><th>Status</th><th>Options</th></tr>";
		
				while($foo = $res->fetch_assoc())
				{
					$plotStatus = "Not Tested Yet";
					if($foo['plotStatus'] == 1)			$plotStatus = 'Accepted';
					else if($foo['plotStatus'] == 2)	$plotStatus = "Testing &nbsp;<img src='../images/busy.gif'></img>";
					else if($foo['plotStatus'] == 3)	$plotStatus = 'Completed';
						
					// XXX: IMPORTANT - if 'hostIpOwnership' is not 1 then we must display that the plot host ip is not confirmed
					if($foo['hostIpOwnership'] == 0)
					{
						$fileName			= md5($COCKPIT_SYSTEM_DEF['userId'] . CSettings::$HOST_IP_OWNERSHIP_FILENAME_SALT) . ".html";
						$plotStatus			= "<img src='../images/warning.png'></img>&nbsp;Host ownership not confirmed&nbsp;<a href='httpDLHostOwnerShipFile.php?fileName=" . $fileName . "&amp;plotName=" . $foo['plotName'] . "' target='_blank'>[Upload this file]</a>";
						$confirmOwnerShip	= "&nbsp;<a href='#' class='checkHostOwnership' id='" . $foo['plotName'] . "|http'>[Check Ownership]</a>";
						$plotStatus			.= $confirmOwnerShip;
					}
		
					// XXX: IMPORTANT - remove all spaces and dots from plot name
					$filteredPlotName = str_replace(array(' ', '.'), array('space', 'dot'), $foo['plotName']);
						
					$data .= "
					<tr>
						<td><input type='radio' value='" . $foo['plotName'] . "' name='plotName' /></td>
						<td>" . $foo['plotName'] . "</td>
						<td><div id='" . $filteredPlotName . "Status'>$plotStatus</div></td>
								<td>
								<a href='#configContent' rel='leanModal' class='plotConfig displayTableCell' id='" . $foo['plotName'] . "|http'>
								<div>
									<img src='../images/config.png'></img>
								</div>
							</a>
							<a href='#editContent' rel='plotEditModal' class='plotEdit displayTableCell' id='" . $foo['plotName'] . "'>
								<div>
									<img src='../images/edit.png'></img>
								</div>
							</a>
							<a href='#deleteContent' class='plotDelete displayTableCell' id='" . $foo['plotName'] . "'>
								<div>
									<img src='../images/closeBtn.png' height='16'></img>
								</div>
							</a>
						</td>
					</tr>";
				}
		
				return $data;
			}
			else
				{
				return '<tr><td colspan="4">No test plot exist</td></tr>';
			}
		}
		
		/**
		 * List up httpUploadTestPlot
		 * @param number $pageStart
		 * @param number $pageEnd
		 * @return string
		 */
		public static function getAllHttpULTestPlotHtmlNameByUserId($pageStart=0, $pageEnd=100)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query	= $db->quoteInto(
				"SELECT plotName, plotStatus, hostIpOwnership FROM httpUploadTestPlot WHERE userId=%s AND isDeleted=0 LIMIT $pageStart, $pageEnd",
				array($COCKPIT_SYSTEM_DEF['userId'])
			);
		
			$res	= $db->queryOther('cockpit', $query);
		
			if($res->num_rows)
			{
				$data = "<tr><th width='5%'></th><th>Plot Name</th><th>Status</th><th>Options</th></tr>";
		
				while($foo = $res->fetch_assoc())
				{
					$plotStatus = "Not Tested Yet";
					if($foo['plotStatus'] == 1)			$plotStatus = 'Accepted';
					else if($foo['plotStatus'] == 2)	$plotStatus = "Testing &nbsp;<img src='../images/busy.gif'></img>";
					else if($foo['plotStatus'] == 3)	$plotStatus = 'Completed';
		
					// XXX: IMPORTANT - if 'hostIpOwnership' is not 1 then we must display that the plot host ip is not confirmed
					if($foo['hostIpOwnership'] == 0)
					{
						$fileName			= md5($COCKPIT_SYSTEM_DEF['userId'] . CSettings::$HOST_IP_OWNERSHIP_FILENAME_SALT) . ".html";
						$plotStatus			= "<img src='../images/warning.png'></img>&nbsp;Host ownership not confirmed&nbsp;<a href='httpULHostOwnerShipFile.php?fileName=" . $fileName . "&amp;plotName=" . $foo['plotName'] . "' target='_blank'>[Upload this file]</a>";
						$confirmOwnerShip	= "&nbsp;<a href='#' class='checkHostOwnership' id='" . $foo['plotName'] . "|http'>[Check Ownership]</a>";
						$plotStatus			.= $confirmOwnerShip;
					}
		
					// XXX: IMPORTANT - remove all spaces and dots from plot name
					$filteredPlotName = str_replace(array(' ', '.'), array('space', 'dot'), $foo['plotName']);
		
					$data .= "
					<tr>
						<td><input type='radio' value='" . $foo['plotName'] . "' name='plotName' /></td>
						<td>" . $foo['plotName'] . "</td>
						<td><div id='" . $filteredPlotName . "Status'>$plotStatus</div></td>
								<td>
								<a href='#configContent' rel='leanModal' class='plotConfig displayTableCell' id='" . $foo['plotName'] . "|http'>
								<div>
									<img src='../images/config.png'></img>
								</div>
							</a>
							<a href='#editContent' rel='plotEditModal' class='plotEdit displayTableCell' id='" . $foo['plotName'] . "'>
									<div>
									<img src='../images/edit.png'></img>
								</div>
							</a>
							<a href='#deleteContent' class='plotDelete displayTableCell' id='" . $foo['plotName'] . "'>
									<div>
									<img src='../images/closeBtn.png' height='16'></img>
								</div>
							</a>
						</td>
					</tr>";
				}
		
				return $data;
			}
			else
			{
			return '<tr><td colspan="4">No test plot exist</td></tr>';
			}
		}
		
		public static function getHttpTestPlotByName($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			$query = $db->quoteInto("SELECT * FROM httpTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				return $foo;
			}
			else
			{
				return false;
			}
		}
		
		public static function getHttpDLTestPlotByName($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$query = $db->quoteInto("SELECT * FROM httpDownloadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				return $foo;
			}
			else
			{
				return false;
			}
		}
		
		public static function getHttpULTestPlotByName($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT * FROM httpUploadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				return $foo;
			}
			else
			{
				return false;
			}
		}

		public static function getHttpTestPlotByUserIdAndName($userId, $plotName)
		{
			global $db;
				
			$query = $db->quoteInto("SELECT * FROM httpTestPlot WHERE userId=%s AND plotName=%s", array($userId, $plotName));
			
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				return $foo;
			}
			else
			{
				return false;
			}
		}

		public static function getHttpDLTestPlotByUserIdAndName($userId, $plotName)
		{
			global $db;
		
			$query = $db->quoteInto("SELECT * FROM httpDownloadTestPlot WHERE userId=%s AND plotName=%s", array($userId, $plotName));
				
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				return $foo;
			}
			else
			{
				return false;
			}
		}
		
		public static function getHttpULTestPlotByUserIdAndName($userId, $plotName)
		{
			global $db;
		
			$query = $db->quoteInto("SELECT * FROM httpUploadTestPlot WHERE userId=%s AND plotName=%s", array($userId, $plotName));
		
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				return $foo;
			}
			else
			{
				return false;
			}
		}
		
		public static function getTotalNumberOfExecutedHttpPlot()
		{
			global $db;
			
			$query = "SELECT totalHttpPlotExecuted FROM systemInfo WHERE id=1";
			
			$res = $db->queryOther('cockpit', $query);
				
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				
				return $foo['totalHttpPlotExecuted'];
			}
			else
			{
				return 0;
			}
		}
		
		public static function isHttpTestPlotExists($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$query = $db->quoteInto("SELECT null FROM httpTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			
			$res = $db->queryOther('cockpit', $query);
			
			if($res->num_rows)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public static function isHttpDLTestPlotExists($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT null FROM httpDownloadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
				
			$res = $db->queryOther('cockpit', $query);
				
			if($res->num_rows)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public static function isHttpULTestPlotExists($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT null FROM httpUploadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
		
			$res = $db->queryOther('cockpit', $query);
		
			if($res->num_rows)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public static function isHttpTestPlotAvailableByStatus($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$query = $db->quoteInto("SELECT plotStatus FROM httpTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				if($foo['plotStatus'] == 0 OR $foo['plotStatus'] == 3) return true;
				else return false;
			}
			else
			{
				return false;
			}
		}

		public static function isHttpDLTestPlotAvailableByStatus($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT plotStatus FROM httpDownloadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				if($foo['plotStatus'] == 0 OR $foo['plotStatus'] == 3) return true;
				else return false;
			}
			else
			{
				return false;
			}
		}
		
		public static function isHttpULTestPlotAvailableByStatus($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT plotStatus FROM httpUploadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				if($foo['plotStatus'] == 0 OR $foo['plotStatus'] == 3) return true;
				else return false;
			}
			else
			{
				return false;
			}
		}
		
		public static function isHttpTestPlotHostIpOwnership($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT hostIpOwnership FROM httpTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				if($foo['hostIpOwnership'] == 1) return true;
				else return false;
			}
			else
			{
				return false;
			}
		}
		
		public static function isHttpDLTestPlotHostIpOwnership($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT hostIpOwnership FROM httpDownloadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				if($foo['hostIpOwnership'] == 1) return true;
				else return false;
			}
			else
			{
				return false;
			}
		}

		public static function isHttpULTestPlotHostIpOwnership($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT hostIpOwnership FROM httpUploadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				if($foo['hostIpOwnership'] == 1) return true;
				else return false;
			}
			else
			{
				return false;
			}
		}
		
		public static function getHttpTestPlotStatusByName($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT plotStatus, hostIpOwnership FROM  httpTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
			
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				
				// XXX: IMPORTANT - host ip not confirmed yet
				if($foo['hostIpOwnership'] == 0) return 4;
				
				return $foo['plotStatus'];
			}
			else
			{
				return -1;
			}
		}
		
		public static function getHttpDLTestPlotStatusByName($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT plotStatus, hostIpOwnership FROM httpDownloadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
				
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
		
				// XXX: IMPORTANT - host ip not confirmed yet
				if($foo['hostIpOwnership'] == 0) return 4;
		
				return $foo['plotStatus'];
			}
			else
			{
				return -1;
			}
		}
		
		public static function getHttpULTestPlotStatusByName($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = $db->quoteInto("SELECT plotStatus, hostIpOwnership FROM httpUploadTestPlot WHERE userId=%s AND plotName=%s", array($COCKPIT_SYSTEM_DEF['userId'], $plotName));
			$res = $db->queryOther('cockpit', $query);
		
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
		
				// XXX: IMPORTANT - host ip not confirmed yet
				if($foo['hostIpOwnership'] == 0) return 4;
		
				return $foo['plotStatus'];
			}
			else
			{
				return -1;
			}
		}
		
		public static function getInstanceStatus($name, $host)
		{
			global $pad;
			try
			{
				$controlCommand = CSettings::$COCKPIT_CMD_INDEX['COCKPITD_CMD_INDEX_HTTPD_STATUS'];
				
				require_once('CCrypt.php');
				$crypt = new CCrypt();
				$controlCommandCrypted = $crypt->encrypt((string)$controlCommand, CSettings::$COMMAND_KEY);
				
				$sendCmdData = $controlCommandCrypted;
				$sendCmdData .= "\0";
				$sendCmdData .= $name;
				$sendCmdData .= "\0";

				// Send Packet and wait for maximum 5 seconds
				$resultRcv = $pad->sendRaw($host, $sendCmdData, 5000);
				$splitFormat = array(4, 4, 255, 1);
				$resultRcv = self::funPack($splitFormat, $resultRcv);
				$status = unpack("istatus", $resultRcv[0]);
				$flagStopWebStatMail = unpack("ihttpdownmail", $resultRcv[1]);
				
				return array($status, $flagStopWebStatMail);
			}
			catch(Exception $e)
			{
				// Unknown error happend
				$status['status'] = 0;
				return $status;
			}
		}
		
		public static function triggerHttpTestPlotRunRequest($postData)
		{
			global $COCKPIT_SYSTEM_DEF;
			global $pad;
			
			try
			{
				$controlCommand = CSettings::$COCKPIT_CMD_INDEX['COCKPIT_CMD_INDEX_RUN_SPECIFIC_PLOT'];
				
				include_once 'CCrypt.php';
				
				$crypt = new CCrypt();
				$controlCommandCrypted = $crypt->encrypt((string)$controlCommand, CSettings::$COMMAND_KEY);
				
				$sendCmdData = $controlCommandCrypted;
				$sendCmdData .= "\0";
				$sendCmdData .= $COCKPIT_SYSTEM_DEF['userId'];
				$sendCmdData .= "\0";
				$sendCmdData .= $postData['plotName'];
				$sendCmdData .= "\0";
				$sendCmdData .= 'http';
				$sendCmdData .= "\0";
			
				// Send run request to testerbrokerd daemon
				$resultRcv = $pad->sendRaw('localhost', $sendCmdData, 20000);
			
				$splitFormat = array(4, 255, 1);
				$resultRcv = self::funPack($splitFormat, $resultRcv);
				$status = unpack("istatus", $resultRcv[0]);
				return $status;
			
			}
			catch(Exception $e)
			{
				// Unknown error happend
				$status['status'] = 0;
				return $status;
			}
		}
		
		public static function triggerHttpDLTestPlotRunRequest($postData)
		{
			global $COCKPIT_SYSTEM_DEF;
			global $pad;
				
			try
			{
				$controlCommand = CSettings::$COCKPIT_CMD_INDEX['COCKPIT_CMD_INDEX_RUN_SPECIFIC_DL_PLOT'];
		
				include_once 'CCrypt.php';
		
				$crypt = new CCrypt();
				$controlCommandCrypted = $crypt->encrypt((string)$controlCommand, CSettings::$COMMAND_KEY);
		
				$sendCmdData = $controlCommandCrypted;
				$sendCmdData .= "\0";
				$sendCmdData .= $COCKPIT_SYSTEM_DEF['userId'];
				$sendCmdData .= "\0";
				$sendCmdData .= $postData['plotName'];
				$sendCmdData .= "\0";
				$sendCmdData .= 'httpdl';
				$sendCmdData .= "\0";
					
				// Send run request to testerbrokerd daemon
				$resultRcv = $pad->sendRaw('localhost', $sendCmdData, 20000);
					
				$splitFormat = array(4, 255, 1);
				$resultRcv = self::funPack($splitFormat, $resultRcv);
				$status = unpack("istatus", $resultRcv[0]);
				return $status;
					
			}
			catch(Exception $e)
			{
				// Unknown error happend
				$status['status'] = 0;
				return $status;
			}
		}
		
		public static function triggerHttpULTestPlotRunRequest($postData)
		{
			global $COCKPIT_SYSTEM_DEF;
			global $pad;
		
			try
			{
				$controlCommand = CSettings::$COCKPIT_CMD_INDEX['COCKPIT_CMD_INDEX_RUN_SPECIFIC_UL_PLOT'];
		
				include_once 'CCrypt.php';
		
				$crypt = new CCrypt();
				$controlCommandCrypted = $crypt->encrypt((string)$controlCommand, CSettings::$COMMAND_KEY);
		
				$sendCmdData = $controlCommandCrypted;
				$sendCmdData .= "\0";
				$sendCmdData .= $COCKPIT_SYSTEM_DEF['userId'];
				$sendCmdData .= "\0";
				$sendCmdData .= $postData['plotName'];
				$sendCmdData .= "\0";
				$sendCmdData .= 'httpul';
				$sendCmdData .= "\0";
					
				// Send run request to testerbrokerd daemon
				$resultRcv = $pad->sendRaw('localhost', $sendCmdData, 20000);
					
				$splitFormat = array(4, 255, 1);
				$resultRcv = self::funPack($splitFormat, $resultRcv);
				$status = unpack("istatus", $resultRcv[0]);
				return $status;
					
			}
			catch(Exception $e)
			{
				// Unknown error happend
				$status['status'] = 0;
				return $status;
			}
		}
		
		/**
		 * For HTTP test plot analyzing
		 */
		public static function generateHttpTestPlotReportToMongo($foo)
		{
			global $mongo;
				
			if($foo)
			{
				if(empty($foo['plotReportDb'])) return false;
					
				$cursor = $mongo->getAllData($foo['plotReportDb'], 'httpTestPlot', array('userId' => (int) $foo['userId'], 'plotName' => $foo['plotName']));
		
				$analyzedData = array(
					'totalHit' => $cursor->count(), 'successHit' => 0, 'failedHit' => 0,
					'response200' => 0, 'response401' => 0, 'response500' => 0, 'responseTimeout' => 0,
					'nameResolutionTime' => 0.0, 'connectTime' => 0.0, 'preTransferTime' => 0.0,
					'startTransferTime' => 0.0, 'avgDownloadSpeed' => 0.0
				);
		
				$totalResponseTimeFor200	= 0;
				$totalResponseTimeFor401	= 0;
				$totalResponseTimeFor500	= 0;
		
				foreach($cursor as $doc)
				{
					($doc['curlStatus'] == 0)	? $analyzedData['successHit'] += 1		: false;
					($doc['curlStatus'] != 0)	? $analyzedData['failedHit'] += 1		: false;
					($doc['curlStatus'] == 28)	? $analyzedData['responseTimeout'] += 1	: false;
						
					if($doc['httpCode'] == 200)
					{
						$analyzedData['response200']		+= 1;
		
						$totalResponseTimeFor200			+= $doc['processDelta'];
		
						$analyzedData['avgDownloadSpeed']	+= $doc['avgDownloadSpeed'];
					}
					else if($doc['httpCode'] == 401)
					{
						$analyzedData['response401'] += 1;
						
						$totalResponseTimeFor401			+= $doc['processDelta'];
					}
					else if($doc['httpCode'] == 500)
					{
						$analyzedData['response500'] += 1;
						
						$totalResponseTimeFor500			+= $doc['processDelta'];
					}
					// XXX: TODO - add more status code
					
					$analyzedData['nameResolutionTime'] 	+= $doc['nameResolutionTime'];
					$analyzedData['connectTime']			+= $doc['connectTime'];
					
					$analyzedData['preTransferTime'] 		+= $doc['preTransferTime'];
					$analyzedData['startTransferTime']		+= $doc['startTransferTime'];
				}
		
				// XXX: IMPORTANT - in milli second
				if($totalResponseTimeFor200 == 0) $analyzedData['avgResponse200'] = 0;
				else $analyzedData['avgResponse200']= ($totalResponseTimeFor200 / $analyzedData['response200']) * 1000;
				
				if($totalResponseTimeFor401 == 0) $analyzedData['avgResponse401'] = 0;
				else $analyzedData['avgResponse401']= ($totalResponseTimeFor401 / $analyzedData['response401']) * 1000;
				
				if($totalResponseTimeFor500 == 0) $analyzedData['avgResponse500'] = 0;
				else $analyzedData['avgResponse500']= ($totalResponseTimeFor500 / $analyzedData['response500']) * 1000;
				
				$analyzedData['nameResolutionTime'] = ($analyzedData['nameResolutionTime'] / $analyzedData['totalHit']) * 1000;
				$analyzedData['connectTime']		= ($analyzedData['connectTime'] / $analyzedData['totalHit']) * 1000;
				
				$analyzedData['preTransferTime']	= ($analyzedData['preTransferTime'] / $analyzedData['totalHit']) * 1000;
				$analyzedData['startTransferTime']	= ($analyzedData['startTransferTime'] / $analyzedData['totalHit']) * 1000;
				
				// in kbyte/sec
				$analyzedData['avgDownloadSpeed']	= ($analyzedData['avgDownloadSpeed'] / $analyzedData['response200']);
				
				$analyzedData['maxHops']			= $foo['maxHops'] ? $foo['maxHops'] : 0;
		
				return $analyzedData;
			}
			else
			{
				return false;
			}
		}
		
		/**
		 * For download test plot analyzing
		 */
		public static function generateHttpDLTestPlotReportToMongo($foo)
		{
			global $mongo;
		
			if($foo)
			{
				if(empty($foo['plotReportDb'])) return false;
					
				$cursor = $mongo->getAllData($foo['plotReportDb'], 'httpDownloadTestPlot', array('userId' => (int) $foo['userId'], 'plotName' => $foo['plotName']));
		
				$analyzedData = array(
					'totalHit' => $cursor->count(), 'successHit' => 0, 'failedHit' => 0,
					'response200' => 0, 'response401' => 0, 'response500' => 0, 'responseTimeout' => 0,
					'nameResolutionTime' => 0.0, 'connectTime' => 0.0, 'preTransferTime' => 0.0,
					'startTransferTime' => 0.0, 'avgDownloadSpeed' => 0.0
				);
		
				$totalResponseTimeFor200	= 0;
				$totalResponseTimeFor401	= 0;
				$totalResponseTimeFor500	= 0;
		
				foreach($cursor as $doc)
				{
					($doc['curlStatus'] == 0)	? $analyzedData['successHit'] += 1		: false;
					($doc['curlStatus'] != 0)	? $analyzedData['failedHit'] += 1		: false;
					($doc['curlStatus'] == 28)	? $analyzedData['responseTimeout'] += 1	: false;
		
					if($doc['httpCode'] == 200)
					{
						$analyzedData['response200']		+= 1;
		
						$totalResponseTimeFor200			+= $doc['processDelta'];
		
						$analyzedData['avgDownloadSpeed']	+= $doc['avgDownloadSpeed'];
					}
					else if($doc['httpCode'] == 401)
					{
						$analyzedData['response401'] += 1;
						
						$totalResponseTimeFor401			+= $doc['processDelta'];
					}
					else if($doc['httpCode'] == 500)
					{
						$analyzedData['response500'] += 1;
						
						$totalResponseTimeFor500			+= $doc['processDelta'];
					}
					// XXX: TODO - add more status code
					
					$analyzedData['nameResolutionTime'] 	+= $doc['nameResolutionTime'];
					$analyzedData['connectTime']			+= $doc['connectTime'];
					
					$analyzedData['preTransferTime'] 		+= $doc['preTransferTime'];
					$analyzedData['startTransferTime']		+= $doc['startTransferTime'];
				}
		
				// XXX: IMPORTANT - in milli second
				if($totalResponseTimeFor200 == 0) $analyzedData['avgResponse200'] = 0;
				else $analyzedData['avgResponse200']= ($totalResponseTimeFor200 / $analyzedData['response200']) * 1000;
				
				if($totalResponseTimeFor401 == 0) $analyzedData['avgResponse401'] = 0;
				else $analyzedData['avgResponse401']= ($totalResponseTimeFor401 / $analyzedData['response401']) * 1000;
				
				if($totalResponseTimeFor500 == 0) $analyzedData['avgResponse500'] = 0;
				else $analyzedData['avgResponse500']= ($totalResponseTimeFor500 / $analyzedData['response500']) * 1000;
				
				$analyzedData['nameResolutionTime'] = ($analyzedData['nameResolutionTime'] / $analyzedData['totalHit']) * 1000;
				$analyzedData['connectTime']		= ($analyzedData['connectTime'] / $analyzedData['totalHit']) * 1000;
				
				$analyzedData['preTransferTime']	= ($analyzedData['preTransferTime'] / $analyzedData['totalHit']) * 1000;
				$analyzedData['startTransferTime']	= ($analyzedData['startTransferTime'] / $analyzedData['totalHit']) * 1000;
				
				// in kbyte/sec
				$analyzedData['avgDownloadSpeed']	= ($analyzedData['avgDownloadSpeed'] / $analyzedData['response200']);
				
				$analyzedData['maxHops']			= $foo['maxHops'] ? $foo['maxHops'] : 0;
		
				return $analyzedData;
			}
			else
			{
				return false;
			}
		}
		
		/**
		 * For upload test plot analyzing
		 */
		public static function generateHttpULTestPlotReportToMongo($foo)
		{
			global $mongo;
		
			if($foo)
			{
				if(empty($foo['plotReportDb'])) return false;
					
				$cursor = $mongo->getAllData($foo['plotReportDb'], 'httpUploadTestPlot', array('userId' => (int) $foo['userId'], 'plotName' => $foo['plotName']));
		
				$analyzedData = array(
						'totalHit' => $cursor->count(), 'successHit' => 0, 'failedHit' => 0,
						'response200' => 0, 'response401' => 0, 'response500' => 0, 'responseTimeout' => 0,
						'nameResolutionTime' => 0.0, 'connectTime' => 0.0, 'preTransferTime' => 0.0,
						'startTransferTime' => 0.0, 'avgUploadSpeed' => 0.0
				);
		
				$totalResponseTimeFor200	= 0;
				$totalResponseTimeFor401	= 0;
				$totalResponseTimeFor500	= 0;
		
				foreach($cursor as $doc)
				{
					($doc['curlStatus'] == 0)	? $analyzedData['successHit'] += 1		: false;
					($doc['curlStatus'] != 0)	? $analyzedData['failedHit'] += 1		: false;
					($doc['curlStatus'] == 28)	? $analyzedData['responseTimeout'] += 1	: false;
		
					if($doc['httpCode'] == 200)
					{
						$analyzedData['response200']		+= 1;
		
						$totalResponseTimeFor200			+= $doc['processDelta'];
		
						$analyzedData['avgUploadSpeed']		+= $doc['avgUploadSpeed'];
					}
					else if($doc['httpCode'] == 401)
					{
						$analyzedData['response401'] += 1;
		
						$totalResponseTimeFor401			+= $doc['processDelta'];
					}
					else if($doc['httpCode'] == 500)
					{
						$analyzedData['response500'] += 1;
		
						$totalResponseTimeFor500			+= $doc['processDelta'];
					}
					// XXX: TODO - add more status code
						
					$analyzedData['nameResolutionTime'] 	+= $doc['nameResolutionTime'];
					$analyzedData['connectTime']			+= $doc['connectTime'];
						
					$analyzedData['preTransferTime'] 		+= $doc['preTransferTime'];
					$analyzedData['startTransferTime']		+= $doc['startTransferTime'];
				}
		
				// XXX: IMPORTANT - in milli second
				if($totalResponseTimeFor200 == 0) $analyzedData['avgResponse200'] = 0;
				else $analyzedData['avgResponse200']= ($totalResponseTimeFor200 / $analyzedData['response200']) * 1000;
		
				if($totalResponseTimeFor401 == 0) $analyzedData['avgResponse401'] = 0;
				else $analyzedData['avgResponse401']= ($totalResponseTimeFor401 / $analyzedData['response401']) * 1000;
		
				if($totalResponseTimeFor500 == 0) $analyzedData['avgResponse500'] = 0;
				else $analyzedData['avgResponse500']= ($totalResponseTimeFor500 / $analyzedData['response500']) * 1000;
		
				$analyzedData['nameResolutionTime'] = ($analyzedData['nameResolutionTime'] / $analyzedData['totalHit']) * 1000;
				$analyzedData['connectTime']		= ($analyzedData['connectTime'] / $analyzedData['totalHit']) * 1000;
		
				$analyzedData['preTransferTime']	= ($analyzedData['preTransferTime'] / $analyzedData['totalHit']) * 1000;
				$analyzedData['startTransferTime']	= ($analyzedData['startTransferTime'] / $analyzedData['totalHit']) * 1000;
		
				// in kbyte/sec
				$analyzedData['avgUploadSpeed']	= ($analyzedData['avgUploadSpeed'] / $analyzedData['response200']);
		
				$analyzedData['maxHops']			= $foo['maxHops'] ? $foo['maxHops'] : 0;
		
				return $analyzedData;
			}
			else
			{
				return false;
			}
		}
		
		public static function insertAnalyzedHttpDataInMongo($userId, $plotName, $analyzedData)
		{
			global $mongo;
			
			$analyzedData['userId']			= (int) $userId;
			$analyzedData['plotName']		= $plotName;
			$analyzedData['dateCreated']	= time(NULL);
						
			$res = $mongo->insert('cockpit', 'httpTestPlotReport', $analyzedData, array('w' => 1));
			
			if(is_array($res))
			{
				return $res['err'] ? false : true;
			}
			
			return $res;
		}
		
		public static function insertAnalyzedHttpDLDataInMongo($userId, $plotName, $analyzedData)
		{
			global $mongo;
				
			$analyzedData['userId']			= (int) $userId;
			$analyzedData['plotName']		= $plotName;
			$analyzedData['dateCreated']	= time(NULL);
		
			$res = $mongo->insert('cockpit', 'httpDownloadTestPlotReport', $analyzedData, array('w' => 1));
				
			if(is_array($res))
			{
				return $res['err'] ? false : true;
			}
				
			return $res;
		}

		public static function insertAnalyzedHttpULDataInMongo($userId, $plotName, $analyzedData)
		{
			global $mongo;
		
			$analyzedData['userId']			= (int) $userId;
			$analyzedData['plotName']		= $plotName;
			$analyzedData['dateCreated']	= time(NULL);
		
			$res = $mongo->insert('cockpit', 'httpUploadTestPlotReport', $analyzedData, array('w' => 1));
		
			if(is_array($res))
			{
				return $res['err'] ? false : true;
			}
		
			return $res;
		}
		
		public static function removeRawHttpTestDataFromMongo($userId, $plotName, $plotReportDb)
		{
			global $mongo;
			
			$res = $mongo->delete($plotReportDb, 'httpTestPlot', array('userId' => (int) $userId, 'plotName' => (string) $plotName), array('w' => 1));
			
			if(is_array($res))
			{
				return $res['err'] ? false : true;
			}
				
			return $res;
		}
		
		public static function removeRawHttpDLTestDataFromMongo($userId, $plotName, $plotReportDb)
		{
			global $mongo;
				
			$res = $mongo->delete($plotReportDb, 'httpDownloadTestPlot', array('userId' => (int) $userId, 'plotName' => (string) $plotName), array('w' => 1));
				
			if(is_array($res))
			{
				return $res['err'] ? false : true;
			}
		
			return $res;
		}

		public static function removeRawHttpULTestDataFromMongo($userId, $plotName, $plotReportDb)
		{
			global $mongo;
		
			$res = $mongo->delete($plotReportDb, 'httpUploadTestPlot', array('userId' => (int) $userId, 'plotName' => (string) $plotName), array('w' => 1));
		
			if(is_array($res))
			{
				return $res['err'] ? false : true;
			}
		
			return $res;
		}
		
		public static function createHttpTestPlot($insert)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			$query	= $db->quoteInto('SELECT null FROM httpTestPlot WHERE userId=%s AND plotName=%s', array($COCKPIT_SYSTEM_DEF['userId'], $insert['plotName']));
			$res	= $db->queryOther('cockpit', $query);
			
			if($res->num_rows) return false;
			
			// remove scheme, port, query, fragment, user, pass from base address
			$baseAddress	= trim($insert['baseAddress']);
			$hostIpAddress	= "";
			$ipLatiTude		= "";
			$ipLongiTude	= "";
			
			$urlParts = parse_url($baseAddress);
			
			$baseAddress = $urlParts['host'] . $urlParts['path'];
			
			if($urlParts['scheme'] == 'http')		$insert['type'] = 1;
			else if($urlParts['scheme'] == 'https')	$insert['type'] = 2;
			
			if(filter_var($urlParts['host'], FILTER_VALIDATE_IP))
			{
				$hostIpAddress = $urlParts['host'];
			}
			else
			{
				// that means base address is a domain/subdomain. IPV4 only at this moment
				$hostIpAddress = gethostbyname($urlParts['host']);
			}
			
			// XXX: IMPORTANT - if we have a valid ip address
			if(strlen($hostIpAddress) > 0)
			{
				if($hostIpAddress !== "127.0.0.1")
				{
					$response = Unirest::get("http://ipinfo.io/" . $hostIpAddress . "/geo");
					
					if($response->body->loc)
					{
						$ipLocation = explode(',', $response->body->loc);
							
						if(is_array($ipLocation))
						{
							$ipLatiTude		= trim($ipLocation[0]);
							$ipLongiTude	= trim($ipLocation[1]);
						}
					}
				}
			}
			
			// XXX: IMPORTANT - '|' character not allowed
			$insert['plotName'] = str_replace('|', '', $insert['plotName']);
			
			$data = array(
				'userId'				=> $COCKPIT_SYSTEM_DEF['userId'],
				'plotName'				=> $insert['plotName'],
				'type'					=> $insert['type'],
				'method'				=> $insert['method'],
				'contentType'			=> $insert['contentType'],
				'accept'				=> $insert['accept'],
				'charset'				=> $insert['charset'],
				'baseAddress'			=> $baseAddress,
				'queryData'				=> trim($insert['queryData']),
				'hostIpAddress'			=> $hostIpAddress,
				'hostIpLatiTude'		=> $ipLatiTude,
				'hostIpLongiTude'		=> $ipLongiTude,
				'accessLimit'			=> $insert['accessLimit'],
				'responseTimeoutLimit'	=> $insert['responseTimeoutLimit'],
				'authType'				=> $insert['authType'],
				'authUser'				=> $insert['authUser'] ? trim($insert['authUser']) : '',
				'authPassword'			=> $insert['authPassword'] ? trim($insert['authPassword']) : '',
				'consumerKey'			=> $insert['consumerKey'] ? trim($insert['consumerKey']) : '',
				'consumerSecret'		=> $insert['consumerSecret'] ? trim($insert['consumerSecret']) : '',
				'token'					=> $insert['token'] ? trim($insert['token']) : '',
				'tokenSecret'			=> $insert['tokenSecret'] ? trim($insert['tokenSecret']) : '',
				'dateCreated'			=> date('Y-m-d H:i:s'),
				'dateUpdated'			=> date('Y-m-d H:i:s'),
			);
				
			$id = $db->insertOther('cockpit', 'httpTestPlot', $data);
			
			return $id;
		}
		
		public static function createHttpDLTestPlot($insert)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$query	= $db->quoteInto('SELECT null FROM httpDownloadTestPlot WHERE userId=%s AND plotName=%s', array($COCKPIT_SYSTEM_DEF['userId'], $insert['plotName']));
			$res	= $db->queryOther('cockpit', $query);
				
			if($res->num_rows) return false;
				
			// remove scheme, port, query, fragment, user, pass from base address
			$baseAddress	= trim($insert['baseAddress']);
			$hostIpAddress	= "";
			$ipLatiTude		= "";
			$ipLongiTude	= "";
				
			$urlParts		= parse_url($baseAddress);
			$baseAddress	= $urlParts['host'] . $urlParts['path'];
			
			if($urlParts['scheme'] == 'http')		$insert['type'] = 1;
			else if($urlParts['scheme'] == 'https')	$insert['type'] = 2;
			
			if(filter_var($urlParts['host'], FILTER_VALIDATE_IP))
			{
				$hostIpAddress = $urlParts['host'];
			}
			else
			{
				// that means base address is a domain/subdomain. IPV4 only at this moment
				$hostIpAddress = gethostbyname($urlParts['host']);
			}
				
			// XXX: IMPORTANT - if we have a valid ip address
			if(strlen($hostIpAddress) > 0)
			{
				if($hostIpAddress !== "127.0.0.1")
				{
					$response = Unirest::get("http://ipinfo.io/" . $hostIpAddress . "/geo");
			
					if($response->body->loc)
					{
						$ipLocation = explode(',', $response->body->loc);
			
						if(is_array($ipLocation))
						{
							$ipLatiTude		= trim($ipLocation[0]);
							$ipLongiTude	= trim($ipLocation[1]);
						}
					}
				}
			}
				
			// XXX: IMPORTANT - '|' character not allowed
			$insert['plotName'] = str_replace('|', '', $insert['plotName']);
				
			$data = array(
				'userId'				=> $COCKPIT_SYSTEM_DEF['userId'],
				'plotName'				=> $insert['plotName'],
				'type'					=> $insert['type'],
				'baseAddress'			=> $baseAddress,
				'hostIpAddress'			=> $hostIpAddress,
				'hostIpLatiTude'		=> $ipLatiTude,
				'hostIpLongiTude'		=> $ipLongiTude,
				'accessLimit'			=> $insert['accessLimit'],
				'responseTimeoutLimit'	=> $insert['responseTimeoutLimit'],
				'dateCreated'			=> date('Y-m-d H:i:s'),
				'dateUpdated'			=> date('Y-m-d H:i:s')
			);
		
			$id = $db->insertOther('cockpit', 'httpDownloadTestPlot', $data);
				
			return $id;
		}
		
		public static function createHttpULTestPlot($insert)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query	= $db->quoteInto('SELECT null FROM httpUploadTestPlot WHERE userId=%s AND plotName=%s', array($COCKPIT_SYSTEM_DEF['userId'], $insert['plotName']));
			$res	= $db->queryOther('cockpit', $query);
		
			if($res->num_rows) return false;
		
			// remove scheme, port, query, fragment, user, pass from base address
			$baseAddress	= trim($insert['baseAddress']);
			$hostIpAddress	= "";
			$ipLatiTude		= "";
			$ipLongiTude	= "";
		
			$urlParts = parse_url($baseAddress);
		
			$baseAddress = $urlParts['host'] . $urlParts['path'];
			
			if($urlParts['scheme'] == 'http')		$insert['type'] = 1;
			else if($urlParts['scheme'] == 'https')	$insert['type'] = 2;
		
			if(filter_var($urlParts['host'], FILTER_VALIDATE_IP))
			{
				$hostIpAddress = $urlParts['host'];
			}
			else
			{
				// that means base address is a domain/subdomain. IPV4 only at this moment
				$hostIpAddress = gethostbyname($urlParts['host']);
			}
		
			// XXX: IMPORTANT - if we have a valid ip address
			if(strlen($hostIpAddress) > 0)
			{
				if($hostIpAddress !== "127.0.0.1")
				{
					$response = Unirest::get("http://ipinfo.io/" . $hostIpAddress . "/geo");
			
					if($response->body->loc)
					{
						$ipLocation = explode(',', $response->body->loc);
			
						if(is_array($ipLocation))
						{
							$ipLatiTude		= trim($ipLocation[0]);
							$ipLongiTude	= trim($ipLocation[1]);
						}
					}
				}
			}
		
			// XXX: IMPORTANT - '|' character not allowed
			$insert['plotName'] = str_replace('|', '', $insert['plotName']);
		
			$temp				= explode(".", $_FILES["File1"]["name"]);
			$extension			= end($temp);
			$uploadedFile		= md5($COCKPIT_SYSTEM_DEF['userId'] . $insert['plotName'] . 'httpUploadTestPlot') . "." . $extension;
			
			$data = array(
				'userId'				=> $COCKPIT_SYSTEM_DEF['userId'],
				'plotName'				=> $insert['plotName'],
				'type'					=> $insert['type'],
				'formType'				=> $insert['formType'],
				'baseAddress'			=> $baseAddress,
				'hostIpAddress'			=> $hostIpAddress,
				'File1'				=> $uploadedFile,
				'queryData'				=> trim($insert['queryData']),
				'hostIpLatiTude'		=> $ipLatiTude,
				'hostIpLongiTude'		=> $ipLongiTude,
				'accessLimit'			=> $insert['accessLimit'],
				'responseTimeoutLimit'	=> $insert['responseTimeoutLimit'],
				'dateCreated'			=> date('Y-m-d H:i:s'),
				'dateUpdated'			=> date('Y-m-d H:i:s')
			);
		
			$id = $db->insertOther('cockpit', 'httpUploadTestPlot', $data);
			
			// XXX: IMPORTANT - update php.ini settings for upload and post max size (10 MB max file size for upload)
			move_uploaded_file($_FILES["File1"]["tmp_name"], CSettings::$UPLOAD_FILE_DIR . $uploadedFile);
		
			return $id;
		}
		
		public static function updateHttpTestPlot($post, $updateChangesOnly = false)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			$query	= $db->quoteInto('SELECT null FROM httpTestPlot WHERE userId=%s AND plotName=%s', array($COCKPIT_SYSTEM_DEF['userId'], $post['plotName']));
			$res	= $db->queryOther('cockpit', $query);
			
			if(!$res->num_rows) return false;
			
			// clean the array (so no other post values accidently slip through)
			$changes = array_intersect_key($post, array_flip(array(
				'method',
				'contentType',
				'accept',
				'charset',
				'queryData',
				'accessLimit',
				'responseTimeoutLimit',
				'authType',
				'authUser',
				'authPassword',
				'consumerKey',
				'consumerSecret',
				'token',
				'tokenSecret',
			)));
			
			if($updateChangesOnly)
			{
				// remove changes that do not need updating.
				$plot = self::getHttpTestPlotByName($post['plotName']);
				
				if(count($plot))
				{
					foreach($changes as $k=>$v)
					{
						if($plot[$k] == $v) unset($changes[$k]);
					}
				}
			}
			
			$changes['baseAddress']			= trim($post['baseAddress']);
			$changes['hostIpAddress']		= "";
			$changes['hostIpLatiTude']		= "";
			$changes['hostIpLongiTude']		= "";
			
			$urlParts						= parse_url($changes['baseAddress']);
			$changes['baseAddress']			= $urlParts['host'] . $urlParts['path'];
				
			if($urlParts['scheme'] == 'http')		$changes['type'] = 1;
			else if($urlParts['scheme'] == 'https')	$changes['type'] = 2;
			
			if(filter_var($urlParts['host'], FILTER_VALIDATE_IP))
			{
				$changes['hostIpAddress'] = $urlParts['host'];
			}
			else
			{
				// that means base address is a domain/subdomain. IPV4 only at this moment
				$changes['hostIpAddress'] = gethostbyname($urlParts['host']);
			}
				
			// XXX: IMPORTANT - if we have a valid ip address
			if(strlen($changes['hostIpAddress']) > 0)
			{
				if($changes['hostIpAddress'] !== "127.0.0.1")
				{
					$response = Unirest::get("http://ipinfo.io/" . $changes['hostIpAddress'] . "/geo");
			
					if($response->body->loc)
					{
						$ipLocation = explode(',', $response->body->loc);
			
						if(is_array($ipLocation))
						{
							$changes['hostIpLatiTude']	= trim($ipLocation[0]);
							$changes['hostIpLongiTude']	= trim($ipLocation[1]);
						}
					}
				}
			}
			
			if(count($changes))
			{
				$changes['dateUpdated'] = date('Y-m-d H:i:s');
				
				if($COCKPIT_SYSTEM_DEF['userId'])
				{
					$changes['userId']		= $COCKPIT_SYSTEM_DEF['userId'];
					$changes['plotName']	= $post['plotName'];
					$db->duplicateRemovePrimary('httpTestPlot', $changes, array('userId', 'plotName'), false);
				}
			}
		}
		
		public static function updateHttpDLTestPlot($post, $updateChangesOnly = false)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$query	= $db->quoteInto('SELECT null FROM httpDownloadTestPlot WHERE userId=%s AND plotName=%s', array($COCKPIT_SYSTEM_DEF['userId'], $post['plotName']));
			$res	= $db->queryOther('cockpit', $query);
		
			if(!$res->num_rows) return false;
				
			// clean the array (so no other post values accidently slip through)
			$changes = array_intersect_key(
				$post, array_flip(
					array(
						'accessLimit',
						'responseTimeoutLimit'
					)
				)
			);
			
			if($updateChangesOnly)
			{
				// remove changes that do not need updating.
				$plot = self::getHttpDLTestPlotByName($post['plotName']);
		
				if(count($plot))
				{
					foreach($changes as $k=>$v)
					{
						if($plot[$k] == $v) unset($changes[$k]);
					}
				}
			}
			
			$changes['baseAddress']			= trim($post['baseAddress']);
			$changes['hostIpAddress']		= "";
			$changes['hostIpLatiTude']		= "";
			$changes['hostIpLongiTude']		= "";
				
			$urlParts = parse_url($changes['baseAddress']);
				
			$changes['baseAddress'] = $urlParts['host'] . $urlParts['path'];
				
			if($urlParts['scheme'] == 'http')		$changes['type'] = 1;
			else if($urlParts['scheme'] == 'https')	$changes['type'] = 2;
			
			if(filter_var($urlParts['host'], FILTER_VALIDATE_IP))
			{
				$changes['hostIpAddress'] = $urlParts['host'];
			}
			else
			{
				// that means base address is a domain/subdomain. IPV4 only at this moment
				$changes['hostIpAddress'] = gethostbyname($urlParts['host']);
			}
				
			// XXX: IMPORTANT - if we have a valid ip address
			if(strlen($changes['hostIpAddress']) > 0)
			{
				if($changes['hostIpAddress'] !== "127.0.0.1")
				{
					$response = Unirest::get("http://ipinfo.io/" . $changes['hostIpAddress'] . "/geo");
			
					if($response->body->loc)
					{
						$ipLocation = explode(',', $response->body->loc);
			
						if(is_array($ipLocation))
						{
							$changes['hostIpLatiTude']	= trim($ipLocation[0]);
							$changes['hostIpLongiTude']	= trim($ipLocation[1]);
						}
					}
				}
			}
				
			if(count($changes))
			{
				$changes['dateUpdated'] = date('Y-m-d H:i:s');
		
				if($COCKPIT_SYSTEM_DEF['userId'])
				{
					$changes['userId']		= $COCKPIT_SYSTEM_DEF['userId'];
					$changes['plotName']	= $post['plotName'];
					$db->duplicateRemovePrimary('httpDownloadTestPlot', $changes, array('userId', 'plotName'), false);
				}
			}
		}
		
		public static function updateHttpULTestPlot($post, $plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query	= $db->quoteInto('SELECT null FROM httpUploadTestPlot WHERE userId=%s AND plotName=%s', array($COCKPIT_SYSTEM_DEF['userId'], $post['plotName']));
			$res	= $db->queryOther('cockpit', $query);
		
			if(!$res->num_rows) return false;
		
			$changes						= array();
			$changes['baseAddress']			= trim($post['baseAddress']);
			$changes['hostIpAddress']		= "";
			$changes['hostIpLatiTude']		= "";
			$changes['hostIpLongiTude']		= "";
			
			$urlParts = parse_url($changes['baseAddress']);
			
			$changes['baseAddress'] = $urlParts['host'] . $urlParts['path'];
			
			if($urlParts['scheme'] == 'http')		$changes['type'] = 1;
			else if($urlParts['scheme'] == 'https')	$changes['type'] = 2;
						
			if(filter_var($urlParts['host'], FILTER_VALIDATE_IP))
			{
				$changes['hostIpAddress'] = $urlParts['host'];
			}
			else
			{
				// that means base address is a domain/subdomain. IPV4 only at this moment
				$changes['hostIpAddress'] = gethostbyname($urlParts['host']);
			}
			
			// XXX: IMPORTANT - if we have a valid ip address
			if(strlen($changes['hostIpAddress']) > 0)
			{
				if($changes['hostIpAddress'] !== "127.0.0.1")
				{
					$response = Unirest::get("http://ipinfo.io/" . $changes['hostIpAddress'] . "/geo");
				
					if($response->body->loc)
					{
						$ipLocation = explode(',', $response->body->loc);
				
						if(is_array($ipLocation))
						{
							$changes['hostIpLatiTude']	= trim($ipLocation[0]);
							$changes['hostIpLongiTude']	= trim($ipLocation[1]);
						}
					}
				}
			}
		
			$temp				= explode(".", $_FILES["File1"]["name"]);
			$extension			= end($temp);
			$uploadedFile		= md5($COCKPIT_SYSTEM_DEF['userId'] . $post['plotName'] . 'httpUploadTestPlot') . "." . $extension;
			
			$changes['formType']				= $post['formType'];
			$changes['queryData']				= trim($post['queryData']);
			$changes['File1']					= $uploadedFile;
			$changes['accessLimit']				= $post['accessLimit'];
			$changes['responseTimeoutLimit']	= $post['responseTimeoutLimit'];
			$changes['dateUpdated']				= date('Y-m-d H:i:s');
	
			if($COCKPIT_SYSTEM_DEF['userId'])
			{
				$changes['userId']		= $COCKPIT_SYSTEM_DEF['userId'];
				$changes['plotName']	= $plotName;
				
				$db->duplicateRemovePrimary('httpUploadTestPlot', $changes, array('userId', 'plotName'), false);
				
				// XXX: IMPORTANT - update php.ini settings for upload and post max size (10 MB max file size for upload)
				move_uploaded_file($_FILES["File1"]["tmp_name"], CSettings::$UPLOAD_FILE_DIR . $uploadedFile);
			}
		}
		
		// XXX: IMPORTANT - this function just change the 'isDeleted' flag to 1 and latter
		// the actual removing process or cron will remove all it's content from mysql and mongo
		public static function deleteHttpTestPlot($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			$db->updateOther('cockpit', 'httpTestPlot', array('userId' => $COCKPIT_SYSTEM_DEF['userId'], 'plotName' => $plotName),
				0, array(
					'isDeleted' => 1,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		// XXX: IMPORTANT - this function just change the 'isDeleted' flag to 1 and latter
		// the actual removing process or cron will remove all it's content from mysql and mongo
		public static function deleteHttpDLTestPlot($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$db->updateOther('cockpit', 'httpDownloadTestPlot', array('userId' => $COCKPIT_SYSTEM_DEF['userId'], 'plotName' => $plotName),
				0, array(
					'isDeleted' => 1,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		// XXX: IMPORTANT - this function just change the 'isDeleted' flag to 1 and latter
		// the actual removing process or cron will remove all it's content from mysql and mongo
		public static function deleteHttpULTestPlot($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			$db->updateOther('cockpit', 'httpUploadTestPlot', array('userId' => $COCKPIT_SYSTEM_DEF['userId'], 'plotName' => $plotName),
				0, array(
					'isDeleted' => 1,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		public static function undoDeleteHttpTestPlot($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$db->updateOther('cockpit', 'httpTestPlot', array('userId' => $COCKPIT_SYSTEM_DEF['userId'], 'plotName' => $plotName),
				0, array(
					'isDeleted' => 0,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		public static function undoDeleteHttpDLTestPlot($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$db->updateOther('cockpit', 'httpDownloadTestPlot', array('userId' => $COCKPIT_SYSTEM_DEF['userId'], 'plotName' => $plotName),
				0, array(
					'isDeleted' => 0,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}

		public static function undoDeleteHttpULTestPlot($plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$db->updateOther('cockpit', 'httpUploadTestPlot', array('userId' => $COCKPIT_SYSTEM_DEF['userId'], 'plotName' => $plotName),
				0, array(
					'isDeleted' => 0,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		public static function getHttpTestPlotAnalyzedData($userId, $plotName, $limit=10)
		{
			global $mongo;
			
			$cursor = $mongo->getAllData('cockpit', 'httpTestPlotReport', array('userId' => (int) $userId, 'plotName' => $plotName));
			
			// sort 'dateCreated' as descending
			$cursor->sort(array('dateCreated' => -1));
			
			// limit cursor (by default 10)
			$cursor->limit($limit);
			
			$inc = 1;
			
			foreach($cursor as $doc)
			{
				if($inc == 1)
				{
					$lastAnalyzedData = $doc;
				}
				
				// XXX: IMPORTANT - line graph if more than one record
				$overAllAnalyzedData[] = $doc;
				
				$inc++;
			}
			
			return array($lastAnalyzedData, $overAllAnalyzedData);
		}
		
		public static function getHttpDLTestPlotAnalyzedData($userId, $plotName, $limit=10)
		{
			global $mongo;
				
			$cursor = $mongo->getAllData('cockpit', 'httpDownloadTestPlotReport', array('userId' => (int) $userId, 'plotName' => $plotName));
				
			// sort 'dateCreated' as descending
			$cursor->sort(array('dateCreated' => -1));
				
			// limit cursor (by default 10)
			$cursor->limit($limit);
				
			$inc = 1;
				
			foreach($cursor as $doc)
			{
				if($inc == 1)
				{
					$lastAnalyzedData = $doc;
				}
		
				// XXX: IMPORTANT - line graph if more than one record
				$overAllAnalyzedData[] = $doc;
		
				$inc++;
			}
				
			return array($lastAnalyzedData, $overAllAnalyzedData);
		}
		
		public static function setHttpPlotStatusToDone($userId, $plotName)
		{
			global $db;
			
			$db->updateOther('cockpit', 'httpTestPlot', array('userId' => $userId, 'plotName' => $plotName),
				0, array(
					'plotStatus' => 3,
					'noOfThreadExecuted' => 0,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}

		public static function setHttpDLPlotStatusToDone($userId, $plotName)
		{
			global $db;
				
			$db->updateOther('cockpit', 'httpDownloadTestPlot', array('userId' => $userId, 'plotName' => $plotName),
				0, array(
					'plotStatus' => 3,
					'noOfThreadExecuted' => 0,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		public static function setHttpULPlotStatusToDone($userId, $plotName)
		{
			global $db;
		
			$db->updateOther('cockpit', 'httpUploadTestPlot', array('userId' => $userId, 'plotName' => $plotName),
				0, array(
					'plotStatus' => 3,
					'noOfThreadExecuted' => 0,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		public static function setHttpPlotHostIpOwnershipCode($hostIpOwnershipCode, $plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			$db->updateOther('cockpit', 'httpTestPlot', array('userId' => $COCKPIT_SYSTEM_DEF['userId'], 'plotName' => $plotName),
				0, array(
					'hostIpOwnershipCode' => $hostIpOwnershipCode,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		public static function setHttpDLPlotHostIpOwnershipCode($hostIpOwnershipCode, $plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$db->updateOther('cockpit', 'httpDownloadTestPlot', array('userId' => $COCKPIT_SYSTEM_DEF['userId'], 'plotName' => $plotName),
				0, array(
					'hostIpOwnershipCode' => $hostIpOwnershipCode,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		public static function setHttpULPlotHostIpOwnershipCode($hostIpOwnershipCode, $plotName)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$db->updateOther('cockpit', 'httpUploadTestPlot', array('userId' => $COCKPIT_SYSTEM_DEF['userId'], 'plotName' => $plotName),
				0, array(
					'hostIpOwnershipCode' => $hostIpOwnershipCode,
					'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
		}
		
		public static function setHostIpOwnershipConfirmed($baseAddress)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			$query = "UPDATE httpTestPlot SET hostIpOwnership = 1 where userId = '" . $COCKPIT_SYSTEM_DEF['userId'] . "' AND baseAddress like '". $baseAddress . "%'";
			
			$db->queryOther('cockpit', $query);
			
			return $query;
		}

		public static function setHttpDLHostIpOwnershipConfirmed($baseAddress)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
				
			$query = "UPDATE httpDownloadTestPlot SET hostIpOwnership = 1 where userId = '" . $COCKPIT_SYSTEM_DEF['userId'] . "' AND baseAddress like '". $baseAddress . "%'";
				
			$db->queryOther('cockpit', $query);
				
			return $query;
		}
		
		public static function setHttpULHostIpOwnershipConfirmed($baseAddress)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
		
			$query = "UPDATE httpUploadTestPlot SET hostIpOwnership = 1 where userId = '" . $COCKPIT_SYSTEM_DEF['userId'] . "' AND baseAddress like '". $baseAddress . "%'";
		
			$db->queryOther('cockpit', $query);
		
			return $query;
		}
		
		public static function incHttpPlotExecuted($userId, $plotName)
		{
			global $db;
			
			$res = self::getHttpTestPlotByUserIdAndName($userId, $plotName);
			
			if($res)
			{
				$noOfThreadExecuted = $res['noOfThreadExecuted'];
				
				$sql = "UPDATE systemInfo SET totalHttpPlotExecuted = totalHttpPlotExecuted + $noOfThreadExecuted WHERE id = 1";
				
				$db->queryOther('cockpit', $sql);
			}
		}
		
		public static function incHttpDLPlotExecuted($userId, $plotName)
		{
			global $db;
				
			$res = self::getHttpDLTestPlotByUserIdAndName($userId, $plotName);
				
			if($res)
			{
				$noOfThreadExecuted = $res['noOfThreadExecuted'];
		
				$sql = "UPDATE systemInfo SET totalHttpDownloadPlotExecuted = totalHttpDownloadPlotExecuted + $noOfThreadExecuted WHERE id = 1";
		
				$db->queryOther('cockpit', $sql);
			}
		}
		
		public static function incHttpULPlotExecuted($userId, $plotName)
		{
			global $db;
		
			$res = self::getHttpULTestPlotByUserIdAndName($userId, $plotName);
		
			if($res)
			{
				$noOfThreadExecuted = $res['noOfThreadExecuted'];
		
				$sql = "UPDATE systemInfo SET totalHttpUploadPlotExecuted = totalHttpUploadPlotExecuted + $noOfThreadExecuted WHERE id = 1";
		
				$db->queryOther('cockpit', $sql);
			}
		}
		
		public static function generateLineGraph($graphId, $xAxisData, $yAxisData, $yMax, $titleX, $titleY)
		{
			return $graph =
			'
				<canvas id="' . $graphId . '" width="445" height="250" style="background-color: #555; border: 5px solid #000; border-radius: 7px; box-shadow: 2px 2px 3px gray">[No canvas support]</canvas>
				<script>
					var line = new RGraph.Line("' . $graphId . '", ' . json_encode($yAxisData, JSON_NUMERIC_CHECK) . ')
					.Set("ymax", ' . $yMax . ')
					.Set("scale.zerostart", true)
					.Set("tooltips", ' . json_encode($yAxisData) . ')
					.Set("background.grid.vlines", false)
					.Set("background.grid.autofit.numhlines", 6)
					.Set("background.grid.border", false)
					.Set("background.grid.color", "#999")
					.Set("shadow", true)
					.Set("linewidth", 3)
					.Set("colors", ["#d00"])
					.Set("hmargin", 45.42)
					.Set("yaxispos", "left")
					.Set("axis.color", "#999")
					.Set("text.color", "#ccc")
					.Set("gutter.left", 50)
					.Set("gutter.right", 10)
					.Set("ylabels.count", 6)
					.Set("numyticks", 6)
					.Set("tickmarks", customTick)
					.Set("labels", ' . json_encode($xAxisData) . ')
					.Set("title.yaxis", "' . $titleY . '")
					.Set("title.yaxis.pos", 0.1)
					.Set("title.yaxis.size", 8)
					.Set("title.xaxis", "' . $titleX . '")
					.Set("title.xaxis.y", 11)
					.Set("title.xaxis.size", 10)
					.Draw();
				</script>
			';
		}

		public static function generatePieGraph($graphId, $xAxisData, $yAxisData)
		{
			return $graph =
			'
				<canvas id="' . $graphId . '" width="350" height="270">[No canvas support]</canvas>
				<script>
					var pie = new RGraph.Pie("' . $graphId . '", ' . json_encode($xAxisData, JSON_NUMERIC_CHECK) . ')
					.Set("exploded", [,15])
					.Set("shadow", true)
					.Set("shadow.color", "#aaa")
					.Set("labels", ' . json_encode($yAxisData) . ')
					.Set("labels.ingraph", ' . json_encode($xAxisData, JSON_NUMERIC_CHECK) . ')
					.Set("colors", ["Gradient(#94f776:#50B332:#B1E59F)", "Gradient(#fe783e:#EC561B:#F59F7D)"])
					.Set("gutter.left", 40)
	        		.Set("gutter.right", 40)
					.Set("gutter.top", 40)
	        		.Set("gutter.bottom", 40)
					.Draw();
				</script>
			';
		}
		
		private function funPack($format, $data)
		{
			$dwPos = 0;
			foreach($format as $key => $len)
			{
				$result[$key] = substr($data, $dwPos, $len);
				$dwPos+= $len;
			}
			
			return $result;
		}
		
	}