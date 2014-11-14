<?php

	class CSettings{

		static $BASE_DIRECTORY;
		
		static $HTTP_PROTOCOL;
		
		static $SYSTEM_DOMAIN_VALUES = array(
			'ec_me'			=> "mechanics.jp",
			'preg_ec' 		=> "mechanics\.jp",
			'smtp_server'	=> "smtp.mechanics.jp",
			'smtp_port'		=> 25,
		);

		static $SYSTEM_HOST = array(
			'host' => "cockpit.mechanics.jp"
		);

		static $SYSTEM_MAIL_VALUES = array(
			'support'		=> "support@mechanics.jp",
			'info'			=> "info@mechanics.jp",
			'tech'			=> "tech@mechanics.jp",
			'noreply'		=> "confirm@mechanics.jp",
		);
		
		static $COMMAND_KEY = "40complex#$!11JY";
		
		static $COCKPIT_CMD_INDEX = array(
			'COCKPIT_CMD_INDEX_RUN_SPECIFIC_PLOT'			=> 1,
			'COCKPIT_CMD_INDEX_RUN_SPECIFIC_DL_PLOT'		=> 49,
			'COCKPIT_CMD_INDEX_RUN_SPECIFIC_UL_PLOT'		=> 50
		);

		// we are using mysqlnd_ms php plugins which is actually load balance mysql connection
		// initialize it in bootstrap.php
		static $MYSQL_CONNECTION_POOL			= array();
		
		// initialize it in bootstrap.php
		static $MONGO_CONNECTION_POOL			= array();
		
		static $HOST_IP_OWNERSHIP_SALT			= "123456";
		static $HOST_IP_OWNERSHIP_FILENAME_SALT	= "654321";
		
		static $UPLOAD_FILE_DIR					= "/var/tmp/cockpit/";
		
		static $GRAPH_STEP_SIZE					= 60;
		
		//Fixes MAGIC_QUOTES
	    static function fixSlashes($foo = '')
	    {
	        if(is_null($foo) || $foo == '') return null;
	        if(!get_magic_quotes_gpc()) return $foo;
	        return is_array($foo) ? array_map('self::fixSlashes', $foo) : stripslashes($foo);
	    }
	}
