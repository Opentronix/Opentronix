<?php
	
	$this->load_template('header.php');
	
?>
					<div id="page_browse_mg">
						<div id="page_browse_mg_left">
							<h2><?= $D->leftcol_title ?></h2>
							<div class="greygrad">
								<div class="greygrad2">
									<div class="greygrad3">
										<?= $D->leftcol_text ?>
									</div>
								</div>
							</div>
							<?php if( $this->user->is_logged ) { ?>
							<div class="greygrad">
								<div class="greygrad2">
									<div class="greygrad3">
										<?= $this->lang('os_members_left_invite_text', array('#SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE)) ?>
										<div class="klear"></div>
										<a href="<?= $C->SITE_URL ?>invite" class="ubluebtn"><b><?= $this->lang('os_members_left_invite_button') ?></b></a>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
						<div id="page_browse_mg_right">
							<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
								<a href="<?= $C->SITE_URL ?>members" class="<?= $D->tab=='all'?'onhtab':'' ?>"><b><?= $this->lang('members_tabs_all') ?> <small>(<?= $D->tabnums['all'] ?>)</small></b></a>
								<?php if( $this->user->is_logged ) { ?>
								<a href="<?= $C->SITE_URL ?>members/tab:ifollow" class="<?= $D->tab=='ifollow'?'onhtab':'' ?>"><b><?= $this->lang('members_tabs_ifollow') ?> <small>(<?= $D->tabnums['ifollow'] ?>)</small></b></a>
								<a href="<?= $C->SITE_URL ?>members/tab:followers" class="<?= $D->tab=='followers'?'onhtab':'' ?>"><b><?= $this->lang('members_tabs_followers') ?> <small>(<?= $D->tabnums['followers'] ?>)</small></b></a>
								<?php } ?>
								<a href="<?= $C->SITE_URL ?>members/tab:admins" class="<?= $D->tab=='admins'?'onhtab':'' ?>"><b><?= $this->lang('members_tabs_admins') ?> <small>(<?= $D->tabnums['admins'] ?>)</small></b></a>
							</div>
							<div id="grouplist">
								<?= $D->users_html ?>
							</div>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>