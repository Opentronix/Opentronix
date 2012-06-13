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
		
		<?php if( isset($D->p->post_attached['videoembed']) ) { ?>
		<div class="attachment">
			<span class="imgtxt"><?= $this->lang('singlepost_atch_videoembed') ?>:</span>
			<div class="klear"></div>
			<a href="<?= str_replace('/view/', '/view/video/', $D->p->permalink) ?>" class="img"><img src="<?= $C->IMG_URL ?>attachments/<?= $this->network->id ?>/<?= $D->p->post_attached['videoembed']->file_thumbnail ?>" alt="" /></a><br />
			<div class="klear"></div>
		</div>
		<?php } ?>
		
		<div class="meta">
			<a href="<?= $D->p->permalink ?>" title="<?= $this->lang('singlepost_ftr_permalink') ?>"><?= post::parse_date($D->p->post_date) ?></a>
			<?= post::parse_api($D->post->post_api_id) ?>
		</div>
	</div>
	<hr />
	
	<?php if( $D->cnm > 0 ) { ?>
		<a name="comments"></a>
		<div class="cttl cmnts">
			<?php if( ! $D->cpg ) { ?>
			<b><?= $this->lang('vpost_cmnts_latest', array('#NUM#'=>$C->POST_LAST_COMMENTS)) ?></b>
			&middot;
			<a href="<?= $D->p->permalink ?>/cpg:1/#comments"> <?= $this->lang('vpost_cmnts_viewall') ?></a>
			<?php } else { ?>
			<b><?= $this->lang('vpost_cmnts_all', array('#NUM#'=>$D->cnm)) ?></b>
			<?php if( $D->cnum_pages > 1 ) { ?>
			&middot;
			<span><?= $this->lang('vpost_cmnts_allpg', array('#NUM#'=>$D->cpg, '#NUM2#'=>$D->cnum_pages)) ?></span>
			<?php } } ?>
		</div>
		<hr />
		<div id="comments">
		<?php $i = 0; foreach($D->comments as $c) { ?>
			<div class="<?= $i++%2==0 ? 'post dark' : 'post' ?>" style="background-image: url('<?= $C->IMG_URL ?>avatars/thumbs2/<?= $c->comment_user->avatar ?>');">
				<div class="postttl"><a href="<?= $C->SITE_URL.$c->comment_user->username ?>" class="user" title="<?= htmlspecialchars($c->comment_user->fullname) ?>"><?= $c->comment_user->username ?></a></div>
				<p class="message"><?= nl2br($c->parse_text()) ?></p>
				<div class="meta" style="padding-left:3px;">
					<?= post::parse_date($c->comment_date) ?>
					<?= post::parse_api($c->comment_api_id) ?>
				</div>
			</div>
			<hr />
		<?php } ?>
		<?php if( $D->cnum_pages>1 && $D->cpg>1 && $D->cpg<$D->cnum_pages ) { ?>
			<div id="backnext">
				<a href="<?= $D->post->permalink ?>/cpg:<?= $D->cpg-1 ?>#comments"><?= $this->lang('comments_paging_prev') ?></a> |
				<a href="<?= $D->post->permalink ?>/cpg:<?= $D->cpg+1 ?>#comments"><?= $this->lang('comments_paging_next') ?></a>
			</div>
		<?php } elseif( $D->cnum_pages>1 && $D->cpg>1 ) { ?>
			<div id="backnext">
				<a href="<?= $D->post->permalink ?>/cpg:<?= $D->cpg-1 ?>#comments"><?= $this->lang('comments_paging_prev') ?></a>
			</div>
		<?php } elseif( $D->cnum_pages>1 && $D->cpg && $D->cpg<$D->cnum_pages ) { ?>
			<div id="backnext">
				<a href="<?= $D->post->permalink ?>/cpg:<?= $D->cpg+1 ?>#comments"><?= $this->lang('comments_paging_next') ?></a>
			</div>
		<?php } ?>
		</div>
	<?php } ?>
	
	<div class="cttl"><b><?= $this->lang('vpost_cmnts_add') ?></b></div>
	<div id="postcomment">
		<form method="post" action="<?= $D->post->permalink ?>#comments" onsubmit="return this.message.value!='';">
			<textarea name="message"></textarea>
			<input type="submit" value="<?= $this->lang('vpost_cmnts_add_sbm') ?>" />
		</form>
	</div>
	<hr />
	<div id="profileftr">
		<?php if( $D->post->post_user->id > 0 ) { ?>
			<?php if( $D->post->post_user->id != $this->user->id ) { ?>
			<a href="<?= $C->SITE_URL ?>newpost/mention:<?= $D->post->post_user->username ?>"><?= $this->lang('vpost_ftr_mention', array('#USERNAME#'=>$D->post->post_user->username)) ?></a>
			<?php } ?>
			<a href="<?= $C->SITE_URL.$D->post->post_user->username ?>"><?= $this->lang('vpost_ftr_profile', array('#USERNAME#'=>$D->post->post_user->username)) ?></a>
		<?php } ?>
		<?php if( $D->post->post_group ) { ?>
		<a href="<?= $C->SITE_URL.$D->post->post_group->groupname ?>"><?= $this->lang('vpost_ftr_group', array('#GROUP#'=>$D->post->post_group->title)) ?></a>
		<?php } ?>
	</div>
<?php
	
	$this->load_template('mobile/footer.php');
	
?>