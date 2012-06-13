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
								<a href="<?= $C->SITE_URL ?>settings/email"  class="onsidenav"><?= $this->lang('settings_menu_email') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/system"><?= $this->lang('settings_menu_system') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/notifications"><?= $this->lang('settings_menu_notif') ?></a>
								<?php if( function_exists('curl_init') ) { ?>
								<a href="<?= $C->SITE_URL ?>settings/rssfeeds"><?= $this->lang('settings_menu_rssfeeds') ?></a>
								<?php } ?>
								<a href="<?= $C->SITE_URL ?>settings/delaccount"><?= $this->lang('settings_menu_delaccount') ?></a>
							</div>
						</div>
						<div id="settings_right">
							<div class="ttl"><div class="ttl2"><h3><?= $this->lang('settings_email_ttl2') ?></h3></div></div>
							
							<?php 
							if($D->submit && !$D->error && !$D->notif) 
							{ 
							?>
								<?= okbox($this->lang('st_email_okttl'), $this->lang('st_email_oktxt'), FALSE, 'margin-top:5px;') ?>
							<?php 
							}elseif($D->submit &&  $D->error && !$D->notif) 
							{ 
							?>
								<?= errorbox($this->lang('st_email_current_errttl'), $D->errmsg, TRUE, 'margin-top:5px;') ?>
							<?php 
							}elseif($D->submit &&  $D->error && $D->notif)
							{
							?>
								<?= msgbox($this->lang('st_email_notif_sendttl'), $this->lang('st_email_notif_send', array('#EMAIL#'=>$D->new_email)), FALSE, 'margin-top:5px;') ?>
							<?php
							}
							?>
							
							<?php
							if(!$D->notif && !$D->new_email_active)
							{
							?>
							<div style='margin-top: 10px;'>
								<?= $this->lang($C->USERS_EMAIL_CONFIRMATION?'st_email_conf_mes':'st_email_current', array('#CURRENT_EMAIL#' => $this->user->info->email )) ?>
							</div>
							<form method="post" action="<?= $C->SITE_URL ?>settings/email">
								<table id="setform" cellspacing="5">
									<tr>
										<td class="setparam"><?= $this->lang('st_email_new') ?></td>
										<td><input type="text" name="new_email" value="<?= htmlspecialchars($D->new_email) ?>" class="setinp" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_email_new_confirm') ?></td>
										<td><input type="text" name="new_email_confirm" value="<?= htmlspecialchars($D->new_email_confirm) ?>" autocomplete="off" class="setinp" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_email_pass') ?></td>
										<td><input type="password" name="user_pass" value="<?= htmlspecialchars($D->user_pass) ?>" autocomplete="off" class="setinp" /></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="submit" value="<?= $this->lang('st_email_cng_btn') ?>" style="padding:4px; font-weight:bold;"/></td>
									</tr>
								</table>
							</form>
							<?php
							}
							?>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>