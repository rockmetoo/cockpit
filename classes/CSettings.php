<?php

	class CSettings{

		static $BASE_DIRECTORY;
		
		static $HTTP_PROTOCOL;
		
		static $SYSTEM_DOMAIN_VALUES = array(
			'ec_me'			=> "smithcp.com",
			'preg_ec' 		=> "smithcp\.com",
			'ec_biz'		=> "smithcp.biz",
			'smtp_server'	=> "smtp.smithcp.biz",
			'smtp_port'		=> 25,
		);

		static $SYSTEM_HOST = array(
			'host' => "smithcp.com"
		);

		static $SYSTEM_MAIL_VALUES = array(
			'support'		=> "support@smithcp.com",
			'info'			=> "info@smithcp.com",
			'tech'			=> "tech@smithcp.com",
			'noreply'		=> "confirm@smithcp.com",
		);
		
		static $COMMAND_KEY = "40complex#$!11JY";
		
		static $SMITH_CMD_INDEX = array(
			'SMITH_CMD_INDEX_RUN_SPECIFIC_PLOT'			=> 1,
			'SMITH_CMD_INDEX_RUN_SPECIFIC_DL_PLOT'		=> 49,
			'SMITH_CMD_INDEX_RUN_SPECIFIC_UL_PLOT'		=> 50
		);

		// we are using mysqlnd_ms php plugins which is actually load balance mysql connection
		// initialize it in bootstrap.php
		static $MYSQL_CONNECTION_POOL			= array();
		
		// initialize it in bootstrap.php
		static $MONGO_CONNECTION_POOL			= array();
		
		static $HOST_IP_OWNERSHIP_SALT			= "123456";
		static $HOST_IP_OWNERSHIP_FILENAME_SALT	= "654321";
		
		static $UPLOAD_FILE_DIR					= "/var/tmp/smithcp/";
		
		//Fixes MAGIC_QUOTES
	    static function fixSlashes($foo = '')
	    {
	        if(is_null($foo) || $foo == '') return null;
	        if(!get_magic_quotes_gpc()) return $foo;
	        return is_array($foo) ? array_map('self::fixSlashes', $foo) : stripslashes($foo);
	    }
	}
