<?php
	
	$this->load_template('header.php');
	
?>
					<div id="settings">
						<div id="settings_left">				
							<div class="ttl" style="margin-right:12px;"><div class="ttl2"><h3><?= $this->lang('settings_menu_title') ?></h3></div></div>
							<div class="sidenav">
								<a href="<?= $C->SITE_URL ?>settings/profile" class="onsidenav"><?= $this->lang('settings_menu_profile') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/contacts"><?= $this->lang('settings_menu_contacts') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/avatar"><?= $this->lang('settings_menu_avatar') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/password"><?= $this->lang('settings_menu_password') ?></a>
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
							<?php if($D->submit) { ?>
							<?= okbox($this->lang('st_profile_ok'), $this->lang('st_profile_okmsg')) ?>
							<?php } ?>
							<div class="ttl"><div class="ttl2">
								<h3><?= $this->lang('settings_profile_ttl2') ?></h3>
								<a class="ttlink" href="<?= $C->SITE_URL ?><?= $this->user->info->username ?>/tab:info"><?= $this->lang('settings_viewprofile_link') ?></a>
							</div></div>
							<form method="post" action="">
								<table id="setform" cellspacing="5">
									<tr>
										<td class="setparam"><?= $this->lang('st_profile_name') ?></td>
										<td><input type="text" name="name" value="<?= htmlspecialchars($D->name) ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_profile_location') ?></td>
										<td><input type="text" name="location" value="<?= htmlspecialchars($D->location) ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_profile_birthdate') ?></td>
										<td>
											<select name="bdate_d" class="setselect" style="width:55px;">
											<?php foreach($D->menu_bdate_d as $k=>$v) { ?>
											<option value="<?= $k ?>"<?= $k==$D->bdate_d?' selected="selected"':'' ?>><?= $v ?></option>
											<?php } ?>
											</select>
											<select name="bdate_m" class="setselect" style="width:130px;">
											<?php foreach($D->menu_bdate_m as $k=>$v) { ?>
											<option value="<?= $k ?>"<?= $k==$D->bdate_m?' selected="selected"':'' ?>><?= $v ?></option>
											<?php } ?>
											</select>
											<select name="bdate_y" class="setselect" style="width:70px;">
											<?php foreach($D->menu_bdate_y as $k=>$v) { ?>
											<option value="<?= $k ?>"<?= $k==$D->bdate_y?' selected="selected"':'' ?>><?= $v ?></option>
											<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="setparam" valign="top"><?= $this->lang('st_profile_gender') ?></td>
										<td>
											<label><input type="radio" name="gender" value="m" <?= $D->gender=='m'?'checked="checked"':'' ?> /> <span><?= $this->lang('st_profile_gender_m') ?></span></label>
											<label><input type="radio" name="gender" value="f" <?= $D->gender=='f'?'checked="checked"':'' ?> /> <span><?= $this->lang('st_profile_gender_f') ?></span></label>
										</td>
									</tr>
									<tr>
										<td class="setparam" valign="top"><?= $this->lang('st_profile_aboutme') ?></td>
										<td><textarea name="aboutme" class="setinp" style="height:90px;"><?= htmlspecialchars($D->aboutme) ?></textarea></td>
									</tr>
									<tr>
										<td class="setparam" valign="top"><?= $this->lang('st_profile_tags') ?></td>
										<td><textarea name="tags" class="setinp"><?= htmlspecialchars($D->tags) ?></textarea></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="submit" name="sbm" value="<?= $this->lang('st_profile_savebtn') ?>" style="padding:4px; font-weight:bold;"/></td>
									</tr>
								</table>
							</form>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>