<?php
	
	$this->load_template('mobile_iphone/header.php');
	
?>
		<div id="postspage">	
			<div id="dropfilter">
				<a href="javascript:;" onclick="toggle_dropmenu();" id="fdropper"><b><?= $this->lang('iphone_dbrd_menu_'.$D->show) ?></b></a>
				<div id="dropmenu" style="display:none;">
					<div class="<?= $D->show=='all'?'current':'' ?>"><span><a href="<?= $C->SITE_URL ?>dashboard"><?= $this->lang('iphone_dbrd_menu_all') ?></a><?= $D->tabnums['all']?('<small>'.$D->tabnums['all'].'</small>'):'' ?></span></div>
					<div class="<?= $D->show=='@me'?'current':'' ?>"><span><a href="<?= $C->SITE_URL ?>dashboard/show:@me"><?= $this->lang('iphone_dbrd_menu_@me') ?></a><strong><?= $D->tabnums['@me']?('<small>'.$D->tabnums['@me'].'</small>'):'' ?></strong></span></div>
					<div class="<?= $D->show=='private'?'current':'' ?>"><span><a href="<?= $C->SITE_URL ?>dashboard/show:private"><?= $this->lang('iphone_dbrd_menu_private') ?></a><strong><?= $D->tabnums['private']?('<small>'.$D->tabnums['private'].'</small>'):'' ?></strong></span></div>
					<div class="<?= $D->show=='commented'?'current':'' ?>"><span><a href="<?= $C->SITE_URL ?>dashboard/show:commented"><?= $this->lang('iphone_dbrd_menu_commented') ?></a><?= $D->tabnums['commented']?('<small>'.$D->tabnums['commented'].'</small>'):'' ?></span></div>
					<?php if($D->show_feeds_tab) { ?>
					<div class="<?= $D->show=='feeds'?'current':'' ?>"><span><a href="<?= $C->SITE_URL ?>dashboard/show:feeds"><?= $this->lang('iphone_dbrd_menu_feeds') ?></a><?= $D->tabnums['feeds']?('<small>'.$D->tabnums['feeds'].'</small>'):'' ?></span></div>
					<?php } ?>
					<div class="<?= $D->show=='bookmarks'?'current':'' ?>"><span><a href="<?= $C->SITE_URL ?>dashboard/show:bookmarks"><?= $this->lang('iphone_dbrd_menu_bookmarks') ?></a></span></div>
					<div class="<?= $D->show=='everybody'?'current':'' ?>"><span><a href="<?= $C->SITE_URL ?>dashboard/show:everybody"><?= $this->lang('iphone_dbrd_menu_everybody') ?></a></span></div>
				</div>
			</div>
			<div id="posts">
				<?php if( $D->num_results == 0 ) { ?>
					<div class="alert yellow"><?= $this->lang('dbrd_nores_'.$D->show) ?></div>
				<?php } else { ?>
					<?= $D->posts_html ?>
				<?php } ?>
			</div>
			<?php if( $D->num_results > $D->posts_number ) { ?>
			<div id="loadmore">
				<div id="loadmoreloader" style="display:none;"></div>
				<a id="loadmorelink" href="javascript:;" onclick="load_more_results('posts', <?= $D->posts_number ?>, <?= $D->num_results ?>);"><b><strong><?= $this->lang('iphone_paging_posts') ?></strong></b></a>
			</div>
			<?php } ?>
		</div>
<?php
	
	$this->load_template('mobile_iphone/footer.php');
	
?>