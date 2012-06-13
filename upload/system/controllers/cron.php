<?php
	
	// 
	// Please read the file ./system/cronjobs/readme.txt
	// 
	
	if( isset($C->CRONJOB_IS_INSTALLED) && $C->CRONJOB_IS_INSTALLED ) {
		echo 'SKIP';
		exit;
	}
	if( $cache->get('real_cronjob_is_set') ) {
		echo 'SKIP';
		exit;
	}
	
	ob_end_clean();
	header("Content-type: text/plain");
	header("Connection: close");
	ignore_user_abort(TRUE);
	
	$run_cron	= FALSE;
	
	$lastrun	= $cache->get('cron_last_run');
	if( ! $lastrun || $lastrun < time()-60 ) {
		$run_cron	= TRUE;
		$cache->set('cron_last_run', time(), 70);
	}
	
	if( $run_cron ) {
		ob_start();
		require( $C->INCPATH.'cronjobs/worker.php' );
		ob_end_clean();
	}
	
	$i	= 0;
	$res	= $db->query('SELECT cron, last_run FROM crons WHERE is_running=1 AND last_run<"'.(time()-6*60*60).'" ');
	while( $obj = $db->fetch_object($res) ) {
		$tmp	= time() + $i*5*60*rand(0.5,1.5);
		$tmp	= round($tmp);
		$db->query('UPDATE crons SET is_running=0, next_run="'.$tmp.'" WHERE cron="'.$obj->cron.'" LIMIT 1');
		$i	++;
	}
	
	echo 'OK';
	exit;
	
?>