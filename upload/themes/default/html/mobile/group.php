<?php
	
	$this->load_template('mobile/header.php');
	
?>
	<div id="profilehdr">
		<img src="<?= $C->IMG_URL.'avatars/thumbs3/'.$D->g->avatar ?>" alt="<?= htmlspecialchars($D->g->title) ?>" align="left" />
		<div id="profilehdrinfo">
			<h3><?= $D->g->title ?></h3>
			
			<?= $this->lang( $D->g->is_private ? 'group_subttl_type_private' : 'group_subttl_type_public' )  ?> &middot;
			<?= $this->lang( $D->num_members==1 ? 'group_subttl_nm_members1' : 'group_subttl_nm_members', array('#NUM#'=>$D->num_members) ) ?> &middot;
			<?= $this->lang( $D->g->num_posts==1 ? 'group_subttl_nm_posts1' : 'group_subttl_nm_posts', array('#NUM#'=>$D->g->num_posts) ) ?>
						
			
		</div>
		<div class="klear"></div>
	</div>
	<hr />
	<div id="select">
		<form method="get" action="<?= $C->SITE_URL.$D->g->groupname ?>/">
			<select name="show" onchange="this.form.submit();">
				<option value="updates" <?= $D->show=='updates'?'selected="selected"':'' ?>><?= $this->lang('group_menu_updates') ?></option>
				<option value="members" <?= $D->show=='members'?'selected="selected"':'' ?>><?= $this->lang('group_menu_members') ?></option>
				<option value="admins" <?= $D->show=='admins'?'selected="selected"':'' ?>><?= $this->lang('group_menu_admins') ?></option>
			</select>
			<input type="submit" value="<?= $this->lang('group_menu_submit') ?>" />
		</form>
	</div>
	<hr />
	<?php if( isset($_GET['do_join']) ) { ?>
		<div class="okbox" style="margin-top:10px;"><?= $this->lang('group_ok_join', array('#GROUP#'=>$D->g->title)) ?></div>
		<hr />
	<?php } else if( isset($_GET['do_leave']) ) { ?>
		<div class="okbox" style="margin-top:10px;"><?= $this->lang('group_ok_leave', array('#GROUP#'=>$D->g->title)) ?></div>
		<hr />
	<?php } ?>
	<?php if( 0 == $D->num_results ) { ?>
		<div class="msgbox" style="margin-top:10px;"><?= $this->lang('group_errmsg_'.$D->show) ?></div>
	<?php } elseif( $D->show == 'updates' ) { ?>
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
	<?php } elseif( $D->show == 'members' || $D->show == 'admins' ) { ?>
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
	<?php } ?>
	<hr />
	<?php if( !$D->i_am_member ) { ?>
	<div id="profileftr">
		<a href="<?= $D->paging_url.$D->pg.'&do_join=1' ?>" class="follow"><?= $this->lang('group_ftr_join', array('#GROUP#'=>$D->g->title)) ?></a>
	</div>
	<?php } ?>
	
	<?php if( $D->i_am_member && $this->user->if_can_leave_group($D->g->id) ) { ?>
	<div id="profileftr">
		<a href="<?= $D->paging_url.$D->pg.'&do_leave=1' ?>" class="stopfollow" onclick="return confirm('<?= $this->lang('group_ftr_leave_c', array('#GROUP#'=>$D->g->title)) ?>');"><?= $this->lang('group_ftr_leave', array('#GROUP#'=>$D->g->title)) ?></a>
	</div>
	<?php } ?>
<?php
	
	$this->load_template('mobile/footer.php');
	
?>