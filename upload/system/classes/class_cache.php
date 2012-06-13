<?php
	
	global $C;
	
	switch($C->CACHE_MECHANISM)
	{
		case 'memcached':
			class cache extends cache_memcached {}
			break;
			
		case 'apc':
			class cache extends cache_apc {}
			break;
		
		case 'mysqlheap':
			class cache extends cache_mysqlheap {}
			break;
		
		default:
			class cache extends cache_filesystem {}
	}
	
?>