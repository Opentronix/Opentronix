<?php
	
	$this->load_template('header.php');
	
?>
		<div id="profile">
			<div id="profile2">
				<div id="profile_left">
					<?php if($D->is_my_profile) { ?>
					<div id="profileavatar"><a href="<?= $C->SITE_URL ?>settings/avatar"><img src="<?= $C->IMG_URL.'avatars/'.$D->usr->avatar ?>" alt="" border="0" /></a></div>
					<?php } else { ?>
					<div id="profileavatar"><img src="<?= $C->IMG_URL.'avatars/'.$D->usr->avatar ?>" /></div>
					<?php } ?>
					<div class="ttl" style="margin-bottom:3px;">
						<div class="ttl2">
							<h3><?= $this->lang('usr_left_cnt_ttl') ?></h3>
							<?php if($D->is_my_profile) { ?>
							<a href="<?= $C->SITE_URL ?>settings/contacts" class="ttlink"><?= $this->lang('usr_left_editlink') ?></a>
							<?php } elseif($D->tab != 'info') { ?>
							<a href="<?= userlink($D->usr->username) ?>/tab:info" class="ttlink"><?= $this->lang('usr_left_cnt_more') ?></a>
							<?php } ?>
						</div>
					</div>
					<table cellpadding="0" cellspacing="3">
						<tr>
							<td class="contactparam"><img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/cicons_username.gif" alt="" title="<?= htmlspecialchars($D->usr->fullname) ?>" /></td>
							<td class="contactvalue"><?= $D->usr->username ?></td>
						</tr>
						<?php if( !empty($D->usr_website) ) { ?>
						<tr>
							<td class="contactparam"><img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/cicons_site.gif" alt="" title="<?= $this->lang('usr_left_cnt_site') ?>" /></td>
							<td class="contactvalue"><a href="<?= htmlspecialchars($D->usr_website) ?>" title="<?= htmlspecialchars($D->usr_website) ?>" target="_blank"><?= htmlspecialchars(str_cut(preg_replace('/^(http(s)?|ftp)\:\/\/(www\.)?/','',$D->usr_website),25)) ?></a></td>
						</tr>
						<?php } ?>
					</table>
					<?php if( count($D->usr->tags)>0 ) { ?>
					<div class="ttl" style="margin-top:5px; margin-bottom:5px;">
						<div class="ttl2">
							<h3><?= $this->lang('usr_left_tgsubx_ttl') ?></h3>
							<?php if($D->is_my_profile) { ?>
							<a href="<?= $C->SITE_URL ?>settings/profile" class="ttlink"><?= $this->lang('usr_left_editlink') ?></a>
							<?php } ?>
						</div>
					</div>
					<div class="taglist">
						<?php foreach($D->usr->tags as $t) { ?>
						<a href="<?= $C->SITE_URL ?>search/usertag:<?= urlencode($t) ?>" title="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars(str_cut($t, 20)) ?></a>
						<? } ?>
					</div>
					<?php } ?>
					
					<?php if( count($D->post_tags) > 0 ) { ?>
					<div class="ttl" style="margin-top:5px; margin-bottom:5px;"><div class="ttl2"><h3><?= $this->lang('usr_left_posttags') ?></h3></div></div>
					<div class="taglist">
						<?php foreach($D->post_tags as $tmp) { ?>
						<a href="<?= $C->SITE_URL ?>search/posttag:%23<?= $tmp ?>" title="#<?= htmlspecialchars($tmp) ?>"><small>#</small><?= htmlspecialchars(str_cut($tmp,25)) ?></a>
						<?php } ?>
					</div>
					<?php } ?>
					
					<?php if( count($D->some_followers) > 0 && !($D->tab=='coleagues'&&$D->filter=='followers') ) { ?>
					<div class="ttl" style="margin-bottom:8px; margin-top:4px;">
						<div class="ttl2">
							<h3><?= $this->lang('usr_left_followers') ?></h3>
							<?php if( count($D->some_followers) > 6 ) { ?>
							<a href="<?= $C->SITE_URL ?><?= $D->usr->username ?>/tab:coleagues/filter:followers" class="ttlink"><?= $this->lang('usr_left_flw_more') ?></a>
							<? } ?>
						</div>
					</div>
					<div class="slimusergroup">
						<?php $i=0; foreach($D->some_followers as $u) { ?>
						<a href="<?= $C->SITE_URL ?><?= $u->username ?>" class="slimuser" title="<?= htmlspecialchars($u->username) ?>"><img src="<?= $C->IMG_URL ?>avatars/thumbs1/<?= $u->avatar ?>" alt="" /></a>
						<?php if(++$i==6) { break; } } ?>
					</div>
					<?php } ?>
				</div>
				<div id="profile_right">
					<div id="profilehdr">
						<?php if( $this->user->is_logged ) { ?>
						<div id="usermenu">
							<?php if( $D->is_my_profile ) { ?>
							<a href="javascript:;" onclick="postform_open();" class="um_ptg" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('usr_toplnks_newpost') ?></b></a>
							<a href="<?= $C->SITE_URL ?>settings" class="um_edit" onfocus="this.blur();" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('usr_toplnks_settings') ?></b></a>
							<?php } else { ?>
							<a href="javascript:;" onclick="postform_open(({username:'<?= $D->usr->username ?>'}));" class="um_pm" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('usr_toplnks_private',array('#USERNAME#'=>$D->usr->username)) ?></b></a>
							<a href="javascript:;" onclick="postform_mention('<?= $D->usr->username ?>',true);" class="um_atuser" onfocus="this.blur();" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('usr_toplnks_mention',array('#USERNAME#'=>$D->usr->username)) ?></b></a>
							<a href="javascript:;" id="usrpg_btn_follow" style="<?= $D->i_follow_him?'display:none':'' ?>" onclick="user_follow('<?= $D->usr->username ?>',this,'usrpg_btn_unfollow','<?= addslashes($this->lang('msg_follow_user_on',array('#USERNAME#'=>$D->usr->username))) ?>');" class="um_follow" onfocus="this.blur();" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('usr_toplnks_follow',array('#USERNAME#'=>$D->usr->username)) ?></b></a>
							<a href="javascript:;" id="usrpg_btn_unfollow" style="<?= $D->i_follow_him?'':'display:none' ?>" onclick="user_unfollow('<?= $D->usr->username ?>',this,'usrpg_btn_follow','<?= addslashes($this->lang('user_unfollow_confirm',array('#USERNAME#'=>$D->usr->username))) ?>','<?= addslashes($this->lang('msg_follow_user_off',array('#USERNAME#'=>$D->usr->username))) ?>');" class="um_unfollow" onfocus="this.blur();" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('usr_toplnks_unfollow',array('#USERNAME#'=>$D->usr->username)) ?></b></a>
							<?php } ?>
							<div id="usrpg_top_tooltip" class="umtt" style="display:none;"><div></div></div>
						</div>
						<?php } ?>
						<h2><?= empty($D->usr->fullname) ? htmlspecialchars($D->usr->username) : htmlspecialchars($D->usr->fullname) ?></h2>
						<span><?= htmlspecialchars($D->usr->position) ?></span>
						<div id="profilenav">
							<a href="<?= userlink($D->usr->username) ?>" class="<?= $D->tab=='updates'?'onptab':'' ?>"><b><?= $this->lang('usr_tab_updates') ?></b></a>
							<a href="<?= userlink($D->usr->username) ?>/tab:info" class="<?= $D->tab=='info'?'onptab':'' ?>"><b><?= $this->lang('usr_tab_info') ?></b></a>
							<a href="<?= userlink($D->usr->username) ?>/tab:coleagues" class="<?= $D->tab=='coleagues'?'onptab':'' ?>"><b><?= $this->lang('usr_tab_coleagues') ?></b></a>
							<?php if($D->num_groups>0) { ?>
							<a href="<?= userlink($D->usr->username) ?>/tab:groups" class="<?= $D->tab=='groups'?'onptab':'' ?>"><b><?= $this->lang('usr_tab_groups') ?></b></a>
							<?php } ?>
							<?php if($D->tab == 'updates') { ?>
							<a href="<?= $C->SITE_URL ?>rss/username:<?= $D->usr->username ?>" id="rssicon" title="<?= $this->lang('usr_updates_rss_dsc',array('#USERNAME#'=>$D->usr->username)) ?>" target="_blank"><?= $this->lang('usr_updates_rss') ?></a>
							<?php } ?>
						</div>
					</div>
					
				<?php if( $D->tab == 'updates' ) { ?>
					<div class="ttl" style="margin-top:8px; margin-bottom:6px;">
						<div class="ttl2">
							<h3><?= $this->lang('usr_updates_title',array('#USERNAME#'=>$D->usr->username)) ?></h3>
							<div id="postfilter">
								<a href="javascript:;" onclick="dropdiv_open('postfilteroptions');" id="postfilterselected" onfocus="this.blur();"><span><?= $this->lang('posts_filter_'.$D->filter) ?></span></a>
								<div id="postfilteroptions" style="display:none;">
									<a href="<?= userlink($D->usr->username) ?>"><?= $this->lang('posts_filter_all') ?></a>
									<a href="<?= userlink($D->usr->username) ?>/filter:links"><?= $this->lang('posts_filter_links') ?></a>
									<a href="<?= userlink($D->usr->username) ?>/filter:images"><?= $this->lang('posts_filter_images') ?></a>
									<a href="<?= userlink($D->usr->username) ?>/filter:videos"><?= $this->lang('posts_filter_videos') ?></a>
									<a href="<?= userlink($D->usr->username) ?>/filter:files" style="border-bottom:0px;"><?= $this->lang('posts_filter_files') ?></a>
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
				<?php } elseif( $D->tab == 'coleagues' ) { ?>
					<div class="htabs" style="margin-bottom:6px;">
						<a href="<?= userlink($D->usr->username) ?>/tab:coleagues/filter:ifollow" class="<?= $D->filter=='ifollow'?'onhtab':'' ?>"><b><?= $D->filter1_title ?> <small>(<?= $D->fnums['ifollow'] ?>)</small></b></a>
						<a href="<?= userlink($D->usr->username) ?>/tab:coleagues/filter:followers" class="<?= $D->filter=='followers'?'onhtab':'' ?>"><b><?= $D->filter2_title ?> <small>(<?= $D->fnums['followers'] ?>)</small></b></a>
					</div>
					<div id="grouplist">
						<?= $D->users_html ?>
					</div>
				<?php } elseif( $D->tab == 'groups' ) { ?>
					<div id="grouplist">
						<div class="ttl" style="margin-top:8px; margin-bottom:6px;"><div class="ttl2"><h3><?= $D->groups_title ?></h3></div></div>
						<?= $D->groups_html ?>
					</div>
				<?php } elseif( $D->tab == 'info' ) { ?>
					<div style="padding-top:8px;">
						<?php if( !empty($D->usr->about_me) ) { ?>
						<b style="display:block; padding:8px; padding-bottom:5px; padding-top:0px;"><?= $this->lang('usr_info_section_aboutme') ?></b>
						<div class="greygrad">
							<div class="greygrad2">
								<div class="greygrad3" style="color:black;">
									<?= htmlspecialchars($D->usr->about_me) ?>
								</div>
							</div>
						</div>
						<?php } ?>
						<div class="ttl"><div class="ttl2">
							<h3><?= $this->lang('usr_info_section_details') ?></h3>
							<?php if( $D->is_my_profile ) { ?>
							<a class="ttlink" href="<?= $C->SITE_URL ?>settings/profile"><?= $this->lang('usr_info_edit') ?></a>
							<?php } ?>
						</div></div>
						<div style="margin-left:4px;">
							<table cellspacing="4">
								<?php if( !empty($D->usr->location) ) { ?>
								<tr>
									<td class="detailsparam"><?= $this->lang('usr_info_aboutme_location') ?></td>
									<td class="detailsvalue"><?= htmlspecialchars($D->usr->location) ?></td>
								</tr>
								<?php } ?>
								<?php if( !empty($D->usr->gender) ) { ?>
								<tr>
									<td class="detailsparam"><?= $this->lang('usr_info_aboutme_gender') ?></td>
									<td class="detailsvalue"><?= $this->lang('usr_info_aboutme_gender_'.$D->usr->gender) ?></td>
								</tr>
								<?php } ?>
								<?php if( !empty($D->birthdate) ) { ?>
								<tr>
									<td class="detailsparam"><?= $this->lang('usr_info_aboutme_birthdate') ?></td>
									<td class="detailsvalue"><?= $D->birthdate ?></td>
								</tr>
								<?php } ?>
								<?php if( !empty($D->i->website) ) { ?>
								<tr>
									<td class="detailsparam"><?= $this->lang('usr_info_aboutme_website') ?></td>
									<td class="detailsvalue"><a href="<?= htmlspecialchars($D->i->website) ?>" target="_blank"><?= htmlspecialchars($D->i->website) ?></a></td>
								</tr>
								<?php } ?>
								<tr>
									<td class="detailsparam"><?= $this->lang('usr_info_aboutme_datereg') ?></td>
									<td class="detailsvalue"><?= $D->date_register ?></td>
								</tr>
								<?php if( !empty($D->date_lastlogin) ) { ?>
								<tr>
									<td class="detailsparam"><?= $this->lang('usr_info_aboutme_datelgn') ?></td>
									<td class="detailsvalue"><?= $D->date_lastlogin ?></td>
								</tr>
								<?php } ?>
							</table>
						</div>
						<?php if( count($D->i->prs) > 0 ) { ?>
						<div class="ttl" style="margin-top:4px;"><div class="ttl2">
							<h3><?= $this->lang('usr_info_section_xtprofiles') ?></h3>
							<?php if( $D->is_my_profile ) { ?>
							<a class="ttlink" href="<?= $C->SITE_URL ?>settings/contacts"><?= $this->lang('usr_info_edit') ?></a>
							<?php } ?>
						</div></div>
						<div style="margin-left:4px;">
							<table cellspacing="4">
								<tr>
								<?php $i=0; foreach($D->i->prs as $k=>$v) { $i++; ?>
									<td><img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/ext_<?= $k ?>.gif" alt="<?= $this->lang('usr_info_'.$k) ?>" title="<?= $this->lang('usr_info_'.$k) ?>"></td>
									<td width="150"><a href="<?= htmlspecialchars($v[0]) ?>" target="_blank"><?= htmlspecialchars($v[1]) ?></a></td>
								<?php if($i%4==0 && count($D->i->prs)>$i) { ?>
								</tr>
								<tr>
								<?php } } ?>
								</tr>
							</table>
						</div>
						<?php } ?>
						<?php if( count($D->i->ims) > 0 ) { ?>
						<div class="ttl" style="margin-top:4px;"><div class="ttl2">
							<h3><?= $this->lang('usr_info_section_messengers') ?></h3>
							<?php if( $D->is_my_profile ) { ?>
							<a class="ttlink" href="<?= $C->SITE_URL ?>settings/contacts"><?= $this->lang('usr_info_edit') ?></a>
							<?php } ?>
						</div></div>
						<div style="margin-left:4px;">
							<table cellspacing="4">
								<tr>
								<?php $i=0; foreach($D->i->ims as $k=>$v) { $i++; ?>
									<td><img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/<?= $k ?>.gif" alt="<?= $this->lang('usr_info_'.$k) ?>" title="<?= $this->lang('usr_info_'.$k) ?>" /></td>
									<td width="170"><?= htmlspecialchars($v) ?></td>
								<?php if($i%4==0 && count($D->i->ims)>$i) { ?>
								</tr>
								<tr>
								<?php } } ?>
								</tr>
							</table>
						</div>
						<?php } ?>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>
<?php
	
	$this->load_template('footer.php');
	
?>