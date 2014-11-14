<?php

    include_once 'bootstrap.php';
	include_once 'CDBSession.php';
	global $COCKPIT_SYSTEM_DEF;

	CDBSession::sessionSetCookie();
	CDBSession::sessionUnlinkUserId($COCKPIT_SYSTEM_DEF["userId"]);

	header('Location: /');
?>