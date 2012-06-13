<?php
	
	$this->load_template('mobile/header.php');
	
?>
	<div id="select">
		<form method="get" action="<?= $C->SITE_URL ?>search/">
			<?= $this->lang('search_form_title') ?>&nbsp;<input type="text" name="lookfor" value="<?= htmlspecialchars($D->search_string) ?>" maxlength="100"/>
			<?= $this->lang('search_form_in') ?>&nbsp;<select name="lookin" onchange="if(this.form.lookfor.value!=''){this.form.submit();}">
				<option value="posts" <?= $D->lookin=='posts'?'selected="selected"':'' ?>><?= $this->lang('search_menu_posts') ?></option>
				<option value="users" <?= $D->lookin=='users'?'selected="selected"':'' ?>><?= $this->lang('search_menu_users') ?></option>
				<option value="groups" <?= $D->lookin=='groups'?'selected="selected"':'' ?>><?= $this->lang('search_menu_groups') ?></option>
			</select>
			<input type="submit" value="<?= $this->lang('search_form_submit') ?>" />
		</form>
	</div>
	<hr />
	<?php if( 0==$D->num_results && !empty($D->search_string) ) { ?>
		<div class="msgbox" style="margin-top:10px;"><?= $this->lang('search_nores') ?></div>
	<?php } elseif( $D->lookin=='users' && !empty($D->search_string) ) { ?>
	<div id="members">
		<?= $D->users_html ?>
		<?php if( $D->num_pages>1 && $D->pg>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('members_paging_prev') ?></a> |
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('members_paging_next') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg>1 ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('members_paging_prev') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('members_paging_next') ?></a>
		</div>
		<?php } ?>
	</div>
	<?php } elseif( $D->lookin=='groups' && !empty($D->search_string) ) { ?>
	<div id="members">
		<?= $D->groups_html ?>
		<?php if( $D->num_pages>1 && $D->pg>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('groups_paging_prev') ?></a> |
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('groups_paging_next') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg>1 ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('groups_paging_prev') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('groups_paging_next') ?></a>
		</div>
		<?php } ?>
	</div>
	<?php } elseif( $D->lookin=='posts' && !empty($D->search_string) ) { ?>
		<?= $D->posts_html ?>
		<?php if( $D->num_pages>1 && $D->pg>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('posts_paging_prev') ?></a> |
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('posts_paging_next') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg>1 ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg-1) ?>"><?= $this->lang('posts_paging_prev') ?></a>
		</div>
		<?php } elseif( $D->num_pages>1 && $D->pg<$D->num_pages ) { ?>
		<div id="backnext">
			<a href="<?= $D->paging_url.($D->pg+1) ?>"><?= $this->lang('posts_paging_next') ?></a>
		</div>
		<?php } ?>
	<?php } ?>
<?php
	
	$this->load_template('mobile/footer.php');
	
?>