<?php
	
	chdir(dirname(__FILE__));
	if( !isset($_SERVER['HTTP_HOST']) ) { $_SERVER['HTTP_HOST'] = '127.0.0.1'; }
	if( !isset($_SERVER['REMOTE_ADDR']) ) { $_SERVER['REMOTE_ADDR'] = '127.0.0.1'; }
	
	require_once('../helpers/func_main.php');
	require_once('../conf_system.php');
	$_SERVER['HTTP_HOST']	= $C->OUTSIDE_DOMAIN;
	$C->DOMAIN			= $C->OUTSIDE_DOMAIN;
	
	@session_start();
	
	if( !isset($cache) ) { $cache = new cache(); }
	if( !isset($db1) ) { $db1 = new mysql($C->DB_HOST, $C->DB_USER, $C->DB_PASS, $C->DB_NAME); }
	if( !isset($db2) ) { $db2 = & $db1; }
	if( !isset($network)) {
		$network	= new network();
		$network->LOAD();
	}
	$user		= new user();
	$page		= new page();
	
	ini_set( 'error_reporting', E_ALL | E_STRICT );
	ini_set( 'display_errors', '1' );
	ini_set( 'max_execution_time',	10*60 );
	ini_set( 'memory_limit',	64*1024*1024 );
	
	$r	= $db1->query('SELECT * FROM crons WHERE is_running=1 AND next_run<"'.(time()-3*60*60).'" ');
	while( $obj = $db1->fetch_object($r) ) {
		$db1->query('UPDATE crons SET is_running=0, next_run="'.time().'" WHERE cron="'.$obj->cron.'" ');
	}
	
	if( function_exists('php_sapi_name') && php_sapi_name()=='cli' ) {
		$cache->set('real_cronjob_is_set', TRUE, 300);
	}
	
	$crons	= array (
		'1min'	=> 1*60,
		'2min'	=> 2*60,
		'5min'	=> 5*60,
		'30min'	=> 30*60,
		'1h'		=> 1*60*60,
		'6h'		=> 6*60*60,
	);
	
	$crons_to_run	= array();
	
	foreach($crons as $cr=>$tm)
	{
		$db1->query('SELECT * FROM crons WHERE cron="'.$cr.'" LIMIT 1');
		if( $obj = $db1->fetch_object() ) {
			if( $obj->is_running == 1 ) {
				continue;
			}
			if( $obj->next_run <= time() ) {
				$db1->query('UPDATE crons SET last_run="'.time().'", next_run="'.(time()+$tm).'", is_running=1 WHERE cron="'.$cr.'" LIMIT 1');
				$crons_to_run[]	= $cr;
			}
			
		}
		else {
			$db1->query('INSERT INTO crons SET cron="'.$cr.'", last_run="'.time().'", next_run="'.(time()+$tm).'", is_running=1');
			$crons_to_run[]	= $cr;
		}
	}
	
	foreach($crons_to_run as $cr)
	{
		echo "RUNNING CRON: ".$cr."\n\n";
		
		$current_directory	= dirname(__FILE__).'/php_'.$cr;
		if( FALSE == is_dir($current_directory) ) {
			continue;
		}
		$dir	= opendir($current_directory);
		$fls	= array();
		while( $file = readdir($dir) ) {
			$fls[]	= $file;
		}
		sort($fls);
		foreach($fls as $file) {
			$current_file	= $current_directory.'/'.$file;
			$tmp	= pathinfo($current_file);
			if( 'php' != $tmp['extension'] ) {
				continue;
			}
			
			echo "FILE: ".$tmp['basename']."\n\n";
			
			include( $current_file );
		}
		$db1->query('UPDATE crons SET is_running=0 WHERE cron="'.$cr.'" LIMIT 1');
	}
	
?>