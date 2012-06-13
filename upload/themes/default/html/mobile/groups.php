<?php
	
	$this->load_template('mobile/header.php');
	
?>
	<div id="ttle"><?= $this->lang('groups_top_title') ?></div>
	<div id="select">
		<form method="get" action="<?= $C->SITE_URL ?>groups/">
			<select name="show" onchange="this.form.submit();">
				<option value="my" <?= $D->show=='my'?'selected="selected"':'' ?>><?= $this->lang('groups_top_menu_my') ?></option>
				<option value="all" <?= $D->show=='all'?'selected="selected"':'' ?>><?= $this->lang('groups_top_menu_all') ?></option>
			</select>
			<input type="submit" value="<?= $this->lang('groups_top_menu_submit') ?>" />
		</form>
	</div>
	<hr />
	<div id="members">
		<?php if( empty($D->groups_html) ) { ?>
		<div class="msgbox" style="margin-top:10px;"><?= $this->lang('groups_nores_'.$D->show) ?></div>
		<?php } else { ?>
			<?= $D->groups_html ?>
			<?php if( $D->num_pages>1 && $D->pg>1 && $D->pg<$D->num_pages ) { ?>
			<div id="backnext">
				<a href="<?= $C->SITE_URL ?>groups/?show=<?= $D->show ?>&pg=<?= $D->pg-1 ?>"><?= $this->lang('groups_paging_prev') ?></a> |
				<a href="<?= $C->SITE_URL ?>groups/?show=<?= $D->show ?>&pg=<?= $D->pg+1 ?>"><?= $this->lang('groups_paging_next') ?></a>
			</div>
			<?php } elseif( $D->num_pages>1 && $D->pg>1 ) { ?>
			<div id="backnext">
				<a href="<?= $C->SITE_URL ?>groups/?show=<?= $D->show ?>&pg=<?= $D->pg-1 ?>"><?= $this->lang('groups_paging_prev') ?></a>
			</div>
			<?php } elseif( $D->num_pages>1 && $D->pg<$D->num_pages ) { ?>
			<div id="backnext">
				<a href="<?= $C->SITE_URL ?>groups/?show=<?= $D->show ?>&pg=<?= $D->pg+1 ?>"><?= $this->lang('groups_paging_next') ?></a>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
<?php
	
	$this->load_template('mobile/footer.php');
	
?>