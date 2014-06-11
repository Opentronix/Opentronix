<?php

	$this->load_template('header.php');

?>
					<div id="settings">
						<div id="settings_left">
							<div class="ttl" style="margin-right:12px;"><div class="ttl2"><h3><?= $this->lang('settings_menu_title') ?></h3></div></div>
							<div class="sidenav">
								<a href="<?= $C->SITE_URL ?>settings/profile"><?= $this->lang('settings_menu_profile') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/contacts"><?= $this->lang('settings_menu_contacts') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/avatar"><?= $this->lang('settings_menu_avatar') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/password"><?= $this->lang('settings_menu_password') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/email"><?= $this->lang('settings_menu_email') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/system"><?= $this->lang('settings_menu_system') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/notifications"><?= $this->lang('settings_menu_notif') ?></a>
								<?php if( function_exists('curl_init') ) { ?>
								<a href="<?= $C->SITE_URL ?>settings/rssfeeds"><?= $this->lang('settings_menu_rssfeeds') ?></a>
								<?php } ?>
								<a href="<?= $C->SITE_URL ?>settings/delaccount" class="onsidenav"><?= $this->lang('settings_menu_delaccount') ?></a>
							</div>
						</div>
						<div id="settings_right">
							<div class="ttl">
								<div class="ttl2">
									<h3><?= $this->lang('settings_delaccount_ttl2') ?></h3>
								</div>
							</div>
							<?php if($D->error) { ?>
							<?= errorbox($this->lang('st_delaccount_error'), $this->lang($D->errmsg), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
							<?php } ?>
							<div class="greygrad" style="margin-top:5px;">
								<div class="greygrad2">
									<div class="greygrad3">
										<?= $this->lang('st_delaccount_description') ?>

										<form method="post" name="delaccount" onsubmit="return confirm('<?= htmlspecialchars($this->lang('st_delaccount_confirm')) ?>');" action="<?= $C->SITE_URL ?>settings/delaccount" autocomplete="off">
										<table id="setform" cellspacing="5" style="margin-top:5px;">
											<tr>
												<td class="setparam"><?= $this->lang('st_delaccount_password') ?></td>
												<td><input type="password" name="userpass" value="" autocomplete="off" class="setinp" /></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" value="<?= $this->lang('st_delaccount_submit') ?>" style="padding:4px; font-weight:bold;"/></td>
											</tr>
										</table>
										</form>
									</div>
								</div>
							</div>

						</div>
					</div>
<?php

	$this->load_template('footer.php');

?>