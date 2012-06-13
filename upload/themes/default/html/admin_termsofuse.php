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
									<h3><?= $this->lang('admtitle_termsofuse') ?></h3>
								</div>
							</div>
							<?php if($D->error) { ?>
							<?= errorbox($this->lang('admtrms_err_ttl'), $this->lang($D->errmsg), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
							<?php } elseif($D->submit) { ?>
							<?= okbox($this->lang('admtrms_ok_ttl'), $this->lang($D->okmsg,array('#A2#'=>'</a>','#A1#'=>'<a href="'.$C->SITE_URL.'terms">')), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
							<?php } ?>
							<div class="greygrad" style="margin-top:5px;">
								<div class="greygrad2">
									<div class="greygrad3" style="padding-top:2px;">
										<form method="post" action="">
											<table id="setform" cellspacing="5" width="100%">
												<tr>
													<td>
														<?= $this->lang('admtrms_description') ?><br />
														<textarea name="tos_content" style="width:99%; height:300px; margin-top:5px; margin-bottom:5px; overflow:auto;" class="setinp"><?= htmlspecialchars($D->tos_content) ?></textarea><br />
														<label><input type="checkbox" name="tos_enabled" value="1" <?= $D->tos_enabled?'checked="checked"':'' ?> /> <span><?= $this->lang('admtrms_enable') ?></span></label>
														<div class="klear"></div>
														<input type="submit" value="<?= $this->lang('admtrms_sbm') ?>" style="margin-top:5px; padding:4px; font-weight:bold;"/>
													</td>
												</tr>
												<tr>
													<td></td>
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