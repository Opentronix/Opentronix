		<div id="viewpost">
			<div id="vposthdr">
				<div id="vposthdr2">
					<?php if( $D->post->post_user->id > 0 ) { ?>
						<div id="usermenu">
							<?php if( $D->post->post_type == 'public' ) { ?>
								<?php if( !empty($D->nextpost) ) { ?>
									<a href="<?= $D->nextpost ?>" class="um_nextpost" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('viewpost_hdr_nextpost') ?></b></a>
								<?php } else { ?>
									<strong class="um_nonextpost"></strong>
								<?php } ?>
								<a href="<?= userlink($D->post->post_user->username) ?>" class="um_backtoprofile" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('viewpost_hdr_profile',array('#USERNAME#'=>$D->post->post_user->username)) ?></b></a>
								<?php if( !empty($D->prevpost) ) { ?>
									<a href="<?= $D->prevpost ?>" class="um_prevpost" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('viewpost_hdr_prevpost') ?></b></a>
								<?php } else { ?>
									<strong class="um_noprevpost"></strong>
								<?php } ?>
							<?php } else { ?>
								<?php if( $D->post->post_user->id == $this->user->id ) { ?>
								<a href="<?= userlink($D->post->post_to_user->username) ?>" class="um_backtoprofile" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('viewpost_hdr_profile',array('#USERNAME#'=>$D->post->post_to_user->username)) ?></b></a>
								<?php } else { ?>
								<a href="<?= userlink($D->post->post_user->username) ?>" class="um_backtoprofile" onmouseover="userpage_top_tooltip(this.firstChild.innerHTML);" onmouseout="userpage_top_tooltip('');"><b><?= $this->lang('viewpost_hdr_profile',array('#USERNAME#'=>$D->post->post_user->username)) ?></b></a>
								<?php } ?>
							<?php } ?>
							<div id="usrpg_top_tooltip" class="umtt" style="display:none;"><div></div></div>
						</div>
					<?php } ?>
					<?php if( $D->post->post_user->id>0 ) { ?>
						<?php if( $D->post->post_api_id == 2 ) { ?>
							<div id="vposthdravatar_rss" style="background-image:url('<?= $C->IMG_URL.'avatars/thumbs1/'.$D->post->post_user->avatar ?>');"><a href="<?= userlink($D->post->post_user->username) ?>" title="<?= $D->post->post_user->fullname ?>"></a></div>
						<?php } else { ?>
							<a href="<?= userlink($D->post->post_user->username) ?>" id="vposthdravatar" title="<?= $D->post->post_user->fullname ?>"><img src="<?= $C->IMG_URL ?>avatars/thumbs1/<?= $D->post->post_user->avatar ?>" alt="<?= htmlspecialchars($D->post->post_user->fullname) ?>" /></a>
						<?php } ?>
						<div id="vposthdrinfo">
							<a href="<?= userlink($D->post->post_user->username) ?>" style="float:left;" title="<?= htmlspecialchars($D->post->post_user->fullname) ?>"><?= $D->post->post_user->username ?></a>
							<?php if($D->post->post_type == 'private') { ?>
								<img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/pmarrows.gif" alt="&raquo;" style="float:left; margin:5px; margin-top:9px;" />
								<a href="<?= userlink($D->post->post_to_user->username) ?>" style="float:left;" title="<?= htmlspecialchars($D->post->post_to_user->fullname) ?>"><?= $D->post->post_to_user->username ?></a>
							<?php } ?>
							<div class="klear"></div>
							<?= htmlspecialchars($D->post->post_user->position) ?>
						</div>
					<?php } elseif( $D->post->post_group ) { ?>
						<?php if( $D->post->post_api_id == 2 ) { ?>
							<div id="vposthdravatar_rss" style="background-image:url('<?= $C->IMG_URL.'avatars/thumbs1/'.$D->post->post_group->avatar ?>');"><a href="<?= $C->SITE_URL ?><?= $D->post->post_group->groupname ?>" title="<?= $D->post->post_group->title ?>"></a></div>
						<?php } else { ?>
							<a href="<?= $C->SITE_URL ?><?= $D->post->post_group->groupname ?>" id="vposthdravatar" title="<?= $D->post->post_group->title ?>"><img src="<?= $C->IMG_URL ?>avatars/thumbs1/<?= $D->post->post_group->avatar ?>" alt="<?= htmlspecialchars($D->post->post_group->title) ?>" /></a>
						<?php } ?>
						<div id="vposthdrinfo">
							<a href="<?= $C->SITE_URL ?><?= $D->post->post_group->groupname ?>" style="float:left;" title="<?= htmlspecialchars($D->post->post_group->title) ?>"><?= $D->post->post_group->title ?></a>
							<div class="klear"></div>
							<?= $this->lang( $D->post->post_group->is_private ? 'vpgroup_subtitle_type_private' : 'vpgroup_subtitle_type_public' )  ?> &middot;
							<?= $this->lang( $D->post->post_group->num_members==1 ? 'vpgroup_subtitle_nm_members1' : 'vpgroup_subtitle_nm_members', array('#NUM#'=>$D->post->post_group->num_members) ) ?> &middot;
							<?= $this->lang( $D->post->post_group->num_posts==1 ? 'vpgroup_subtitle_nm_posts1' : 'vpgroup_subtitle_nm_posts', array('#NUM#'=>$D->post->post_group->num_posts) ) ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<div id="vpostbody">
				<div id="vpostbody2">
					<div class="postcontrols">
						<?php if( $this->user->is_logged ) { ?>
							<a href="javascript:;" id="postlink_fave_<?= $D->post->post_tmp_id ?>" class="pfave" title="<?= $this->lang('post_fave_link') ?>" style="<?= $D->post->is_post_faved()?'display:none;':'' ?>" onfocus="this.blur();" onclick="post_fave('<?= $D->post->post_tmp_id ?>');"></a>
							<a href="javascript:;" id="postlink_unfave_<?= $D->post->post_tmp_id ?>" class="pfave saved" title="<?= $this->lang('post_unfave_link') ?>" style="<?= $D->post->is_post_faved()?'':'display:none;' ?>" onfocus="this.blur();" onclick="post_unfave('<?= $D->post->post_tmp_id ?>','<?= $this->lang('post_unfave_confirm') ?>',<?= $this->request[0]=='dashboard'&&isset($D->tab)&&$D->tab=='bookmarks' ? 'true' :'false' ?>);"></a>
							<?php if( $D->post->if_can_edit() ) { ?>
								<a href="javascript:;" class="pedit" title="<?= $this->lang('post_edit_link') ?>" onfocus="this.blur();" onclick="postform_open(({editpost:'<?= $D->post->post_tmp_id ?>'}));"></a>
							<?php } ?>
							<?php if( $D->delete_enabled ) { ?>
								<a href="javascript:;" id="postlink_del_<?= $D->post->post_tmp_id ?>" class="pdel" title="<?= $this->lang('post_delete_link') ?>" onfocus="this.blur();" onclick="post_delete('<?= $D->post->post_tmp_id ?>', '<?= $this->lang('post_delete_confirm') ?>', function(){self.location.href='<?= $D->delete_urlafter ?>';});"></a>
							<?php } ?>
						<?php } else { ?>
							<a href="<?= $C->SITE_URL ?>signin" class="pfave" title="<?= $this->lang('post_fave_link') ?>"" onfocus="this.blur();"></a>
						<?php } ?>
					</div>
					<div id="vposttext">
						<?= $D->post->parse_text(); ?>
					</div>
					<?php if( isset($D->post->post_attached['link']) ) { ?>
						<a href="<?= htmlspecialchars($D->post->post_attached['link']->link) ?>" class="postlink" target="_blank" rel="nofollow"><?= htmlspecialchars(str_cut_link($D->post->post_attached['link']->link,90)) ?></a>
					<?php } ?>
					<?php if( isset($D->post->post_attached['file']) ) { ?>
						<a href="<?= $C->SITE_URL ?>getfile/pid:<?= $D->post->post_tmp_id ?>/<?= htmlspecialchars($D->post->post_attached['file']->title) ?>" class="filelink" title="<?= htmlspecialchars($D->post->post_attached['file']->title) ?>"><b><?= htmlspecialchars(str_cut($D->post->post_attached['file']->title,90)) ?></b> &middot; <?= show_filesize($D->post->post_attached['file']->filesize) ?></a>
					<?php } ?>
					<div id="vpostftr">
						<?= strftime($this->lang('viewpost_date_format'), $D->post->post_date) ?>
						<?= $D->post->parse_group(60) ?>
						<?= post::parse_api($D->post->post_api_id) ?>
						<?php if( $D->post->post_type=='public' && $D->post->post_api_id!=2 && (!$D->post->post_group || $D->post->post_group->is_public) ) { ?>
						<?= $D->post->show_share_link() ?>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php if( isset($D->post->post_attached['image']) ) { ?>
			<div class="embedbox">
				<div class="embedbox2">
					<div class="embedbox3">
						<div class="embedbox4">
							<?= $this->lang('viewpost_attached_image') ?>
							<div class="theattachment">
								<img src="<?= $C->IMG_URL ?>attachments/<?= $this->network->id ?>/<?= $D->post->post_attached['image']->file_preview ?>" style="width:<?= $D->post->post_attached['image']->size_preview[0] ?>px; height:<?= $D->post->post_attached['image']->size_preview[1] ?>px;" alt="<?= htmlspecialchars($D->post->post_attached['image']->title) ?>" />
							</div>
							<div class="attachmentinfo">
								<b><?= htmlspecialchars($D->post->post_attached['image']->title) ?></b> &middot;
								<a href="<?= $C->SITE_URL ?>getfile/pid:<?= $D->post->post_tmp_id ?>/tp:image/<?= htmlspecialchars($D->post->post_attached['image']->title) ?>" target="_top">
									<?= $D->post->post_attached['image']->size_original[0] ?>x<?= $D->post->post_attached['image']->size_original[1] ?>px, 
									<?= show_filesize($D->post->post_attached['image']->filesize) ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if( isset($D->post->post_attached['videoembed']) ) { ?>
			<div class="embedbox">
				<div class="embedbox2">
					<div class="embedbox3">
						<div class="embedbox4">
							<?= $this->lang('viewpost_attached_video') ?>
							<div class="theattachment">
								<?= $D->post->post_attached['videoembed']->embed_code ?>
							</div>
							<div class="attachmentinfo">
								<a href="<?= $D->post->post_attached['videoembed']->orig_url ?>" target="_blank"><?= $D->post->post_attached['videoembed']->orig_url ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<script type="text/javascript">
				postcomments_open_state["<?= $D->post->post_tmp_id ?>"]	= 1;
			</script>
			<a name="comments"></a>
			<div class="postcomments" id="postcomments_<?= $D->post->post_tmp_id ?>">
				<?php if( $D->post->post_commentsnum == 0 ) { ?>
					<?php if( $this->user->is_logged ) { ?>
					<div class="slimpostcommentshdr"><div class="slimpostcommentshdr2"></div></div>
					<div class="postcommentsftr">
						<div class="postcommentsftr2" style="padding-top:2px;">
	 						<div class="addpc_big">
								<img src="<?= $C->IMG_URL.'avatars/thumbs3/'.$this->user->info->avatar ?>" class="addpc_avatar" alt="" />
								<div class="addpc_right">
									<textarea id="postcomments_<?= $D->post->post_tmp_id ?>_textarea" onkeyup="textarea_autoheight(this);"></textarea>
									<input id="postcomments_<?= $D->post->post_tmp_id ?>_submitbtn" onclick="postcomments_submit('<?= $D->post->post_tmp_id ?>');" type="submit" value="<?= $this->lang('viewpost_comment_submit') ?>" />
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				<?php } else { ?>
				<div class="postcommentshdr">
					<div class="postcommentshdr2">
						<b><?= $this->lang( $D->post->post_commentsnum==0?'post_viwcomments_0':($D->post->post_commentsnum==1?'post_viwcomments_1':'post_viwcomments_all'), array('#NUM#'=>$D->post->post_commentsnum) ) ?></b>
					</div>
				</div>
				<div class="postcommentscontent">
					<?php $i=0; foreach($D->post->post_comments as $c) { ?>
					<div class="comment<?= $i==0?' firstcomment':'' ?>" id="postcomment_<?= $c->comment_id ?>">
						<a href="<?= userlink($c->comment_user->username) ?>" class="commentavatar" title="<?= htmlspecialchars($c->comment_user->fullname) ?>"><img src="<?= $C->IMG_URL.'avatars/thumbs3/'.$c->comment_user->avatar ?>"/></a>
						<div class="comment_right">
							<a href="<?= userlink($c->comment_user->username) ?>" class="commentname" title="<?= htmlspecialchars($c->comment_user->fullname) ?>"><?= $c->comment_user->username ?></a>
							<p><?= nl2br($c->parse_text()) ?></p>
							<?= post::parse_date($c->comment_date) ?>
							<?= post::parse_api($c->comment_api_id) ?>
							<?php if( $c->if_can_delete() ) { ?>
							&middot; <a onclick="postcomment_delete('<?= $c->post->post_tmp_id ?>', <?= $c->comment_id ?>, '<?= $this->lang('post_delcomment_cnfrm') ?>');" href="javascript:;" onfocus="this.blur();" class="smalllink"><?= $this->lang('post_delcomment_lnk') ?></a>
							<?php } ?>
						</div>
					</div>
					<?php $i++; } ?>
				</div>
				<div class="postcommentsftr">
					<div class="postcommentsftr2">
						<?php if( $this->user->is_logged ) { ?>
	 					<div class="addpc_big" id="postcomments_<?= $D->post->post_tmp_id ?>_bigform">
							<img src="<?= $C->IMG_URL.'avatars/thumbs3/'.$this->user->info->avatar ?>" class="addpc_avatar" alt="" />
							<div class="addpc_right">
								<textarea id="postcomments_<?= $D->post->post_tmp_id ?>_textarea" onkeyup="textarea_autoheight(this);"></textarea>
								<input id="postcomments_<?= $D->post->post_tmp_id ?>_submitbtn" onclick="postcomments_submit('<?= $D->post->post_tmp_id ?>');" type="submit" value="<?= $this->lang('post_comments_submit') ?>" />
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>