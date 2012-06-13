<?php
	
	$this->load_template('header.php');
	
?>
		<?php if( $D->i_am_member ) { ?>
		<script type="text/javascript">
			pf_hotkeyopen_loadgroup	= "<?= $D->g->title ?>";
		</script>
		<?php } ?>
		<?php if( $this->param('msg') == 'join' ) { ?>
		<script type="text/javascript">
			slim_msgbox("<?= addslashes($this->lang('msg_follow_group_on',array('#GROUP#'=>$D->g->title))) ?>");
		</script>
		<?php } elseif( $this->param('msg') == 'leave' ) { ?>
		<script type="text/javascript">
			slim_msgbox("<?= addslashes($this->lang('msg_follow_group_off',array('#GROUP#'=>$D->g->title))) ?>");
		</script>
		<?php } ?>
		<?php if($this->param('msg')=='created') { ?>
		<?= okbox($this->lang('group_justcreated_box_ttl'), $this->lang('group_justcreated_box_txt',array('#A1#'=>'<a href="'.userlink($D->g->groupname).'/invite">','#A2#'=>'</a>','#A3#'=>'<a href="'.userlink($D->g->groupname).'/tab:settings">','#A4#'=>'</a>',)) ) ?>
		<?php } elseif($this->param('msg')=='invited') { ?>
		<?= okbox($this->lang('group_invited_box_ttl'), $this->lang('group_invited_box_txt') ) ?>
		<?php } ?>
		<div id="profile">
			<div id="profile2">
				<div id="profile_left">
					<div id="profileavatar"><img src="<?= $C->IMG_URL.'avatars/'.$D->g->avatar ?>" alt="" /></div>
					<?php if( !empty($D->g->about_me) ) { ?>
					<div class="greygrad">
						<div class="greygrad2">
							<div class="greygrad3">
								<?= $D->about_me ?>
							</div>
						</div>
					</div>
					<?php } ?>
					<div class="ttl" style="margin-bottom:8px;">
						<div class="ttl2">
							<h3><?= $this->lang('group_left_members') ?></h3>
							<?php if( $D->num_members > 6 ) { ?>
							<a href="<?= userlink($D->g->groupname) ?>/tab:members" class="ttlink"><?= $this->lang('group_left_viewall') ?></a>
							<? } ?>
						</div>
					</div>
					<div class="slimusergroup">
						<?php foreach($D->some_members as $u) { ?>
						<a href="<?= userlink($u->username) ?>" class="slimuser" title="<?= htmlspecialchars($u->username) ?>"><img src="<?= $C->IMG_URL ?>avatars/thumbs1/<?= $u->avatar ?>" alt="" /></a>
						<?php } ?>
					</div>
					<div class="ttl" style="margin-bottom:8px; margin-top:4px;">
						<div class="ttl2">
							<h3><?= $this->lang('group_left_admins') ?></h3>
							<?php if( $D->num_admins > 3 ) { ?>
							<a href="<?= userlink($D->g->groupname) ?>/tab:members/filter:admins" class="ttlink"><?= $this->lang('group_left_viewall') ?></a>
							<? } ?>
						</div>
					</div>
					<div class="slimusergroup">
						<?php foreach($D->some_admins as $u) { ?>
						<a href="<?= userlink($u->username) ?>" class="slimuser" title="<?= htmlspecialchars($u->username) ?>"><img src="<?= $C->IMG_URL ?>avatars/thumbs1/<?= $u->avatar ?>" alt="" /></a>
						<?php } ?>
					</div>
					
					<?php if( count($D->post_tags) > 0 ) { ?>
					<div class="ttl" style="margin-top:5px; margin-bottom:5px;"><div class="ttl2"><h3><?= $this->lang('group_left_posttags') ?></h3></div></div>
					<div class="taglist">
						<?php foreach($D->post_tags as $tmp) { ?>
						<a href="<?= $C->SITE_URL ?>search/posttag:%23<?= $tmp ?>" title="#<?= htmlspecialchars($tmp) ?>"><small>#</small><?= htmlspecialchars(str_cut($tmp,25)) ?></a>
						<?php } ?>
					</div>
					<?php } ?>
					
					<?php if( $D->i_can_invite ) { ?>
					<div class="greygrad" style="margin-top:3px;">
						<div class="greygrad2">
							<div class="greygrad3">
								<?= $this->lang('group_left_invite_txt', array('#GROUP#'=>htmlspecialchars($D->g->title))) ?>
								<div class="klear"></div>
								<a href="<?= userlink($D->g->groupname) ?>/invite" class="ubluebtn"><b><?= $this->lang('group_left_invite_btn') ?></b></a>
							</div>
						</div>
					</div>
					<?php } ?>
					
				</div>
				<div id="profile_right">
					<div id="profilehdr">
						<?php if($this->user->is_logged) { ?>
						<div id="usermenu">
							<a href="javascript:;" onclick="postform_open(({groupname:'<?= $D->g->title ?>'}));" class="um_ptg" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('grp_toplnks_post',array('#GROUP#'=>$D->g->title)) ?></b></a>
							<?php if( ! $D->i_am_member ) { ?>
							<a href="<?= userlink($D->g->groupname) ?>/tab:<?= $D->tab ?>/act:join" id="grppg_btn_follow" class="um_joingr" onfocus="this.blur();" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('grp_toplnks_follow',array('#GROUP#'=>$D->g->title)) ?></b></a>
							<?php } elseif( $this->user->if_can_leave_group($D->g->id) ) { ?>
							<a href="<?= userlink($D->g->groupname) ?>/tab:<?= $D->tab ?>/act:leave" id="grppg_btn_unfollow" onclick="return confirm('<?= $this->lang('group_unfollow_confirm',array('#GROUP#'=>$D->g->groupname)) ?>');" class="um_leavegr" onfocus="this.blur();" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('grp_toplnks_unfollow',array('#GROUP#'=>$D->g->title)) ?></b></a>
							<?php } ?>
							<div id="usrpg_top_tooltip" class="umtt" style="display:none;"><div></div></div>
						</div>
						<?php } ?>
						<h2><?= htmlspecialchars(str_cut($D->g->title,25)) ?></h2>
						<span>
						<?= $this->lang( $D->g->is_private ? 'group_subtitle_type_private' : 'group_subtitle_type_public' )  ?> &middot;
						<?= $this->lang( $D->num_members==1 ? 'group_subtitle_nm_members1' : 'group_subtitle_nm_members', array('#NUM#'=>$D->num_members) ) ?> &middot;
						<?= $this->lang( $D->g->num_posts==1 ? 'group_subtitle_nm_posts1' : 'group_subtitle_nm_posts', array('#NUM#'=>$D->g->num_posts) ) ?>
						</span>
						<div id="profilenav">
							<a href="<?= userlink($D->g->groupname) ?>" class="<?= $D->tab=='updates'?'onptab':'' ?>"><b><?= $this->lang('grp_tab_updates') ?></b></a>
							<a href="<?= userlink($D->g->groupname) ?>/tab:members" class="<?= $D->tab=='members'?'onptab':'' ?>"><b><?= $this->lang('grp_tab_members') ?></b></a>
							<?php if( $D->i_am_admin ) { ?>
							<a href="<?= userlink($D->g->groupname) ?>/tab:settings" class="<?= $D->tab=='settings'||$D->tab=='deletegroup'?'onptab':'' ?>"><b><?= $this->lang('grp_tab_settings') ?></b></a>
							<?php } ?>
							<?php if($D->tab == 'updates' && $D->i_am_member) { ?>
							<a href="<?= $C->SITE_URL ?>rss/groupname:<?= $D->g->groupname ?>" id="rssicon" title="<?= $this->lang('group_updates_rss_dsc',array('#GROUP#'=>$D->g->groupname)) ?>" target="_blank"><?= $this->lang('group_updates_rss') ?></a>
							<?php } ?>
						</div>
					</div>
					
				<?php if( $D->tab == 'updates' ) { ?>
					<div class="ttl" style="margin-top:8px; margin-bottom:6px;">
						<div class="ttl2">
							<h3><?= $this->lang('group_title_updates',array('#GROUP#'=>htmlspecialchars($D->g->title))) ?></h3>
							<div id="postfilter">
								<a href="javascript:;" onclick="dropdiv_open('postfilteroptions');" id="postfilterselected" onfocus="this.blur();"><span><?= $this->lang('posts_filter_'.$D->filter) ?></span></a>
								<div id="postfilteroptions" style="display:none;">
									<a href="<?= userlink($D->g->groupname) ?>"><?= $this->lang('posts_filter_all') ?></a>
									<a href="<?= userlink($D->g->groupname) ?>/filter:links"><?= $this->lang('posts_filter_links') ?></a>
									<a href="<?= userlink($D->g->groupname) ?>/filter:images"><?= $this->lang('posts_filter_images') ?></a>
									<a href="<?= userlink($D->g->groupname) ?>/filter:videos"><?= $this->lang('posts_filter_videos') ?></a>
									<a href="<?= userlink($D->g->groupname) ?>/filter:files" style="border-bottom:0px;"><?= $this->lang('posts_filter_files') ?></a>
								</div>
								<span><?= $this->lang('posts_filter_ttl') ?></span>
							</div>		
						</div>
					</div>
					<?php if($this->param('msg')=='deletedpost') { ?>
					<?= okbox($this->lang('msg_post_deleted_ttl'), $this->lang('msg_post_deleted_txt'), TRUE, 'margin-bottom:6px;') ?>
					<?php } ?>
					<div id="userposts">
						<div id="posts_html">
							<?= $D->posts_html ?>
						</div>
					</div>
				<?php } elseif( $D->tab == 'members' ) { ?>
					<div class="htabs" style="margin-bottom:6px;">
						<a href="<?= userlink($D->g->groupname) ?>/tab:members/filter:all" class="<?= $D->filter=='all'?'onhtab':'' ?>"><b><?= $this->lang('group_tab_members_all') ?> <small>(<?= $D->num_members ?>)</small></b></a>
						<a href="<?= userlink($D->g->groupname) ?>/tab:members/filter:admins" class="<?= $D->filter=='admins'?'onhtab':'' ?>"><b><?= $this->lang('group_tab_members_admins') ?> <small>(<?= $D->num_admins ?>)</small></b></a>
					</div>
					<div id="grouplist">
						<?= $D->users_html ?>
					</div>
				<?php } elseif( $D->tab == 'settings' ) { ?>
					<script type="text/javascript" src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/js/inside_admintools.js"></script>
					<div class="htabs" style="margin-bottom:6px;">
						<a href="<?= userlink($D->g->groupname) ?>/tab:settings/subtab:main" class="<?= $D->subtab=='main'?'onhtab':'' ?>"><b><?= $this->lang('group_sett_subtabs_main') ?></b></a>
						<?php if( function_exists('curl_init') ) { ?>
						<a href="<?= userlink($D->g->groupname) ?>/tab:settings/subtab:rssfeeds" class="<?= $D->subtab=='rssfeeds'?'onhtab':'' ?>"><b><?= $this->lang('group_sett_subtabs_rssfeeds') ?> <small>(<?= $D->num_rssfeeds ?>)</small></b></a>
						<?php } ?>
						<a href="<?= userlink($D->g->groupname) ?>/tab:settings/subtab:admins" class="<?= $D->subtab=='admins'?'onhtab':'' ?>"><b><?= $this->lang('group_sett_subtabs_admins') ?> <small>(<?= $D->num_admins ?>)</small></b></a>
						<?php if($D->g->is_private) { ?>
						<a href="<?= userlink($D->g->groupname) ?>/tab:settings/subtab:privmembers" class="<?= $D->subtab=='privmembers'?'onhtab':'' ?>"><b><?= $this->lang('group_sett_subtabs_privmembers') ?></b></a>
						<?php } ?>
						<a href="<?= userlink($D->g->groupname) ?>/tab:settings/subtab:delgroup" class="<?= $D->subtab=='delgroup'?'onhtab':'' ?>"><b><?= $this->lang('group_sett_subtabs_delgroup') ?></b></a>
					</div>
					<?php if( $D->subtab == 'main' ) { ?>
						<?php if( $D->error ) { ?>
						<?= errorbox($this->lang('group_settings_f_err'), $this->lang($D->errmsg, array('#SITE_TITLE#'=>$C->SITE_TITLE)), TRUE, 'margin-top:5px; margin-bottom:4px;') ?>
						<?php } elseif( $D->submit ) { ?>
						<?= okbox($this->lang('group_settings_f_ok'), $this->lang('group_settings_f_oktxt'), TRUE, 'margin-top:5px; margin-bottom:4px;') ?>
						<?php } ?>
						<div class="greygrad" style="margin-top:6px;">
							<div class="greygrad2">
								<div class="greygrad3" style="padding-bottom:0px;">
									<form method="post" action="<?= userlink($D->g->groupname) ?>/tab:settings/subtab:main" enctype="multipart/form-data">
										<table id="setform" cellspacing="5">
											<tr>
												<td width="80" class="setparam"><?= $this->lang('group_settings_f_title') ?></td>
												<td><input type="text" class="setinp" name="form_title" value="<?= htmlspecialchars($D->form_title) ?>" maxlength="30" style="width:320px; padding:3px;" /></td>
											</tr>
											<tr>
												<td class="setparam"><?= $this->lang('group_settings_f_url') ?></td>
												<td><?= $C->SITE_URL ?><input type="text" name="form_groupname" value="<?= htmlspecialchars($D->form_groupname) ?>" class="setinp" maxlength="30" style="width:120px; padding:3px; margin-left:2px;" /></td>
											</tr>
											<tr>
												<td class="setparam" valign="top"><?= $this->lang('group_settings_f_descr') ?></td>
												<td><textarea style="width:326px;" name="form_description"><?= htmlspecialchars($D->form_description) ?></textarea></td>
											</tr>
											<tr>
												<td class="setparam" valign="top"><?= $this->lang('group_settings_f_type') ?></td>
												<td>
													<label><input type="radio" name="form_type" value="public" <?= $D->form_type=='public'?'checked="checked"':'' ?> /><span><?= $this->lang('group_settings_f_tp_public') ?></span></label>
													<label><input type="radio" name="form_type" value="private" <?= $D->form_type=='private'?'checked="checked"':'' ?> /><span><?= $this->lang('group_settings_f_tp_private') ?></span></label>
												</td>
											</tr>
											<tr>
												<td class="setparam"><?= $this->lang('group_settings_f_avatar') ?></td>
												<td><input type="file" name="form_avatar" value="" class="setinp" style="padding:3px; margin-left:2px;" /></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" value="<?= $this->lang('group_settings_f_btn') ?>" name="sbm" style="padding:4px; font-weight:bold;" /></td>
											</tr>
										</table>
									</form>
								</div>
							</div>
						</div>
					<?php } elseif( $D->subtab == 'rssfeeds' ) { ?>
						<?php if( $this->param('msg') == 'added' ) { ?>
							<?= okbox($this->lang('group_feedsett_ok'), $this->lang('group_feedsett_ok_txt'), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
						<?php } elseif( $this->param('msg') == 'deleted' ) { ?>
							<?= okbox($this->lang('group_feedsett_ok'), $this->lang('group_feedsett_okdel_txt'), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
						<?php } ?>
						<?php if( count($D->feeds) > 0 ) { ?>
						<div class="greygrad" style="margin-top:6px;">
							<div class="greygrad2">
								<div class="greygrad3">
									<table id="setform" cellspacing="0" cellpadding="5">
										<tr>
											<td width="110" class="setparam" valign="top"><?= $this->lang('group_feedsett_feedslist') ?></td>
											<td width="400">
												<div class="groupfeedslist">
												<?php foreach($D->feeds as $f) { ?>
												<div class="groupfeed">
													<a href="<?= userlink($D->g->groupname).'/tab:settings/subtab:rssfeeds/delfeed:'.$f->id ?>" onclick="return confirm('<?= $this->lang('group_feedsett_feed_delcnf') ?>');" title="<?= $this->lang('group_feedsett_feed_delete') ?>" onfocus="this.blur();" class="grpdelbtn"></a>
													<?= htmlspecialchars(str_cut($f->feed_title,35)) ?>				
													<span><a href="<?= htmlspecialchars($f->feed_url) ?>" target="_blank"><?= htmlspecialchars(str_cut_link($f->feed_url,50)) ?></a></span>
													<?php if( !empty($f->filter_keywords) ) { ?>
													<span><?= $this->lang('group_feedsett_feed_filter') ?> <?= htmlspecialchars($f->filter_keywords) ?></span>
													<?php } ?>
												</div>
												<?php } ?>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<?php } ?>
						<div class="ttl" style="margin-top:8px; margin-bottom:6px;"><div class="ttl2"><h3><?= $this->lang('group_feedsett_f_title') ?></h3></div></div>
						<?php if( $D->error ) { ?>
							<?= errorbox($this->lang('group_feedsett_err'), $this->lang($D->errmsg), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
						<?php } elseif( $D->newfeed_auth_msg ) { ?>
							<?= msgbox($this->lang('group_feedsett_pwdreq_ttl'), $this->lang('group_feedsett_pwdreq_txt'), TRUE, 'margin-top:5px;margin-bottom:5px;') ?>
						<?php } ?>
						<div class="greygrad" style="margin-top:6px;">
							<div class="greygrad2">
								<div class="greygrad3" style="padding-bottom:0px;">
									<form method="post" action="<?= userlink($D->g->groupname).'/tab:settings/subtab:rssfeeds' ?>">
										<table id="setform" cellspacing="0" cellpadding="5">
											<tr>
												<td width="110" class="setparam"><?= $this->lang('group_feedsett_f_url') ?></td>
												<td><input type="text" class="setinp" name="newfeed_url" value="<?= htmlspecialchars($D->newfeed_url) ?>" maxlength="255" style="width:320px; padding:3px;" /></td>
											</tr>
											<?php if( $D->newfeed_auth_req ) { ?>
											<tr>
												<td class="setparam"><?= $this->lang('group_feedsett_f_usr') ?></td>
												<td><input type="text" class="setinp" name="newfeed_username" value="<?= htmlspecialchars($D->newfeed_username) ?>" autocomplete="off" maxlength="255" style="width:320px; padding:3px;" /></td>
											</tr>
											<tr>
												<td class="setparam"><?= $this->lang('group_feedsett_f_pwd') ?></td>
												<td><input type="password" class="setinp" name="newfeed_password" value="<?= htmlspecialchars($D->newfeed_password) ?>" autocomplete="off" maxlength="255" style="width:320px; padding:3px;" /></td>
											</tr>
											<?php } ?>
											<tr>
												<td style="padding-bottom:0px;" class="setparam"><?= $this->lang('group_feedsett_f_filter') ?></td>
												<td style="padding-bottom:0px;"><input type="text" class="setinp" name="newfeed_filter" value="<?= htmlspecialchars($D->newfeed_filter) ?>" maxlength="255" style="width:320px; padding:3px;" /></td>
											</tr>
											<tr>
												<td style="padding:0px;"></td>
												<td class="setparam" style="text-align:left; font-size:10px; padding-top:2px;"><?= $this->lang('group_feedsett_f_filtertxt') ?></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" name="sbm" value="<?= $this->lang('group_feedsett_f_submit') ?>" style="padding:4px; font-weight:bold;" /></td>
											</tr>
										</table>
									</form>
								</div>
							</div>
						</div>
					<?php } elseif( $D->subtab == 'admins' ) { ?>
						<?php if( $this->param('msg')=='admsaved' ) { ?>
						<?= okbox($this->lang('group_admsett_f_ok'), $this->lang('group_admsett_f_ok_txt'), TRUE, 'margin-top:5px; margin-bottom:4px;') ?>
						<?php } ?>
						<div class="greygrad" style="margin-top:6px;">
							<div class="greygrad2">
								<div class="greygrad3" style="padding-bottom:0px;">
									<table id="setform" cellspacing="5">
										<tr>
											<td width="110" class="setparam" valign="top"><?= $this->lang('group_admsett_f_adm') ?></td>
											<td width="400">
												<div class="groupadmins">
													<div class="addadmins"><?= $this->lang('group_admsett_f_adm_you') ?></div>
												</div>
												<div id="group_admins_list"></div>
											</td>
										</tr>
										<tr>
											<td class="setparam"><?= $this->lang('group_admsett_f_add') ?></td>
											<td>
												<input type="text" id="addadmin_inp" name="username" value="" style="width:200px;" rel="autocomplete" autocompleteoffset="0,3" />
												<input type="button" id="addadmin_btn" onclick="group_admins_add(); return false;" value="<?= $this->lang('group_admsett_f_add_btn') ?>" />
											</td>
										</tr>
										<tr>
											<td></td>
											<td>
												<form method="post" name="admform" action="<?= userlink($D->g->groupname) ?>/tab:settings/subtab:admins">
													<input type="hidden" name="admins" value="" />
													<input type="submit" value="<?= $this->lang('group_admsett_f_btn') ?>" style="padding:4px; font-weight:bold;" />
												</form>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<script type="text/javascript">
							jserr_add_admin_invalid_user	= "<?= $this->lang('group_admsett_jserr_user1', array('#GROUP#'=>$D->g->title)) ?>";
							jserr_add_admin_not_member	= "<?= $this->lang('group_admsett_jserr_user2', array('#GROUP#'=>$D->g->title)) ?>";
							jsconfirm_admin_remove		= "<?= $this->lang('group_admsett_jscnf_del') ?>";
							js_group_members	= ",<?= implode(',', $D->jsmembers) ?>,";
							<?php foreach($D->admins as $u) { if($u->id==$this->user->id) { continue; } ?>
							group_admins_putintolist("<?= $u->username ?>");
							<?php } ?>
						</script>
					<?php } elseif( $D->subtab == 'privmembers' ) { ?>
						<?php if( $this->param('msg')=='mmbsaved' ) { ?>
						<?= okbox($this->lang('group_privmmb_f_ok'), $this->lang('group_privmmb_f_ok_txt'), TRUE, 'margin-top:5px; margin-bottom:4px;') ?>
						<?php } ?>
						<div class="greygrad" style="margin-top:6px;">
							<div class="greygrad2">
								<div class="greygrad3" style="padding-bottom:0px;">
									<?= $this->lang('group_privmmb_title') ?>
									<table id="setform" cellspacing="5">
										<tr>
											<td width="110" class="setparam" valign="top"><?= $this->lang('group_privmmb_f_curr') ?></td>
											<td width="400">
												<div class="groupadmins">
													<div class="addadmins"><?= $this->lang('group_privmmb_f_curr_you') ?></div>
													<?php foreach($D->cannot_be_removed as $u) { if($u->id==$this->user->id) { continue; } ?>
													<div class="addadmins"><?= $u->username ?></div>
													<?php } ?>
												</div>
												<div id="group_admins_list"></div>
											</td>
										</tr>
										<tr>
											<td></td>
											<td>
												<form method="post" name="admform" action="<?= userlink($D->g->groupname) ?>/tab:settings/subtab:privmembers">
													<input type="hidden" name="admins" value="" />
													<input type="submit" value="<?= $this->lang('group_privmmb_f_btn') ?>" style="padding:4px; font-weight:bold;" />
												</form>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<script type="text/javascript">
							jsconfirm_admin_remove		= "<?= $this->lang('group_privmmb_jscnf_del') ?>";
							<?php foreach($D->can_be_removed as $u) { if($u->id==$this->user->id) { continue; } ?>
							group_admins_putintolist("<?= $u->username ?>");
							<?php } ?>
						</script>
					<?php } elseif( $D->subtab == 'delgroup' ) { ?>
						<?php if( $D->error ) { ?>
						<?= errorbox($this->lang('group_del_f_err'), $this->lang($D->errmsg), TRUE, 'margin-top:5px; margin-bottom:4px;') ?>
						<?php } ?>
						<div class="greygrad" style="margin-top:6px;">
							<div class="greygrad2">
								<div class="greygrad3" style="padding-bottom:0px;">
									<form method="post" action="" onsubmit="return confirm('<?= $this->lang('group_del_f_btn_cnfrm') ?>');">
										<table id="setform" cellspacing="5">
											<?php if( $D->g->is_public ) { ?>
											<tr>
												<td class="setparam" valign="top"><?= $this->lang('group_del_f_posts') ?></td>
												<td>
													<label><input type="radio" name="postsact" value="keep" <?= $D->f_postsact=='keep'?'checked="checked"':'' ?> /><span><?= $this->lang('group_del_f_posts_keep') ?></span></label>
													<label><input type="radio" name="postsact" value="del" <?= $D->f_postsact=='del'?'checked="checked"':'' ?> /><span><?= $this->lang('group_del_f_posts_del') ?></span></label>
												</td>
											</tr>
											<?php } else { ?>
											<input type="hidden" name="postsact" value="del" />
											<?php } ?>
											<tr>
												<td class="setparam"><?= $this->lang('group_del_f_password') ?></td>
												<td><input type="password" class="setinp" name="password" value="" autocomplete="off" style="width:320px; padding:3px;" /></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" value="<?= $this->lang('group_del_f_btn') ?>" name="sbm" style="padding:4px; font-weight:bold;" /></td>
											</tr>
										</table>
									</form>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				</div>
			</div>
		</div>
<?php
	
	$this->load_template('footer.php');
	
?>