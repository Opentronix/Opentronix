<?php
	
	$this->load_template('header.php');
	
?>
			<div class="ttl" style="margin-bottom:10px;">
				<div class="ttl2">
					<h3><?= $this->lang('signinforg_form_title', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?> - <?= $C->COMPANY ?></h3>
				</div>
			</div>
			<?php if( ! $D->have_key ) { ?> 
				<?php if( $D->submit && !$D->error ) { ?>
					<div class="greenbox">
						<div class="greenbox2">
							<h2><?= $this->lang('signinforg_sentmail_ttl') ?></h2>
							<?= $this->lang('signinforg_sentmail_txt', array('#EMAIL#'=>$D->email)) ?>
						</div>
					</div>
				<?php } else { ?>
					<?php if($D->error) { ?>
						<?= errorbox($this->lang('signinforg_err'), $this->lang($D->errmsg)); ?>
					<?php } ?>
					<form method="post" action="">
						<table id="regform" cellspacing="5">
							<tr>
								<td class="regparam"><?= $this->lang('signinforg_form_email') ?></td>
								<td><input type="text" name="email" value="<?= htmlspecialchars($D->email) ?>" maxlength="100" class="reginp" /></td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" value="<?= $this->lang('signinforg_form_submit') ?>" style="padding:4px; font-weight:bold;" /></td>
							</tr>
						</table>
					</form>
				<?php } ?>
			<?php } elseif( $D->error_key ) { ?>
				<?= errorbox($this->lang('signinforg_errkey_ttl'), $this->lang('signinforg_errkey_txt'), FALSE) ?>
			<?php } else { ?>
				<?php if($D->error) { ?>
					<?= errorbox($this->lang('signinforg_err'), $this->lang($D->errmsg)); ?>
				<?php } ?>
				<form method="post" action="">
					<table id="regform" cellspacing="5">
						<tr>
							<td class="regparam"><?= $this->lang('signinforg_form_password') ?></td>
							<td><input type="password" name="pass1" value="" maxlength="100" class="reginp" autocomplete="off" /></td>
						</tr>
						<tr>
							<td class="regparam"><?= $this->lang('signinforg_form_password2') ?></td>
							<td><input type="password" name="pass2" value="" maxlength="100" class="reginp" autocomplete="off" /></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="<?= $this->lang('signinforg_form_submit2') ?>" style="padding:4px; font-weight:bold;" /></td>
						</tr>
					</table>
				</form>
			<?php } ?>
			
<?php
	
	$this->load_template('footer.php');
	
?>