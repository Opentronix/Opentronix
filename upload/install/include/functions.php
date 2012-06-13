<?php
	
	function msgbox($title, $text, $closebtn=TRUE, $incss='')
	{
		$div_id	= 'tmpid'.rand(0,99999);
		$html	= '
				<div class="alert" style="'.$incss.'" id="'.$div_id.'">
					<div class="alerttop"><div class="alerttop2"></div></div>
					<div class="alertcontent">
						<div class="alertcontent2">';
		if( $closebtn ) {
			$html	.= '
							<a href="javascript:;" class="alertclose" onclick="this.parentNode.parentNode.parentNode.style.display=\'none\';this.blur();"></a>
							<script type="text/javascript">
								msgbox_to_close.'.$div_id.'	= true;
							</script>';
		}
		$html	.= '
							<strong>'.$title.'</strong>
							'.$text.'
						</div>
					</div>
					<div class="alertbottom"><div class="alertbottom2"></div></div>
				</div>';
		return $html;
	}
	
	function okbox($title, $text, $closebtn=TRUE, $incss='')
	{
		$div_id	= 'tmpid'.rand(0,99999);
		$html	= '
				<div class="alert green" style="'.$incss.'" id="'.$div_id.'">
					<div class="alerttop"><div class="alerttop2"></div></div>
					<div class="alertcontent">
						<div class="alertcontent2">';
		if( $closebtn ) {
			$html	.= '
							<a href="javascript:;" class="alertclose" onclick="this.parentNode.parentNode.parentNode.style.display=\'none\';this.blur();"></a>
							<script type="text/javascript">
								msgbox_to_close.'.$div_id.'	= true;
							</script>';
		}
		$html	.= '
							<strong>'.$title.'</strong>
							'.$text.'
						</div>
					</div>
					<div class="alertbottom"><div class="alertbottom2"></div></div>
				</div>';
		return $html;
	}
	
	function errorbox($title, $text, $closebtn=TRUE, $incss='')
	{
		$div_id	= 'tmpid'.rand(0,99999);
		$html	= '
				<div class="alert red" style="'.$incss.'" id="'.$div_id.'">
					<div class="alerttop"><div class="alerttop2"></div></div>
					<div class="alertcontent">
						<div class="alertcontent2">';
		if( $closebtn ) {
			$html	.= '
							<a href="javascript:;" class="alertclose" onclick="this.parentNode.parentNode.parentNode.style.display=\'none\';this.blur();"></a>
							<script type="text/javascript">
								msgbox_to_close.'.$div_id.'	= true;
							</script>';
		}
		$html	.= '
							<strong>'.$title.'</strong>
							'.$text.'
						</div>
					</div>
					<div class="alertbottom"><div class="alertbottom2"></div></div>
				</div>';
		return $html;
	}
	
	function str_cut($str, $mx)
	{
		return mb_strlen($str)>$mx ? mb_substr($str, 0, $mx-1).'..' : $str;
	}
	
	function nowrap($string)
	{
		return str_replace(' ', '&nbsp;', $string);
	}
	
	function br2nl($string)
	{
		return str_replace(array('<br />', '<br/>', '<br>'), "\r\n", $string);
	}
	
	function strip_url($url)
	{
		$url	= preg_replace('/^(http|https):\/\/(www\.)?/u', '', trim($url));
		$url	= preg_replace('/\/$/u', '', $url);
		return trim($url);
	}
	
	function my_ucwords($str)
	{
		return mb_convert_case($str, MB_CASE_TITLE);
	}
	
	function my_ucfirst($str)
	{
		if( function_exists('mb_strtoupper') ) {
			return mb_strtoupper(mb_substr($str,0,1)).mb_substr($str,1);
		}
		else return $str;
	}
	
	function is_valid_email($email)
	{
		return preg_match('/^[a-zA-Z0-9._%-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z]{2,4}$/u', $email);
	}
	
	function is_valid_username($uname, $check_scripts=TRUE)
	{
		if( FALSE == preg_match('/^[a-zA-Z0-9\-]{4,20}$/', $uname) ) {
			return FALSE;
		}
		if( $check_scripts ) {
			if( file_exists(INCPATH.'../../system/controllers/'.strtolower($uname).'.php') ) {
				return FALSE;
			}
			if( file_exists(INCPATH.'../../system/controllers/mobile/'.strtolower($uname).'.php') ) {
				return FALSE;
			}
			if( file_exists(INCPATH.'../../'.strtolower($uname)) ) {
				return FALSE;
			}
		}
		return TRUE;
	}
	
	function config_replace_variable($source, $variable, $value, $keep_quots=TRUE)
	{
		$pattern	= '/('.preg_quote($variable).'\s*\=\s*)\'([^\\\']*)(\'\s*)/su';
		if( $keep_quots ) {
			return preg_replace($pattern, '${1}\''.$value.'\'${2}', $source);
		}
		return preg_replace($pattern, '${1}'.$value.'${2}', $source);
	}
	
	function load_old_config()
	{
		$file	= INCPATH.'../../system/conf_main.php';
		if( file_exists($file) ) {
			$C	= new stdClass;
			$C->INCPATH	= realpath(INCPATH.'../../system/').'/';
			include($file);			
			$conn	= my_mysql_connect($C->DB_HOST, $C->DB_USER, $C->DB_PASS);
			if( $conn ) {
				$dbs	= my_mysql_select_db($C->DB_NAME, $conn);
				if( $dbs ) {
					$tmp	= my_mysql_query('SELECT * FROM `settings` ', $conn);
					while($obj = my_mysql_fetch_object($tmp)) {
						$C->{$obj->word}	= stripslashes($obj->value);
					}
				}
			}
			return $C;
		}
		$file	= INCPATH.'../../include/conf_main.php';
		if( file_exists($file) ) {
			$C	= new stdClass;
			$src	= file_get_contents($file);
			$pattern	= '/(define(\s)*\((\s)*\'([a-z0-9\-\_]+)\'\,(\s)*)(\')([^\\\']*)(\')((\s)*\))/isu';
			preg_match_all($pattern, $src, $matches, PREG_SET_ORDER);
			foreach($matches as $dfmatches) {
				$key	= trim($dfmatches[4]);
				$val	= trim($dfmatches[7]);
				if( empty($key) ) {
					continue;
				}
				$C->$key	= $val;
			}
			$C->VERSION	= 'unofficial';
			return $C;
		}
		return new stdClass;
	}
	
	function directory_tree_is_writable($node)
	{
		$node	= realpath($node);
		if( ! $node ) {
			return TRUE;
		}
		if( !is_readable($node) || !is_writable($node) ) {
			@chmod($node, 0777);
			if( !is_readable($node) || !is_writable($node) ) {
				return FALSE;
			}
		}
		if( is_dir($node) ) {
			$dir	= opendir($node);
			while($file = readdir($dir)) {
				if( $file == '.' || $file == '..' ) {
					continue;
				}
				if( ! directory_tree_is_writable($node.'/'.$file) ) {
					return FALSE;
				}
			}
			closedir($dir);
		}
		return TRUE;
	}
	
	function directory_tree_delete($node)
	{
		$node	= realpath($node);
		if( ! $node ) {
			return;
		}
		if( ! is_dir($node) ) {
			@unlink($node);
			return;
		}
		$dir	= opendir($node);
		while($file = readdir($dir)) {
			if( $file == '.' || $file == '..' ) {
				continue;
			}
			directory_tree_delete($node.'/'.$file);
		}
		closedir($dir);
		@rmdir($node);
		return;
	}
	
	
	
	
	function myext() {
		$myext	= 'mysql';
		if( function_exists('mysqli_connect') ) {
			$myext	= 'mysqli';
		}
		global $s;
		if( isset($s['MYSQL_MYEXT']) ) {
			if( $s['MYSQL_MYEXT'] == 'mysqli' && function_exists('mysqli_connect') ) {
				$myext	= 'mysqli';
			}
			elseif( $s['MYSQL_MYEXT'] == 'mysql' && function_exists('mysql_connect') ) {
				$myext	= 'mysql';
			}
		}
		return $myext;
	}
	function my_mysql_connect($host, $user, $pass) {
		if( myext() == 'mysqli' ) {
			return @mysqli_connect($host, $user, $pass);
		}
		else {
			return @mysql_connect($host, $user, $pass);
		}
	}
	function my_mysql_select_db($dbname, $conn=FALSE) {
		if( myext() == 'mysqli' ) {
			return @mysqli_select_db($conn, $dbname);
		}
		else {
			return $conn ? @mysql_select_db($dbname, $conn) : @mysql_select_db($dbname);
		}
	}
	function my_mysql_get_server_info($conn=FALSE) {
		if( myext() == 'mysqli' ) {
			return @mysqli_get_server_info($conn);
		}
		else {
			return $conn ? @mysql_get_server_info($conn) : @mysql_get_server_info();
		}
	}
	function my_mysql_query($query, $conn=FALSE)
	{
		if( myext() == 'mysqli' ) {
			return @mysqli_query($conn, $query);
		}
		else {
			return $conn ? @mysql_query($query, $conn) : @mysql_query($query);
		}
	}
	function my_mysql_num_rows($res) {
		if( myext() == 'mysqli' ) {
			return @mysqli_num_rows($res);
		}
		else {
			return @mysql_num_rows($res);
		}
	}
	function my_mysql_fetch_row($res) {
		if( myext() == 'mysqli' ) {
			return @mysqli_fetch_row($res);
		}
		else {
			return @mysql_fetch_row($res);
		}
	}
	function my_mysql_fetch_object($res) {
		if( myext() == 'mysqli' ) {
			return @mysqli_fetch_object($res);
		}
		else {
			return @mysql_fetch_object($res);
		}
	}
	function my_mysql_real_escape_string($str, $conn=FALSE) {
		if( myext() == 'mysqli' ) {
			return @mysqli_real_escape_string($conn, $str);
		}
		else {
			return $conn ? @mysql_real_escape_string($str, $conn) : @mysql_real_escape_string($str);
		}
	}
	
?>