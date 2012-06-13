<?php
	
	$PAGE_TITLE	= 'Installation - Step 7';
	
	$s	= & $_SESSION['INSTALL_DATA'];
	
	$conn	= my_mysql_connect($s['MYSQL_HOST'], $s['MYSQL_USER'], $s['MYSQL_PASS']);
	$dbs	= my_mysql_select_db($s['MYSQL_DBNAME'], $conn);
	if( !$conn || !$dbs ) {
		$_SESSION['INSTALL_STEP']	= 1;
		header('Location: ?next&r='.rand(0,99999));
	}
	
	$convert_version	= FALSE;
	$res	= my_mysql_query('SHOW TABLES FROM '.$s['MYSQL_DBNAME'], $conn);
	if( my_mysql_num_rows($res) ) {
		$tables	= array();
		while($tbl = my_mysql_fetch_row($res)) {
			$tables[]	= $tbl[0];
		}
		sort($tables);
	}
	if( isset($OLDC->VERSION) ) {
		$convert_version	= $OLDC->VERSION; 
	}
	elseif( file_exists(INCPATH.'../../include/conf_main.php') && in_array('users_watched', $tables) ) {
		$convert_version	= 'unofficial';
	}
	if( $convert_version ) {
		if( $convert_version == 'unofficial' ) {
			$res	= my_mysql_query('SELECT id, username, email FROM users WHERE id=1 LIMIT 1', $conn);
		}
		else {
			$res	= my_mysql_query('SELECT id, username, email FROM users WHERE is_network_admin=1 ORDER BY active=1 DESC, id ASC LIMIT 1', $conn);
		}
		if($adm = my_mysql_fetch_object($res)) {
			$s['ADMIN_ID']	= $adm->id;
			$s['ADMIN_USER']	= $adm->username;
			$s['ADMIN_EMAIL']	= $adm->email;
			$_SESSION['INSTALL_STEP']	= 7;
			header('Location: ?next&r='.rand(0,99999));
		}
	}
	
	if( ! isset($s['ADMIN_ID']) ) {
		$s['ADMIN_ID']	= FALSE;
	}
	if( ! isset($s['ADMIN_USER']) ) {
		$s['ADMIN_USER']	= '';
	}
	if( ! isset($s['ADMIN_PASS']) ) {
		$s['ADMIN_PASS']	= '';
	}
	if( ! isset($s['ADMIN_PASS2']) ) {
		$s['ADMIN_PASS2']	= '';
	}
	if( ! isset($s['ADMIN_EMAIL']) ) {
		$s['ADMIN_EMAIL']	= '';
	}
	
	$submit	= FALSE;
	$error	= FALSE;
	$errmsg	= '';
	if( isset($_POST['ADMIN_USER'], $_POST['ADMIN_PASS'], $_POST['ADMIN_PASS2'], $_POST['ADMIN_EMAIL']) ) {
		$submit	= TRUE;
		$_SESSION['INSTALL_STEP']	= 6;
		$s['ADMIN_ID']	= 0;
		$s['ADMIN_USER']	= trim($_POST['ADMIN_USER']);
		$s['ADMIN_PASS']	= trim($_POST['ADMIN_PASS']);
		$s['ADMIN_PASS2']	= trim($_POST['ADMIN_PASS2']);
		$s['ADMIN_EMAIL']	= trim($_POST['ADMIN_EMAIL']);
		if( empty($s['ADMIN_USER']) ) {
			$error	= TRUE;
			$errmsg	= 'Please enter Username.';
		}
		if( !$error && ! is_valid_username($s['ADMIN_USER'], TRUE) ) {
			$error	= TRUE;
			$errmsg	= 'Please enter valid Username.';
		}
		if( !$error ) {
			$res	= my_mysql_query('SELECT id FROM users WHERE username="'.addslashes($s['ADMIN_USER']).'" LIMIT 1', $conn);
			if( $res ) {
				if( my_mysql_num_rows($res) > 0 ) {
					$error	= TRUE;
					$errmsg	= 'This username is already registered.';
				}
			}
		}
		if( !$error && strlen($s['ADMIN_PASS'])<5 ) {
			$error	= TRUE;
			$errmsg	= 'Password must be at least 5 characters long.';
		}
		if( !$error && $s['ADMIN_PASS']!=$s['ADMIN_PASS2'] ) {
			$error	= TRUE;
			$errmsg	= 'Passwords don`t match.';
		}
		if( !$error && !is_valid_email($s['ADMIN_EMAIL']) ) {
			$error	= TRUE;
			$errmsg	= 'Invalid E-mail address.';
		}
		if( !$error ) {
			$res	= my_mysql_query('SELECT id FROM users WHERE email="'.addslashes($s['ADMIN_EMAIL']).'" LIMIT 1', $conn);
			if( $res ) {
				if( my_mysql_num_rows($res) > 0 ) {
					$error	= TRUE;
					$errmsg	= 'This e-mail is already registered.';
				}
			}
		}
		if( ! $error ) {
			unset($s['ADMIN_PASS2']);
			$_SESSION['INSTALL_STEP']	= 7;
			header('Location: ?next&r='.rand(0,99999));
		}
	}
	
	$html	.= '
							<div class="ttl">
								<div class="ttl2">
									<h3>Administrative Account</h3>
								</div>
							</div>
							<div class="greygrad" style="margin-top: 5px;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-top:0px;">
										<p>Create an Administrative account for accessing the Administration center.</p>';
	if( $error ) {
		$html	.= errorbox('Error', $errmsg);
	}
	$html	.= '
										<form method="post" action="">
										<table id="setform" cellpadding="5">
											<tr>
												<td class="setparam" width="120">Admin Username:</td>
												<td style="padding-bottom:2px;"><input type="text" autocomplete="off" class="setinp" name="ADMIN_USER" value="'.htmlspecialchars($s['ADMIN_USER']).'" /></td>
											</tr>
											<tr>
												<td class="setparam">Admin Password:</td>
												<td style="padding-bottom:2px;"><input type="password" autocomplete="off" class="setinp" name="ADMIN_PASS" value="'.htmlspecialchars($s['ADMIN_PASS']).'" /></td>
											</tr>
											<tr>
												<td class="setparam">Password Again:</td>
												<td style="padding-bottom:2px;"><input type="password" autocomplete="off" class="setinp" name="ADMIN_PASS2" value="'.htmlspecialchars($s['ADMIN_PASS2']).'" /></td>
											</tr>
											<tr>
												<td class="setparam" width="120">E-Mail Address:</td>
												<td style="padding-bottom:2px;"><input type="text" class="setinp" name="ADMIN_EMAIL" value="'.htmlspecialchars($s['ADMIN_EMAIL']).'" /></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" name="submit" value="Continue" style="padding:4px; font-weight:bold;" /></td>
											</tr>
										</table>
										</form>
									</div>
								</div>
							</div>';
	
?>