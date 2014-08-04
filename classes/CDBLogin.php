<?php

	require_once('CDBQuery.php');
	
	class CDBLogin extends CDBQuery
	{
		
		public static function getCheckUsername($username)
		{
			global $db;
			
			$query = "SELECT userId FROM user WHERE username = %s";
			$res = $db->quotedQueryOther('siteUser', $query, $username);
			if(!$res->num_rows)
			{
				return 0;
			}
			
			$foo = $res->fetch_assoc();
			return $foo["userId"];
		}

		public static function getCheckUserPrimaryEmail($primaryEmail)
		{
			global $db;
			$query = $db->quoteInto('SELECT NULL FROM userProfile WHERE primaryEmail = %s', $primaryEmail);
			$res = $db->query($query);
			if(!$res->num_rows)
			{
				return 0;
			}
			
			return 1;
		}

		public static function getLoginDetails($username, $password, $plain_password)
		{
			global $db;
			$query = $db->quoteInto(
				'SELECT userId, username, userStatus, loginCount, failedLoginCount, lastLogin
				, DATEDIFF(lastChangedPassword, now()) AS daysSincePasswordChange FROM
				user WHERE LOWER(username)=%s && (password=%s OR password=%s) && userStatus!=3'
				, array(mb_strtolower($username), $password, $plain_password)
			);
			$res = $db->queryOther('siteUser', $query);
			
			if(!$res->num_rows)
			{
				return 0;
			}
			
			$foo = $res->fetch_assoc();
			return $foo;
		}

		public static function getPassword($userId)
		{
			global $db;
			$userId=intval($userId);
			$query = $db->quoteInto("SELECT password FROM user WHERE userId = %s", $userId);
			$res = $db->queryOther('siteUser', $query);
			if(!$res->num_rows)
			{
				return 0;
			}
			
			$foo = $res->fetch_assoc();
			return $foo['password'];
		}

		public static function getStatus($userId)
		{
			global $db;
			$userId=intval($userId);
			$query = $db->quoteInto("SELECT userStatus FROM user WHERE userId=%s", $userId);
			$res = $db->queryOther('siteUser', $query);
			if(!$res->num_rows)
			{
				return 0;
			}
			
			$foo = $res->fetch_assoc();
			return $foo['userStatus'];
		}
		
		public static function getAccountType($userId)
		{
			global $db;
			$userId=intval($userId);
			$query = $db->quoteInto("SELECT accountType FROM user WHERE userId=%s", $userId);
			$res = $db->queryOther('siteUser', $query);
			if(!$res->num_rows)
			{
				return 0;
			}
				
			$foo = $res->fetch_assoc();
			return $foo['accountType'];
		}

		public static function getUsername($userId)
		{
			global $db;
			$userId = intval($userId);
			$query = $db->quoteInto("SELECT username FROM user WHERE userId=%s", $userId);
			$res = $db->queryOther('siteUser', $query);
			if(!$res->num_rows)
			{
				return 0;
			}
			
			$foo = $res->fetch_assoc();
			return $foo['username'];
		}
		
		public static function getFailedLoginCount($username)
		{
			global $db;
			$query = "SELECT failedLoginCount FROM user WHERE username = %s";
			$res = $db->quotedQueryOther('siteUser', $query, $username);
			if($res->num_rows)
			{
				$foo = $res->fetch_assoc();
				return $foo;
			}
		}

		public static function login($username, $password)
		{
			global $db;
			
			// check how many times user failed to login
			$foo_failed_count = CDBLogin::getFailedLoginCount($username);
			if($foo_failed_count['failedLoginCount'] >= 5)
			{
				return $foo_failed_count;
			}
			
			$plain_password = $password;
			// Generate a 32bit hash of $password
			$password = md5($password);
			$foo = CDBLogin::getLoginDetails($username, $password, $plain_password);
			
			if($foo['userId'])
			{
				// Update Last Login
				CDBLogin::setLastLogin($foo['userId'], $foo['loginCount'] + 1, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
				return $foo;
			}
			else
			{
				if($foo_failed_count['failedLoginCount'] < 5)
				{
					$query = "UPDATE user SET failedLoginCount = failedLoginCount + 1 WHERE username = %s";
					$db->quotedQueryOther('siteUser', $query, $username);
				}

				return false;
			}
		}

		public static function setLastLogin($userId, $login_count, $last_login_ip, $last_login_browser)
		{
			global $db;
			$userId=intval($userId);
			$db->updateOther('siteUser', 'user', 'userId', $userId,
				array(
					'loginCount' => $login_count,
					'lastLogin' => date('Y-m-d H:i:s'),
					'lastLoginIp' => $last_login_ip,
					'lastLoginBrowser' => $last_login_browser
				)
			);
		}

		public static function setPassword($userId, $password)
		{
			global $db;
			$userId = intval($userId);
			$db->updateOther('siteUser', 'user', 'userId', $userId,
				array(
					'password' => md5($password),
					'lastChangedPassword'=>date('Y-m-d H:i:s')
				)
			);
		}
	}
?>