<?php
	
	$this->load_template('header.php');
	
?>
					<script type="text/javascript" src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/js/inside_admintools.js"></script>
					<div id="settings">
						<div id="settings_left">
							<?php $this->load_template('admin_leftmenu.php') ?>
						</div>
						<div id="settings_right">
							<div class="ttl">
								<div class="ttl2">
									<h3><?= $this->lang('admtitle_administrators') ?></h3>
								</div>
							</div>
							<?php if( $this->param('msg')=='admsaved' ) { ?>
							<?= okbox($this->lang('admadm_frm_ok'), $this->lang('admadm_frm_ok_txt'), TRUE, 'margin-top:5px; margin-bottom:4px;') ?>
							<?php } ?>
							
							<div class="greygrad" style="margin-top:5px;">
								<div class="greygrad2">
									<div class="greygrad3">
										<?= $this->lang('admadm_descr') ?>
							
										<table id="setform" cellspacing="5" style="margin-top:5px;">
											<tr>
												<td width="110" class="setparam" valign="top"><?= $this->lang('admadm_frm_adm') ?></td>
												<td width="400">
													<div class="groupadmins">
														<div class="addadmins"><?= $this->lang('admadm_frm_adm_you') ?></div>
													</div>
													<div id="group_admins_list"></div>
												</td>
											</tr>
											<tr>
												<td class="setparam"><?= $this->lang('admadm_frm_add') ?></td>
												<td>
													<input type="text" id="addadmin_inp" name="username" value="" style="width:200px;" rel="autocomplete" autocompleteoffset="0,3" />
													<input type="button" id="addadmin_btn" onclick="group_admins_add(); return false;" value="<?= $this->lang('admadm_frm_add_btn') ?>" />
												</td>
											</tr>
											<tr>
												<td></td>
												<td>
													<form method="post" name="admform" action="<?= $C->SITE_URL ?>admin/administrators">
														<input type="hidden" name="admins" value="" />
														<input type="submit" value="<?= $this->lang('admadm_frm_sbm') ?>" style="padding:4px; font-weight:bold;" />
													</form>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<script type="text/javascript">
								jserr_add_admin_invalid_user	= "<?= $this->lang('admadm_jserr_user1') ?>";
								jsconfirm_admin_remove		= "<?= $this->lang('admadm_jscnf_del') ?>";
								js_group_membercheck	= false;
								<?php foreach($D->admins as $u) { if($u->id==$this->user->id) { continue; } ?>
								group_admins_putintolist("<?= $u->username ?>");
								<?php } ?>
							</script>
							
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>