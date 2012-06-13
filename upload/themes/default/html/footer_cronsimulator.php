<?php
	
	// 
	// Please read the file ./system/cronjobs/readme.txt
	// 
	
	if( !isset($C->CRONJOB_IS_INSTALLED) || !$C->CRONJOB_IS_INSTALLED ) {
		$lastrun	= $GLOBALS['cache']->get('cron_last_run');
		if( ! $lastrun || $lastrun < time()-60 ) {
			echo '
				<script type="text/javascript">
					var tmpreq = ajax_init(false);
					if( tmpreq ) {
						tmpreq.onreadystatechange	= function() {  };
						tmpreq.open("HEAD", siteurl+"cron/r:"+Math.round(Math.random()*1000), true);
						tmpreq.setRequestHeader("connection", "close");
						tmpreq.send("");
						setTimeout( function() { tmpreq.abort(); }, 3000 );
					}			
				</script>';
		}
	}
	
?>