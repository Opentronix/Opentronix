<?php
	
	$this->load_template('mobile_iphone/header.php');
	
?>

	<div id="loginintro">
		<h2><?= $this->lang('home_form_title', array( '#SITE_TITLE#' => isset($C->STEALTH_MODE)&&$C->STEALTH_MODE==1 ? $C->COMPANY : $C->OUTSIDE_SITE_TITLE )) ?></h2>
		<!-- <p>...</p> -->
	</div>
	<div id="loginbox">
		<?php if( $D->error ) { ?>
		<h4 class="error"><?= $this->lang($D->errmsg) ?></h4>
		<?php } ?>
		<form name="lf" method="post" action="">
			<b><?= $this->lang('home_form_email') ?></b>
			<div class="loginputdiv"><input type="text" name="email" value="<?= htmlspecialchars($D->email) ?>" maxlength="100" /></div>
			<b><?= $this->lang('home_form_pass') ?><br /></b>
			<div class="loginputdiv"><input type="password" name="password" value="<?= htmlspecialchars($D->password) ?>" maxlength="100" /></div>
			<a href="javascript:;" id="loginbtn" onclick="document.lf.submit();"><strong><?= $this->lang('home_form_btn') ?></strong></a>
			<label>
				<input type="checkbox" name="rememberme" value="1" <?= $D->rememberme?'checked="checked"':'' ?> /><span><?= $this->lang('home_form_rmbme') ?></span>
			</label>
		</form>
	</div>
	<!--
	<div id="forgpasslink">
		<a href="#">...</a>
	</div>
	-->
<?php
	
	$this->load_template('mobile_iphone/footer.php');
	
?>