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
								<a href="<?= $C->SITE_URL ?>settings/notifications" class="onsidenav"><?= $this->lang('settings_menu_notif') ?></a>
								<?php if( function_exists('curl_init') ) { ?>
								<a href="<?= $C->SITE_URL ?>settings/rssfeeds"><?= $this->lang('settings_menu_rssfeeds') ?></a>
								<?php } ?>
								<a href="<?= $C->SITE_URL ?>settings/delaccount"><?= $this->lang('settings_menu_delaccount') ?></a>
							</div>
						</div>
						<div id="settings_right">
							<?php if( $D->submit ) { ?>
							<?= okbox($this->lang('os_st_notif_ok'), $this->lang('os_st_notif_okmsg')) ?>
							<?php } ?>
							<form method="post" action="">
								<div class="ttl"><div class="ttl2"><h3><?= $this->lang('os_st_notif_title') ?></h3></div></div>
								<table cellpadding="7" cellspacing="0" width="100%">
									<tr>
										<td><b><?= $this->lang('os_st_notif_ttl1') ?></b></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_them_if_i_follow_usr') ?></td>
										<td colspan="2"><input type="checkbox" name="ntf_them_if_i_follow_usr" value="1" <?= $D->i->ntf_them_if_i_follow_usr==1 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_them_if_i_comment') ?></td>
										<td colspan="2"><input type="checkbox" name="ntf_them_if_i_comment" value="1" <?= $D->i->ntf_them_if_i_comment==1 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_them_if_i_edt_profl') ?></td>
										<td colspan="2"><input type="checkbox" name="ntf_them_if_i_edt_profl" value="1" <?= $D->i->ntf_them_if_i_edt_profl==1 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_them_if_i_edt_pictr') ?></td>
										<td colspan="2"><input type="checkbox" name="ntf_them_if_i_edt_pictr" value="1" <?= $D->i->ntf_them_if_i_edt_pictr==1 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_them_if_i_create_grp') ?></td>
										<td colspan="2"><input type="checkbox" name="ntf_them_if_i_create_grp" value="1" <?= $D->i->ntf_them_if_i_create_grp==1 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_them_if_i_join_grp') ?></td>
										<td colspan="2"><input type="checkbox" name="ntf_them_if_i_join_grp" value="1" <?= $D->i->ntf_them_if_i_join_grp==1 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td><b><?= $this->lang('os_st_notif_ttl2') ?></b></td>
										<td align="right" style="color:#999;"><?= $this->lang('os_st_notif_type') ?></td>
										<td width="50"><b><?= $this->lang('os_st_notif_type_email') ?></b></td>
										<td width="70"><b><?= $this->lang('os_st_notif_type_spost') ?></b></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_follows_me') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_follows_me[3]" value="1" <?= $D->i->ntf_me_if_u_follows_me==1 || $D->i->ntf_me_if_u_follows_me==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" checked="checked" disabled="disabled" /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_follows_u2') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_follows_u2[3]" value="1" <?= $D->i->ntf_me_if_u_follows_u2==1 || $D->i->ntf_me_if_u_follows_u2==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" name="ntf_me_if_u_follows_u2[2]" value="1" <?= $D->i->ntf_me_if_u_follows_u2==1 || $D->i->ntf_me_if_u_follows_u2==2 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_commments_me') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_commments_me[3]" value="1" <?= $D->i->ntf_me_if_u_commments_me==1 || $D->i->ntf_me_if_u_commments_me==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" disabled="disabled" /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_commments_m2') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_commments_m2[3]" value="1" <?= $D->i->ntf_me_if_u_commments_m2==1 || $D->i->ntf_me_if_u_commments_m2==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" disabled="disabled" /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_edt_profl') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_edt_profl[3]" value="1" <?= $D->i->ntf_me_if_u_edt_profl==1 || $D->i->ntf_me_if_u_edt_profl==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" name="ntf_me_if_u_edt_profl[2]" value="1" <?= $D->i->ntf_me_if_u_edt_profl==1 || $D->i->ntf_me_if_u_edt_profl==2 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_edt_pictr') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_edt_pictr[3]" value="1" <?= $D->i->ntf_me_if_u_edt_pictr==1 || $D->i->ntf_me_if_u_edt_pictr==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" name="ntf_me_if_u_edt_pictr[2]" value="1" <?= $D->i->ntf_me_if_u_edt_pictr==1 || $D->i->ntf_me_if_u_edt_pictr==2 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_creates_grp') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_creates_grp[3]" value="1" <?= $D->i->ntf_me_if_u_creates_grp==1 || $D->i->ntf_me_if_u_creates_grp==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" name="ntf_me_if_u_creates_grp[2]" value="1" <?= $D->i->ntf_me_if_u_creates_grp==1 || $D->i->ntf_me_if_u_creates_grp==2 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_joins_grp') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_joins_grp[3]" value="1" <?= $D->i->ntf_me_if_u_joins_grp==1 || $D->i->ntf_me_if_u_joins_grp==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" name="ntf_me_if_u_joins_grp[2]" value="1" <?= $D->i->ntf_me_if_u_joins_grp==1 || $D->i->ntf_me_if_u_joins_grp==2 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_invit_me_grp') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_invit_me_grp[3]" value="1" <?= $D->i->ntf_me_if_u_invit_me_grp==1 || $D->i->ntf_me_if_u_invit_me_grp==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" checked="checked" disabled="disabled" /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_posts_qme') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_posts_qme[3]" value="1" <?= $D->i->ntf_me_if_u_posts_qme==1 || $D->i->ntf_me_if_u_posts_qme==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" disabled="disabled" /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_posts_prvmsg') ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_posts_prvmsg[3]" value="1" <?= $D->i->ntf_me_if_u_posts_prvmsg==1 || $D->i->ntf_me_if_u_posts_prvmsg==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" disabled="disabled" /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2" style="padding-left:30px;"><?= $this->lang('os_st_notif_ntf_me_if_u_registers',array('#COMPANY#'=>htmlspecialchars($C->COMPANY))) ?></td>
										<td><input type="checkbox" name="ntf_me_if_u_registers[3]" value="1" <?= $D->i->ntf_me_if_u_registers==1 || $D->i->ntf_me_if_u_registers==3 ? 'checked="checked"' : '' ?> /></td>
										<td><input type="checkbox" name="ntf_me_if_u_registers[2]" value="1" <?= $D->i->ntf_me_if_u_registers==1 || $D->i->ntf_me_if_u_registers==2 ? 'checked="checked"' : '' ?> /></td>
									</tr>
									<tr><td colspan="4" style="background-color:#eeeeee; padding:0px; height:1px;"></td></tr>
									<tr>
										<td colspan="2"></td>
										<td colspan="2"><input type="submit" name="sbm" value="<?= $this->lang('os_st_notif_savebtn') ?>" style="padding:4px; font-weight:bold;"/></td>
									</tr>
								</table>
							</form>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>