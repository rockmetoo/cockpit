<?php

	include_once 'CDBQuery.php';
	
	
	class COAuthProvider
	{
		const TOKEN_REQUEST = 0; //try to get a request token
		const TOKEN_ACCESS	= 1; //try to get an access token
		const TOKEN_VERIFY	= 2; //try to verify an access token so an API call can be made
	
		private $oauth;
		private $username;
		private $instanceType;
		private $oauth_error = false;
	
		public function __construct($instanceType)
		{
			$this->oauth = new OAuthProvider();
			$this->oauth->consumerHandler(array($this, 'checkConsumer'));
			$this->oauth->timestampNonceHandler(array($this, 'checkNonce'));
			$this->oauth->tokenHandler(array($this, 'checkToken'));
			$this->instanceType = $instanceType;
		}
		
		public function setInstance($instanceType)
		{
			$this->instanceType = $instanceType;
		}
	
		/**
		 * This function check the handlers that we added in the constructor
		 * and then checks for a valid signature
		 * */
		public function checkRequest()
		{
			// now that everything is setup we run the checks
			try
			{
				$this->oauth->checkOAuthRequest();
				$this->oauth_error = false;
			}
			catch(OAuthException $e)
			{
				//echo OAuthProvider::reportProblem($E);
				//$reply = array('error'=>1, 'data'=>'NULL');
				//echo json_encode($reply);
				$this->oauth_error = true;
			}
		}
		
		public function isOauthError()
		{
			return $this->oauth_error;
		}
		
		// $salt can be username
		public function createConsumer($salt='')
		{
			$key	= sha1(OAuthProvider::generateToken(40, true) . $salt);
			$secret	= sha1(OAuthProvider::generateToken(40, true) . $salt);
			
			return array($key, $secret);
		}
		
		/**
		 * This function generates an Access token
		 */
		public function generateAccessToken($salt='')
		{
			$accessToken		= sha1(OAuthProvider::generateToken(20, true) . $salt);
			$accessTokenSecret	= sha1(OAuthProvider::generateToken(20, true) . $salt);
			 
			return array($accessToken, $accessTokenSecret);
		}
		
		// return array of consumer key and secret else 0
		public function getConsumerKeyAndSecret($username)
		{
			global $db;
				
			$query	= "SELECT consumerKey, consumerSecret, nonce FROM user WHERE username = %s";
			$res	= $db->quotedQueryOther('siteUser', $query, $username);
			
			if(!$res->num_rows)
			{
				return 0;
			}
			
			$foo = $res->fetch_assoc();
			
			return $foo;
		}

		public function getNonce($nonce, $timestamp)
		{
			
		}
		
		public function addNonce($nonce)
		{
			
		}
		
		/**
		 * XXX: IMPORTANT
		 * This function checks if the consumer exist in the DB and that it is active.
		 * You can modify it at your will but you __HAVE TO__
		 * set $provider->consumer_secret to the right value or the signature will fail
		 * It's called by OAuthCheckRequest()
		 * @param $provider
		 */
		public function checkConsumer($provider)
		{
			global $db;
			
		 	$query	= "SELECT username, consumerKey, consumerSecret FROM user WHERE consumerKey = %s";
		 	$res	= $db->quotedQueryOther('siteUser', $query, $provider->consumer_key);
		 	
			if(!$res->num_rows)
		 	{
		 		return OAUTH_CONSUMER_KEY_UNKNOWN;
		 	}
		 	
		 	$foo = $res->fetch_assoc();
		 	
		 	$provider->consumer_secret	= $foo['consumerSecret'];
		 	$provider->username			= $foo['username'];
		 	$return						= OAUTH_OK;
		 	
		 	return $return;
		}
		 
		public function checkNonce($provider)
		{
		 	$epoch = strtotime('01-01-1970');
		 	
		 	if($this->oauth->timestamp < $epoch)
		 	{
		 		return OAUTH_BAD_TIMESTAMP;
		 	}
		 	else
		 	{
		 		return OAUTH_OK;
		 	}
		}

		public function checkToken($provider)
		{
		 	global $db;
		 	
		 	$query	= "SELECT username, token, tokenSecret FROM user WHERE token = %s";
		 	$res	= $db->quotedQueryOther('siteUser', $query, $provider->token);
		 	
		 	if(!$res->num_rows)
		 	{
		 		return OAUTH_TOKEN_REJECTED;
		 	}
		 	
		 	$foo					= $res->fetch_assoc();
		 	$provider->token_secret	= $foo['tokenSecret'];
		 	$provider->username		= $foo['username'];
		 	
		 	return OAUTH_OK;
		}
		 
		public function getToken()
		{
			if(isset($this->token)) return $this->token;
		 	else return '';
		}
		 
		public function getUsername()
		{
			if(isset($this->username)) return $this->username;
			else return '';
		}
	}