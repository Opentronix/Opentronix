		<?php if( $D->p->is_system_post ) { ?>
			<div class="mpost">
				<a href="<?= $C->SITE_URL.$D->p->tmp->syspost_about_user->username ?>" class="postavatar"><img src="<?= $C->IMG_URL.'avatars/thumbs3/'.$D->p->tmp->syspost_about_user->avatar ?>" alt="" /></a>
				<div class="mpostbody"><div class="mpostbody2">			
					<div class="thempostbody">
						<?= $D->p->parse_text() ?>
						<small><?= post::parse_date($D->p->post_date) ?></small>
					</div>
				</div></div>
				<div class="mpftr"><div class="mpftr2"></div></div>
			</div>
		<?php } else { ?>
			<div class="post">
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
							<b class="pmto"></b>
							<a href="<?= $C->SITE_URL.$D->p->post_to_user->username ?>" class="postauthorname" title="<?= htmlspecialchars($D->p->post_to_user->fullname) ?>"><?= $D->p->post_to_user->username ?></a>
							<?php } else { ?>
							<a href="<?= $C->SITE_URL.$D->p->post_user->username ?>" class="postauthorname" title="<?= htmlspecialchars($D->p->post_user->fullname) ?>"><?= $D->p->post_user->username ?></a>
							<?php } ?>
							<?php if( $D->p->post_user->id ) { ?>
							<div class="posticons">
								<a href="<?= $C->SITE_URL ?>post/mention:<?= $D->p->post_user->username ?>" class="pi_pm"></a>
								<a href="<?= $C->SITE_URL ?>post/private:<?= $D->p->post_user->username ?>" class="pi_at"></a>
							</div>
							<?php } ?>
						</div>
					</div>
					<div class="postbody">
						<?= $D->p->parse_text() ?>
						<small>
							<?= post::parse_date($D->p->post_date) ?>
							<?= post::parse_api($D->p->post_api_id) ?>
							<?= $D->p->parse_group(30) ?>
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
					<?php if( isset($D->p->post_attached['image']) || isset($D->p->post_attached['videoembed']) ) { ?>
						<div class="postimages">
						<?php if( isset($D->p->post_attached['image']) ) { ?>
							<a href="<?= $C->IMG_URL.'attachments/1/'.$D->p->post_attached['image']->file_original ?>" class="pa_image"><img src="<?= $C->IMG_URL.'attachments/1/'.$D->p->post_attached['image']->file_thumbnail ?>" alt="" /><span></span></a>
						<?php } ?>
						<?php if( isset($D->p->post_attached['videoembed']) ) { ?>
							<a href="<?= $D->p->permalink ?>/at:videoembed/#at-videoembed" class="pa_video"><img src="<?= $C->IMG_URL.'attachments/1/'.$D->p->post_attached['videoembed']->file_thumbnail ?>" alt=""/><span></span></a>
						<?php } ?>
						</div>
					<?php } ?>
					<div class="postftr">
						<div class="postftr2">
							<div class="postftr3">
								<?php if( $D->p->post_commentsnum == 0 ) { ?>
								<a href="<?= $D->p->permalink ?>#comments_add" class="addcomment"><?= $this->lang('iphone_singlepost_ftr_comments_0') ?></a>
								<?php } elseif( $D->p->if_new_comments() ) { ?>
								<a href="<?= $D->p->permalink ?>#comments" class="hasnewcomments"><b><?= $this->lang($D->p->post_commentsnum==1?'iphone_singlepost_ftr_comments_1':'iphone_singlepost_ftr_comments_mr', array('#NUM#'=>$D->p->post_commentsnum)) ?></b><span><strong><?= $this->lang($D->p->if_new_comments()==1?'iphone_singlepost_ftr_newcomments_1':'iphone_singlepost_ftr_newcomments_mr', array('#NUM#'=>$D->p->if_new_comments())) ?></strong></span></a>
								<?php } else { ?>
								<a href="<?= $D->p->permalink ?>#comments" class="hascomments"><?= $this->lang($D->p->post_commentsnum==1?'iphone_singlepost_ftr_comments_1':'iphone_singlepost_ftr_comments_mr', array('#NUM#'=>$D->p->post_commentsnum)) ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>