<?php
	
	$this->load_template('mobile/header.php');
	
?>
	<div class="post" style="background-image: url('<?= $C->IMG_URL ?>avatars/thumbs3/<?= $D->p->post_user->id==0&&$D->p->post_group ? $D->p->post_group->avatar : $D->p->post_user->avatar ?>');">
		<div class="postttl">
			<?php if( $D->p->post_user->id==0 && $D->p->post_group ) { ?>
			<a href="<?= $C->SITE_URL.$D->p->post_group->groupname ?>" class="user" title="<?= htmlspecialchars($D->p->post_group->title) ?>"><?= $D->p->post_group->title ?></a>
			<?php } elseif( $D->p->post_type == 'private' ) { ?>
			<a href="<?= $C->SITE_URL.$D->p->post_user->username ?>" class="user" title="<?= htmlspecialchars($D->p->post_user->fullname) ?>"><?= $D->p->post_user->username ?></a>
			<b>&raquo;</b>
			<a href="<?= $C->SITE_URL.$D->p->post_to_user->username ?>" class="user" title="<?= htmlspecialchars($D->p->post_to_user->fullname) ?>"><?= $D->p->post_to_user->username ?></a>
			<?php } else { ?>
			<a href="<?= $C->SITE_URL.$D->p->post_user->username ?>" class="user" title="<?= htmlspecialchars($D->p->post_user->fullname) ?>"><?= $D->p->post_user->username ?></a>
			<?= $D->p->parse_group(20) ?>
			<?php } ?>
		</div>
		<p class="message"><?= $D->p->parse_text() ?></p>
		
		<?php if( isset($D->p->post_attached['link']) ) { ?>
		<div class="attachment">
			<a href="<?= htmlspecialchars($D->p->post_attached['link']->link) ?>" target="_blank" rel="nofollow"><?= htmlspecialchars(str_cut_link($D->p->post_attached['link']->link,40)) ?></a><br />
			<div class="klear"></div>
		</div>
		<?php } ?>
		
		<?php if( isset($D->p->post_attached['file']) ) { ?>
		<div class="attachment">
			<a href="<?= $C->SITE_URL ?>getfile/pid:<?= $D->p->post_tmp_id ?>/<?= htmlspecialchars($D->p->post_attached['file']->title) ?>" title="<?= htmlspecialchars($D->p->post_attached['file']->title) ?>"><?= htmlspecialchars(str_cut($D->p->post_attached['file']->title,40)) ?> (<?= show_filesize($D->p->post_attached['file']->filesize) ?>)</a><br />
			<div class="klear"></div>
		</div>
		<?php } ?>
		
		<?php if( isset($D->p->post_attached['image']) ) { ?>
		<div class="attachment">
			<span class="imgtxt"><?= $this->lang('singlepost_atch_image') ?> (<?= show_filesize($D->p->post_attached['image']->filesize) ?>,&nbsp;<?= $D->p->post_attached['image']->size_original[0] ?>x<?= $D->p->post_attached['image']->size_original[1] ?>px):</span>
			<div class="klear"></div>
			<a href="<?= $C->SITE_URL ?>getfile/pid:<?= $D->p->post_tmp_id ?>/tp:image/<?= htmlspecialchars($D->p->post_attached['image']->title) ?>" class="img"><img src="<?= $C->IMG_URL ?>attachments/<?= $this->network->id ?>/<?= $D->p->post_attached['image']->file_thumbnail ?>" alt="<?= htmlspecialchars($D->p->post_attached['image']->title) ?>" /></a><br />
			<div class="klear"></div>
		</div>
		<?php } ?>
		
		<div class="attachment">
			<span class="imgtxt"><?= $this->lang('vpost_video') ?></span>
			<div class="klear"></div>
			<div class="postattachedvideo">
				<?= $D->video->embed_code ?>
			</div>
			<div class="klear"></div>
		</div>
		
		<div class="meta">
			<?php if( $D->p->post_commentsnum == 0 ) { ?>
			<a href="<?= $D->p->permalink ?>#comments" class="comments"><?= $this->lang('singlepost_ftr_comments_0') ?></a>
			<?php } else { ?>
			<a href="<?= $D->p->permalink ?>#comments" class="comments"><?= $this->lang($D->p->post_commentsnum==1?'singlepost_ftr_comments_1':'singlepost_ftr_comments_mr', array('#NUM#'=>$D->p->post_commentsnum)) ?></a>
			<?php } ?>
			&middot; <a href="<?= $D->p->permalink ?>" title="<?= $this->lang('singlepost_ftr_permalink') ?>"><?= post::parse_date($D->p->post_date) ?></a>
			<?= post::parse_api($D->p->post_api_id) ?>
		</div>
	</div>
<?php
	
	$this->load_template('mobile/footer.php');
	
?>