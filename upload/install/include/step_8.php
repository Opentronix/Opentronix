<?php

	ini_set('memory_limit', -1);

	$PAGE_TITLE	= 'Installation - Step 8';

	$s	= & $_SESSION['INSTALL_DATA'];

	$error	= FALSE;

	if( isset($s['INSTALLED']) && $s['INSTALLED'] ) {
		$configfile	= INCPATH.'../../system/conf_main.php';
		$is_ok	= FALSE;
		if( file_exists($configfile) ) {
			$C	= new stdClass;
			$C->INCPATH	= realpath(INCPATH.'../../system/').'/';
			include($configfile);
			if( $C->INSTALLED == TRUE && $C->VERSION >= VERSION ) {
				$is_ok	= TRUE;
			}
		}
		if( ! $is_ok ) {
			unset($s['INSTALLED']);
			$_SESSION['INSTALL_STEP']	= 0;
			header('Location: ?reset');
			exit;
		}
	}

	$error	= FALSE;
	$errmsg	= '0';

	if( !isset($s['INSTALLED']) || !$s['INSTALLED'] )
	{
		$s['LANGUAGE']	= 'en';
		if( isset($OLDC->LANGUAGE) ) {
			$s['LANGUAGE']	= $OLDC->LANGUAGE;
			if( empty($s['LANGUAGE']) || !file_exists(INCPATH.'../../system/languages/'.$s['LANGUAGE']) ) {
				$s['LANGUAGE']	= 'en';
			}
		}
		if( ! file_exists( INCPATH.'../../i/attachments/1/' ) ) {
			@mkdir( INCPATH.'../../i/attachments/1/' );
		}
		@chmod( INCPATH.'../../i/attachments/1/', 0777 );

		$s['SITE_URL']	= rtrim($s['SITE_URL'],'/').'/';

		if( ! $error ) {
			$rwbase	= '/';
			$tmp	= preg_replace('/^http(s)?\:\/\//', '', $s['SITE_URL']);
			$tmp	= trim($tmp, '/');
			$pos	= strpos($tmp, '/');
			if( FALSE !== $pos ) {
				$tmp	= substr($tmp, $pos);
				$tmp	= '/'.trim($tmp,'/').'/';
				$rwbase	= $tmp;
			}
			$htaccess	= '<IfModule mod_rewrite.c>'."\n";
			$htaccess	.= '	RewriteEngine On'."\n";
			$htaccess	.= '	RewriteBase '.$rwbase."\n";
			$htaccess	.= '	RewriteCond %{REQUEST_FILENAME} !-f'."\n";
			$htaccess	.= '	RewriteCond %{REQUEST_FILENAME} !-d'."\n";
			$htaccess	.= '	RewriteRule ^(.*)$ index.php?%{QUERY_STRING} [NE,L]'."\n";
			$htaccess	.= '</IfModule>'."\n";
			$filename	= INCPATH.'../../.htaccess';
			$res	= file_put_contents($filename, $htaccess);
			if( ! $res ) {
				$error	= TRUE;
				$errmsg	= 'QgZHVt';
			}
			@chmod($filename, 0777);
		}

		$convert_version	= FALSE;

		if( ! $error ) {
			$conn	= my_mysql_connect($s['MYSQL_HOST'], $s['MYSQL_USER'], $s['MYSQL_PASS']);
			$dbs	= my_mysql_select_db($s['MYSQL_DBNAME'], $conn);
			if( !$conn || !$dbs ) {
				$_SESSION['INSTALL_STEP']	= 1;
				header('Location: ?next&r='.rand(0,99999));
			}
			my_mysql_query('SET NAMES utf8', $conn);
			$tables	= array();
			$res	= my_mysql_query('SHOW TABLES FROM '.$s['MYSQL_DBNAME'], $conn);
			if( my_mysql_num_rows($res) ) {
				while($tbl = my_mysql_fetch_row($res)) {
					$tables[]	= $tbl[0];
				}
			}
			$convert_version	= FALSE;
			if( isset($OLDC->VERSION) ) {
				$convert_version	= $OLDC->VERSION;
			}
			elseif( file_exists(INCPATH.'../../include/conf_main.php') && in_array('users_watched', $tables) ) {
				$convert_version	= 'unofficial';
			}
			require_once(INCPATH.'func_database.php');
			$res	= create_database($convert_version);
			if( ! $res ) {
				$error	= TRUE;
				$errmsg	= 'V4dCBldm';
			}
		}

		if( ! $error ) {
			if( $convert_version == 'unofficial' ) {
				$resize	= array();
				$path	= INCPATH.'../../i/attachments/1/';
				$dir	= opendir($path);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( FALSE === strpos($file, '_thumb.') ) { continue; }
					list($w, $h) = getimagesize($path.$file);
					if( $w!=60 || $h!=60 ) {
						$resize[]	= $path.$file;
					}
				}
				closedir($dir);
				include_once(INCPATH.'../../system/helpers/func_images.php');
				$C->IMAGE_MANIPULATION	= 'gd';
				foreach($resize as $file) {
					copy_attachment_videoimg($file, $file, 60);
				}
			}
		}
		if( ! $error ) {
			if( $convert_version == 'unofficial' ) {
				$path1	= INCPATH.'../../img/avatars/';
				$path2	= INCPATH.'../../i/avatars/';
				$dir	= opendir($path1);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( ! is_file($path1.$file) ) { continue; }
					@copy($path1.$file, $path2.$file);
					@chmod($path2.$file, 0777);
					copy_attachment_videoimg($path1.$file, $path2.$file, 200);
				}
				closedir($dir);
				$path1	= INCPATH.'../../img/avatars/thumbs/';
				$path2	= INCPATH.'../../i/avatars/thumbs1/';
				$dir	= opendir($path1);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( ! is_file($path1.$file) ) { continue; }
					@copy($path1.$file, $path2.$file);
					@chmod($path2.$file, 0777);
				}
				closedir($dir);
				$path1	= INCPATH.'../../img/avatars/thumbs2/';
				$path2	= INCPATH.'../../i/avatars/thumbs2/';
				$dir	= opendir($path1);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( ! is_file($path1.$file) ) { continue; }
					@copy($path1.$file, $path2.$file);
					@chmod($path2.$file, 0777);
				}
				closedir($dir);
				include_once(INCPATH.'../../system/helpers/func_images.php');
				$C->IMAGE_MANIPULATION	= 'gd';
				$path1	= INCPATH.'../../img/avatars/thumbs2/';
				$path2	= INCPATH.'../../i/avatars/thumbs3/';
				$dir	= opendir($path1);
				while($file = readdir($dir)) {
					if( $file=='.' || $file=='..' ) { continue; }
					if( ! is_file($path1.$file) ) { continue; }
					copy_attachment_videoimg($path1.$file, $path2.$file, 30);
				}
				closedir($dir);
			}
		}
		if( ! $error ) {
			$config	= @file_get_contents(INCPATH.'conf_main_empty.php');
			if( ! $config ) {
				$error	= TRUE;
				$errmsg	= 'zbyB0a';
			}
			if( ! $error ) {
				$rndkey	= substr(md5(time().rand()),0,5);
				$config	= config_replace_variable( $config,	'$C->DOMAIN',		$s['DOMAIN'] );
				$config	= config_replace_variable( $config,	'$C->SITE_URL',		$s['SITE_URL'] );
				$config	= config_replace_variable( $config,	'$C->RNDKEY',	$rndkey );
				$config	= config_replace_variable( $config,	'$C->DB_HOST',	$s['MYSQL_HOST'] );
				$config	= config_replace_variable( $config,	'$C->DB_USER',	$s['MYSQL_USER'] );
				$config	= config_replace_variable( $config,	'$C->DB_PASS',	$s['MYSQL_PASS'] );
				$config	= config_replace_variable( $config,	'$C->DB_NAME',	$s['MYSQL_DBNAME'] );
				$config	= config_replace_variable( $config,	'$C->DB_MYEXT',	$s['MYSQL_MYEXT'] );
				$config	= config_replace_variable( $config,	'$C->CACHE_MECHANISM',	$s['CACHE_MECHANISM'] );
				$config	= config_replace_variable( $config,	'$C->CACHE_EXPIRE',	$s['CACHE_EXPIRE'], FALSE );
				$config	= config_replace_variable( $config,	'$C->CACHE_MEMCACHE_HOST',	$s['CACHE_MEMCACHE_HOST'] );
				$config	= config_replace_variable( $config,	'$C->CACHE_MEMCACHE_PORT',	$s['CACHE_MEMCACHE_PORT'] );
				$config	= config_replace_variable( $config,	'$C->CACHE_KEYS_PREFIX',	$rndkey );
				$config	= config_replace_variable( $config,	'$C->CACHE_FILESYSTEM_PATH',	'$C->INCPATH.\'cache/\'', FALSE );
				$config	= config_replace_variable( $config,	'$C->IMAGE_MANIPULATION',	isset($OLDC->IMAGE_MANIPULATION)&&$OLDC->IMAGE_MANIPULATION=='imagemagick_cli' ? 'imagemagick_cli' : 'gd' );
				$config	= config_replace_variable( $config,	'$C->IM_CONVERT',			isset($OLDC->IM_CONVERT) ? $OLDC->IM_CONVERT : 'convert'	 );
				$config	= config_replace_variable( $config,	'$C->USERS_ARE_SUBDOMAINS',	isset($OLDC->USERS_ARE_SUBDOMAINS)&&$OLDC->USERS_ARE_SUBDOMAINS ? 'TRUE' : 'FALSE', FALSE );
				$config	= config_replace_variable( $config,	'$C->LANGUAGE',	$s['LANGUAGE'] );
				$config	= config_replace_variable( $config,	'$C->USERS_ARE_SUBDOMAINS',		isset($OLDC->USERS_ARE_SUBDOMAINS)&&$OLDC->USERS_ARE_SUBDOMAINS ? 'TRUE' : 'FALSE', FALSE );
				$config	= config_replace_variable( $config,	'$C->RPC_PINGS_ON',		isset($OLDC->RPC_PINGS_ON)&&!$OLDC->RPC_PINGS_ON ? 'FALSE' : 'TRUE', FALSE );
				$config	= config_replace_variable( $config,	'$C->RPC_PINGS_SERVERS',	isset($OLDC->RPC_PINGS_SERVERS) ? var_export($OLDC->RPC_PINGS_SERVERS,TRUE) : 'array(\'http://rpc.pingomatic.com\')', FALSE );
				$config	= config_replace_variable( $config,	'$C->FACEBOOK_API_KEY',			isset($OLDC->FACEBOOK_API_KEY) ? $OLDC->FACEBOOK_API_KEY : ''	 );
				$config	= config_replace_variable( $config,	'$C->TWITTER_CONSUMER_KEY',		isset($OLDC->TWITTER_CONSUMER_KEY) ? $OLDC->TWITTER_CONSUMER_KEY : ''	 );
				$config	= config_replace_variable( $config,	'$C->TWITTER_CONSUMER_SECRET',	isset($OLDC->TWITTER_CONSUMER_SECRET) ? $OLDC->TWITTER_CONSUMER_SECRET : ''	 );
				$config	= config_replace_variable( $config,	'$C->BITLY_LOGIN',			isset($OLDC->BITLY_LOGIN) ? $OLDC->BITLY_LOGIN : 'blogtronixmicro'	 );
				$config	= config_replace_variable( $config,	'$C->BITLY_API_KEY',			isset($OLDC->BITLY_API_KEY) ? $OLDC->BITLY_API_KEY : 'R_ffd756f66a4f5082e37989f1bc3301a6'	 );
				$config	= config_replace_variable( $config,	'$C->YAHOO_CONSUMER_KEY',		isset($OLDC->YAHOO_CONSUMER_KEY) ? $OLDC->YAHOO_CONSUMER_KEY : ''	 );
				$config	= config_replace_variable( $config,	'$C->YAHOO_CONSUMER_SECRET',		isset($OLDC->YAHOO_CONSUMER_SECRET) ? $OLDC->YAHOO_CONSUMER_SECRET : ''	 );
				$config	= config_replace_variable( $config,	'$C->CRONJOB_IS_INSTALLED',			'FALSE', FALSE );
				$config	= config_replace_variable( $config,	'$C->INSTALLED',			'TRUE', FALSE );
				$config	= config_replace_variable( $config,	'$C->VERSION',			VERSION );
				$config	= config_replace_variable( $config,	'$C->DEBUG_USERS',		isset($OLDC->DEBUG_USERS) ? var_export($OLDC->DEBUG_USERS,TRUE) : 'array()', FALSE );
				$filename	= INCPATH.'../../system/conf_main.php';
				$res	= file_put_contents($filename, $config);
				if( ! $res ) {
					$error	= TRUE;
					$errmsg	= 'lZW4gdGh';
				}
			}
		}
		if( ! $error ) {
			if( $convert_version && $convert_version < '1.4.0' && $convert_version != 'unofficial' ) {
				$tplnam	= 'opentronix-'.date('YmdHi');
				$tpldir	= '../../themes/'.$tplnam;
				@mkdir($tpldir); 			@chmod($tpldir, 0777);
				@mkdir($tpldir.'/imgs');	@chmod($tpldir.'/imgs', 0777);
				@mkdir($tpldir.'/js');		@chmod($tpldir.'/js', 0777);
				@mkdir($tpldir.'/css');		@chmod($tpldir.'/css', 0777);
				@mkdir($tpldir.'/html');	@chmod($tpldir.'/html', 0777);
				$move_files	= array(
					'../../system/templates/'	=> $tpldir.'/html/',
					'../../i/design/'			=> $tpldir.'/imgs/',
					'../../i/css/'			=> $tpldir.'/css/',
					'../../i/js/'			=> $tpldir.'/js/',
				);
				foreach($move_files as $k=>$v) {
					$fp	= opendir($k);
					while($f = readdir($fp)) {
						if( $f=='.' || $f=='..' ) { continue; }
						$r	= @rename( $k.$f, $v.$f );
						if( preg_match('/\.php$/', $f) ) {
							@chmod($v.$f, 0755);
						}
						if( ! $r ) {
							$error	= TRUE;
							$errmsg	= 'fl7d4xPz';
							break 2;
						}
					}
					closedir($fp);
					@rmdir($k);
				}
				if( ! $error ) {
					$tmp	= '<'.'?'.'php'."\n\t\n";
					$tmp	.= "\t".'$current_theme = (object) array'."\n\t(\n";
					$tmp	.= "\t\t'name'\t\t=> 'Opentronix ".date('Y-m-d H:i')."',\n";
					$tmp	.= "\t\t'version'\t\t=> '',\n";
					$tmp	.= "\t\t'image'\t\t=> '',\n";
					$tmp	.= "\t\t'description'\t=> 'This theme was automatically generated on the Opentronix upgrade process (for keeping old changes). It will now support some of the new Opentronix features. If you don\'t need this theme, please delete it from the Themes folder.',\n";
					$tmp	.= "\t\t\n";
					$tmp	.= "\t\t'author_name'\t=> 'Opentronix',\n";
					$tmp	.= "\t\t'author_url'\t=> 'http://...',\n";
					$tmp	.= "\t\t'author_email'\t=> '...@...org',\n";
					$tmp	.= "\t\t\n";
					$tmp	.= "\t\t'logo_height'\t=> '34',\n";
					$tmp	.= "\t\t'logo_bgcolor'\t=> '#0055a4',\n";
					$tmp	.= "\t".');'."\n\t\n";
					$tmp	.= '?'.'>'."\n";
					if( ! @file_put_contents($tpldir.'/theme.php', $tmp) ) {
						$error	= TRUE;
						$errmsg	= '8svq58r4';
					}
				}
				if( ! $error ) {
					$repl_infiles	= array( 'header.php', 'header_flybox.php', 'mobile/header.php', 'mobile_iphone/header.php', );
					$repl_strings	= array (
						'$C->IMG_URL ?'.'>css/'		=> '$C->SITE_URL ?'.'>themes/'.$tplnam.'/css/',
						'$C->IMG_URL ?'.'>js/'		=> '$C->SITE_URL ?'.'>themes/'.$tplnam.'/js/',
						'$C->IMG_URL ?'.'>design/'	=> '$C->SITE_URL ?'.'>themes/'.$tplnam.'/imgs/',
						'$C->SITE_URL ?'.'>i/css/'		=> '$C->SITE_URL ?'.'>themes/'.$tplnam.'/css/',
						'$C->SITE_URL ?'.'>i/js/'		=> '$C->SITE_URL ?'.'>themes/'.$tplnam.'/js/',
						'$C->SITE_URL ?'.'>i/design/'		=> '$C->SITE_URL ?'.'>themes/'.$tplnam.'/imgs/',
						'$C->IMG_URL.\'css/'		=> '$C->SITE_URL.\'themes/'.$tplnam.'/css/',
						'$C->IMG_URL.\'js/'		=> '$C->SITE_URL.\'themes/'.$tplnam.'/js/',
						'$C->IMG_URL.\'design/'		=> '$C->SITE_URL.\'themes/'.$tplnam.'/imgs/',
						'$C->SITE_URL.\'i/css/'			=> '$C->SITE_URL.\'themes/'.$tplnam.'/css/',
						'$C->SITE_URL.\'i/js/'			=> '$C->SITE_URL.\'themes/'.$tplnam.'/js/',
						'$C->SITE_URL.\'i/design/'		=> '$C->SITE_URL.\'themes/'.$tplnam.'/imgs/',
					);
					foreach($repl_infiles as $f) {
						$f	= $tpldir.'/html/'.$f;
						if( ! file_exists($f) ) { continue; }
						$c	= @file_get_contents($f);
						if( ! $c ) { continue; }
						$c	= str_replace(array_keys($repl_strings), array_values($repl_strings), $c, $tmp);
						if( ! $tmp ) { continue; }
						@chmod($f, 0777);
						$r	= @file_put_contents( $f, $c );
						@chmod($f, 0755);
					}
					$repl_infiles	= array( 'mobile/header.php', 'mobile_iphone/header.php', );
					$repl_strings	= array (
						'$C->SITE_URL ?'.'>themes/'.$tplnam.'/css/'	=> '$C->OUTSIDE_SITE_URL ?'.'>themes/'.$tplnam.'/css/',
						'$C->SITE_URL ?'.'>themes/'.$tplnam.'/js/'	=> '$C->OUTSIDE_SITE_URL ?'.'>themes/'.$tplnam.'/js/',
						'$C->SITE_URL ?'.'>themes/'.$tplnam.'/imgs/'	=> '$C->OUTSIDE_SITE_URL ?'.'>themes/'.$tplnam.'/imgs/',
						'$C->SITE_URL.\'themes/'.$tplnam.'/css/'		=> '$C->OUTSIDE_SITE_URL.\'themes/'.$tplnam.'/css/',
						'$C->SITE_URL.\'themes/'.$tplnam.'/js/'		=> '$C->OUTSIDE_SITE_URL.\'themes/'.$tplnam.'/js/',
						'$C->SITE_URL.\'themes/'.$tplnam.'/imgs/'		=> '$C->OUTSIDE_SITE_URL.\'themes/'.$tplnam.'/imgs/',
					);
					foreach($repl_infiles as $f) {
						$f	= $tpldir.'/html/'.$f;
						$c	= @file_get_contents($f);
						if( ! $c ) { continue; }
						$c	= str_replace(array_keys($repl_strings), array_values($repl_strings), $c, $tmp);
						if( ! $tmp ) { continue; }
						@chmod($f, 0777);
						$r	= @file_put_contents( $f, $c );
						@chmod($f, 0755);
					}
					$copy_new2old	= array('admin_themes.php', 'admin_leftmenu.php');
					foreach($copy_new2old as $f) {
						@copy( '../../themes/default/html/'.$f, $tpldir.'/html/'.$f );
					}
				}
				if( ! $error ) {
					$fp	= opendir($tpldir.'/css/');
					while($f = readdir($fp)) {
						if( $f=='.' || $f=='..' ) { continue; }
						$f	= $tpldir.'/css/'.$f;
						$c	= @file_get_contents($f);
						if( ! $c ) { continue; }
						$c	= str_replace('../design/', '../imgs/', $c);
						@chmod($f, 0777);
						$r	= @file_put_contents( $f, $c );
						@chmod($f, 0755);
					}
					closedir($fp);
					my_mysql_query("REPLACE INTO `settings` SET `word`='THEME', `value`='default' ", $conn);
					if( ! file_exists('../../themes/include_in_footer.php') ) {
						$tmp	= "<"."?php\n\n\t\n\t";
						$tmp	.= "/"."**\n\t\t\n\t\t";
						$tmp	.= "This file in included to the Footer of all themes, right before the </body> tag\n\t\t\n\t\t";
						$tmp	.= "Here you can place web counters.\n\t\t\n\t";
						$tmp	.= "*/\n\t\n";
						$tmp	.= "?".">\n";
						@file_put_contents('../../themes/include_in_footer.php', $tmp);
					}
				}
				if( ! $error ) {
					directory_tree_delete(INCPATH.'../../system/templates/');
					directory_tree_delete(INCPATH.'../../i/design/');
					directory_tree_delete(INCPATH.'../../i/css/');
					directory_tree_delete(INCPATH.'../../i/js/');
				}
			}
		}
		if( ! $error ) {
			if( $convert_version == 'unofficial' ) {
				directory_tree_delete(INCPATH.'../../js/');
				directory_tree_delete(INCPATH.'../../css/');
				directory_tree_delete(INCPATH.'../../img/');
				directory_tree_delete(INCPATH.'../../api/');
				directory_tree_delete(INCPATH.'../../include/');
			}
		}
		if( ! $error ) {
			@chmod( INCPATH.'../../.htaccess', 0664 );
			@chmod( INCPATH.'../../system/conf_main.php', 0755 );
			@chmod( INCPATH.'../../system/cache', 0777 );
			@chmod( INCPATH.'../../i/avatars', 0777 );
			@chmod( INCPATH.'../../i/avatars/thumbs1', 0777 );
			@chmod( INCPATH.'../../i/avatars/thumbs2', 0777 );
			@chmod( INCPATH.'../../i/avatars/thumbs3', 0777 );
			@chmod( INCPATH.'../../i/attachments', 0777 );
			@chmod( INCPATH.'../../i/tmp', 0777 );
			@chmod( INCPATH.'../../system', 0755 );
			$url	= $s['SITE_URL'];
			$url	= rtrim($url,'/').'/installed:ok';
			session_unset();
			session_destroy();
			header('Location: '.$url);
		}
	}

	$html	.= '

							<div class="ttl">
								<div class="ttl2">
									<h3>Finishing Installation</h3>
								</div>
							</div>';
	$html	.= errorbox('Installation Failed!', 'Please <a href="?reset" style="font-size:inherit;">try again</a> or contact our team for help. Error code: '.$errmsg.'.', FALSE, 'margin-top:5px;');

?>
