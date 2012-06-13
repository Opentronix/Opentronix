<?php
	
	$this->load_template('mobile_iphone/header.php');
	
?>
		<div id="profile">
			<div id="profile_avatar"><img src="<?= $C->TMP_URL.$D->g_avatar ?>"/></div>
			<div id="profile_text">
				<h1><?= htmlspecialchars($D->g->title) ?></h1>
				<strong>
					<?= $this->lang($D->g->is_private ? 'iphone_group_private' : 'iphone_group_public') ?>
					&middot;
					<?= $this->lang($D->g->num_followers==1 ? 'iphone_group_member1' : 'iphone_group_members', array('#NUM#'=>$D->g->num_followers)) ?>
					&middot;
					<?= $this->lang($D->g->num_posts==1 ? 'iphone_group_post1' : 'iphone_group_posts', array('#NUM#'=>$D->g->num_posts)) ?>
				</strong>
				<div id="profileicons">
					<a href="javascript:;" id="lnkfo" style="<?= $D->i_am_member?'display:none;':'' ?>" onclick="group_follow('<?= $D->g->groupname ?>',this,'lnkuf', '<?= addslashes($this->lang('iphone_group_follow',array('#GROUP#'=>$D->g->title))) ?>');" class="pri_add"></a>
					<a href="javascript:;" id="lnkuf" style="<?= !$D->i_am_member?'display:none;':($D->i_am_adming&&$D->num_admins==1?'display:none;':'') ?>" onclick="group_unfollow('<?= $D->g->groupname ?>',this,'lnkfo', '<?= addslashes($this->lang('iphone_group_unfollow',array('#GROUP#'=>$D->g->title))) ?>', '<?= addslashes($this->lang('iphone_group_unfollowc',array('#GROUP#'=>$D->g->title))) ?>');" class="pri_rem"></a>
					<a href="<?= $C->SITE_URL ?>post/group:<?= $D->g->groupname ?>" id="lnknp" style="<?= !$D->i_am_member?'display:none;':'' ?>" class="pri_ptg"></a>
				</div>
			</div>
		</div>
		<div id="profilenav">
			<a href="<?= $C->SITE_URL.$D->g->groupname ?>" style="width:35%;" class="<?= $D->show=='updates'?'onpnav':'' ?>"><b><?= $this->lang('iphone_group_menu_updates') ?></b></a>
			<a href="<?= $C->SITE_URL.$D->g->groupname ?>/show:members" style="width:35%;" class="<?= $D->show=='members'?'onpnav':'' ?>"><b><?= $this->lang('iphone_group_menu_members') ?></b></a>
			<a href="<?= $C->SITE_URL.$D->g->groupname ?>/show:admins" style="width:30%;" class="<?= $D->show=='admins'?'onpnav':'' ?>"><b><?= $this->lang('iphone_group_menu_admins') ?></b></a>
		</div>
		
		<?php if( $D->show == 'updates' ) { ?>
			
			<div id="postspage" style="border-top:0px;">
				<div id="posts">
					<?php if( $D->num_results == 0 ) { ?>
						<div class="alert yellow"><?= $this->lang('iphone_group_no_posts', array('#GROUP#'=>$D->g->title)) ?></div>
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
			
		<?php } elseif( $D->show == 'members' || $D->show == 'admins' ) { ?>
			
			<div id="listpage">
			<?php if(  $D->num_results == 0 ) { ?>
				<div class="alert yellow"><?= $this->lang($D->show=='admins'?'iphone_group_no_admins':'iphone_group_no_members', array('#GROUP#'=>$D->g->title)) ?></div>
			<?php } else { ?>
				<div id="prlist">
					<div id="prlist2">
						<div id="prlist3">
							<div id="prlist4">
								<div id="prlist5">
									<div id="prlist6">
										<?= $D->users_html ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php if( $D->num_pages > 1 ) { ?>
				<div id="nextback">
					<div id="nextback2">
						<div id="nextback3">
							<?php if( $D->pg > 1 ) { ?>
							<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:<?= $D->show ?>/pg:<?= $D->pg-1 ?>" class="nb_back"><?= $this->lang('iphone_paging_back') ?></a>
							<?php } ?>
							<?php if( $D->pg < $D->num_pages ) { ?>
							<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:<?= $D->show ?>/pg:<?= $D->pg+1 ?>" class="nb_next"><?= $this->lang('iphone_paging_next') ?></a>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php } ?>
			<?php } ?>	
			</div>
			
		<?php } ?>
		
<?php
	
	$this->load_template('mobile_iphone/footer.php');
	
?>