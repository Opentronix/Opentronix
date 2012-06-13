<?php
	
	$this->load_template('header.php');
	
	if( $D->nobody ) { ?>
						<div id="invcenter">
							<h2><?= $this->lang('os_grpinv_title', array('#GROUP#'=>$D->g->title)) ?></h2>
						</div>
						<div class="noposts">
							<div class="nopoststop"><div class="nopoststop2"></div></div>
							<div class="nopostsbody">
								<h3><?= $this->lang('grpinv_nobody_ttl') ?></h3>
								<p><?= $this->lang('grpinv_nobody_txt',array('#GROUP#'=>'<a href="'.$C->SITE_URL.$D->g->groupname.'">'.$D->g->title.'</a>')) ?></p>
							</div>
							<div class="nopostsbottom"><div class="nopostsbottom2"></div></div>
						</div>
	
	
<?php } else { ?>
						<link rel="stylesheet" href="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/css/user_selector.css" type="text/css" />
						<script type="text/javascript" src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/js/user_selector.js"></script>
						<div id="invcenter" style="padding:0px; margin-top:0;">
							<div class="inviterttl" style="margin-top:0;">
								<b><?= $this->lang('os_grpinv_title', array('#GROUP#'=>$D->g->title)) ?></b>
							</div>
							<div class="reguserlist" id="reguserlist">
							</div>
							<div class="submitreguserlist" id="submitreguserlist" style="display:none;">
								<form method="post" action="" name="invf" onsubmit="if(this.invite_users.value=='') { alert('<?= $this->lang('grpinv_submit_err') ?>'); return false; }">
									<input type="hidden" name="invite_users" value="" />
									<input type="submit" value="<?= $this->lang('grpinv_submit') ?>" />
									<?= $this->lang('grpinv_submit_or') ?>
									<a href="<?= $C->SITE_URL ?><?= $D->g->groupname ?>"><?= $this->lang('grpinv_submit_or_back') ?></a>
								</form>
							</div>
						</div>
						<div class="klear"></div>
						<script type="text/javascript">
							window.onload	= function() {
								var u = new UserSelector();
								u.form_input	= document.invf.invite_users;
								u.container		= document.getElementById("reguserlist");
								u.avatars_url	= "<?= $C->IMG_URL ?>avatars/thumbs1/";
								u.texts.searchinp	= "<?= $this->lang('userselector_srchinp') ?>";
								u.texts.taball	= "<?= $this->lang('userselector_tab_all') ?>";
								u.texts.tabsel	= "<?= $this->lang('userselector_tab_sel') ?>";
								u.data	= <?= json_encode($D->members) ?>;
								u.onload	= function() {
									d.getElementById("submitreguserlist").style.display	= "block";
								};
								u.init();
							};
						</script>
<?php }
	
	$this->load_template('footer.php');
	
?>