<?php
	
	$this->load_template('header.php');
	
?>
					<div id="settings">
						<div id="settings_left">				
							<div class="ttl" style="margin-right:12px;"><div class="ttl2"><h3><?= $this->lang('settings_menu_title') ?></h3></div></div>
							<div class="sidenav">
								<a href="<?= $C->SITE_URL ?>settings/profile"><?= $this->lang('settings_menu_profile') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/contacts"><?= $this->lang('settings_menu_contacts') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/avatar" class="onsidenav"><?= $this->lang('settings_menu_avatar') ?></a>
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
							<?php if($D->error) { ?>
							<?= errorbox($this->lang('st_avatat_err'), $this->lang($D->errmsg)) ?>
							<?php } elseif($D->submit) { ?>
							<?= okbox($this->lang('st_avatat_ok'), $this->lang('st_avatat_okmsg')) ?>
							<?php } elseif(isset($D->msg) && $D->msg=='deleted') { ?>
							<?= okbox($this->lang('st_avatat_ok'), $this->lang('st_avatat_okdelmsg')) ?>
							<?php } ?>
							<div class="ttl"><div class="ttl2">
								<h3><?= $this->lang('settings_avatar_ttl2') ?></h3>
								<a class="ttlink" href="<?= $C->SITE_URL ?><?= $this->user->info->username ?>/tab:info"><?= $this->lang('settings_viewprofile_link') ?></a>
							</div></div>
							<form method="post" action="<?= $C->SITE_URL ?>settings/avatar/" enctype="multipart/form-data">
								<table id="setform" cellspacing="5">
									<tr>
										<td class="setparam" valign="top"><?= $this->lang('st_avatar_current_picture') ?></td>
										<td><a href="javascript:;" onclick="flybox_open(<?= $D->currw+34 ?>,<?= $D->currh+129 ?>,'<?= $this->lang('st_avatar_current_pic_flybox') ?>','<?= htmlspecialchars('<img src="'.$C->IMG_URL.'avatars/'.$D->u->info->avatar.'" style="width:'.$D->currw.'px;height:'.$D->currh.'px;margin-top:5px;margin-left:5px;" alt="" />') ?>');"><img src="<?= $C->IMG_URL ?>avatars/thumbs1/<?= $D->u->info->avatar ?>" alt="" border="0" /></a></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_avatar_change_picture') ?></td>
										<td><input type="file" name="avatar" value="" class="setinp" /></td>
									</tr>
									<tr>
										<td></td>
										<td class="setparam" style="text-align:left; font-size:10px; padding:0px; padding-left:2px;"><?= $this->lang('st_avatar_change_info') ?></td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="submit" value="<?= $this->lang('st_avatar_uploadbtn') ?>" style="padding:4px; font-weight:bold;"/>
											<?php if($D->u->info->avatar != $C->DEF_AVATAR_USER) { ?>
											<?= $this->lang('st_avatar_upload_or') ?>
											<a href="<?= $C->SITE_URL ?>settings/avatar/del:current" onclick="return confirm('<?= $this->lang('st_avatar_upload_delete_confirm') ?>');" onfocus="this.blur();"><?= $this->lang('st_avatar_upload_or_delete') ?></a>
											<?php } ?>
										</td>
									</tr>
								</table>
							</form>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>