<?php
	
	/**
	 * IMPORTANT: make this file unable to execute via WEB Server
	 */

	require_once('classes'. DIRECTORY_SEPARATOR . 'CSettings.php');
	
	define('ALLOWED_REFERRER', CSettings::$SYSTEM_DOMAIN_VALUES['ec_me']);
	
	global $COCKPIT_SYSTEM_DEF;
	
	// set language settings
	if(isset($_REQUEST['lang'])) $COCKPIT_SYSTEM_DEF['lang'] = $_REQUEST['lang'];
	if(!isset($COCKPIT_SYSTEM_DEF['lang'])) $COCKPIT_SYSTEM_DEF['lang'] = 'en';
	
	// IMPORTANT: 3 kinds of environment we have [local, staging, live]
	// Set this in your web server virtual host config as: setEnv APPLICATION_ENV local
	define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
	
	if(APPLICATION_ENV === 'local')
	{
		CSettings::$MYSQL_CONNECTION_POOL = array(
			'cockpit'=>array('cockpit', 'root', '123456', 'cockpit', '3306', NULL),
			'siteUser'=>array('siteUser', 'root', '123456', 'siteUser', '3306', NULL),
		);
		
		// host, port, user, password, db, persistence, connectionObject
		CSettings::$MONGO_CONNECTION_POOL = array(
			'cockpit' => array('127.0.0.1', '27017', '', '', 'cockpit', false, NULL),
			'cockpit1' => array('127.0.0.1', '27017', '', '', 'cockpit1', false, NULL),
			'cockpit2' => array('127.0.0.1', '27017', '', '', 'cockpit2', false, NULL),
			'cockpit3' => array('127.0.0.1', '27017', '', '', 'cockpit3', false, NULL)
		);
	}
	else if(APPLICATION_ENV === 'staging')
	{
		CSettings::$MYSQL_CONNECTION_POOL = array(
			'cockpit'=>array('cockpit', 'root', '123456', 'cockpit', '3306', NULL),
			'siteUser'=>array('siteUser', 'root', '123456', 'siteUser', '3306', NULL),
		);
		
		// host, port, user, password, db, persistence, connectionObject
		CSettings::$MONGO_CONNECTION_POOL = array(
			'cockpit' => array('127.0.0.1', '27017', '', '', 'cockpit', false, NULL),
			'cockpit1' => array('127.0.0.1', '27017', '', '', 'cockpit1', false, NULL),
			'cockpit2' => array('127.0.0.1', '27017', '', '', 'cockpit2', false, NULL),
			'cockpit3' => array('127.0.0.1', '27017', '', '', 'cockpit3', false, NULL)
		);
	}
	else if(APPLICATION_ENV === 'production')
	{
		CSettings::$MYSQL_CONNECTION_POOL = array(
			'cockpit'=>array('cockpit', 'root', '123456', 'cockpit', '3306', NULL),
			'siteUser'=>array('siteUser', 'root', '123456', 'siteUser', '3306', NULL),
		);
		
		// host, port, user, password, db, persistence, connectionObject
		CSettings::$MONGO_CONNECTION_POOL = array(
			'cockpit' => array('127.0.0.1', '27017', '', '', 'cockpit', false, NULL),
			'cockpit1' => array('127.0.0.1', '27017', '', '', 'cockpit1', false, NULL),
			'cockpit2' => array('127.0.0.1', '27017', '', '', 'cockpit2', false, NULL),
			'cockpit3' => array('127.0.0.1', '27017', '', '', 'cockpit3', false, NULL)
		);
	}
	else
	{
		CSettings::$MYSQL_CONNECTION_POOL = array(
			'cockpit'=>array('cockpit', 'root', '123456', 'cockpit', '3306', NULL),
			'siteUser'=>array('siteUser', 'root', '123456', 'siteUser', '3306', NULL),
		);
		
		// host, port, user, password, db, persistence, connectionObject
		CSettings::$MONGO_CONNECTION_POOL = array(
			'cockpit' => array('127.0.0.1', '27017', '', '', 'cockpit', false, NULL),
			'cockpit1' => array('127.0.0.1', '27017', '', '', 'cockpit1', false, NULL),
			'cockpit2' => array('127.0.0.1', '27017', '', '', 'cockpit2', false, NULL),
			'cockpit3' => array('127.0.0.1', '27017', '', '', 'cockpit3', false, NULL)
		);
	}
	
	//init config
	CSettings::$BASE_DIRECTORY = dirname(__FILE__);

	set_include_path(
	    '.' . PATH_SEPARATOR . CSettings::$BASE_DIRECTORY . PATH_SEPARATOR
		. CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'ajax' . PATH_SEPARATOR
	    . CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'classes' . PATH_SEPARATOR
	    . CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'forms' . PATH_SEPARATOR
	    . CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'includes' . PATH_SEPARATOR
	    . CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'localization' . PATH_SEPARATOR
	    . CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
	    . CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'templates' . PATH_SEPARATOR
	    . CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'dcontents' . PATH_SEPARATOR
	    . CSettings::$BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'pear' . PATH_SEPARATOR
	    . get_include_path()
	);

	date_default_timezone_set('Asia/Tokyo');
	
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'])
	{
		CSettings::$HTTP_PROTOCOL = 'https://';
	}
	else CSettings::$HTTP_PROTOCOL = 'http://';

	//Fix magic quotes
    if(get_magic_quotes_gpc())
    {
        $_POST		= CSettings::fixSlashes($_POST);
        $_GET		= CSettings::fixSlashes($_GET);
        $_REQUEST	= CSettings::fixSlashes($_REQUEST);
        $_COOKIE	= CSettings::fixSlashes($_COOKIE);
    }
?>