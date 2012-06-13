<?php
	
	class page
	{
		public function __construct()
		{
			$this->network	= & $GLOBALS['network'];
			$this->user		= & $GLOBALS['user'];
			$this->cache	= & $GLOBALS['cache'];
			$this->db1		= & $GLOBALS['db1'];
			$this->db2		= & $GLOBALS['db2'];
			$this->request	= array();
			$this->params	= new stdClass;
			$this->params->user	= FALSE;
			$this->params->group	= FALSE;
			$this->title	= NULL;
			$this->html		= NULL;
			$this->controllers	= $GLOBALS['C']->INCPATH.'controllers/';
			$this->lang_data		= array();
			$this->tpl_name	 	= 'default';
		}
		
		public function LOAD()
		{
			$this->_parse_input();
			
			if( !$this->user->is_logged && $this->param('invited') ) {
				$_SESSION['invite_code']	= trim($this->param('invited'));
			}
			
			$this->_set_template();
			$this->_send_headers();
			$this->_load_controller();
		}
		
		private function _parse_input()
		{
			global $C;
			$this->params->user	= FALSE;
			$this->params->group	= FALSE;
			$request	= $_SERVER['REQUEST_URI'];
			$pos		= strpos($request, '?');
			if( FALSE !== $pos ) {
				$request	= substr($request, 0, $pos);
			}
			if( FALSE !== strpos($request, '//') ) {
				$request	= preg_replace('/\/+/iu', '/', $request);
			}
			$tmp	= str_replace(array('http://','https://'), '', $C->SITE_URL);
			if( FALSE !== strpos($tmp, '//') ) {
				$tmp	= preg_replace('/\/+/iu', '/', $tmp);
			}
			$tmp	= substr($tmp, strpos($tmp, '/'));
			if( substr($request,0,strlen($tmp)) == $tmp ) {
				$request	= substr($request, strlen($tmp));
			}
			$is_mobile	= FALSE;
			$request	= '/'.ltrim($request);
			if( substr($_SERVER['HTTP_HOST'], 0, 2) == 'm.' ) {
				$is_mobile	= TRUE;
				$tmp	= substr($_SERVER['HTTP_HOST'], 2);
				$C->DOMAIN		= 'm.'.$C->DOMAIN;
				$C->SITE_URL	= preg_replace('/^(http(s)?\:\/\/)(.*)$/iu', '$1m.$3', $C->SITE_URL);
				$C->API_ID		= 1;
			}
			elseif( $request=='/m' || substr($request, 0, 3)=='/m/' ) {
				$is_mobile	= TRUE;
				$C->SITE_URL	.= 'm/';
				$C->API_ID		= 1;
			}
			if( $is_mobile ) {
				$is_touch	= FALSE;
				if( isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'iPhone') ) {
					$is_touch	= TRUE;
				}
				elseif( isset($_COOKIE['mobitouch']) && $_COOKIE['mobitouch']==1 ) {
					$is_touch	= TRUE;
				}
				if( $is_touch ) {
					$this->controllers	.= 'mobile_iphone/';
				}
				else {
					$this->controllers	.= 'mobile/';
				}
			}
			if( $_SERVER['HTTP_HOST']!=$C->DOMAIN && FALSE!==strpos($_SERVER['HTTP_HOST'], '.'.$C->DOMAIN) ) {
				$tmp	= str_replace('.'.$C->DOMAIN, '', $_SERVER['HTTP_HOST']);
				$tmp	= preg_replace('/^www\./', '', $tmp);
				$tmp	= trim($tmp);
				if( ! empty($tmp) ) {
					$request	= $tmp.'/'.$request;
				}
			}
			$request	= trim($request, '/');
			if( empty($request) ) {
				$this->request[]	= 'home';
				return;
			}
			if( substr($request, 0, 2) == 'm/' ) {
				$request	= substr($request, 2);
			}
			$request	= explode('/', $request);
			foreach($request as $i=>$one) {
				if( FALSE!==strpos($one,':') && preg_match('/^([a-z0-9\-_]+)\:(.*)$/iu',$one,$m) ) {
					$this->params->{$m[1]}	= $m[2];
					unset($request[$i]);
					continue;
				}
				if( ! preg_match('/^([a-z0-9\-_]+)$/iu', $one) ) {
					unset($request[$i]);
					continue;
				}
			}
			if( 0 == count($request) ) {
				$this->request[]	= 'home';
				return;
			}
			$request	= array_values($request);
			$first	= $request[0];
			if( file_exists($this->controllers.$first.'.php') ) {
				$this->request[]	= $first;
			}
			elseif( $u = $this->network->get_user_by_username($first, FALSE, TRUE) ) {
				$this->request[]	= 'user';
				$this->params->user	= $u;
			}
			elseif( $g = $this->network->get_group_by_name($first, FALSE, TRUE) ) {
				$this->request[]	= 'group';
				$this->params->group	= $g;
			}
			else {
				$this->request[]	= 'home';
				return;
			}
			unset($request[0]);
			foreach($request as $one) {
				$t	= $this->request;
				$t[]	= $one;
				if( file_exists( $this->controllers.implode('_', $t).'.php') ) {
					$this->request[]	= $one;
					continue;
				}
				break;
			}
			if( ! $this->params->user ) {
				$this->params->user	= $this->user->is_logged ? $this->user->id : FALSE;
			}
			if( 0 == count($this->request) ) {
				$this->request[]	= 'home';
				return;
			}
		}
		
		private function _send_headers()
		{
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', FALSE);
			header('Pragma: no-cache');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s'). ' GMT');
			
			if( $this->request[0] == 'ajax' ) {
				if( $this->param('ajaxtp') == 'xml' ) {
					header('Content-type: application/xml; charset=utf-8');
				}
				else {
					header('Content-type: text/plain; charset=utf-8');
				}
			}
			else {
				header('Content-type: text/html; charset=utf-8');
			}
		}
		
		public function _set_template()
		{
			if( isset($GLOBALS['C']->THEME) && file_exists($GLOBALS['C']->INCPATH.'../themes/'.$GLOBALS['C']->THEME.'/theme.php') ) {
				$this->tpl_name		= $GLOBALS['C']->THEME;
			}
			$this->tpl_dir		= $GLOBALS['C']->INCPATH.'../themes/'.$this->tpl_name.'/';
			$current_theme	= FALSE;
			@include( $this->tpl_dir.'theme.php' );
			$GLOBALS['C']->LOGO_HEIGHT	= 0;
			if( $current_theme && isset($current_theme->logo_height) ) {
				$GLOBALS['C']->LOGO_HEIGHT	= intval($current_theme->logo_height);
			}
			$GLOBALS['C']->THEME	= $this->tpl_name;
			return $current_theme;
		}
		
		private function _load_controller()
		{
			global $C, $D;
			$D	= new stdClass;
			$D->page_title	= $C->SITE_TITLE;
			$db1		= & $this->db1;
			$db2		= & $this->db2;
			$db		= & $db2;
			$cache	= & $this->cache;
			$user		= & $this->user;
			$network	= & $this->network;
			
			require_once( $this->controllers.implode('_',$this->request).'.php' );
		}
		
		public function load_template($filename, $output_content=TRUE)
		{
			global $C, $D;
			$filename	= $this->tpl_dir.'html/'.$filename;
			if( $output_content ) {
				require($filename);
				return TRUE;
			}
			else {
				ob_start();
				require($filename);
				$cnt	= ob_get_contents();
				ob_end_clean();
				return $cnt;
			}
		}
		
		public function load_langfile($filename)
		{
			if( ! isset($this->tmp_loaded_langfiles) ) {
				$this->tmp_loaded_langfiles	= array();
			}
			$this->tmp_loaded_langfiles[]	= $filename;
			global $C;
			$lang	= array();
			ob_start();
			require( $GLOBALS['C']->INCPATH.'languages/'.$GLOBALS['C']->LANGUAGE.'/'.$filename );
			ob_end_clean();
			if( ! is_array($lang) ) {
				return FALSE;
			}
			foreach($lang as $k=>$v) {
				$this->lang_data[$k]	= $v;
			}
		}
		
		public function lang($key, $replace_strings=array(), $in_another_language=FALSE)
		{
			if( $in_another_language && $in_another_language!=$GLOBALS['C']->LANGUAGE && is_dir($GLOBALS['C']->INCPATH.'languages/'.$in_another_language) ) {
				return $this->lang_in_another_language($key, $replace_strings, $in_another_language);
			}
			if( empty($key) ) {
				return '';
			}
			if( ! isset($this->lang_data[$key]) ) {
				return '';
			}
			$txt	= $this->lang_data[$key];
			if( 0 == count($replace_strings) ) {
				return $txt;
			}
			return str_replace(array_keys($replace_strings), array_values($replace_strings), $txt);
		}
		
		public function lang_in_another_language($key, $replace_strings=array(), $in_language=FALSE)
		{
			if( empty($key) ) {
				return '';
			}
			if( ! isset($this->tmp_loaded_langfiles) ) {
				return '';
			}
			$lang_data	= array();
			foreach($this->lang_data as $k=>$v) {
				$lang_data[$k]	= $v;
			}
			if( $in_language && is_dir($GLOBALS['C']->INCPATH.'languages/'.$in_language) ) {
				foreach($this->tmp_loaded_langfiles as $f) {
					$lang	= array();
					ob_start();
					require( $GLOBALS['C']->INCPATH.'languages/'.$in_language.'/'.$filename );
					ob_end_clean();
					if( is_array($lang) ) {
						foreach($lang as $k=>$v) {
							$lang_data[$k]	= $v;
						}
					}
				}
			}
			if( ! isset($lang_data[$key]) ) {
				return '';
			}
			$txt	= $lang_data[$key];
			if( 0 == count($replace_strings) ) {
				return $txt;
			}
			return str_replace(array_keys($replace_strings), array_values($replace_strings), $txt);
		}
		
		public function param($key)
		{
			if( FALSE == isset($this->params->$key) ) {
				return FALSE;
			}
			$value	= $this->params->$key;
			if( is_numeric($value) ) {
				return floatval($value);
			}
			if( $value=="true" || $value=="TRUE" ) {
				return TRUE;
			}
			if( $value=="false" || $value=="FALSE" ) {
				return FALSE;
			}
			return $value;
		}
		
		public function redirect($loc, $abs=FALSE)
		{
			global $C;
			if( ! $abs && preg_match('/^http(s)?\:\/\//', $loc) ) {
				$abs	= TRUE;
			}
			if( ! $abs ) {
				if( $loc{0} != '/' ) {
					$loc	= $C->SITE_URL.$loc;
				}
			}
			if( ! headers_sent() ) {
				header('Location: '.$loc);
			}
			echo '<meta http-equiv="refresh" content="0;url='.$loc.'" />';
			echo '<script type="text/javascript"> self.location = "'.$loc.'"; </script>';
			exit;
		}
		
		public function set_lasturl($url='')
		{
			if( ! empty($url) ) {
				$_SESSION['LAST_URL']	= $url;
			}
			else {
				$_SESSION['LAST_URL']	= 'http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'];
			}
			$_SESSION['LAST_URL']	= rtrim($_SESSION['LAST_URL'], '/');
		}
		public function get_lasturl()
		{
			return isset($_SESSION['LAST_URL']) ? $_SESSION['LAST_URL'] : '/';
		}
	}
	
?>