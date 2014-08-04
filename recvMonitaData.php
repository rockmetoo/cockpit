<?php

	include_once 'bootstrap.php';
	include_once 'COAuthProvider.php';
	include_once 'CHelperFunctions.php';
	
	$provider = new COAuthProvider('monita');
	
	$provider->checkRequest();
	
	// XXX: IMPORTANT - invalid oauth request
	if($provider->isOauthError())
	{
		/*echo json_encode(
		 * array(
		 * 'error'=>1, 'message'=>'invalid oauth request', 'ckey'=>$provider->consumer_key,
		 * 'csecret'=>$provider->consumer_secret, 'token'=>$provider->token,
		 * 'tokenSecret'=>$provider->token_secret
		 * )
		 * );*/
		echo json_encode(array('error'=>1, 'message'=>'invalid oauth request'));
		exit;
	}
	else
	{
		if(isset($_REQUEST['data']))
		{
			$_REQUEST['data']	= CHelperFunctions::fixJSON($_REQUEST['data']);
			$MONITA_DATA		= json_decode($_REQUEST['data']);
			
			if($MONITA_DATA == NULL)
			{
				// XXX: IMPORTANT - invalid json post data
				echo json_encode(array('error'=>2, 'message'=>'invalid JSON post data'));
				exit;
			}
			
			// XXX: IMPORTANT - successfully monitad data received
			echo json_encode(array('error'=>0, 'message'=>'successfully data received'));
			
			
			exit;
		}
		else
		{
			// XXX: IMPORTANT - invalid post data
			echo json_encode(array('error'=>3, 'message'=>'invalid post data'));
			exit;
		}
	}
