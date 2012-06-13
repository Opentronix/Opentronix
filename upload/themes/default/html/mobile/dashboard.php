<?php
	
	$this->load_template('mobile/header.php');
	
?>
	<div id="select">
		<form method="get" action="<?= $C->SITE_URL ?>dashboard/">
			<select name="show" onchange="this.form.submit();">
				<option value="all" <?= $D->show=='all'?'selected="selected"':'' ?>><?= $this->lang('dbrd_menu_all') ?><?= $D->tabnums['all']==0?'':' ('.$D->tabnums['all'].')' ?></option>
				<option value="@me" <?= $D->show=='@me'?'selected="selected"':'' ?>><?= $this->lang('dbrd_menu_@me') ?><?= $D->tabnums['@me']==0?'':' ('.$D->tabnums['@me'].')' ?></option>
				<option value="private" <?= $D->show=='private'?'selected="selected"':'' ?>><?= $this->lang('dbrd_menu_private') ?><?= $D->tabnums['private']==0?'':' ('.$D->tabnums['private'].')' ?></option>
				<option value="commented" <?= $D->show=='commented'?'selected="selected"':'' ?>><?= $this->lang('dbrd_menu_commented') ?><?= $D->tabnums['commented']==0?'':' ('.$D->tabnums['commented'].')' ?></option>
				<?php if($D->show_feeds_tab) { ?>
				<option value="feeds" <?= $D->show=='feeds'?'selected="selected"':'' ?>><?= $this->lang('dbrd_menu_feeds') ?><?= $D->tabnums['feeds']==0?'':' ('.$D->tabnums['feeds'].')' ?></option>
				<?php } ?>
				<option value="bookmarks" <?= $D->show=='bookmarks'?'selected="selected"':'' ?>><?= $this->lang('dbrd_menu_bookmarks') ?></option>
				<option value="everybody" <?= $D->show=='everybody'?'selected="selected"':'' ?>><?= $this->lang('dbrd_menu_everybody') ?></option>
				<?php foreach($D->menu_groups as $g) { ?>
				<option value="group_<?= $g->groupname ?>" <?= $D->show=='group'&&$D->onlygroup->id==$g->id?'selected="selected"':'' ?>><?= htmlspecialchars(str_cut($g->title,30)) ?></option>
				<?php } ?>
			</select>
			<input type="submit" value="<?= $this->lang('dbrd_menu_submit') ?>" />
		</form>
	</div>
	<hr />
	<?php if( empty($D->posts_html) ) { ?>
	<div class="msgbox" style="margin-top:10px;"><?= $this->lang('dbrd_nores_'.$D->show) ?></div>
	<?php } else { ?>
		<?= $D->posts_html ?>
		<?php if( $D->num_pages>1 && $D->pg>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url ?><?= $D->pg-1 ?>"><?= $this->lang('members_paging_prev') ?></a> |
			<a href="<?= $D->paging_url ?><?= $D->pg+1 ?>"><?= $this->lang('members_paging_next') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg>1 ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url ?><?= $D->pg-1 ?>"><?= $this->lang('members_paging_prev') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url ?><?= $D->pg+1 ?>"><?= $this->lang('members_paging_next') ?></a>
		</div>
		<?php } ?>
	<?php } ?>
<?php
	
	$this->load_template('mobile/footer.php');
	
?>