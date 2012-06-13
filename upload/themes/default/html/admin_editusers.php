<?php
	
	$this->load_template('header.php');
	
?>
					<div id="settings">
						<div id="settings_left">
							<?php $this->load_template('admin_leftmenu.php') ?>
						</div>
						<div id="settings_right">
							
							<?php if( ! $D->user ) { ?> 
							
								<div class="ttl">
									<div class="ttl2">
										<h3><?= $this->lang('admtitle_editusers') ?></h3>
									</div>
								</div>
								<div class="greygrad" style="margin-top:5px;">
									<div class="greygrad2">
										<div class="greygrad3">
											<?= $this->lang('admeditu_chooseuser_descr') ?>
											<form method="post" name="edituser">
												<input type="hidden" name="lookin" value="users" />
												<table id="setform" cellspacing="5" style="margin-top:5px;">
													<tr>
														<td class="setparam"><?= $this->lang('admeditu_chooseuser_usr') ?></td>
														<td>
															<input type="text" name="editusername" value="" rel="autocomplete" autocomplete="off" autocompleteoffset="0,2" style="width:200px;" />
															<input type="submit" value="<?= $this->lang('admeditu_chooseuser_sbm') ?>" />
														</td>
													</tr>
												</table>
											</form>
										</div>
									</div>
								</div>
							
							<?php } else { ?>
							
								<div class="htabs" style="margin-top:0; margin-bottom:10px;">
									<strong><?= $this->lang('admeditu_ifuser_title', array('#USERNAME#'=>$D->user->username)) ?></strong>
									<a href="<?= $C->SITE_URL ?>admin/editusers/user:<?= $D->user->username ?>" class="<?= $D->tab=='profile'?'onhtab':'' ?>"><b><?= $this->lang('admeditu_tab_profile') ?></b></a>
									<a href="<?= $C->SITE_URL ?>admin/editusers/user:<?= $D->user->username ?>/tab:picture" class="<?= $D->tab=='picture'?'onhtab':'' ?>"><b><?= $this->lang('admeditu_tab_picture') ?></b></a>
									<a href="<?= $C->SITE_URL ?>admin/editusers/user:<?= $D->user->username ?>/tab:rssfeeds" class="<?= $D->tab=='rssfeeds'?'onhtab':'' ?>"><b><?= $this->lang('admeditu_tab_rssfeeds') ?></b></a>
									<div style="float:right; padding-top:6px;"> 
										<a href="<?= $C->SITE_URL.$D->user->username ?>" style="display:inline; float:none;">
											<b style="display:inline; float:none; padding:0;">&raquo;</b>
											<?= $this->lang('admeditu_ifuser_view',array('#USER#'=>$D->user->username)) ?>
										</a>
									</div>
								</div>
								
								<?php if( $D->tab == 'profile' ) { ?>
									
									<?php if($D->submit) { ?>
									<?= okbox($this->lang('st_profile_ok'), $this->lang('st_profile_okmsg')) ?>
									<?php } ?>
									<form method="post" action="<?= $C->SITE_URL ?>admin/editusers/user:<?= $D->user->username ?>/tab:profile">
										<table id="setform" cellspacing="5">
											<tr>
												<td class="setparam"><?= $this->lang('st_profile_name') ?></td>
												<td><input type="text" name="name" value="<?= htmlspecialchars($D->name) ?>" class="setinp" maxlength="255" /></td>
											</tr>
											<tr>
												<td class="setparam"><?= $this->lang('st_profile_position') ?></td>
												<td><input type="text" name="position" value="<?= htmlspecialchars($D->position) ?>" class="setinp" maxlength="255" /></td>
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
									
								<?php } elseif( $D->tab == 'picture' ) { ?>
									
									<?php if($D->error) { ?>
									<?= errorbox($this->lang('st_avatat_err'), $this->lang($D->errmsg)) ?>
									<?php } elseif($D->submit) { ?>
									<?= okbox($this->lang('st_avatat_ok'), $this->lang('st_avatat_okmsg', array('#USER#'=>$D->user->username))) ?>
									<?php } elseif(isset($D->msg) && $D->msg=='deleted') { ?>
									<?= okbox($this->lang('st_avatat_ok'), $this->lang('st_avatat_okdelmsg', array('#USER#'=>$D->user->username))) ?>
									<?php } ?>
									<form method="post" action="<?= $C->SITE_URL ?>admin/editusers/user:<?= $D->user->username ?>/tab:picture" enctype="multipart/form-data">
										<table id="setform" cellspacing="5">
											<tr>
												<td class="setparam" valign="top"><?= $this->lang('st_avatar_current_picture') ?></td>
												<td><a href="javascript:;" onclick="flybox_open(<?= $D->currw+34 ?>,<?= $D->currh+129 ?>,'<?= $this->lang('st_avatar_current_pic_flybox') ?>','<?= htmlspecialchars('<img src="'.$C->IMG_URL.'avatars/'.$D->user->avatar.'" style="width:'.$D->currw.'px;height:'.$D->currh.'px;margin-top:5px;margin-left:5px;" alt="" />') ?>');"><img src="<?= $C->IMG_URL ?>avatars/thumbs1/<?= $D->user->avatar ?>" alt="" border="0" /></a></td>
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
													<?php if($D->user->avatar != $C->DEF_AVATAR_USER) { ?>
													<?= $this->lang('st_avatar_upload_or') ?>
													<a href="<?= $C->SITE_URL ?>admin/editusers/user:<?= $D->user->username ?>/tab:picture/del:current" onclick="return confirm('<?= $this->lang('st_avatar_upload_delete_confirm') ?>');" onfocus="this.blur();"><?= $this->lang('st_avatar_upload_or_delete') ?></a>
													<?php } ?>
												</td>
											</tr>
										</table>
									</form>
									
								<?php } elseif( $D->tab == 'rssfeeds' ) { ?>
									
									<?php if( $this->param('msg') == 'added' ) { ?>
										<?= okbox($this->lang('st_rssfeeds_ok'), $this->lang('st_rssfeeds_ok_txt'), TRUE, 'margin-bottom:5px;') ?>
									<?php } elseif( $this->param('msg') == 'deleted' ) { ?>
										<?= okbox($this->lang('st_rssfeeds_ok'), $this->lang('st_rssfeeds_okdel_txt'), TRUE, 'margin-bottom:5px;') ?>
									<?php } ?>
									<?php if( count($D->feeds) == 0 ) { ?>
										<?= msgbox($this->lang('st_rssfeeds_nofeeds_ttl'), $this->lang('st_rssfeeds_nofeeds_txt', array('#USER#'=>$D->user->username)), FALSE, 'margin-bottom:5px;') ?>
									<?php } else { ?>
									<table id="setform" cellspacing="0" cellpadding="5">
										<tr>
											<td width="110" class="setparam" valign="top"><?= $this->lang('st_rssfeeds_feedslist') ?></td>
											<td width="400">
												<div class="groupfeedslist">
												<?php foreach($D->feeds as $f) { ?>
												<div class="groupfeed">
													<a href="<?= $C->SITE_URL ?>admin/editusers/user:<?= $D->user->username ?>/tab:rssfeeds/delfeed:<?= $f->id ?>" onclick="return confirm('<?= $this->lang('st_rssfeeds_feed_delcnf') ?>');" title="<?= $this->lang('st_rssfeeds_feed_delete') ?>" onfocus="this.blur();" class="grpdelbtn"></a>
													<?= htmlspecialchars(str_cut($f->feed_title,35)) ?>				
													<span><a href="<?= htmlspecialchars($f->feed_url) ?>" target="_blank"><?= htmlspecialchars(str_cut_link($f->feed_url,50)) ?></a></span>
													<?php if( !empty($f->filter_keywords) ) { ?>
													<span><?= $this->lang('st_rssfeeds_feed_filter') ?> <?= htmlspecialchars($f->filter_keywords) ?></span>
													<?php } ?>
												</div>
												<?php } ?>
												</div>
											</td>
										</tr>
									</table>		
									<?php } ?>
									<div class="ttl" style="margin-top:10px; margin-bottom:6px;"><div class="ttl2"><h3><?= $this->lang('st_rssfeeds_f_title') ?></h3></div></div>
									<?php if( $D->error ) { ?>
										<?= errorbox($this->lang('st_rssfeeds_err'), $this->lang($D->errmsg), TRUE, 'margin-bottom:5px;') ?>
									<?php } elseif( $D->newfeed_auth_msg ) { ?>
										<?= msgbox($this->lang('st_rssfeeds_pwdreq_ttl'), $this->lang('st_rssfeeds_pwdreq_txt'), TRUE, 'margin-bottom:5px;') ?>
									<?php } ?>
									<form method="post" action="<?= $C->SITE_URL ?>admin/editusers/user:<?= $D->user->username ?>/tab:rssfeeds">
										<table id="setform" cellspacing="0" cellpadding="5">
											<tr>
												<td width="110" class="setparam"><?= $this->lang('st_rssfeeds_f_url') ?></td>
												<td><input type="text" class="setinp" name="newfeed_url" value="<?= htmlspecialchars($D->newfeed_url) ?>" maxlength="255" style="width:320px; padding:3px;" /></td>
											</tr>
											<?php if( $D->newfeed_auth_req ) { ?>
											<tr>
												<td class="setparam"><?= $this->lang('st_rssfeeds_f_usr') ?></td>
												<td><input type="text" class="setinp" name="newfeed_username" value="<?= htmlspecialchars($D->newfeed_username) ?>" autocomplete="off" maxlength="255" style="width:320px; padding:3px;" /></td>
											</tr>
											<tr>
												<td class="setparam"><?= $this->lang('st_rssfeeds_f_pwd') ?></td>
												<td><input type="password" class="setinp" name="newfeed_password" value="<?= htmlspecialchars($D->newfeed_password) ?>" autocomplete="off" maxlength="255" style="width:320px; padding:3px;" /></td>
											</tr>
											<?php } ?>
											<tr>
												<td style="padding-bottom:0px;" class="setparam"><?= $this->lang('st_rssfeeds_f_filter') ?></td>
												<td style="padding-bottom:0px;"><input type="text" class="setinp" name="newfeed_filter" value="<?= htmlspecialchars($D->newfeed_filter) ?>" maxlength="255" style="width:320px; padding:3px;" /></td>
											</tr>
											<tr>
												<td style="padding:0px;"></td>
												<td class="setparam" style="text-align:left; font-size:10px; padding-top:2px;"><?= $this->lang('st_rssfeeds_f_filtertxt') ?></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" name="sbm" value="<?= $this->lang('st_rssfeeds_f_submit') ?>" style="padding:4px; font-weight:bold;" /></td>
											</tr>
										</table>
									</form>
									
									
								<?php } ?>
								
							<?php } ?>
							
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>