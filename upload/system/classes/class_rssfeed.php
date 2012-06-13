<?php
	
	class rssfeed
	{
		public $url;
		private $userpwd;
		public $is_read;
		public $is_fetched;
		public $error;
		public $raw;
		public $title;
		public $items;
		
		public function __construct($feed_url, $feed_auth_userpwd='')
		{
			$this->url		= $feed_url;
			$this->userpwd	= $feed_auth_userpwd;
			$this->is_read	= FALSE;
			$this->is_fetched	= FALSE;
			$this->error	= FALSE;
			$this->raw		= '';
			$this->title	= '';
			$this->items	= array();
			$this->has_curl	= function_exists('curl_init');
		}
		
		public function check_if_requires_auth()
		{
			global $C;
			$result	= FALSE;
			if( $this->has_curl ) {
				$ch	= curl_init();
				curl_setopt_array($ch, array(
					CURLOPT_AUTOREFERER	=> TRUE,
					CURLOPT_RETURNTRANSFER	=> TRUE,
					CURLOPT_HEADER	=> TRUE,
					CURLOPT_NOBODY	=> TRUE,
					CURLOPT_CONNECTTIMEOUT	=> 5,
					CURLOPT_TIMEOUT	=> 5,
					CURLOPT_MAXREDIRS	=> 3,
					CURLOPT_REFERER	=> $C->SITE_URL,
					CURLOPT_URL		=> $this->url,
					// Do not add CURLOPT_USERAGENT - it causes bugs in feedburner
				));
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
				if( ! empty($this->userpwd) ) {
					curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
				}
				$result	= curl_exec($ch);
				$result	= trim($result);
				curl_close($ch);
			}
			elseif( function_exists('get_headers') ) {
				$result	= get_headers($this->url);
				$result	= $result ? implode("\n", $result) : FALSE;
			}
			if( !$result || empty($result) ) {
				return FALSE;
			}
			if( preg_match('/(^|\n|\r)(\s)*HTTP\/[0-9.]+(\s)+401(\s)+Authorization(\s)+Required(\s)*($|\n|\r)/is', $result) ) {
				if( preg_match('/(^|\n|\r)(\s)*WWW\-Authenticate\:\s([a-z0-9-]+)/i', $result) ) {
					return TRUE;
				}
			}			
			if( preg_match('/(^|\n|\r)(\s)*HTTP\/[0-9.]+(\s)+401(\s)+Unauthorized(\s)*($|\n|\r)/is', $result) ) {
				if( preg_match('/(^|\n|\r)(\s)*WWW\-Authenticate\:\s([a-z0-9-]+)/i', $result) ) {
					return TRUE;
				}
				$this->error	= TRUE;
				return FALSE;
			}
			return FALSE;
		}
		
		public function set_userpwd($feed_auth_userpwd)
		{
			$this->userpwd	= $feed_auth_userpwd;
		}
		
		public function read()
		{
			if( $this->is_read ) {
				return $this->raw;
			}
			if( $this->error ) {
				return FALSE;
			}	
			global $C;
			if( $this->has_curl ) {
				$ch	= curl_init();
				curl_setopt_array($ch, array(
					CURLOPT_AUTOREFERER	=> TRUE,
					CURLOPT_RETURNTRANSFER	=> TRUE,
					CURLOPT_HEADER	=> FALSE,
					CURLOPT_NOBODY	=> FALSE,
					CURLOPT_CONNECTTIMEOUT	=> 5,
					CURLOPT_TIMEOUT	=> 5,
					CURLOPT_MAXREDIRS	=> 3,
					CURLOPT_REFERER	=> $C->SITE_URL,
					CURLOPT_URL		=> $this->url,
					// Do not add CURLOPT_USERAGENT - it causes bugs in feedburner
				));
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
				if( ! empty($this->userpwd) ) {
					curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
				}
				$result	= curl_exec($ch);
				$result	= trim($result);
				curl_close($ch);
			}
			else {
				$url	= $this->url;
				if( ! empty($this->userpwd) ) {
					if( preg_match('/^(http(s)?\:\/\/)(.*)$/', $url, $m) ) {
						$url	= $m[1].$this->userpwd.'@'.$m[3];
					}
				}
				$result	= @file_get_contents($url);
				$result	= $result ? trim($result) : FALSE;
			}
			if( !$result || empty($result) ) {
				$this->error	= TRUE;
				return FALSE;
			}
			$this->raw	= $result;
			if( preg_match('/xml.*encoding\=(\"|\\\')([a-z0-9-]+)(\"|\\\')/isU', $this->raw, $matches) ) {
				$enc	= strtolower(trim($matches[2]));
				if( $enc != 'utf-8' ) {
					$valid	= FALSE;
					$all		= mb_list_encodings();
					foreach($all as $e) {
						if( strtolower($e) == $enc ) { $valid = TRUE; break; }
					}
					if( $valid ) { $this->raw = mb_convert_encoding($this->raw, 'UTF-8', $enc); }
				}
			}
			if( preg_match('/\<feed/', $this->raw) && preg_match('/\<\/feed/', $this->raw) ) {
				$this->is_read	= TRUE;
				return $this->raw;
			}
			if( preg_match('/\<rss/', $this->raw) && preg_match('/\<\/rss/', $this->raw) ) {
				if( preg_match('/\<channel/', $this->raw) && preg_match('/\<\/channel/', $this->raw) ) {
					$this->is_read	= TRUE;
					return $this->raw;
				}
			}
			$this->error	= TRUE;
			return FALSE;
		}
		
		public function fetch()
		{
			if( $this->is_fetched ) {
				return $this->items;
			}
			if( ! $this->is_read ) {
				$this->read();
			}
			if( $this->error ) {
				return FALSE;
			}
			if( preg_match('/\<title\>(.*)<\/title/iusU', $this->raw, $m) ) {
				$this->title	= trim($m[1]);
				$this->title	= preg_replace('/\s+/ius', ' ', $this->title);
				$this->title	= trim($this->title);
			}
			$this->items	= array();
			if( 0 == count($this->items) ) {
				$tmpdata	= array();
				preg_match_all('/\<item>(.*)\<\/item\>/iusU', $this->raw, $matches, PREG_PATTERN_ORDER);
				foreach($matches[1] as $entry) {
					$data	= (object) array('source_url'=>'', 'source_date'=>'', 'source_title'=>'', 'source_description'=>'', 'source_image'=>'', 'source_video'=>'',);
					if( preg_match('/\<link>(.*)\<\/link\>/iu', $entry, $m) ) {
						$data->source_url			= trim(htmlspecialchars_decode($m[1]));
					}
					if( preg_match('/\<title>(.*)\<\/title\>/iu', $entry, $m) ) {
						$data->source_title		= trim(htmlspecialchars_decode($m[1]));
					}
					if( preg_match('/\<description>(.*)\<\/description\>/ius', $entry, $m) ) {
						$data->source_description	= trim(htmlspecialchars_decode($m[1]));
					}
					if( preg_match('/\<pubDate>(.*)\<\/pubDate\>/iu', $entry, $m) ) {
						$data->source_date	= $this->parse_date_to_timestamp($m[1]);
					}
					if( preg_match('/\<enclosure.*url\=\"(.*)\".*type\=\"image/iuU', $entry, $m) ) {
						$m[1]	= trim(htmlspecialchars_decode($m[1]));
						if( ! preg_match('/feedads/', $m[1]) ) {
							$data->source_image	= $m[1];
						}
					}
					if( empty($data->source_image) && preg_match('/\<img.*src\=\"(.*)\"/iuU', $data->source_description, $m) ) {
						$m[1]	= trim(htmlspecialchars_decode($m[1]));
						if( ! preg_match('/feedads/', $m[1]) ) {
							$data->source_image	= $m[1];
						}
					}
					if( preg_match('/\<content(.*)\<\/content/ius', $entry, $m) ) {
						$m	= $m[1];
						if( empty($data->source_image) && preg_match('/\<img.*src\=\"(.*)\"/iuU', $m, $mm) ) {
							$mm[1]	= trim(htmlspecialchars_decode($mm[1]));
							if( ! preg_match('/feedads/', $m[1]) ) {
								$data->source_image	= $mm[1];
							}
						}
						if( empty($data->source_video) && preg_match('/\<embed.*src\=\"(.*)\"/iuU', $m, $mm) ) {
							$data->source_video	= trim(htmlspecialchars_decode($mm[1]));
						}
					}
					if( preg_match('/feedads/', $data->source_description) ) {
						$data->source_description	= '';
					}
					if( FALSE !== strpos($data->source_title, '<![CDATA[') ) {
						$data->source_title	= str_replace( array('<![CDATA[',']]>'), '', $data->source_title );
					}
					if( FALSE !== strpos($data->source_description, '<![CDATA[') ) {
						$data->source_description	= str_replace( array('<![CDATA[',']]>'), '', $data->source_description );
					}
					$data->source_title		= trim(strip_tags($data->source_title));
					$data->source_description	= trim(strip_tags($data->source_description));
					$data->source_title		= html_entity_decode($data->source_title, ENT_COMPAT, 'UTF-8');
					$data->source_description	= html_entity_decode($data->source_description, ENT_COMPAT, 'UTF-8');
					
					if( empty($data->source_date) ) {
						continue;
					}
					$this->items[]	= $data;
				}
			}
			if( 0 == count($this->items) ) {
				preg_match_all('/\<entry[^\>]*>(.*)\<\/entry\>/iusU', $this->raw, $matches, PREG_PATTERN_ORDER);
				foreach($matches[1] as $entry) {
					$data	= (object) array('source_url'=>'', 'source_date'=>'', 'source_title'=>'', 'source_description'=>'', 'source_image'=>'', 'source_video'=>'',);
					if( preg_match('/\<link.*alternate.*html.*href\=(\"|\\\')(.*)(\"|\\\').*\>/iuU', $entry, $m) ) {
						$data->source_url			= trim(htmlspecialchars_decode($m[2]));
					}
					elseif( preg_match('/\<link.*href\=(\"|\\\')(.*)(\"|\\\').*\>/iuU', $entry, $m) ) {
						$data->source_url			= trim(htmlspecialchars_decode($m[2]));
					}
					if( preg_match('/\<title.*>(.*)\<\/title\>/iu', $entry, $m) ) {
						$data->source_title		= trim(htmlspecialchars_decode($m[1]));
					}
					if( preg_match('/\<published>(.*)\<\/published\>/iu', $entry, $m) ) {
						$data->source_date	= $this->parse_date_to_timestamp($m[1]);
					}
					elseif( preg_match('/\<updated>(.*)\<\/updated\>/iu', $entry, $m) ) {
						$data->source_date	= $this->parse_date_to_timestamp($m[1]);
					}
					if( preg_match('/\<content.*>(.*)\<\/content\>/iu', $entry, $m) ) {
						$data->source_description	= trim(htmlspecialchars_decode($m[1]));
					}
					if( preg_match('/\<img.*src\=(\"|\\\')(.*)(\"|\\\')/iuU', $data->source_description, $m) ) {
						$data->source_image	= trim(htmlspecialchars_decode($m[2]));
					}
					if( preg_match('/\<embed.*src\=\"(.*)\"/iuU', $data->source_description, $m) ) {
						$data->source_video	= trim(htmlspecialchars_decode($m[1]));
					}
					if( FALSE !== strpos($data->source_title, '<![CDATA[') ) {
						$data->source_title	= str_replace( array('<![CDATA[',']]>'), '', $data->source_title );
					}
					if( FALSE !== strpos($data->source_description, '<![CDATA[') ) {
						$data->source_description	= str_replace( array('<![CDATA[',']]>'), '', $data->source_description );
					}
					$data->source_title		= trim(strip_tags($data->source_title));
					$data->source_description	= trim(strip_tags($data->source_description));
					$data->source_title		= html_entity_decode($data->source_title, ENT_COMPAT, 'UTF-8');
					$data->source_description	= html_entity_decode($data->source_description, ENT_COMPAT, 'UTF-8');
					if( empty($data->source_date) ) {
						continue;
					}
					$this->items[]	= $data;
				}
			}
			$this->is_fetched	= TRUE;
			return $this->items;
		}
		
		public function get_lastitem_date()
		{
			if( ! $this->is_fetched ) {
				$this->fetch();
			}
			if( $this->error ) {
				return FALSE;
			}
			$dt	= 0;
			foreach($this->items as $itm) {
				$dt = max($dt, $itm->source_date);
			}
			return $dt==0 ? FALSE : $dt;
		}
		
		public static function parse_date_to_timestamp($date)
		{
			$date	= trim($date);
			if( preg_match('/^[0-9]{4}\-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}/', $date) ) {
				$d	= strptime($date, '%Y-%m-%d %H:%M:%S');
				return gmmktime($d['tm_hour'], $d['tm_min'], $d['tm_sec'], $d['tm_mon']+1, $d['tm_mday'], $d['tm_year']+1900);
			}
			elseif( preg_match('/^([0-9]{4}\-[0-9]{1,2}-[0-9]{1,2}([^0-9]+)[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2})/', $date, $m) ) {
				$d	= strptime($m[1], '%Y-%m-%d'.$m[2].'%H:%M:%S');
				return gmmktime($d['tm_hour'], $d['tm_min'], $d['tm_sec'], $d['tm_mon']+1, $d['tm_mday'], $d['tm_year']+1900);
			}
			if( $d = strptime($date, '%a, %d %b %Y %H:%M:%S %z') ) {
				return gmmktime($d['tm_hour'], $d['tm_min'], $d['tm_sec'], $d['tm_mon']+1, $d['tm_mday'], $d['tm_year']+1900);
			}
			if( $d = strtotime($date) ) {
				return $d;
			}
		}
		
		public function get_ordered_items($after_date, $keywords_filter='')
		{
			if( ! $this->is_fetched ) {
				$this->fetch();
			}
			if( $this->error ) {
				return FALSE;
			}
			if( 0 == count($this->items) ) {
				return array();
			}
			$after_date	= intval($after_date);
			if( $after_date >= $this->get_lastitem_date() ) {
				return array();
			}
			$items	= array();
			foreach($this->items as $item) {
				if( $item->source_date > $after_date ) {
					$items[]	= $item;
				}
			}
			if( 0 == count($items) ) {
				return array();
			}
			$keywords_filter	= trim($keywords_filter);
			if( ! empty($keywords_filter) ) {
				$keywords_filter	= explode(',', $keywords_filter);
				foreach($keywords_filter as &$keyword) {
					$keyword	= mb_strtolower(trim($keyword));
				}
				$items2	= array();
				foreach($keywords_filter as $keyword) {
					foreach($items as $i=>$item) {
						if( FALSE===strpos($item->source_title,$keyword) && FALSE===strpos($item->source_description,$keyword) ) {
							continue;
						}
						$items2[]	= $item;
						unset($items[$i]);
					}
				}
				$items	= $items2;
			}
			foreach($items as $k=>$item) {
				$items[$k]	= $this->item_validate_attachments($item);
			}
			$dates	= array();
			foreach($items as $k=>$item) {
				$dates[$k]	= $item->source_date;
			}
			asort($dates);
			$items2	= array();
			foreach($dates as $k=>$v) {
				$items2[]	= $items[$k];
			}
			return $items2;
		}
		
		private function item_validate_attachments($item)
		{
			if( ! empty($item->source_image) ) {
				$p	= new newpost();
				if( ! $a = $p->attach_image($item->source_image) ) {
					$item->source_image	= '';
				}
				elseif( $a->size_original[0]<100 || $a->size_original[1]<100 ) {
					$item->source_image	= '';
				}
			}
			if( ! empty($item->source_video) ) {
				$p	= new newpost();
				if( ! $a = $p->attach_videoembed($item->source_video) ) {
					$item->source_video	= '';
				}
			}
			return $item;
		}
	}
	
?>