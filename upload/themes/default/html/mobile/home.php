<?php
	
	$this->load_template('mobile/header.php');
	
?>
	<h3 id="ttle"><?= $this->lang('home_form_title', array('#SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE)) ?></h3>
	<div id="postpage">
		<?php if( $D->error ) { ?>
		<h4 class="error"><?= $this->lang($D->errmsg) ?></h4>
		<?php } ?>
		<form method="post" action="" style="line-height:1.4;">
			<?= $this->lang('home_form_email') ?><br />
			<input type="text" name="email" value="<?= htmlspecialchars($D->email) ?>" maxlength="100" /><br />
			<?= $this->lang('home_form_pass') ?><br />
			<input type="password" name="password" value="<?= htmlspecialchars($D->password) ?>" maxlength="100" /><br />
			<input type="checkbox" name="rememberme" value="1" <?= $D->rememberme?'checked="checked"':'' ?>  style="margin-top:10px; margin-bottom:10px;" />&nbsp;<?= $this->lang('home_form_rmbme') ?><br />
			<input type="submit" value="<?= $this->lang('home_form_btn') ?>" class="submitbtn" />
		</form>
	</div>	
<?php
	
	$this->load_template('mobile/footer.php');
	
?>