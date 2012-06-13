<?php
	
	$PAGE_TITLE	= 'Installation - Step 6';
	
	$s	= & $_SESSION['INSTALL_DATA'];
	
	if( isset($OLDC->DOMAIN, $OLDC->SITE_URL, $OLDC->SITE_TITLE) ) {
		$s['DOMAIN']	= trim($OLDC->DOMAIN);
		$s['SITE_URL']	= trim($OLDC->SITE_URL);
		$s['SITE_URL']	= rtrim($OLDC->SITE_URL,'/').'/';
		$s['SITE_TITLE']	= trim($OLDC->SITE_TITLE);
		if( !empty($s['DOMAIN']) || !empty($s['SITE_URL']) || !empty($s['SITE_TITLE']) ) {
			$_SESSION['INSTALL_STEP']	= 6;
			header('Location: ?next&r='.rand(0,99999));
		}
	}
	elseif( isset($OLDC->DOMAIN, $OLDC->SITE_TITLE) && !isset($OLDC->SITE_URL) ) {
		$s['DOMAIN']	= trim($OLDC->DOMAIN);
		$s['DOMAIN']	= preg_replace('/^(http|https)\:\/\//', '', $s['DOMAIN']);
		$s['DOMAIN']	= trim($s['DOMAIN'], '/');
		$s['DOMAIN']	= preg_replace('/\/.*$/', '', $s['DOMAIN']);
		$s['SITE_URL']	= '/';
		$tmp	= preg_replace('/^(http|https)\:\/\//', '', $OLDC->DOMAIN);
		$tmp	= trim($tmp, '/');
		$pos	= strpos($tmp, '/');
		if( FALSE !== $pos ) {
			$s['SITE_URL']	.= trim(substr($tmp, $pos), '/');
		}
		$s['SITE_URL']	= 'http://'.$s['DOMAIN'].'/'.trim($s['SITE_URL'],'/').'/';
		$s['SITE_TITLE']	= trim($OLDC->SITE_TITLE);
		if( !empty($s['DOMAIN']) || !empty($s['SITE_URL']) || !empty($s['SITE_TITLE']) ) {
			if( preg_match('/^(http|https)\:\/\/([a-z0-9-\.\_\/])+$/i', $s['SITE_URL']) ) {
				$_SESSION['INSTALL_STEP']	= 6;
				header('Location: ?next&r='.rand(0,99999));
			}
		}
	}
	
	if( ! isset($s['SITE_URL']) ) {
		$s['SITE_URL']	= 'http://'.trim($_SERVER['HTTP_HOST']);
		$uri	= $_SERVER['REQUEST_URI'];
		$pos	= strpos($uri, 'install');
		if( FALSE !== $pos ) {
			$uri	= substr($uri, 0, $pos);
			$uri	= trim($uri, '/');
			$s['SITE_URL']	.= '/'.$uri;
			$s['SITE_URL']	= trim($s['SITE_URL'], '/');
		}
	}
	$s['SITE_URL']	= rtrim($s['SITE_URL'], '/');
	if( ! isset($s['SITE_TITLE']) ) {
		$s['SITE_TITLE']	= '';
	}
	
	$submit	= FALSE;
	$error	= FALSE;
	if( isset($_POST['SITE_URL'], $_POST['SITE_TITLE']) ) {
		$submit	= TRUE;
		$_SESSION['INSTALL_STEP']	= 5;
		$s['SITE_URL']	= strtolower(trim($_POST['SITE_URL']));
		$s['SITE_URL']	= trim($s['SITE_URL']);
		$s['SITE_URL']	= rtrim($s['SITE_URL'], '/');
		$s['SITE_URL']	= trim($s['SITE_URL']);
		$s['SITE_TITLE']	= trim($_POST['SITE_TITLE']);
		if( empty($s['SITE_URL']) ) {
			$error	= TRUE;
			$errmsg	= 'Please enter Website Address';
		}
		if( ! $error ) {
			if( ! preg_match('/^(http|https)\:\/\/([a-z0-9-\.\_\/])+$/i', $s['SITE_URL']) ) {
				$error	= TRUE;
				$errmsg	= 'Please enter valid Website Address.';
			}
		}
		if( ! $error && empty($s['SITE_TITLE']) ) {
			$error	= TRUE;
			$errmsg	= 'Please enter Website Title';
		}
		if( ! $error ) {
			$s['SITE_URL']	= rtrim($s['SITE_URL'], '/');
			$s['SITE_URL']	= $s['SITE_URL'].'/';
			$s['DOMAIN']	= preg_replace('/^(http|https)\:\/\//', '', $s['SITE_URL']);
			$s['DOMAIN']	= trim($s['DOMAIN'], '/');
			$s['DOMAIN']	= preg_replace('/\/.*$/', '', $s['DOMAIN']);
			$_SESSION['INSTALL_STEP']	= 6;
			header('Location: ?next&r='.rand(0,99999));
		}
	}
	
	$html	.= '
							<div class="ttl">
								<div class="ttl2">
									<h3>Website Settings</h3>
								</div>
							</div>';
	if( $error ) {
		$html	.= errorbox('Error', $errmsg, TRUE, 'margin-top:5px; margin-bottom:5px;');
	}
	$html	.= '
							<div class="greygrad" style="margin-top: 5px;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-top:0px;">
										<form method="post" action="">
										<table id="setform" cellpadding="5">
											<tr>
												<td class="setparam" width="120">Website Address:</td>
												<td><input type="text" name="SITE_URL" value="'.htmlspecialchars($s['SITE_URL']).'" class="setinp" /></td>
											</tr>
											<tr>
												<td class="setparam">Website Title:</td>
												<td><input type="text" name="SITE_TITLE" value="'.htmlspecialchars($s['SITE_TITLE']).'" class="setinp" /></td>
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