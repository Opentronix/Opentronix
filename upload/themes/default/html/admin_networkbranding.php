<?php

	$this->load_template('header.php');

?>
					<script type="text/javascript">
						function adm_switch_customlogo(b) {
							document.getElementById("adm_customlogo_on").style.display	= b ? "block" : "none";
							document.getElementById("adm_customlogo_off").style.display	= b ? "none" : "block";
						}
						function adm_switch_customfavicon(b) {
							document.getElementById("adm_customfavicon_on").style.display	= b ? "block" : "none";
							document.getElementById("adm_customfavicon_off").style.display	= b ? "none" : "block";
						}
					</script>
					<div id="settings">
						<div id="settings_left">
							<?php $this->load_template('admin_leftmenu.php') ?>
						</div>
						<div id="settings_right">
							<div class="ttl">
								<div class="ttl2">
									<h3><?= $this->lang('admtitle_networkbranding') ?></h3>
								</div>
							</div>
							<?php if($D->error) { ?>
							<?= errorbox($this->lang('admbrnd_frm_err'), $this->lang($D->errmsg), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
							<?php } elseif($D->submit) { ?>
							<?= okbox($this->lang('admbrnd_frm_ok'), $this->lang('admbrnd_frm_ok_txt'), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
							<?php } ?>
							<form method="post" action="<?= $C->SITE_URL ?>admin/networkbranding" enctype="multipart/form-data">
								<table id="setform" cellspacing="5">
									<tr>
										<td width="120" valign="top" class="setparam" nowrap="nowrap"><?= $this->lang('admbrnd_frm_logo') ?></td>
										<td>
											<div class="radioptions">
												<label>
													<input type="radio" name="hdr_show_logo" value="1" <?= $D->hdr_show_logo==1?'checked="checked"':'' ?> onclick="adm_switch_customlogo(!this.checked);" onchange="adm_switch_customlogo(!this.checked);" />
													<b><?= $this->lang('admbrnd_frm_logo_default', array('#SITE_TITLE#'=>'Opentronix')) ?></b>
												</label>
												<label id="adm_customlogo_off" style="display: <?= $D->hdr_show_logo==2?'none':'block' ?>;">
													<input type="radio" name="hdr_show_logo" value="2" <?= $D->hdr_show_logo==2?'checked="checked"':'' ?> onclick="adm_switch_customlogo(this.checked);" onchange="adm_switch_customlogo(this.checked);" />
													<b><?= $this->lang('admbrnd_frm_logo_custom') ?></b>
												</label>
												<div id="adm_customlogo_on" style="display: <?= $D->hdr_show_logo==2?'block':'none' ?>;">
													<label class="onoption">
														<input type="radio" checked="checked" />
														<b><?= $this->lang('admbrnd_frm_logo_custom') ?></b>
													</label>
													<div class="radioptiondetails">
														<small><?= $this->lang('admbrnd_frm_logo_custom_current') ?></small>
														<div class="currentlogo" style="background-color:<?= $D->theme->logo_bgcolor ?>;">
															<img src="<?= empty($D->hdr_custom_logo) ? ($C->SITE_URL.'themes/'.$C->THEME.'/imgs/logo.gif') : ($C->IMG_URL.$D->hdr_custom_logo) ?>" alt="" border="0" />
														</div>
														<small><?= $this->lang('admbrnd_frm_logo_custom_choose') ?></small>
														<input type="file" name="custom_logo" value="" />
														<div class="important">
															<?= $this->lang('admbrnd_frm_logo_custom_descr_new', array('#H#'=>$D->theme->logo_height, '#C#'=>$D->theme->logo_bgcolor)) ?>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td valign="top" class="setparam" nowrap="nowrap"><?= $this->lang('admbrnd_frm_ficn') ?></td>
										<td>
											<div class="radioptions">
												<label>
													<input type="radio" name="hdr_show_favicon" value="1" <?= $D->hdr_show_favicon==1?'checked="checked"':'' ?> onclick="adm_switch_customfavicon(!this.checked);" onchange="adm_switch_customfavicon(!this.checked);" />
													<b><?= $this->lang('admbrnd_frm_ficn_default', array('#SITE_TITLE#'=>'Opentronix')) ?></b>
												</label>
												<label id="adm_customfavicon_off" style="display: <?= $D->hdr_show_favicon==2?'none':'block' ?>;">
													<input type="radio" name="hdr_show_favicon" value="2" <?= $D->hdr_show_favicon==2?'checked="checked"':'' ?> onclick="adm_switch_customfavicon(this.checked);" onchange="adm_switch_customfavicon(this.checked);" />
													<b><?= $this->lang('admbrnd_frm_ficn_custom') ?></b>
												</label>
												<div id="adm_customfavicon_on" style="display: <?= $D->hdr_show_favicon==2?'block':'none' ?>;">
													<label class="onoption">
														<input type="radio" checked="checked" />
														<b><?= $this->lang('admbrnd_frm_ficn_custom') ?></b>
													</label>
													<div class="radioptiondetails">
														<small style="display:block;float:left; margin-top:2px;"><?= $this->lang('admbrnd_frm_ficn_custom_current') ?></small>
														<img src="<?= empty($D->hdr_custom_favicon) ? ($C->SITE_URL.'themes/'.$C->THEME.'/imgs/favicon.ico') : ($C->IMG_URL.$D->hdr_custom_favicon) ?>" style="margin-left:5px; margin-bottom:5px; float:left;" alt="" border="0" />
														<div class="klear"></div>
														<small><?= $this->lang('admbrnd_frm_ficn_custom_choose') ?></small>
														<input type="file" name="custom_favicon" value="" />
														<div class="important">
															<?= $this->lang('admbrnd_frm_ficn_custom_descr') ?>
														</div>
													</div>
												</div>
												<label>
													<input type="radio" name="hdr_show_favicon" value="0" <?= $D->hdr_show_favicon==0?'checked="checked"':'' ?> onclick="adm_switch_customfavicon(!this.checked);" onchange="adm_switch_customfavicon(!this.checked);" />
													<b><?= $this->lang('admbrnd_frm_ficn_noicon') ?></b>
												</label>
											</div>
										</td>
									</tr>
									<tr>
										<td></td>
										<td><input type="submit" value="<?= $this->lang('admbrnd_frm_sbm') ?>" name="sbm" style="padding:4px; font-weight:bold;"/></td>
									</tr>
								</table>
							</form>
						</div>
					</div>
<?php

	$this->load_template('footer.php');

?>
