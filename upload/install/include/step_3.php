<?php
	
	$PAGE_TITLE	= 'Installation - Step 3';
	
	$s	= & $_SESSION['INSTALL_DATA'];
	
	$texts	= array (
		'is_apache'			=> 'Apache HTTP Server required',
		'apache_mod_rewrite'	=> 'Apache: mod_rewrite module required',
		'mysql_version_5'		=> 'MySQL: version 5.0 or higher required',
		'php_version_51'		=> 'PHP: version 5.1 or higher required',
		'php_curl_or_urlfopen'	=> 'PHP: cURL is needed, or else "allow_url_fopen" directive should be On',
		'php_short_open_tag_on'	=> 'PHP: "short_open_tag" directive should be On',
		'php_gd'			=> 'PHP: gd extension required',
	);
	
	$check	= array (
		'is_apache'			=> FALSE,
		'apache_mod_rewrite'	=> FALSE,
		'mysql_version_5'		=> FALSE,
		'php_version_51'		=> FALSE,
		'php_curl_or_urlfopen'	=> FALSE,
		'php_short_open_tag_on'	=> FALSE,
		'php_gd'			=> FALSE,
	);
	
	
	if( function_exists('apache_get_version') ) {
		$check['is_apache']		= TRUE;
	}
	elseif( isset($_SERVER['SERVER_SIGNATURE']) && preg_match('/Apache/i', $_SERVER['SERVER_SIGNATURE']) ) {
		$check['is_apache']		= TRUE;
	}
	elseif( isset($_SERVER['SERVER_SOFTWARE']) && preg_match('/Apache/i', $_SERVER['SERVER_SOFTWARE']) ) {
		$check['is_apache']		= TRUE;
	}
	
	$tmp	= floatval(substr(phpversion(), 0, 3));
	if( $tmp >= 5.1 ) {
		$check['php_version_51']	= TRUE;
	}
	
	$tmp	= my_mysql_connect($s['MYSQL_HOST'], $s['MYSQL_USER'], $s['MYSQL_PASS']);
	if( $tmp ) {
		$tmp	= my_mysql_get_server_info($tmp);
		$tmp	= str_replace('.','',substr($tmp, 0, 5));
		$tmp	= intval($tmp);
		if( $tmp >= 500 ) {
			$check['mysql_version_5']	= TRUE;
		}
	}
	
	if( function_exists('gd_info') ) {
		$check['php_gd']	= TRUE;
	}
	
	if( function_exists('curl_init') ) {
		$check['php_curl_or_urlfopen']	= TRUE;
	}
	else {
		$tmp	= intval(ini_get('allow_url_fopen'));
		if( $tmp == 1 ) {
			$check['php_curl_or_urlfopen']	= TRUE;
		}
	}
	
	$tmp	= intval(ini_get('short_open_tag'));
	if( $tmp == 1 ) {
		$check['php_short_open_tag_on']	= TRUE;
	}
	
	if( function_exists('apache_get_modules') ) {
		$tmp	= @apache_get_modules();
		if( is_array($tmp) ) {
			foreach($tmp as $mod) {
				if( $mod != 'mod_rewrite' ) {
					continue;
				}
				$check['apache_mod_rewrite']	= TRUE;
				break;
			}
		}
	}
	if( !$check['apache_mod_rewrite'] ) {
		ob_start();
		phpinfo(8);
		$tmp	= ob_get_contents();
		ob_get_clean();
		if( ! empty($tmp) ) {
			$pos	= strpos($tmp, 'Loaded Modules');
			if( FALSE !== $pos ) {
				$tmp	= substr($tmp, $pos);
				$pos	= strpos($tmp, '</table>');
				if( FALSE !== $pos ) {
					$tmp	= substr($tmp, 0, $pos);
					if( preg_match('/mod_rewrite/ius', $tmp) ) {
						$check['apache_mod_rewrite']	= TRUE;
					}
				}
			}
		}
	}
	if( !$check['apache_mod_rewrite'] ) {
		$url	= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$pos	= strpos($url, 'install');
		if( FALSE !== $pos ) {
			$url	= rtrim(substr($url, 0, $pos+8),'/').'/etc/modrewritetest/';
			$tmp1	= @file_get_contents($url.'test1.txt');
			$tmp2	= @file_get_contents($url.'test2.txt');
			if( $tmp1=='123' && $tmp2!='123' ) {
				$check['apache_mod_rewrite']	= TRUE;
			}
		}
	}
	
	$error	= FALSE;
	foreach($check as $tmp) {
		if( ! $tmp ) {
			$error	= TRUE;
			break;
		}
	}
	
	if( ! $error ) {
		$_SESSION['INSTALL_STEP']	= 3;
		header('Location: ?next&r='.rand(0,99999));
	}
	
	$html	.= '
							<div class="ttl">
								<div class="ttl2">
									<h3>System Compatibility Check</h3>
								</div>
							</div>';
	if( $error ) {
		$html	.= errorbox('Not Compatible', 'Please correct the highlighted settings and hit "Refresh".', FALSE, 'margin-top:5px;margin-bottom:0px;');
		$_SESSION['INSTALL_STEP']	= 2;
	}
	
	$html	.= '
							<div class="greygrad" style="margin-top: 5px;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-top:0px;">
										<table cellpadding="5" style="width:100%;">';
	foreach($check as $k=>$v) {
		$txt	= $texts[$k];
		$html	.= '
											<tr>
												<td colspan="2" style="font-size:0; line-height:0; height: 0; padding: 0; border-bottom: 1px solid #efefef;"></td>
											</tr>
											<tr>
												<td style="'.($v?'':'color:red;font-weight:bold;').'">'.$txt.'</td>
												<td style="text-align:right; font-weight:bold;">'.($v?'<span style="color:#008506;">OK</span>':'<span style="color:red;">FAIL</span>').'</td>
											</tr>';
	}
	$html	.= '
											<tr>
												<td colspan="2" style="font-size:0; line-height:0; height: 0; padding: 0; border-bottom: 1px solid #efefef;"></td>
											</tr>
										</table>';
	
	if( ! $error ) {
		$html	.= '
										<div style="margin-top:20px;">
											<a href="?next">&raquo; Continue</a>
										</div>';
	}
	
	$html	.= '
									</div>
								</div>
							</div>';
	
?>