<?php
	
	$this->load_template('mobile_iphone/header.php');
	
?>
		<div id="postspage">
			<div id="posts">
				<div class="post" style="margin-bottom:0px;">
					<?php if( $D->p->post_user->id==0 && $D->p->post_group ) { ?>
					<a href="<?= $C->SITE_URL.$D->p->post_group->groupname ?>" class="postavatar"><img src="<?= $C->IMG_URL.'avatars/thumbs3/'.$D->p->post_group->avatar ?>" alt="<?= htmlspecialchars($D->p->post_group->title) ?>" /></a>
					<?php } else { ?>
					<a href="<?= $C->SITE_URL.$D->p->post_user->username ?>" class="postavatar"><img src="<?= $C->IMG_URL.'avatars/thumbs3/'.$D->p->post_user->avatar ?>" alt="<?= htmlspecialchars($D->p->post_user->fullname) ?>" /></a>
					<?php } ?>
					<div class="thepost">
						<div class="posthdr">
							<div class="posthdr2">
								<?php if( $D->p->post_user->id==0 && $D->p->post_group ) { ?>
								<a href="<?= $C->SITE_URL.$D->p->post_group->groupname ?>" class="postauthorname" title="<?= htmlspecialchars($D->p->post_group->title) ?>"><?= htmlspecialchars($D->p->post_group->title) ?></a>
								<?php } elseif( $D->p->post_type == 'private' ) { ?>
								<a href="<?= $C->SITE_URL.$D->p->post_user->username ?>" class="postauthorname" title="<?= htmlspecialchars($D->p->post_user->fullname) ?>"><?= $D->p->post_user->username ?></a>
								<b style="float:left; margin-top:7px; margin-left:3px; color:#aaa; font-weight:normal; font-size:16px;">&raquo;</b>
								<a href="<?= $C->SITE_URL.$D->p->post_to_user->username ?>" class="postauthorname" title="<?= htmlspecialchars($D->p->post_to_user->fullname) ?>"><?= $D->p->post_to_user->username ?></a>
								<?php } else { ?>
								<a href="<?= $C->SITE_URL.$D->p->post_user->username ?>" class="postauthorname" title="<?= htmlspecialchars($D->p->post_user->fullname) ?>"><?= $D->p->post_user->username ?></a>
								<?php } ?>
								<?php if( $D->p->post_user->id ) { ?>
								<div class="posticons">
									<a href="javascript:;" onclick="postform_open(({username:'<?= $D->p->post_user->username ?>'}));" class="pi_pm"></a>
									<a href="javascript:;" onclick="postform_mention('<?= $D->p->post_user->username ?>',true);" class="pi_at"></a>
								</div>
								<?php } ?>
							</div>
						</div>
						<div class="postbody">
							<?= $D->p->parse_text() ?>
							<small>
								<?= post::parse_date($D->p->post_date) ?>
								<?= post::parse_api($D->p->post_api_id) ?>
							</small>
						</div>
						<?php if( isset($D->p->post_attached['link']) ) { ?>
						<div class="postlink">
							<b></b>
							<a href="<?= htmlspecialchars($D->p->post_attached['link']->link) ?>" target="_blank" rel="nofollow"><?= htmlspecialchars($D->p->post_attached['link']->link) ?></a>
						</div>
						<?php } ?>
						<?php if( isset($D->p->post_attached['file']) ) { ?>
						<div class="postlink">
							<b></b>
							<a href="<?= $C->SITE_URL ?>getfile/pid:<?= $D->p->post_tmp_id ?>/<?= htmlspecialchars($D->p->post_attached['file']->title) ?>"><?= htmlspecialchars($D->p->post_attached['file']->title) ?> (<?= show_filesize($D->p->post_attached['file']->filesize) ?>)</a>
						</div>
						<?php } ?>
						<?php if( isset($D->p->post_attached['videoembed']) && $this->param('at')=='videoembed' ) { ?>
							<a name="at-videoembed"></a>
							<div class="postimages">
								<div id="videoembed_box" style="width: <?= $D->video_w ?>px; height: <?= $D->video_h ?>px;">
									<?= $D->video->embed_code ?>
								</div>
								<script type="text/javascript">
									function videoembed_update_size() {
										var w	= window;
										if( w.orientation === undefined ) { return; }
										var o	= w.orientation;
										var c	= document.getElementById("videoembed_box");
										if( ! c ) { return; }
										var new_width, new_height;
										var old_width	= parseInt(c.style.width, 10);
										var old_height	= parseInt(c.style.height, 10);
										if( o == 0 || o == 180 ) {
											new_width	= 320 - 70;
											new_height	= Math.round(new_width * old_height / old_width);
											c.style.width	= new_width + "px";
											c.style.height	= new_height + "px";
										}
										else if( o == 90 || o == -90 ) {
											new_width	= 480 - 70;
											new_height	= Math.round(new_width * old_height / old_width);
											c.style.width	= new_width + "px";
											c.style.height	= new_height + "px";
										}
									}
									document.body.addEventListener("orientationchange", videoembed_update_size, false);
									videoembed_update_size();
								</script>
							</div>
						<?php } ?>
						<?php if( isset($D->p->post_attached['image']) || (isset($D->p->post_attached['videoembed'])&&$this->param('at')!='videoembed') ) { ?>
							<div class="postimages">
							<?php if( isset($D->p->post_attached['image']) ) { ?>
								<a href="<?= $C->IMG_URL.'attachments/1/'.$D->p->post_attached['image']->file_original ?>" class="pa_image"><img src="<?= $C->IMG_URL.'attachments/1/'.$D->p->post_attached['image']->file_thumbnail ?>" alt="" /><span></span></a>
							<?php } ?>
							<?php if( isset($D->p->post_attached['videoembed']) && $this->param('at')!='videoembed' ) { ?>
								<a href="<?= $D->p->permalink ?>/at:videoembed/#at-videoembed" class="pa_video"><img src="<?= $C->IMG_URL.'attachments/1/'.$D->p->post_attached['videoembed']->file_thumbnail ?>" alt=""/><span></span></a>
							<?php } ?>
							</div>
						<?php } ?>
						<?php if( isset($D->p->post_attached['externalpost']) ) { ?>
							<?php $this->load_template('mobile_iphone/single_post_atchpost.php') ?>
						<?php } ?>
					</div>
				</div>
				<a name="comments"></a>
				<div id="comments">
					<?php if( $D->cnm > 0 ) { ?>
					<div class="commentsttl"><?= $this->lang($D->cnm==1?'iphone_vpost_cmnts_1':'iphone_vpost_cmnts_mr', array('#NUM#'=>$D->cnm)) ?>
					<a href="#comments_add" onclick="setTimeout(function(){document.cmf.message.focus();},100);"><?= $this->lang('iphone_vpost_addcommentlink') ?></a></div>
					<?php } ?>
					<?php $i = 0; foreach($D->comments as $c) { $i++; ?>
					<a name="comments_id_<?= $c->comment_id ?>"></a>
					<a name="comments_indx_<?= $i ?>"></a>
					<?= $i==$D->cnm ? '<a name="comments_last"></a>' : '' ?>
					<div class="acomment">
						<a href="<?= $C->SITE_URL.$c->comment_user->username ?>" class="postavatar"><img src="<?= $C->IMG_URL.'avatars/thumbs3/'.$c->comment_user->avatar ?>" alt="<?= htmlspecialchars($c->comment_user->fullname) ?>" /></a>
						<div class="commentbody" style="border-top:1px solid #cbcbcb;">
							<a href="<?= $C->SITE_URL.$c->comment_user->username ?>" class="commentauthor"><?= $c->comment_user->username ?></a>
							<?= nl2br($c->parse_text()) ?>
							<div class="commentmeta">
								<?= post::parse_date($c->comment_date) ?>
								<?= post::parse_api($c->comment_api_id) ?>
								<?php if( $c->if_can_delete() ) { ?>
								&middot;
								<a href="<?= $D->p->permalink ?>/delcomment:<?= $c->comment_id ?>#<?= $i==1?'comments':('comments_indx_'.($i-1)) ?>" onclick="return confirm('<?= $this->lang('iphone_vpost_delcomment_c') ?>');"><?= $this->lang('iphone_vpost_delcomment') ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } ?>
					<a name="comments_add"></a>
					<div class="commentsttl" style="<?= $D->cnm==0?'':'border-top:1px solid #cbcbcb;' ?>"><?= $this->lang('iphone_vpost_addcommentttl') ?></div>
					<div id="postcomment">
						<form method="post" name="cmf" onsubmit="if(this.message.value==''){this.message.focus();return false;}" action="<?= $D->p->permalink ?>" style="display:inline; margin:0; padding:0;">
							<div id="textar">
								<textarea name="message"></textarea>
							</div>
						</form>
						<a href="javascript:;" onclick="with(document.cmf){if(message.value==''){message.focus();}else{submit();message.disabled=true;}}" id="combtn"><b><?= $this->lang('iphone_vpost_addcommentbtn') ?></b></a>
					</div>
					<div id="commentsftr"><div id="commentsftr2"><div id="commentsftr3"></div></div></div>
				</div>
			</div>
		</div>

<?php
	
	$this->load_template('mobile_iphone/footer.php');
	
?>