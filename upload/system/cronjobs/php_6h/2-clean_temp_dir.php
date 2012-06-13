<?php
	
	$d	= opendir( $C->TMP_DIR );
	
	while( $fl = readdir($d) ) {
		if( $fl=='.' || $fl=='..' ) {
			continue;
		}
		$tm	= fileatime( $C->TMP_DIR.$fl );
		if( $tm < time()-1*60*60 ) {
			rm( $C->TMP_DIR.$fl );
		}
	}
	
?>