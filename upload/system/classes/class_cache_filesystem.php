<?php
	
	class cache_filesystem
	{
		private $path;
		private $keys_prefix;
		private $debug_mode;
		private $debug_info;
		
		public function __construct()
		{
			global $C;
			$this->path	= $C->CACHE_FILESYSTEM_PATH;
			$this->keys_prefix	= $C->CACHE_KEYS_PREFIX;
			$this->debug_mode	= $C->DEBUG_MODE==TRUE;
			$this->debug_info	= (object) array (
				'queries'	=> array(),
				'time'	=> 0,
			);
		}
		
		private function find_filename($key)
		{
			return $this->path.'/'.md5($this->keys_prefix).'-'.md5($key);
		}
		
		public function get($key)
		{
			$file	= $this->find_filename($key);
			$time	= microtime(TRUE);
			$res	= FALSE;
			if( file_exists($file) && is_readable($file) ) {
				$data	= file($file);
				if( $data && is_array($data) && count($data)==2 ) {
					if( intval($data[0]) >= time() ) {
						$res	= unserialize($data[1]);
					}
				}
				if( FALSE === $res ) {
					$this->del($key);
				}
			}
			if( $this->debug_mode ) {
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'GET',
					'key'		=> $this->keys_prefix.$key,
					'result'	=> !$res ? 'FALSE' : gettype($res),
					'time'	=> number_format($time, 5, '.', ''),
				);
			}
			return $res;
		}
		
		public function set($key, $data, $ttl)
		{
			$file	= $this->find_filename($key);
			$time	= microtime(TRUE);
			$this->del($key);
			$data	= (time()+$ttl)."\n".serialize($data);
			$res	= file_put_contents($file, $data);
			chmod($file, 0777);
			if( $this->debug_mode ) {
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'SET',
					'key'		=> $this->keys_prefix.$key,
					'result'	=> $res ? 'TRUE' : 'FALSE',
					'time'	=> number_format($time, 5, '.', ''),
				);
			}
			return $res;
		}
		
		public function del($key)
		{
			$file	= $this->find_filename($key);
			$time	= microtime(TRUE);
			if( file_exists($file) && is_writable($file) ) {
				unlink($file);
			}
			$res	= !file_exists($file);
			if( $this->debug_mode ) {
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'DELETE',
					'key'		=> $this->keys_prefix.$key,
					'result'	=> $res ? 'TRUE' : 'FALSE',
					'time'	=> number_format($time, 5, '.', ''),
				);
				return $res;
			}
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
			$prefix	= md5($this->keys_prefix).'-';
			$prefixlen	= strlen($prefix);
			$time	= microtime(TRUE);
			$dir	= opendir($this->path);
			$i	= 0;
			while($filename = readdir($dir)) {
				if($filename=="." || $filename=="..") {
					continue;
				}
				if( substr($filename, 0, $prefixlen) != $prefix ) {
					continue;
				}
				$file	= $this->path.'/'.$filename;
				$fp	= fopen($file, 'r');
				$tm	= fread($fp, 10);
				fclose($fp);
				if( intval($tm) <= time() && is_writable($file) ) {
					unlink($file);
					$i	++;
				}
			}
			if( $this->debug_mode ) {
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'GARBAGE',
					'key'		=> 'delete',
					'result'	=> $i,
					'time'	=> number_format($time, 5, '.', ''),
				);
			}
			return $i;
		}
	}
	
?>