<?php
	
	$this->load_template('header.php');
	
	$use_yahoo	= isset($C->YAHOO_CONSUMER_KEY,$C->YAHOO_CONSUMER_SECRET) && !empty($C->YAHOO_CONSUMER_KEY) && !empty($C->YAHOO_CONSUMER_SECRET);
	
?>
		<div id="invcenter">
			<h2><?= $this->lang('invite_title') ?></h2>			
			<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
				<a href="<?= $C->SITE_URL ?>invite"><b><?= $this->lang('os_invite_tab_colleagues') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/parsemail" class="onhtab"><b><?= $this->lang('os_invite_tab_parsemail') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/uploadcsv"><b><?= $this->lang('os_invite_tab_uploadcsv') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/personalurl"><b><?= $this->lang('os_invite_tab_personalurl') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/sentinvites"><b><?= $this->lang('os_invite_tab_sentinvites') ?></b></a>
			</div>
			<div class="invinfo">
				<?= $this->lang('os_invite_txt_parsemail') ?>
			</div>
			<div id="emailinvites">
				<div id="emailservices">
					<a href="<?= $C->SITE_URL ?>invite/parsemail/tab:gmail" class="<?= $D->tab=='gmail'?'onfirstmailservice':'' ?>"><img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/mailservices_gmail.gif" alt="" /></a>
					<a href="<?= $C->SITE_URL ?>invite/parsemail/tab:yahoo" class="<?= $D->tab=='yahoo'?'onmailservice':'' ?>" style="<?= $use_yahoo?'':'display:none' ?>"><img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/mailservices_yahoo.gif" alt="" /></a>
					<a href="<?= $C->SITE_URL ?>invite/parsemail/tab:facebook" class="<?= $D->tab=='facebook'?'onmailservice':'' ?>"><img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/mailservices_facebook.gif" alt="" /></a>
					<a href="<?= $C->SITE_URL ?>invite/parsemail/tab:twitter" class="<?= $D->tab=='twitter'?($use_yahoo?'onlastmailservice':'onmailservice'):'' ?>"><img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/mailservices_twitter.gif" alt="" /></a>
				</div>
				<div id="emailinvitescontent">
					<div id="emailinvitescontent2">
						
						<?php if( $D->tab == 'gmail' ) { ?>
							
							<?php if( $D->error ) { ?>
							<h3 style="color:red;"><?= $this->lang($D->errmsg) ?></h3>
							<?php } else { ?>
							<h3><?= $this->lang('inv_prsml_gmail_ttl', array('#SITE_TITLE#'=>$C->SITE_TITLE))  ?></h3>
							<?php } ?>
							<div class="greygrad" style="width:440px; float:left;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-bottom:0px;">
										<form method="post" action="">
											<table id="setform" cellspacing="5" >
												<tr>
													<td class="setparam" style="text-align:right;"><?= $this->lang('inv_prsml_gmail_email') ?></td>
													<td><input type="text" name="email" value="<?= htmlspecialchars($D->email) ?>" autocomplete="off" class="setinp" style="width:200px; padding:3px;" /></td>
													<td><b><?= $this->lang('inv_prsml_gmail_emailat') ?></b></td>
												</tr>
												<tr>
													<td class="setparam" style="text-align:right;"><?= $this->lang('inv_prsml_gmail_pass') ?></td>
													<td><input type="password" name="pass" value="" autocomplete="off" class="setinp" style="width:200px; padding:3px;" /></td>
												</tr>
												<tr>
													<td></td>
													<td><input type="submit" value="<?= $this->lang('inv_prsml_gmail_button') ?>" style="padding:4px; font-weight:bold;"/></td>
												</tr>
											</table>
										</form>
									</div>
								</div>
							</div>
							<div id="securealert">
								<?= $this->lang('inv_prsml_gmail_secure') ?>
							</div>
							
						<?php } elseif( $D->tab == 'yahoo' ) { ?>
								
							<h3><?= $this->lang('inv_prsml_yahoo_title') ?></h3>
							<?php
							if(!$D->error) {
								?>
								<p style="margin:0;padding:0;margin-bottom:10px;">
									<?= $this->lang('inv_prsml_yahoo_descr', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?>
								</p>
								<a href='<?= $C->SITE_URL ?>invite/parsemail/tab:yahoo?start=1'><?= $this->lang('inv_prsml_yahoo_link') ?></a>
								<?php
							}
							else {
								?>
								<h3 style="color:red;"><?= $this->lang('inv_prsml_yahoo_err') ?></h3>
								<?php	
							}
							?>	
									
						<?php } elseif( $D->tab == 'facebook' ) { ?>
							
							<?php if( ! $D->use_fb_connect ) { ?>
								<h3><?= $this->lang('inv_prsml_fb_nofbconnect_title') ?></h3>
								<p style="margin:0;padding:0;margin-bottom:10px;"><?= $this->lang('inv_prsml_fb_nofbconnect_descr', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?></p>
								<a href="<?= $D->facebook_link ?>" target="_blank"><?= $this->lang('inv_prsml_fb_nofbconnect_link') ?></a>
							<?php } else { ?>
								<script type="text/javascript">
									function fbinvite(){
										var message	= '<?= $this->lang('inv_prsml_fb_fbconnect_post', array('#PROFILE#'=>'<a href=\\\''.$C->SITE_URL.$this->user->info->username.'\\\'>'.$this->user->info->username.'</a>', '#SITE_TITLE#'=>'<a href=\\\''.$C->SITE_URL.'\\\'>'.$C->SITE_TITLE.'</a>')) ?>';
										var contents = '<fb:request-form action="<?= $C->SITE_URL ?>invite/parsemail/tab:facebook"  method="POST" invite="true" type="<?= $C->SITE_TITLE ?>" content="'+message+' <fb:req-choice url=\'<?= $C->SITE_URL ?>\' label=\'Go to <?= $C->SITE_TITLE ?>\' /> "><fb:multi-friend-selector showborder="false" cols="4" rows="3" exclude_ids="" actiontext="<?= addslashes($this->lang('inv_prsml_fb_fbconnect_txt')) ?>"></fb:multi-friend-selector> </fb:request-form>';
										var dialog = new FB.UI.FBMLPopupDialog('<?= addslashes($this->lang('inv_prsml_fb_fbconnect_ttl', array('#SITE_TITLE#'=>$C->SITE_TITLE))) ?>', contents, false, true);
										dialog.setContentWidth(630);
										dialog.setContentHeight(540);
										dialog.set_placement(FB.UI.PopupPlacement.topCenter);
										dialog.show();
									}
								</script>
								<h3><?= $this->lang('inv_prsml_fb_nofbconnect_title') ?></h3>
								<p style="margin:0;padding:0;margin-bottom:10px;"><?= $this->lang('inv_prsml_fb_nofbconnect_descr', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?></p>
								<a href="javascript:;" onclick="fbinvite();"><?= $this->lang('inv_prsml_fb_nofbconnect_link') ?></a>
							<?php } ?>
							
						<?php } elseif( $D->tab == 'twitter' ) { ?>
							
							<h3><?= $this->lang('inv_prsml_twitter_title') ?></h3>
							<p style="margin:0;padding:0;margin-bottom:10px;"><?= $this->lang('inv_prsml_twitter_descr', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?></p>
							<a href="<?= $D->twitter_link ?>" target="_blank"><?= $this->lang('inv_prsml_twitter_link') ?></a>
							
						<?php } ?> 
						
					</div>
				</div>
			</div>
		</div>
<?php
	
	$this->load_template('footer.php');
	
?>