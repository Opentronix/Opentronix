<?php
	
	class cache_mysqlheap
	{
		private $keys_prefix;
		private $debug_mode;
		private $debug_info;
		private $db;
		
		public function __construct()
		{
			global $C;
			$this->keys_prefix	= $C->CACHE_KEYS_PREFIX;
			$this->debug_mode	= $C->DEBUG_MODE==TRUE;
			$this->debug_info	= (object) array (
				'queries'	=> array(),
				'time'	=> 0,
			);
			$this->db	= isset($GLOBALS['db1']) ? $GLOBALS['db1'] : new mysql($C->DB_HOST, $C->DB_USER, $C->DB_PASS, $C->DB_NAME);
		}
		
		public function get($key)
		{
			$time	= microtime(TRUE);
			$keyy	= md5($this->keys_prefix.$key);
			$res	= FALSE;
			$sdf	= $this->db->query('SELECT data, expire FROM cache WHERE `key`="'.$keyy.'" LIMIT 1', FALSE);
			if( $tmp = $this->db->fetch_object($sdf) ) {
				if( intval($tmp->expire) <= time() ) {
					$this->del($key);
				}
				else {
					$tmp	= stripslashes($tmp->data);
					if( $tmp = unserialize($tmp) ) {
						$res	= $tmp;
					}
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
			$time	= microtime(TRUE);
			$keyy	= md5($this->keys_prefix.$key);
			$data	= $this->db->escape(serialize($data));
			$exp	= time() + $ttl;
			$res	= $this->db->query('REPLACE INTO cache SET `key`="'.$keyy.'", data="'.$data.'", expire="'.$exp.'" ', FALSE);
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
			$time	= microtime(TRUE);
			$keyy	= md5($this->keys_prefix.$key);
			$sdf	= $this->db->query('DELETE FROM cache WHERE `key`="'.$keyy.'" LIMIT 1', FALSE);
			$res	= $this->db->affected_rows($sdf)==0 ? FALSE : TRUE;
			if( $this->debug_mode ) {
				$time	= microtime(TRUE)-$time;
				$this->debug_info->time	+= $time;
				$this->debug_info->queries[]	= (object) array (
					'action'	=> 'DELETE',
					'key'		=> $this->keys_prefix.$key,
					'result'	=> $res ? 'TRUE' : 'FALSE',
					'time'	=> number_format($time, 5, '.', ''),
				);
			}
			return $res;
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
			$time	= microtime(TRUE);
			$sdf	= $this->db->query('DELETE FROM cache WHERE expire<="'.time().'" ', FALSE);
			$i	= $this->db->affected_rows($sdf);
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