<?php

	include_once 'CDBQuery.php';
	include_once 'CSettings.php';
	include_once 'CHelperFunctions.php';

	class CDBUser extends CDBQuery
	{
		public static function setUserDashboardPos($pos, $userId)
		{
			global $db;
			$userId = intval($userId);
			$affected_rows = $db->update(
				'userDashboardPos'
				, array(
					'userId' => $userId), 0
					, array('dashboardPosVal' => $pos, 'dateUpdated' => date('Y-m-d H:i:s')
				)
			);
			
			if($affected_rows == 0)
			{
				$userDashboardPosId = $db->insert('userDashboardPos',
					array(
						'userId' => $userId,
						'dashboardPosVal' => $pos,
						'dateCreated' => date('Y-m-d H:i:s'),
						'dateUpdated' => date('Y-m-d H:i:s')
					)
				);
				return $userDashboardPosId;
			}
			
			return $affected_rows;
		}
		
		public static function setMachineDataDashboardPos($pos, $userId)
		{
			global $db;
			$userId = intval($userId);
			$affected_rows = $db->update(
				'machineDataDashboardPos'
				, array('userId' => $userId), 0, array('dashboardPosVal' => $pos, 'dateUpdated' => date('Y-m-d H:i:s'))
			);
				
			if($affected_rows == 0)
			{
				$userDashboardPosId = $db->insert('machineDataDashboardPos',
					array(
						'userId' => $userId,
						'dashboardPosVal' => $pos,
						'dateCreated' => date('Y-m-d H:i:s'),
						'dateUpdated' => date('Y-m-d H:i:s')
					)
				);
				return $userDashboardPosId;
			}
				
			return $affected_rows;
		}
		
		public static function setLogDataDashboardPos($pos, $userId)
		{
			global $db;
			$userId = intval($userId);
			$affected_rows = $db->update(
				'logDataDashboardPos'
				, array('userId' => $userId), 0, array('dashboardPosVal' => $pos, 'dateUpdated' => date('Y-m-d H:i:s'))
			);
		
			if($affected_rows == 0)
			{
				$userDashboardPosId = $db->insert('logDataDashboardPos',
					array(
						'userId' => $userId,
						'dashboardPosVal' => $pos,
						'dateCreated' => date('Y-m-d H:i:s'),
						'dateUpdated' => date('Y-m-d H:i:s')
					)
				);
				return $userDashboardPosId;
			}
		
			return $affected_rows;
		}
		
		public static function getUserDetails($userId)
		{
			global $db;
			$userId = intval($userId);
			$query = $db->quoteInto("SELECT * FROM userProfile WHERE userId=%s", $userId);
			$res = $db->query($query);
			if(!$res->num_rows)
			{
				return array();
			}
			
			return $res->fetch_assoc();
		}
		
		/**
		 * Sets employer details
		 *
		 * @param int $employer_id
		 * @param array $changes
		 * @param bool $updateChangesOnly
		 * @return int $employer_id
		 */
		public static function setUserBasicProfile($userId, $changes, $updateChangesOnly = false)
		{
			global $db;
			$userId = intval($userId);
			
			if(empty($changes['firstName']) || empty($changes['primaryEmail']))
			{
				$changes['isProfileUpdate'] = 0;
			}
			else
			{
				$changes['isProfileUpdate'] = 1;
			}
			
			// clean the array (so no other post values accidently slip through)
			$changes = array_intersect_key($changes, array_flip(array(
				'firstName',
				'lastName',
				'companyName',
				'primaryEmail',
				'telePhone',
				'mobilePhone',
				'fax',
				'address_1',
				'address_2',
				'countryId',
				'stateId',
				'cityId',
				'postcode',
				'isProfileUpdate',
				'dateCreated',
				'dateUpdated'
			)));
			
			if($userId && $updateChangesOnly)
			{
				// remove changes that do not need updating.
				$user = CDBUser::getUserDetails($userId);
				if(count($user))
				{
					foreach($changes as $k=>$v)
					{
						if($user[$k] == $v) unset($changes[$k]);
					}
				}
			}

			if(count($changes))
			{
				$changes['dateUpdated'] = $changes['dateCreated'] = date('Y-m-d H:i:s');
				if($userId)
				{
					$changes['userId'] = $userId;
					$db->duplicateRemovePrimary('userProfile', $changes, array('userId', 'dateCreated'), false);
				}
				else
				{
					$userId = $db->insert('userProfile', $changes, false);
				}
			}

			return $userId;
		}
		
		// TODO: for registered user we need authentic information to avoid after spamming claim
		public static function setUserOfficialProfile($userId, $changes, $updateChangesOnly = false)
		{
			
		}
		
		public static function setPassword($old_password, $new_password, $re_new_password)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			if($new_password === $re_new_password)
			{
				include_once 'CDBLogin.php';
				
				$password = CDBLogin::getPassword($COCKPIT_SYSTEM_DEF['userId']);
				if(md5($old_password) === $password)
				{
					$db->updateOther('siteUser', 'user', 'userId', $COCKPIT_SYSTEM_DEF['userId'],
						array(
							'password' => md5($new_password),
							'lastChangedPassword'=>date('Y-m-d H:i:s')
						)
					);
					return array(1, "New password successfully changed. Hurray...");
				}
				else
				{
					return array(0, "Old password not matched!!!");
				}
			}
			else
			{
				return array(0, "New password and Re-type new password are not same!!!");
			}
		}
		
		public static function setSpecificUserSettings($userid, $user_status)
		{
			global $db;
			global $COCKPIT_SYSTEM_DEF;
			
			if($user_status === 1)
			{
				$db->updateOther('siteUser', 'user', 'userId', $userid,
					array(
						'userStatus' => $user_status,
						'failedLoginCount' => 0,
						'deactivatedDate' => date('Y-m-d H:i:s'),
						'deactivatedBy' => $COCKPIT_SYSTEM_DEF['userId'],
						'userStatusChangeDate' => date('Y-m-d H:i:s'),
						'dateUpdated' => date('Y-m-d H:i:s')
					)
				);
			}
			
			// If inactive then also remove session data immidiately
			if($user_status === 3)
			{
				$db->updateOther('siteUser', 'user', 'userId', $userid,
					array(
						'userStatus' => $user_status,
						'deactivatedDate' => date('Y-m-d H:i:s'),
						'deactivatedBy' => $COCKPIT_SYSTEM_DEF['userId'],
						'userStatusChangeDate' => date('Y-m-d H:i:s'),
						'dateUpdated' => date('Y-m-d H:i:s')
					)
				);
				
				$db->updateOther('siteUser', 'systemSession', 'userId', $userid,
					array(
						'userStatus'	=> $user_status,
						'userId'		=> 0
					)
				);
			}
		}
		
		public static function createCockpitUser($username, $password, $re_password, $primary_email, $firstName, $user_status)
		{
			global $db;
			if($password === $re_password)
			{
				$userId = $db->insertOther('siteUser',  'user',
					array(
						'username'				=> $username,
						'password'				=> md5($password),
						'userStatus'			=> $user_status,
						'registrationDate'		=> date('Y-m-d H:i:s'),
						'loginCount'			=> 0,
						'lastChangedPassword'	=> date('Y-m-d H:i:s'),
						'userStatusChangeDate'	=> date('Y-m-d H:i:s'),
						'dateCreated'			=> date("Y-m-d H:i:s"),
						'dateUpdated'			=> date("Y-m-d H:i:s")
					)
				);
				
				if($userId)
				{
					$employer_id = $db->insertOther('cockpit',  'userProfile',
						array(
							'userId'			=> $userId,
							'firstName'			=> $firstName,
							'primaryEmail'		=> $primary_email,
							'dateCreated'		=> date("Y-m-d H:i:s"),
							'dateUpdated'		=> date("Y-m-d H:i:s")
						)
					);
				}
				else
				{
					return array(0, "Error: Can't create user in COCKPIT");
				}
				
				return array(1, "Create a New COCKPIT User. Hurray...");
			}
			else
			{
				return array(0, "Password and Re-type password are not same!!!");
			}
		}

		public static function getUserServiceACL($userId)
		{
			global $db;
			global $EC_CONTROL_PANEL;
			global $EC_CONTROL_PANEL_SUB;
			
			$controlPanelKeys = array_keys($EC_CONTROL_PANEL);
			
			$userId	= intval($userId);
			$query	= $db->quoteInto("SELECT * FROM userServiceAcl WHERE userId=%s AND active = 1 ORDER BY serviceGroup ASC, serviceFunction ASC", $userId);
			$res	= $db->query($query);
			
			if(!$res->num_rows)
			{
				return array();
			}
			
			$myAvailableService = array();
			
			while($foo = $res->fetch_assoc())
			{
				$controlPanelSubKeys = array_keys($EC_CONTROL_PANEL_SUB[$controlPanelKeys[$foo['serviceGroup']]]);
				$myAvailableService[$controlPanelKeys[$foo['serviceGroup']]][] = $controlPanelSubKeys[$foo['serviceFunction']];
			}
			
			return $myAvailableService;
		}
		
		public static function getMachineDataServiceACL($userId)
		{
			global $db;
			global $MACHINEDATA_CONTROL_PANEL;
			global $MACHINEDATA_CONTROL_PANEL_SUB;
			
			$controlPanelKeys = array_keys($MACHINEDATA_CONTROL_PANEL);
			
			$userId	= intval($userId);
			$query	= $db->quoteInto("SELECT * FROM machineDataServiceAcl WHERE userId=%s AND active = 1 ORDER BY serviceGroup ASC, serviceFunction ASC", $userId);
			$res	= $db->query($query);
			
			if(!$res->num_rows)
			{
				return array();
			}
			
			$myAvailableService = array();
			
			while($foo = $res->fetch_assoc())
			{
				$controlPanelSubKeys = array_keys($MACHINEDATA_CONTROL_PANEL_SUB[$controlPanelKeys[$foo['serviceGroup']]]);
				$myAvailableService[$controlPanelKeys[$foo['serviceGroup']]][] = $controlPanelSubKeys[$foo['serviceFunction']];
			}
			
			return $myAvailableService;
		}
		
		public static function getLogDataServiceACL($userId)
		{
			global $db;
			global $LOGDATA_CONTROL_PANEL;
			global $LOGDATA_CONTROL_PANEL_SUB;
				
			$controlPanelKeys = array_keys($LOGDATA_CONTROL_PANEL);
				
			$userId	= intval($userId);
			$query	= $db->quoteInto("SELECT * FROM logDataServiceAcl WHERE userId=%s AND active = 1 ORDER BY serviceGroup ASC, serviceFunction ASC", $userId);
			$res	= $db->query($query);
				
			if(!$res->num_rows)
			{
				return array();
			}
				
			$myAvailableService = array();
				
			while($foo = $res->fetch_assoc())
			{
				$controlPanelSubKeys = array_keys($LOGDATA_CONTROL_PANEL_SUB[$controlPanelKeys[$foo['serviceGroup']]]);
				$myAvailableService[$controlPanelKeys[$foo['serviceGroup']]][] = $controlPanelSubKeys[$foo['serviceFunction']];
			}
				
			return $myAvailableService;
		}
		
		/**
		 * Get company name
		 *
		 * @param int $employer_id
		 * @param string $lang
		 * @return string
		 */
		public static function getUserName($userId)
		{
			global $db;
			$userId = intval($userId);
			$query = $db->quoteInto("SELECT firstName, lastName FROM userProfile WHERE userId = %s", $userId);
			$res = $db->query($query);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				return $foo['firstName'] . ' ' . $foo['lastName'];
			}
		}

		public static function getUserDashboardPosition($userId, $memcached = -1)
		{
			global $db;
			$dashPosition = array();
			$userId = intval($userId);
			$query = $db->quoteInto("SELECT dashboardPosVal FROM userDashboardPos WHERE userId = %s", $userId);
			$res = $db->query($query, $memcached);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				$dashPosition = explode('|', $foo['dashboardPosVal']);
				return $dashPosition;
			}
			else
			{
				// XXX: IMPORTANT, READ IT - If you increment anything in form_values_en then fix it here as well
				$dashPosition[0] = 0; $dashPosition[1] = 1; $dashPosition[2] = 2;
				$dashPosition[3] = 3; $dashPosition[4] = 4; $dashPosition[5] = 5;
				
				return $dashPosition;
			}
		}
		
		public static function getMachineDataDashboardPosition($userId, $memcached = -1)
		{
			global $db;
			$dashPosition = array();
			$userId = intval($userId);
			$query = $db->quoteInto("SELECT dashboardPosVal FROM machineDataDashboardPos WHERE userId = %s", $userId);
			$res = $db->query($query, $memcached);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				$dashPosition = explode('|', $foo['dashboardPosVal']);
				return $dashPosition;
			}
			else
			{
				// XXX: IMPORTANT, READ IT - If you increment anything in form_values_en then fix it here as well
				$dashPosition[0] = 0; $dashPosition[1] = 1; $dashPosition[2] = 2;
				$dashPosition[3] = 3; $dashPosition[4] = 4; $dashPosition[4] = 5;
		
				return $dashPosition;
			}
		}
		
		public static function getLogDataDashboardPosition($userId, $memcached = -1)
		{
			global $db;
			$dashPosition = array();
			$userId = intval($userId);
			$query = $db->quoteInto("SELECT dashboardPosVal FROM logDataDashboardPos WHERE userId = %s", $userId);
			$res = $db->query($query, $memcached);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				$dashPosition = explode('|', $foo['dashboardPosVal']);
				return $dashPosition;
			}
			else
			{
				// XXX: IMPORTANT, READ IT - If you increment anything in form_values_en then fix it here as well
				$dashPosition[0] = 0; $dashPosition[1] = 1; $dashPosition[2] = 2;
				$dashPosition[3] = 3;
		
				return $dashPosition;
			}
		}

		public static function rearrangeEmployerDashboard($userId, $QUICK_LINK_COLUMNS = array())
		{
			$dashPosition = self::getUserDashboardPosition($userId, -1);
			$quickLinkColumnsRearrange = array();
			
			if(!empty($dashPosition))
			{
				foreach($dashPosition as $key => $val)
				{
					$rearrange_key = $QUICK_LINK_COLUMNS[$val];
					$quickLinkColumnsRearrange[0][$rearrange_key] = $rearrange_key;
				}
			}
			
			return $quickLinkColumnsRearrange;
		}
		
		public static function rearrangeMachineDataDashboard($userId, $MACHINEDATA_QUICK_LINK = array())
		{
			$dashPosition = self::getMachineDataDashboardPosition($userId, -1);
			
			$quickLinkColumnsRearrange = array();
				
			if(!empty($dashPosition))
			{
				foreach($dashPosition as $key => $val)
				{
					$rearrange_key = $MACHINEDATA_QUICK_LINK[$val];
					$quickLinkColumnsRearrange[0][$rearrange_key] = $rearrange_key;
				}
			}
				
			return $quickLinkColumnsRearrange;
		}
		
		public static function rearrangeLogDataDashboard($userId, $LOGDATA_QUICK_LINK = array())
		{
			$dashPosition = self::getLogDataDashboardPosition($userId, -1);
				
			$quickLinkColumnsRearrange = array();
		
			if(!empty($dashPosition))
			{
				foreach($dashPosition as $key => $val)
				{
					$rearrange_key = $LOGDATA_QUICK_LINK[$val];
					$quickLinkColumnsRearrange[0][$rearrange_key] = $rearrange_key;
				}
			}
		
			return $quickLinkColumnsRearrange;
		}
	}
?>