<?php
	 
	$PAGE_TITLE	= 'Installation - Step 5';
	
	$s	= & $_SESSION['INSTALL_DATA'];
	
	$convert_version	= FALSE;
	if( isset($OLDC->VERSION) ) {
		$convert_version	= $OLDC->VERSION; 
	}
	elseif( file_exists(INCPATH.'../../include/conf_main.php') ) {
		$convert_version	= 'unofficial';
	}
	
	$path	= INCPATH.'../../';
	
	$files	= array(
		'./themes/',
		'./i/attachments/',
		'./i/avatars/thumbs1/',
		'./i/avatars/thumbs2/',
		'./i/avatars/thumbs3/',
		'./i/avatars/',
		'./i/tmp/',
		'./system/cache/',
		'./.htaccess',
	);
	if( file_exists($path.'system/conf_main.php') ) {
		$files[]	= './system/conf_main.php';
	}
	else {
		$files[]	= './system/';
	}
	
	$recursive	= array();
	if( $convert_version == 'unofficial' ) {
		$recursive	= array(
			'./api/',
			'./css/',
			'./img/',
			'./include/',
			'./js/',
			'./error/',
		);
		foreach($recursive as $i=>$fl) {
			if( !file_exists($path.$fl) || !is_dir($path.$fl) ) {
				unset($recursive[$i]);
			}
		}
	}
	if( $convert_version < '1.4.0' ) {
		$recursive[]	= './system/templates/';
		$recursive[]	= './i/design/';
		$recursive[]	= './i/css/';
		$recursive[]	= './i/js/';
	}
	
	$perms	= array();
	$error	= FALSE;
	@clearstatcache();
	foreach($files as $i=>$fl) {
		$curr_error	= FALSE;
		if( !is_readable($path.$fl) || !is_writable($path.$fl) ) {
			@chmod($path.$fl, 0777);
			@clearstatcache();
			if( !is_readable($path.$fl) || !is_writable($path.$fl) ) {
				$error	= TRUE;
				$curr_error	= TRUE;
			}
		}
		if( ! $curr_error ) {
			unset($perms[$fl]);
			unset($files[$i]);
		}
	}
	
	$rperms	= array();
	foreach($recursive as $i=>$dr) {
		$curr_error	= FALSE;
		$rperms[$dr]	= directory_tree_is_writable($path.$dr);
		if( ! $rperms[$dr] ) {
			$error	= TRUE;
			$curr_error	= TRUE;
		}
		if( ! $curr_error ) {
			unset($rperms[$dr]);
			unset($recursive[$i]);
		}
	}
	
	if( ! $error ) {
		$_SESSION['INSTALL_STEP']	= 5;
		header('Location: ?next&r='.rand(0,99999));
	}
	
	$html	.= '
							<div class="ttl">
								<div class="ttl2">
									<h3>Files & Folders Permissions</h3>
								</div>
							</div>';
	if( $error ) {
		$html	.= errorbox('Please set the permissions', 'Set the permissions with your FTP client and hit "Refresh".', FALSE, 'margin-top:5px;margin-bottom:5px;');
		$_SESSION['INSTALL_STEP']	= 4;
	}
	$html	.= '
							<div class="greygrad" style="margin-top: 5px;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-top:0px;">
										<p>The following files and folders must have read and write permissions (CHMOD <b>0777</b> or <b>0766</b>)</p>
										<table cellpadding="5" style="width:100%;">';
	foreach($files as $fl) {
		$html	.= '
											<tr>
												<td colspan="2" style="font-size:0; line-height:0; height: 0; padding: 0; border-bottom: 1px solid #efefef;"></td>
											</tr>
											<tr>
												<td>'.$fl.'</td>
											</tr>';
	}
	foreach($recursive as $dr) {
		$md	= $rperms[$dr];
		$html	.= '
											<tr>
												<td colspan="2" style="font-size:0; line-height:0; height: 0; padding: 0; border-bottom: 1px solid #efefef;"></td>
											</tr>
											<tr>
												<td><span style="'.($md?'':'color:red;').'">'.$dr.'</span> <span style="color:#666;">&nbsp;including all files and folders inside</span></td>
												<td></td>
											</tr>';
	}
	$html	.= '
											<tr>
												<td colspan="2" style="font-size:0; line-height:0; height: 0; padding: 0; border-bottom: 1px solid #efefef;"></td>
											</tr>
										</table>
									</div>
								</div>
							</div>';
	
	if( ! $error ) {
		$html	.= '
							<div style="margin-top:20px;">
								<a href="?next">&raquo; Continue</a>
							</div>';
	}
	
?>