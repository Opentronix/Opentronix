<?php
	
	$this->load_template('mobile/header.php');
	
	if( $this->param('okmsg') ) {
	
?>
	<div class="okbox" style="margin-top:10px;"><?= $this->lang('newpost_okmsg_'.($this->param('okmsg')=='sent'?'sent':'posted')) ?></div>
<?php
	} else {
?>
	<div id="postpage">
		<form method="post" action="" name="postform" enctype="multipart/form-data">
			<?php if( $D->to_user ) { ?>
				<?= $this->lang('newpost_title_private') ?>
				<a href="<?= $C->SITE_URL.$D->to_user->username ?>" title="<?= htmlspecialchars($D->to_user->fullname) ?>"><b><?= $D->to_user->username ?></b></a>
			<?php } elseif( count($D->menu_groups) > 0 ) { ?>
				<?= $this->lang('newpost_title_public_menu') ?>
				<select name="sharewith">
					<option value="all" <?= !$D->to_group&&!$D->to_user ? 'selected="selected"' : '' ?>><?= $this->lang('newpost_title_public_menu_all') ?></option>
					<?php foreach($D->menu_groups as $g) { ?>
					<option value="group_<?= $g->groupname ?>" <?= $D->to_group&&$D->to_group->id==$g->id ? 'selected="selected"' : '' ?>><?= htmlspecialchars($g->title) ?></option>
					<?php } ?>
				</select>
			<?php } else { ?>
				<?= $this->lang('newpost_title_public_nomenu') ?>
			<?php } ?>
			<span id="post_msglen"></span>
			<div class="klear"></div>
			<?php if( $D->error ) { ?>
				<div class="error" style="margin-top:5px; margin-bottom:0px;"><?= $this->lang($D->errmsg) ?></div>
			<?php } ?>
			<textarea name="message" onkeypress="postform_validate();"><?= htmlspecialchars($D->message) ?></textarea><br />
			<?php if( $C->ATTACH_IMAGE_DISABLED==0 || $C->ATTACH_FILE_DISABLED==0 ) { ?>
				<?= $this->lang('newpost_form_attachtext') ?>
				<input type="file" name="attach" value="" /><br />
			<?php } ?>
			<input type="submit" value="<?= $this->lang( $D->to_user ? 'newpost_form_submit_private' : 'newpost_form_submit_public' ) ?>" />
			<br />
			<div id="post_msglen_warning"><?= $this->lang('newpost_msglen_warning', array('#NUM#'=>$C->POST_MAX_SYMBOLS)) ?></div>
		</form>
	</div>
	<script language="javascript" type="text/javascript">
		<!--
		document.getElementById("post_msglen_warning").style.display	= "none";
		msglen_max	= <?= intval($C->POST_MAX_SYMBOLS) ?>;
		msglen_counter_prefix	= "<?= $this->lang('newpost_msglen_counter_prefix') ?>";
		msglen_counter_suffix	= "<?= $this->lang('newpost_msglen_counter_suffix') ?>";
		document.postform.message.focus();
		//-->
	</script>
<?php
	
	}
	
	$this->load_template('mobile/footer.php');
	
?>