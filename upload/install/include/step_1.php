<?php
	
	$PAGE_TITLE	= 'Installation';
		
	$installed	= FALSE;
	//if( isset($OLDC->INSTALLED, $OLDC->VERSION) && $OLDC->INSTALLED == 1 && $OLDC->VERSION>=VERSION ) {
	//	$installed	= TRUE;
	//}
	
	if( $installed ) {
		$_SESSION['INSTALL_STEP']	= 0;
		
		$html	.= '
							<div class="ttl">
								<div class="ttl2">
									<h3>Welcome to '.SITE_TITLE.' Installation Wizard</h3>
								</div>
							</div>
							'.errorbox('Oops', SITE_TITLE.' is already installed on your system. Please remove the "install/" folder.', FALSE, 'margin-top:5px;');
	}
	else {
		$_SESSION['INSTALL_STEP']	= 0;
		
		$error	= FALSE;
		if( isset($_POST['submit']) ) {
			$a	= isset($_POST['accept']) && $_POST['accept']=="1";
			if( ! $a ) {
				$error	= TRUE;
			}
			if( ! $error ) {
				$_SESSION['INSTALL_STEP']	= 1;
				header('Location: ?next');
				exit;
			}
		}
		
		$html	.= '
							<div class="ttl">
								<div class="ttl2">
									<h3>Welcome to '.SITE_TITLE.' Installation Wizard</h3>
								</div>
							</div>
							<div class="greygrad" style="margin-top: 5px;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-top:0px;">
										<p>This wizard will help you install '.SITE_TITLE.' '.VERSION.' on your webserver.</p>';
		if( $error ) {
			$html	.= errorbox('Sorry', 'You must accept the '.SITE_TITLE.' Terms of Use and License limitations to proceed with installation.');
		}
		$html	.= '
								   		<form method="post" action="">
								   			<div style="margin-top: 10px; margin-left:2px;">
									   			<label>
													<input type="checkbox" name="accept" value="1" style="margin:0px; padding:0px; border:0px solid; line-height:1; margin-right:8px;" />I accept the '.SITE_TITLE.' <a href="http://..." target="_blank">Terms&nbsp;of&nbsp;Use</a> and <a href="license.txt" target="_blank">License&nbsp;limitations</a>.
												</label>
											</div>
											<div style="margin-top: 10px;">
												<input type="submit" name="submit" value="Install" style="padding:4px; font-weight:bold;" />
								   			</div>
										</form>
									</div>
								</div>
							</div>';
	}
	
?>
