<?php
	
	$this->load_template('mobile_iphone/header.php');
	
?>
		<div id="profile">
			<div id="profile_avatar"><img src="<?= $C->TMP_URL.$D->usr_avatar ?>"/></div>
			<div id="profile_text">
				<h1><?= $D->usr->username ?></h1>
				<strong><?= htmlspecialchars(empty($D->usr->position) ? $D->usr->fullname : $D->usr->position) ?></strong>
				<?php if( ! $D->is_my_profile ) { ?>
				<div id="profileicons">
					<a href="javascript:;" id="lnkfo" style="<?= $D->i_follow_him?'display:none;':'' ?>" onclick="user_follow('<?= $D->usr->username ?>',this,'lnkuf', '<?= addslashes($this->lang('iphone_user_follow',array('#USER#'=>$D->usr->username))) ?>');" class="pri_add"></a>
					<a href="javascript:;" id="lnkuf" style="<?= $D->i_follow_him?'':'display:none;' ?>" onclick="user_unfollow('<?= $D->usr->username ?>',this,'lnkfo', '<?= addslashes($this->lang('iphone_user_unfollow',array('#USER#'=>$D->usr->username))) ?>', '<?= addslashes($this->lang('iphone_user_unfollowc',array('#USER#'=>$D->usr->username))) ?>');" class="pri_rem"></a>
					<a href="<?= $C->SITE_URL ?>post/private:<?= $D->usr->username ?>" class="pri_pm"></a>
					<a href="<?= $C->SITE_URL ?>post/mention:<?= $D->usr->username ?>" onclick="postform_mention('<?= $D->usr->username ?>',true);" class="pri_at"></a>
				</div>
				<?php } ?>
			</div>
		</div>
		<div id="profilenav">
			<a href="<?= $C->SITE_URL.$D->usr->username ?>" style="width:28%;" class="<?= $D->show=='updates'?'onpnav':'' ?>"><b><?= $this->lang('iphone_user_menu_updates') ?></b></a>
			<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:info" style="width:18%;" class="<?= $D->show=='info'?'onpnav':'' ?>"><b><?= $this->lang('iphone_user_menu_info') ?></b></a>
			<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:friends" style="width:27%;" class="<?= $D->show=='friends'?'onpnav':'' ?>"><b><?= $this->lang('iphone_user_menu_friends') ?></b></a>
			<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:groups" style="width:27%;" class="<?= $D->show=='groups'?'onpnav':'' ?>"><b><?= $this->lang('iphone_user_menu_groups') ?></b></a>
		</div>
		
		<?php if( $D->show == 'updates' ) { ?>
			
			<div id="postspage" style="border-top:0px;">
				<div id="posts">
					<?php if( $D->num_results == 0 ) { ?>
						<div class="alert yellow"><?= $this->lang('iphone_user_no_posts', array('#USER#'=>$D->usr->username)) ?></div>
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
			
		<?php } elseif( $D->show == 'info' ) { ?>
			
			<div id="infopage">
				
				<?php if( !empty($D->usr->about_me) ) { ?>
				<b><?= $this->lang('iphone_user_info_about') ?></b>
				<div id="aboutme">
					<?= htmlspecialchars($D->usr->about_me) ?>
				</div>
				<?php } ?>
				<b><?= $this->lang('iphone_user_info_details') ?></b>
				<div id="infopagepanel">
					<?php if( !empty($D->usr->location) ) { ?>
					<div><span><?= $this->lang('uinfo_location') ?></span> <?= htmlspecialchars($D->usr->location) ?></div>
					<?php } ?>
					<?php if( !empty($D->i->website) ) { ?>
					<div><span><?= $this->lang('uinfo_site') ?></span> <a href="<?= htmlspecialchars($D->i->website) ?>" target="_blank"><?= htmlspecialchars($D->i->website) ?></a></div>
					<?php } ?>
					<?php if( !empty($D->birthdate) ) { ?>
					<div><span><?= $this->lang('uinfo_birthday') ?></span> <?= $D->birthdate ?></div>
					<?php } ?>
					<?php if( !empty($D->date_lastlogin) ) { ?>
					<div><span><?= $this->lang('uinfo_lastonline') ?></span> <?= $D->date_lastlogin ?></div>
					<?php } ?>
					<div style="border:0px;"><span><?= $this->lang('uinfo_datereg') ?></span> <?= $D->date_register ?></div>
				</div>
				<?php if( $this->network->is_private ) { ?>
				<b><?= $this->lang('iphone_user_info_contacts') ?></b>
				<div id="infopagepanel">
					<div style="border:0px;"><span><?= $this->lang('uinfo_email1') ?></span> <a href="mailto:<?= $D->usr->email ?>"><?= $D->usr->email ?></a></div>
					<?php if( !empty($D->i->personal_email) && $D->usr->email!=$D->i->personal_email ) { ?>
					<div style="border:0px; border-top:1px solid #eee;"><span><?= $this->lang('uinfo_email2') ?></span> <a href="mailto:<?= htmlspecialchars($D->i->personal_email) ?>"><?= htmlspecialchars($D->i->personal_email) ?></a></div>
					<?php } ?>
					<?php if( !empty($D->i->work_phone) ) { ?>
					<div style="border:0px; border-top:1px solid #eee;"><span><?= $this->lang('uinfo_phone1') ?></span> <?= htmlspecialchars($D->i->work_phone) ?></div>
					<?php } ?>
					<?php if( !empty($D->i->personal_phone) && $D->i->personal_phone!=$D->i->work_phone ) { ?>
					<div style="border:0px; border-top:1px solid #eee;"><span><?= $this->lang('uinfo_phone2') ?></span> <?= htmlspecialchars($D->i->personal_phone) ?></div>
					<?php } ?>
				</div>
				<?php } ?>
				<?php if( count($D->i->prs) > 0 ) { ?>
				<b><?= $this->lang('iphone_user_info_profiles') ?></b>
				<div id="infopagepanel" class="exps">
					<?php $i=0; foreach($D->i->prs as $k=>$v) { $i++; ?>
						<a class="<?= $k ?>" href="<?= htmlspecialchars($v[0]) ?>" target="_blank" style="<?= $i==count($D->i->prs)?'border:0px;':'' ?>"><?= htmlspecialchars($v[1]) ?></a>
					<?php } ?>
				</div>
				<?php } ?>
				<?php foreach( $D->i->ims as $k=>$v ) { if(empty($v)) unset($D->i->ims[$k]); } ?>
				<?php if( count($D->i->ims) > 0 ) { ?>
				<b><?= $this->lang('iphone_user_info_messengers') ?></b>
				<div id="infopagepanel" class="mesgrs" style="margin-bottom:0px;">
					<?php $i=0; foreach($D->i->ims as $k=>$v) { $i++; ?>
						<div class="<?= $k ?>" style="<?= $i==count($D->i->ims)?'border:0px;':'' ?>"><?= htmlspecialchars($v) ?></div>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
			
		<?php } elseif( $D->show == 'friends' ) { ?>
			
			<div id="listpage">
				<div id="listfilter">
					<a href="javascript:;" onclick="toggle_listfilter();" id="listfilterchosen"><b><strong><?= $this->lang($D->filter=='followers'?'iphone_user_submenu_followers':'iphone_user_submenu_following', array('#USER#'=>$D->usr->username)) ?> <span>&middot; <?= $D->filter=='followers'?$D->num_followers:$D->num_following ?></span></strong></b></a>
					<div id="listfilteroptions" style="display:none;">
						<div id="listfilteroptions2">
							<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:friends/filter:followers"><?= $this->lang('iphone_user_submenu_followers',array('#USER#'=>$D->usr->username)) ?> <span>&middot; <?= $D->num_followers ?></span></a>
							<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:friends/filter:following" style="border-bottom:0px;"><?= $this->lang('iphone_user_submenu_following',array('#USER#'=>$D->usr->username)) ?> <span>&middot; <?= $D->num_following ?></span></a>
						</div>
					</div>
				</div>
				<?php if(  $D->num_results == 0 ) { ?>
					<div class="alert yellow"><?= $this->lang($D->filter=='followers'?'iphone_user_no_followers':'iphone_user_no_following', array('#USER#'=>$D->usr->username)) ?></div>
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
								<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:friends/filter:<?= $D->filter ?>/pg:<?= $D->pg-1 ?>" class="nb_back"><?= $this->lang('iphone_paging_back') ?></a>
								<?php } ?>
								<?php if( $D->pg < $D->num_pages ) { ?>
								<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:friends/filter:<?= $D->filter ?>/pg:<?= $D->pg+1 ?>" class="nb_next"><?= $this->lang('iphone_paging_next') ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } ?>
				<?php } ?>	
			</div>
			
		<?php } elseif( $D->show == 'groups' ) { ?>
			
			<div id="listpage">
				<?php if( $D->num_results == 0 ) { ?>
					<div class="alert yellow"><?= $this->lang('iphone_user_no_groups', array('#USER#'=>$D->usr->username)) ?></div>
				<?php } else { ?>
					<div id="prlist">
						<div id="prlist2">
							<div id="prlist3">
								<div id="prlist4">
									<div id="prlist5">
										<div id="prlist6">
											<?= $D->groups_html ?>
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
								<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:groups/pg:<?= $D->pg-1 ?>" class="nb_back"><?= $this->lang('iphone_paging_back') ?></a>
								<?php } ?>
								<?php if( $D->pg < $D->num_pages ) { ?>
								<a href="<?= $C->SITE_URL.$D->usr->username ?>/show:groups/pg:<?= $D->pg+1 ?>" class="nb_next"><?= $this->lang('iphone_paging_next') ?></a>
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