<?php
	
	class cache_apc
	{
		private $keys_prefix;
		private $debug_mode;
		private $debug_info;
		
		public function __construct()
		{
			global $C;
			$this->keys_prefix	= $C->CACHE_KEYS_PREFIX;
			$this->debug_mode	= $C->DEBUG_MODE==TRUE;
			$this->debug_info	= (object) array (
				'queries'	=> array(),
				'time'	=> 0,
			);
		}
		
		public function get($key)
		{
			$key	= $this->keys_prefix.$key;
			$time	= microtime(TRUE);
			$res	= apc_fetch($key);
			$time	= microtime(TRUE)-$time;
			if($this->debug_mode) {
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'GET',
					'key'		=> $key,
					'result'	=> !$res ? 'FALSE' : gettype($res),
					'time'	=> number_format($time, 5, '.', ''),
				);
			}
			return $res;
		}
		
		public function set($key, $data, $ttl)
		{
			$key	= $this->keys_prefix.$key;
			if($this->debug_mode) {
				$time	= microtime(TRUE);
				$res	= apc_store($key, $data, $ttl);
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'SET',
					'key'		=> $key,
					'result'	=> $res ? 'TRUE' : 'FALSE',
					'time'	=> number_format($time, 5, '.', ''),
				);
			}
			return apc_store($key, $data, $ttl);
		}
		
		public function del($key)
		{
			$key	= $this->keys_prefix.$key;
			if( $this->debug_mode ) {
				$time	= microtime(TRUE);
				$res	= apc_delete($key);
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'DELETE',
					'key'		=> $key,
					'result'	=> $res ? 'TRUE' : 'FALSE',
					'time'	=> number_format($time, 5, '.', ''),
				);
				return $res;
			}
			return apc_delete($key);
		}
		
		public function get_debug_info()
		{
			$debug_info	= clone($this->debug_info);
			$debug_info->time	= number_format($debug_info->time, 4, '.', '');
			$debug_info->queries	= array_reverse($debug_info->queries);
			return $debug_info;
		}
		
		public function garbage_collector()
		{
		}
	}
	
?>