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
									<h3>Activate Users</h3>
								</div>
							</div>
							<?php if( $this->param('msg')=='actisaved' ) { ?>
							<?= okbox($this->lang('admsusp_frm_ok'), $this->lang('admsusp_frm_ok_txt'), TRUE, 'margin-top:5px; margin-bottom:4px;') ?>
							<?php } ?>
							<div class="greygrad" style="margin-top:5px;">
								<div class="greygrad2">
									<div class="greygrad3">
										
										<table id="setform" cellspacing="5" style="margin-top:5px;">
											<tr>
												<td width="150" class="setparam" valign="top" nowrap="nowrap">Users waiting for activation:</td>
												<td width="400">
													<div id="group_admins_list">
														<div id="group_admins_link_empty_msg" class="yellowbox" style="border:0px solid; margin:0px;"><?= $this->lang('admsusp_frm_nobody') ?></div>
													</div>
												</td>
											</tr>

											<tr>
												<td></td>
												<td>
													<form method="post" name="admform" action="<?= $C->SITE_URL ?>admin/suspendusers">
														<input type="hidden" name="admins" value="" />
														<input type="submit" value="<?= $this->lang('admsusp_frm_sbm') ?>" style="padding:4px; font-weight:bold;" />
													</form>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<script type="text/javascript">
								jserr_add_admin_invalid_user	= "<?= $this->lang('admsusp_jserr_user1') ?>";
								jsconfirm_admin_remove		= "";
								js_group_membercheck	= false;
								<?php foreach($D->users as $u) { ?>
								group_admins_putintolist("<?= $u->username ?>");
								<?php } ?>
							</script>
							
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>