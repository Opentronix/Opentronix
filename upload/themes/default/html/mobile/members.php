<?php
	
	$this->load_template('mobile/header.php');
	
?>
	<div id="ttle"><?= $this->lang('members_top_title') ?></div>
	<div id="select">
		<form method="get" action="<?= $C->SITE_URL ?>members/">
			<select name="show" onchange="this.form.submit();">
				<option value="following" <?= $D->show=='following'?'selected="selected"':'' ?>><?= $this->lang('members_top_menu_following') ?></option>
				<option value="followers" <?= $D->show=='followers'?'selected="selected"':'' ?>><?= $this->lang('members_top_menu_followers') ?></option>
				<option value="everybody" <?= $D->show=='everybody'?'selected="selected"':'' ?>><?= $this->lang('members_top_menu_everybody') ?></option>
			</select>
			<input type="submit" value="<?= $this->lang('members_top_menu_submit') ?>" />
		</form>
	</div>
	<hr />
	<div id="members">
		<?php if( empty($D->users_html) ) { ?>
		<div class="msgbox" style="margin-top:10px;"><?= $this->lang('members_nores_'.$D->show) ?></div>
		<?php } else { ?>
			<?= $D->users_html ?>
			<?php if( $D->num_pages>1 && $D->pg>1 && $D->pg<$D->num_pages ) { ?>
			<div id="backnext">
				<a href="<?= $C->SITE_URL ?>members/?show=<?= $D->show ?>&pg=<?= $D->pg-1 ?>"><?= $this->lang('members_paging_prev') ?></a> |
				<a href="<?= $C->SITE_URL ?>members/?show=<?= $D->show ?>&pg=<?= $D->pg+1 ?>"><?= $this->lang('members_paging_next') ?></a>
			</div>
			<?php } elseif( $D->num_pages>1 && $D->pg>1 ) { ?>
			<div id="backnext">
				<a href="<?= $C->SITE_URL ?>members/?show=<?= $D->show ?>&pg=<?= $D->pg-1 ?>"><?= $this->lang('members_paging_prev') ?></a>
			</div>
			<?php } elseif( $D->num_pages>1 && $D->pg<$D->num_pages ) { ?>
			<div id="backnext">
				<a href="<?= $C->SITE_URL ?>members/?show=<?= $D->show ?>&pg=<?= $D->pg+1 ?>"><?= $this->lang('members_paging_next') ?></a>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
<?php
	
	$this->load_template('mobile/footer.php');
	
?>