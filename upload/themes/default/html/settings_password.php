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
								<a href="<?= $C->SITE_URL ?>settings/password"  class="onsidenav"><?= $this->lang('settings_menu_password') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/email"><?= $this->lang('settings_menu_email') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/system"><?= $this->lang('settings_menu_system') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/notifications"><?= $this->lang('settings_menu_notif') ?></a>
								<?php if( function_exists('curl_init') ) { ?>
								<a href="<?= $C->SITE_URL ?>settings/rssfeeds"><?= $this->lang('settings_menu_rssfeeds') ?></a>
								<?php } ?>
								<a href="<?= $C->SITE_URL ?>settings/delaccount"><?= $this->lang('settings_menu_delaccount') ?></a>
							</div>
						</div>
						<div id="settings_right">
							<?php if($D->submit && !$D->error) { ?>
							<?= okbox($this->lang('st_password_ok'), $this->lang('st_password_okmsg')) ?>
							<?php } elseif($D->error) { ?>
							<?= errorbox($this->lang('st_password_err'), $this->lang($D->errmsg)) ?>
							<?php } ?>
							<div class="ttl"><div class="ttl2"><h3><?= $this->lang('settings_password_ttl2') ?></h3></div></div>
							<form method="post" action="">
								<table id="setform" cellspacing="5">
									<tr>
										<td class="setparam"><?= $this->lang('st_password_current') ?></td>
										<td><input type="password" name="pass_old" value="<?= htmlspecialchars($D->pass_old) ?>" autocomplete="off" class="setinp" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_password_newpass') ?></td>
										<td><input type="password" name="pass_new" value="<?= htmlspecialchars($D->pass_new) ?>" autocomplete="off" class="setinp" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_password_newconfirm') ?></td>
										<td><input type="password" name="pass_new2" value="<?= htmlspecialchars($D->pass_new2) ?>" autocomplete="off" class="setinp" /></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="submit" value="<?= $this->lang('st_password_changebtn') ?>" style="padding:4px; font-weight:bold;"/></td>
									</tr>
								</table>
							</form>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>