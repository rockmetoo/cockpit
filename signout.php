<?php

    include_once 'bootstrap.php';
	include_once 'CDBSession.php';
	global $SMITH_SYSTEM_DEF;

	CDBSession::sessionSetCookie();
	CDBSession::sessionUnlinkUserId($SMITH_SYSTEM_DEF["userId"]);

	header('Location: /');
?>