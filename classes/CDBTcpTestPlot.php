<?php

	include_once 'CMQC.php';
	include_once 'CDBQuery.php';
	include_once 'CDBMongo.php';
	include_once 'CSettings.php';
	include_once 'CUnirest.php';
	
	class CDBTcpTestPlot extends CDBQuery
	{
		public static function createTcpTestPlot($insert)
		{
			global $db;
			global $SMITH_SYSTEM_DEF;
				
			$query	= $db->quoteInto('SELECT null FROM tcpTestPlot WHERE userId=%s AND plotName=%s', array($SMITH_SYSTEM_DEF['userId'], $insert['plotName']));
			$res	= $db->queryOther('smith', $query);
				
			if($res->num_rows) return false;
				
			// remove scheme, port, query, fragment, user, pass from base address
			$baseAddress	= trim($insert['baseAddress']);
			$hostIpAddress	= "";
			$ipLatiTude		= "";
			$ipLongiTude	= "";
				
			if(filter_var($baseAddress, FILTER_VALIDATE_IP))
			{
				$hostIpAddress = $baseAddress;
			}
			else
			{
				// that means base address is a domain/subdomain. IPV4 only at this moment
				$hostIpAddress = gethostbyname($baseAddress);
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
			
			$tcpControlBits		= trim($insert['tcpControlBits']);
			$tcpControlBits		= explode(',', $tcpControlBits);
			$newTcpControlBits	= array();
			
			foreach($tcpControlBits as $key=>$value)
			{
				$value					= trim($value);
				$newTcpControlBits[]	= $value;
			}
			
			$newTcpControlBits = implode(',', $newTcpControlBits);
			
			// XXX: IMPORTANT - '|' character not allowed
			$insert['plotName'] = str_replace('|', '', $insert['plotName']);
				
			$data = array(
					'userId'				=> $SMITH_SYSTEM_DEF['userId'],
					'plotName'				=> $insert['plotName'],
					'tcpControlBits'		=> $newTcpControlBits,
					'baseAddress'			=> $baseAddress,
					'basePort'				=> $insert['basePort'],
					'payLoadData'			=> trim($insert['payLoadData']),
					'hostIpAddress'			=> $hostIpAddress,
					'hostIpLatiTude'		=> $ipLatiTude,
					'hostIpLongiTude'		=> $ipLongiTude,
					'accessLimit'			=> $insert['accessLimit'],
					'responseTimeoutLimit'	=> $insert['responseTimeoutLimit'],
					'dateCreated'			=> date('Y-m-d H:i:s'),
					'dateUpdated'			=> date('Y-m-d H:i:s'),
			);
		
			$id = $db->insertOther('smith', 'tcpTestPlot', $data);
				
			return $id;
		}
	}