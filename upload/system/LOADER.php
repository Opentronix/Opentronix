<?php
	
	$SCRIPT_START_TIME	= microtime(TRUE);
	chdir(dirname(__FILE__));
	
	require_once('./helpers/func_main.php');
	require_once('./conf_system.php');
	
	session_start();
	
	$cache	= new cache();
	$db1 		= new mysql($C->DB_HOST, $C->DB_USER, $C->DB_PASS, $C->DB_NAME);
	$db2		= &$db1;
	
	if( ! $C->INSTALLED ) {
		exit;
	}
	
	$network	= new network();
	$network->LOAD();
	
	$user		= new user();
	$user->LOAD();
	
	ob_start('ob_gzhandler', 6);
	
	$page		= new page();
	$page->LOAD();
	
?>