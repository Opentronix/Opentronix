<?php
	
	$this->load_template('header.php');
	
?>
					<div id="settings">
						<div id="settings_left">
							<?php $this->load_template('admin_leftmenu.php') ?>
						</div>
						<div id="settings_right">
							<div class="ttl">
								<div class="ttl2">
									<h3><?= $this->lang('admtitle_general') ?></h3>
								</div>
							</div>
							<?php if($D->error) { ?>
							<?= errorbox($this->lang('admgnrl_error'), $this->lang($D->errmsg,array('#SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE)), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
							<?php } elseif($D->submit) { ?>
							<?= okbox($this->lang('admgnrl_okay'), $this->lang('admgnrl_okay_txt'), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
							<?php } ?>
							<div class="greygrad" style="margin-top:5px;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-top:0px;">
										<form method="post" action="">
											<table id="setform" cellspacing="5">
												<tr>
													<td class="setparam"><?= $this->lang('admgnrl_frm_network') ?></td>
													<td><input type="text" name="network_name" value="<?= htmlspecialchars($D->network_name) ?>" class="setinp" maxlength="50" /></td>
												</tr>
												<tr>
													<td class="setparam"><?= $this->lang('admgnrl_frm_intro_ttl') ?></td>
													<td><input type="text" name="intro_ttl" value="<?= htmlspecialchars($D->intro_ttl) ?>" class="setinp" maxlength="50" /></td>
												</tr>
												<tr>
													<td class="setparam" valign="top"><?= $this->lang('admgnrl_frm_intro_txt') ?></td>
													<td><textarea class="setinp" name="intro_txt" style="height:100px;"><?= htmlspecialchars($D->intro_txt) ?></textarea></td>
												</tr>
												<tr>
													<td class="setparam"><?= $this->lang('admgnrl_frm_email') ?></td>
													<td><input type="text" name="system_email" value="<?= htmlspecialchars($D->system_email) ?>" class="setinp" maxlength="50" /></td>
												</tr>
												<tr>
													<td class="setparam"><?= $this->lang('admgnrl_frm_deflang') ?></td>
													<td>
														<select name="def_language" class="setselect">
														<?php foreach($D->menu_languages as $k=>$v) { ?>
														<option value="<?= $k ?>"<?= $k==$D->def_language?' selected="selected"':'' ?>><?= htmlspecialchars($v) ?></option>
														<?php } ?>
														</select>
													</td>
												</tr>
												<?php if( count($D->menu_timezones) > 0 ) { ?>
												<tr>
													<td class="setparam"><?= $this->lang('admgnrl_frm_deftzone') ?></td>
													<td>
														<select name="def_timezone" class="setselect">
														<?php foreach($D->menu_timezones as $tz=>$txt) { ?>
														<option value="<?= htmlspecialchars($tz) ?>"<?= $tz==$D->def_timezone?' selected="selected"':'' ?>><?= htmlspecialchars($txt) ?></option>
														<?php } ?>
														</select>
													</td>
												</tr>
												<?php } ?>
												<tr>
													<td class="setparam"><?= $this->lang('admgnrl_frm_postslen') ?></td>
													<td>
														<select name="post_maxlength" class="setselect">
														<?php foreach($D->menu_postlength as $ln) { ?>
														<option value="<?= $ln ?>"<?= $ln==$D->post_maxlength?' selected="selected"':'' ?>><?= $ln ?> <?= $this->lang('admgnrl_frm_postslenc') ?></option>
														<?php } ?>
														</select>
													</td>
												</tr>
												<tr>
													<td class="setparam" valign="top"><?= $this->lang('admgnrl_frm_postsatch') ?></td>
													<td>
														<label style="width:120px; float:left; clear:none;"><input type="checkbox" name="atch_link" value="1" <?= $D->post_atch_link?'checked="checked"':'' ?> /> <span><?= $this->lang('admgnrl_frm_patlinks') ?></span></label>
														<label style="width:120px; float:left; clear:none;"><input type="checkbox" name="atch_image" value="1" <?= $D->post_atch_image?'checked="checked"':'' ?> /> <span><?= $this->lang('admgnrl_frm_patimages') ?></span></label>
														<label style="width:120px; float:left; clear:both;"><input type="checkbox" name="atch_video" value="1" <?= $D->post_atch_video?'checked="checked"':'' ?> /> <span><?= $this->lang('admgnrl_frm_patvideos') ?></span></label>
														<label style="width:120px; float:left; clear:none;"><input type="checkbox" name="atch_file" value="1" <?= $D->post_atch_file?'checked="checked"':'' ?> /> <span><?= $this->lang('admgnrl_frm_patfiles') ?></span></label>
													</td>
												</tr>
												<tr>
													<td class="setparam" valign="top"><?= $this->lang('admgnrl_frm_mobile') ?></td>
													<td>
														<label style="float:left; margin-right:5px; clear:none;"><input type="radio" name="mobi_enabled" value="1" <?= $D->mobi_enabled?'checked="checked"':'' ?> /> <span><?= $this->lang('admgnrl_frm_mobi_on') ?></span></label>
														<label style="float:left; clear:none;"><input type="radio" name="mobi_enabled" value="0" <?= !$D->mobi_enabled?'checked="checked"':'' ?> /> <span><?= $this->lang('admgnrl_frm_mobi_off') ?></span></label>
													</td>
												</tr>
												<tr>
													<td class="setparam" valign="top"><?= $this->lang('admgnrl_frm_emlconfirm') ?></td>
													<td>
														<label style="float:left; margin-right:5px; clear:none;"><input type="radio" name="email_confirm" value="1" <?= $D->email_confirm?'checked="checked"':'' ?> /> <span><?= $this->lang('admgnrl_frm_emlconfirm_on') ?></span></label>
														<label style="float:left; clear:none;"><input type="radio" name="email_confirm" value="0" <?= !$D->email_confirm?'checked="checked"':'' ?> /> <span><?= $this->lang('admgnrl_frm_emlconfirm_off') ?></span></label>
													</td>
												</tr>
												<tr>
													<td></td>
													<td><input type="submit" name="sbm" value="<?= $this->lang('admgnrl_frm_sbm') ?>" style="padding:4px; font-weight:bold;"/></td>
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