<?php

	include_once '../bootstrap.php';
	include_once 'CDBSession.php';
	include_once 'CDBHttpTestPlot.php';
	include_once 'formValues' . $SMITH_SYSTEM_DEF['lang'] . '.php';
	
	global $SMITH_SYSTEM_DEF;
	
	if(ALLOWED_REFERRER !== ''
		&& (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']),
			strtoupper(ALLOWED_REFERRER)) === false
		)
	){
		die("Internal server error. Please contact system administrator.");
	}
	
	// Only allow employers who are logged in to view this page
	CDBSession::validateUser();

	$plotName = $_POST['plotName'];
	
	CDBHttpTestPlot::undoDeleteHttpTestPlot($plotName);
	
	echo '{"error": 0, "message": "Undo operation successfully executed"}';

	exit;