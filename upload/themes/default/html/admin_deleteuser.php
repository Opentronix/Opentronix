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
									<h3><?= $this->lang('admtitle_deleteuser') ?></h3>
								</div>
							</div>
							<?php if($D->error) { ?>
							<?= errorbox($this->lang('admdelu_error'), $this->lang($D->errmsg), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
							<?php } elseif( $this->param('msg')=='deleted' ) { ?>
							<?= okbox($this->lang('admdelu_ok'), $this->lang('admdelu_ok_txt'), TRUE, 'margin-top:5px; margin-bottom:4px;') ?>
							<?php } ?>
							<div class="greygrad" style="margin-top:5px;">
								<div class="greygrad2">
									<div class="greygrad3">
										<?= $this->lang('admdelu_descr') ?>
										<?= $this->lang('admdelu_descr2', array('#A2#'=>'</a>', '#A1#'=>'<a href="'.$C->SITE_URL.'admin/suspendusers">',)) ?>
										
										<form method="post" name="deluser" onsubmit="return confirm('<?= htmlspecialchars($this->lang('admdelu_confirm')) ?>');" action="<?= $C->SITE_URL ?>admin/deleteuser" autocomplete="off">
										<table id="setform" cellspacing="5" style="margin-top:5px;">
											<tr>
												<td class="setparam"><?= $this->lang('admdelu_user') ?></td>
												<td><input type="text" name="deluser" value="<?= htmlspecialchars($D->deluser) ?>" rel="autocomplete" autocomplete="off" autocompleteoffset="0,2" class="setinp" /></td>
											</tr>
											<tr>
												<td class="setparam"><?= $this->lang('admdelu_password') ?></td>
												<td><input type="password" name="admpass" value="" autocomplete="off" class="setinp" /></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" value="<?= $this->lang('admdelu_submit') ?>" style="padding:4px; font-weight:bold;"/></td>
		
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