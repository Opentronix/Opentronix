<?php
	
	$this->load_template('header.php');
	
?>
					<div id="settings">
						<div id="settings_left">				
							<div class="ttl" style="margin-right:12px;"><div class="ttl2"><h3><?= $this->lang('settings_menu_title') ?></h3></div></div>
							<div class="sidenav">
								<a href="<?= $C->SITE_URL ?>settings/profile"><?= $this->lang('settings_menu_profile') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/contacts" class="onsidenav"><?= $this->lang('settings_menu_contacts') ?></a>
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
							<?php if( $D->submit && empty($D->errmsg1) && empty($D->errmsg2) && empty($D->errmsg3) ) { ?>
							<?= okbox($this->lang('st_cnt_ok'), $this->lang('st_cnt_okmsg')) ?>
							<?php } ?>
							<form method="post" action="">
								<div class="ttl"><div class="ttl2">
									<h3><?= $this->lang('st_contacts_section1') ?></h3>
									<a class="ttlink" href="<?= $C->SITE_URL ?><?= $this->user->info->username ?>/tab:info"><?= $this->lang('settings_viewprofile_link') ?></a>
								</div></div>
								<?php if( !empty($D->errmsg1) ) { ?>
								<?= errorbox($this->lang('st_cnt_error'), $this->lang($D->errmsg1), TRUE, 'margin-top:5px;margin-bottom:0px;') ?>
								<?php } ?>
								<table id="setform" cellspacing="5">
									<tr>
										<td width="110" class="setparam"><?= $this->lang('st_cnt_s1_website') ?></td>
										<td><input type="text" name="website" value="<?= isset($D->i->website) ? htmlspecialchars($D->i->website) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
								</table>
								<div class="ttl"><div class="ttl2">
									<h3><?= $this->lang('st_contacts_section2') ?></h3>
									<a class="ttlink" href="<?= $C->SITE_URL ?><?= $this->user->info->username ?>/tab:info"><?= $this->lang('settings_viewprofile_link') ?></a>
								</div></div>
								<?php if( !empty($D->errmsg2) ) { ?>
								<?= errorbox($this->lang('st_cnt_error'), $this->lang($D->errmsg2), TRUE, 'margin-top:5px;margin-bottom:0px;') ?>
								<?php } ?>
								<table id="setform" cellspacing="5">
									<tr>
										<td width="110" class="setparam"><?= $this->lang('st_cnt_s2_skype') ?></td>
										<td><input type="text" name="im_skype" value="<?= isset($D->i->im_skype) ? htmlspecialchars($D->i->im_skype) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s2_icq') ?></td>
										<td><input type="text" name="im_icq" value="<?= isset($D->i->im_icq) ? htmlspecialchars($D->i->im_icq) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s2_gtalk') ?></td>
										<td><input type="text" name="im_gtalk" value="<?= isset($D->i->im_gtalk) ? htmlspecialchars($D->i->im_gtalk) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s2_msn') ?></td>
										<td><input type="text" name="im_msn" value="<?= isset($D->i->im_msn) ? htmlspecialchars($D->i->im_msn) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s2_yahoo') ?></td>
										<td><input type="text" name="im_yahoo" value="<?= isset($D->i->im_yahoo) ? htmlspecialchars($D->i->im_yahoo) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s2_aim') ?></td>
										<td><input type="text" name="im_aim" value="<?= isset($D->i->im_aim) ? htmlspecialchars($D->i->im_aim) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s2_jabber') ?></td>
										<td><input type="text" name="im_jabber" value="<?= isset($D->i->im_jabber) ? htmlspecialchars($D->i->im_jabber) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
								</table>
								<div class="ttl"><div class="ttl2">
									<h3><?= $this->lang('st_contacts_section3') ?></h3>
									<a class="ttlink" href="<?= $C->SITE_URL ?><?= $this->user->info->username ?>/tab:info"><?= $this->lang('settings_viewprofile_link') ?></a>
								</div></div>
								<?php if( !empty($D->errmsg3) ) { ?>
								<?= errorbox($this->lang('st_cnt_error'), $this->lang($D->errmsg3), TRUE, 'margin-top:5px;margin-bottom:0px;') ?>
								<?php } ?>
								<table id="setform" cellspacing="5">
									<tr>
										<td width="110" class="setparam"><?= $this->lang('st_cnt_s3_linkedin') ?></td>
										<td><input type="text" name="prof_linkedin" value="<?= isset($D->i->prof_linkedin) ? htmlspecialchars($D->i->prof_linkedin) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td width="110" class="setparam"><?= $this->lang('st_cnt_s3_facebook') ?></td>
										<td><input type="text" name="prof_facebook" value="<?= isset($D->i->prof_facebook) ? htmlspecialchars($D->i->prof_facebook) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td width="110" class="setparam"><?= $this->lang('st_cnt_s3_myspace') ?></td>
										<td><input type="text" name="prof_myspace" value="<?= isset($D->i->prof_myspace) ? htmlspecialchars($D->i->prof_myspace) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td width="110" class="setparam"><?= $this->lang('st_cnt_s3_orcut') ?></td>
										<td><input type="text" name="prof_orcut" value="<?= isset($D->i->prof_orcut) ? htmlspecialchars($D->i->prof_orcut) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s3_twitter') ?></td>
										<td><input type="text" name="prof_twitter" value="<?= isset($D->i->prof_twitter) ? htmlspecialchars($D->i->prof_twitter) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s3_flickr') ?></td>
										<td><input type="text" name="prof_flickr" value="<?= isset($D->i->prof_flickr) ? htmlspecialchars($D->i->prof_flickr) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s3_friendfeed') ?></td>
										<td><input type="text" name="prof_friendfeed" value="<?= isset($D->i->prof_friendfeed) ? htmlspecialchars($D->i->prof_friendfeed) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s3_youtube') ?></td>
										<td><input type="text" name="prof_youtube" value="<?= isset($D->i->prof_youtube) ? htmlspecialchars($D->i->prof_youtube) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s3_delicious') ?></td>
										<td><input type="text" name="prof_delicious" value="<?= isset($D->i->prof_delicious) ? htmlspecialchars($D->i->prof_delicious) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s3_digg') ?></td>
										<td><input type="text" name="prof_digg" value="<?= isset($D->i->prof_digg) ? htmlspecialchars($D->i->prof_digg) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s3_mixx') ?></td>
										<td><input type="text" name="prof_mixx" value="<?= isset($D->i->prof_mixx) ? htmlspecialchars($D->i->prof_mixx) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s3_favit') ?></td>
										<td><input type="text" name="prof_favit" value="<?= isset($D->i->prof_favit) ? htmlspecialchars($D->i->prof_favit) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_cnt_s3_edno23') ?></td>
										<td><input type="text" name="prof_edno23" value="<?= isset($D->i->prof_edno23) ? htmlspecialchars($D->i->prof_edno23) : '' ?>" class="setinp" maxlength="255" /></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="submit" name="sbm" value="<?= $this->lang('st_cnt_btn') ?>" style="padding:4px; font-weight:bold;"/></td>
									</tr>
								</table>
							</form>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>