<?php

	include_once 'CSettings.php';
	include_once 'CMail.php';
	include_once 'CDBQuery.php';
	include_once 'Net/UserAgent/Mobile.php';

	class CDBSession extends CDBQuery{

		public static function getUserPassword($userId){
			global $db;
			$userId = intval($userId);
			$query = "SELECT password FROM user	WHERE userId = '$userId'";
			$res = $db->queryOther('siteUser', $query);
			if(!$res->num_rows) return 0;
			$foo = $res->fetch_assoc();
			return $foo['password'];
		}

		public static function getUserName($userId){
			global $db;
			$userId = intval($userId);
			if($userId){
				$query = "SELECT username FROM user WHERE userId = '$userId'";
				$res = $db->queryOther('siteUser', $query);
				if(!$res->num_rows) return 0;
				$foo = $res->fetch_assoc();
				return $foo;
			}
		}

		public static function sessionGetId(){
			global $db;
			global $COCKPIT;
			$query = "SELECT sessionId FROM systemSession WHERE cookie='$COCKPIT'";
			$res = $db->queryOther('siteUser', $query);
			if(!$res->num_rows){
				return 0;
			}
			$foo = $res->fetch_assoc();
			return $foo["sessionId"];
		}

		public static function sessionGetStatus(){
			global $db;
			global $COCKPIT;
			$query = "SELECT userStatus FROM systemSession WHERE cookie='$COCKPIT'";
			$res = $db->queryOther('siteUser', $query);
			if(!$res->num_rows){
				return 0;
			}
			$foo = $res->fetch_assoc();
			return $foo["userStatus"];
		}

		public static function sessionGetUserId(){
			global $db;
			global $COCKPIT;
			global $COCKPIT_SESSION;
			global $COCKPIT_SYSTEM_DEF;
			global $mobile_agent;
			if(!$COCKPIT || ($COCKPIT != $COCKPIT_SESSION && $mobile_agent->isNonMobile())){
				return 0;
				exit;
			}
			$query = "SELECT userId FROM systemSession WHERE sessionId='" . $COCKPIT_SYSTEM_DEF["sessionId"] . "'";
			$res = $db->queryOther('siteUser', $query);
			if(!$res->num_rows){
				return 0;
			}
			$foo = $res->fetch_assoc();
			return $foo["userId"];
		}

		public static function sessionLinkUserId($userId = 0, $user_status = 0, $permanent = 0){
			global $COCKPIT;
			global $db;
			global $mobile_agent;
			$db->updateOther(
				'siteUser', 'systemSession', 'cookie', $COCKPIT, array('userId'=>$userId, 'userStatus'=>$user_status)
			);
			if($mobile_agent->isNonMobile()){
				//90 days cookie
				if($permanent) setcookie("COCKPIT_SESSION", $COCKPIT, time() + 7776000, '/');
				//session cookie
				else setcookie("COCKPIT_SESSION", $COCKPIT, 0, '/');
			}
		}

		public static function sessionSetCookie(){
			global $db;
			global $COCKPIT;
			global $mobile_agent;
			if(!$mobile_agent->isNonMobile() && $mobile_agent->getUID()){
				$fulldomain = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
   				$domainlist = array_reverse(explode('.', $fulldomain));
   				if(preg_match("/\.(or[g]?|co[m]?|ne[t]?|)/i", $domainlist[1])){
       				$domain = $domainlist[2].'.'.$domainlist[1].'.'.$domainlist[0];
   				}else{
       				$domain = $domainlist[1].'.'.$domainlist[0];
   				}
   				if(preg_match("/[0-9]{1,3}\.[0-9]{1,3}/", $domain)){
					$domain = $_SERVER['REMOTE_ADDR'];
				}
				$COCKPIT = sprintf('%s %s', $mobile_agent->getUID(), $domain);
			}
			if($COCKPIT && CDBSession::sessionCheckCookie($COCKPIT)){
				$db->updateOther(
					'siteUser', 'systemSession', 'cookie', $COCKPIT, array('lastSeen'=>date("Y-m-d H:i:s"))
				);
				return true;
			}
			if(!$COCKPIT){
				srand((double)microtime() * 1000000);
				$loop = true;
				while($loop == true){
					$COCKPIT = md5(uniqid(rand()));
					$query = "SELECT sessionId FROM systemSession WHERE cookie='$COCKPIT'";
					$res = $db->queryOther('siteUser', $query);
					if(!$res->num_rows) $loop=false;
				}
				setcookie("COCKPIT", $COCKPIT, (time() + 31536000), '/');
			}
			$db->insertOther(
				'siteUser', 'systemSession'
				, array(
					'cookie' => $COCKPIT, 'ip' => $_SERVER["REMOTE_ADDR"], 'lastSeen' => date("Y-m-d H:i:s")
				)
			);
		}

		public static function sessionUnlinkUserId($userId){
			global $db;
			global $COCKPIT;
			$db->updateOther('siteUser',  'systemSession', 'cookie', $COCKPIT, array('userId' => 0));
			setcookie("COCKPIT_SESSION", "", time() - 3600);
			setcookie("COCKPIT_SESSION", "", time() - 3600, "/");
		}

		public static function validate(){
			if(!isset($COCKPIT_SYSTEM_DEF["userId"])){
				header('location: /login.php?loginto=' . $_SERVER['REQUEST_URI']);
				exit;
			}
		}

		/**
		 * Validate Employer
		 *
		 * @param boolean $redirect If true, will redirect browser without returning.
		 * @return boolean true on success, false if validation failed.
		 */
		public static function validateUser($redirect = true){
			
			global $COCKPIT_SYSTEM_DEF;
			if(
				!isset($COCKPIT_SYSTEM_DEF["userId"]) || $COCKPIT_SYSTEM_DEF["userId"] == 0
			){
				if(!$redirect) return false;
				header('location: signin.php?signinto=' . $_SERVER['REQUEST_URI']);
				exit;
			}
			return true;
		}

		/* PRIVATE FUNCTIONS */
		private static function sessionCheckCookie($cookie){
			global $db;
			$query = "SELECT lastSeen FROM systemSession WHERE cookie='$cookie'";
			$res = $db->queryOther('siteUser', $query);
			return $res->num_rows;
		}
	}

	if(isset($_COOKIE["COCKPIT"])) $COCKPIT = $_COOKIE["COCKPIT"];
	if(isset($_COOKIE["COCKPIT_SESSION"])) $COCKPIT_SESSION = $_COOKIE["COCKPIT_SESSION"];
	$mobile_agent = Net_UserAgent_Mobile::factory();
	CDBSession::sessionSetCookie();
	$COCKPIT_SYSTEM_DEF = array();
	$COCKPIT_SYSTEM_DEF["sessionId"] = CDBSession::sessionGetId();
	$COCKPIT_SYSTEM_DEF["userStatus"] = CDBSession::sessionGetStatus();
	$COCKPIT_SYSTEM_DEF["cookie"] = $COCKPIT;
	$COCKPIT_SYSTEM_DEF["userId"] = (int)CDBSession::sessionGetUserId();
	$foo = CDBSession::getUserName($COCKPIT_SYSTEM_DEF['userId']);
	$COCKPIT_SYSTEM_DEF["username"] = $foo['username'];

	// check bootstrap.php
	$COCKPIT_SYSTEM_DEF["protocol"] = CSettings::$HTTP_PROTOCOL;

	if(substr($_SERVER['REQUEST_URI'], 0, 4) == '/en/') $COCKPIT_SYSTEM_DEF["lang"] = 'en';
	else if(substr($_SERVER['REQUEST_URI'], 0, 4) == '/ja/') $COCKPIT_SYSTEM_DEF["lang"] = 'ja';
	else if(substr($_SERVER['REQUEST_URI'], 0, 4) == '/bn/') $COCKPIT_SYSTEM_DEF["lang"] = 'bn';
	else $COCKPIT_SYSTEM_DEF["lang"] = 'en';
	
?>