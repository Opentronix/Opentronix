<?php
		
	$this->load_template('header.php');
	
?>	
		<div id="pagebody" style="margin:0px; border-top:1px solid #fff;">
			<div>
				<div id="contacts_left" style="width:100%;">			
					<div class="ttl">
						<div class="ttl2"><h3><?= $this->lang('contacts_left_ttl') ?></h3></div>
					</div>
					<?php if( $D->submit && !$D->error ) { ?>
						<?= okbox($this->lang('cntf_ok_ttl'), $this->lang('cntf_ok_txt'), FALSE, 'margin-top:5px;margin-bottom:5px;') ?>
					<?php } else { ?>
						<?php if( $D->error ) { ?>
							<?= errorbox($this->lang('cntf_error'), $this->lang($D->errmsg), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
						<?php } ?>
						<div class="greygrad" style="margin-top:5px;">
							<div class="greygrad2">
								<div class="greygrad3">
									<form method="post" action="">	
										<table id="setform" style="width:100%;" cellspacing="5">			
											<tr>				
												<td class="setparam"><?= $this->lang('cnt_frm_fullname') ?></td>
												<td><input type="text" class="setinp" name="fullname" value="<?= htmlspecialchars($D->fullname) ?>" maxlength="100" /></td>			
											</tr>			
											<tr>
												<td class="setparam"><?= $this->lang('cnt_frm_email') ?></td>
												<td><input type="text" class="setinp" name="email" value="<?= htmlspecialchars($D->email) ?>" maxlength="100" /></td>			
											</tr>
											<tr>
												<td class="setparam" valign="top"><?= $this->lang('cnt_frm_message') ?></td>
												<td><textarea style="width:700px; height:300px;" name="message"><?= htmlspecialchars($D->message) ?></textarea></td>
											</tr>
											<tr>
												<td class="setparam" valign="top" style="padding-top:12px;"><?= $this->lang('cnt_frm_captcha') ?></td>
												<td>
													<input type="hidden" name="captcha_key" value="<?= $D->captcha_key ?>" />
													<?= $D->captcha_html ?><br />
													<input type="text" maxlength="20" name="captcha_word" value="" class="setinp"  style="width:168px; margin-top:5px;" />
												</td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" name="sbm" value="<?= $this->lang('cnt_frm_sbm') ?>" style="padding:4px; font-weight:bold;" /></td>			
											</tr>	
										</table>
									</form>		
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	
	$this->load_template('footer.php');
	
?>