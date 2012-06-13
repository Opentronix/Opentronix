<?php
	
	$this->load_template('header_flybox.php');
	
	if( $D->type == 'image' ) {
?>
			<div class="flyboxattachment">
				<img src="<?= $C->IMG_URL ?>attachments/<?= $this->network->id ?>/<?= $D->att->file_preview ?>" style="width:<?= $D->att->size_preview[0] ?>px; height:<?= $D->att->size_preview[1] ?>px;" alt="<?= htmlspecialchars($D->att->title) ?>" />
			</div>
			<div class="flyboxdata">
				<b title="<?= htmlspecialchars($D->att->title) ?>"><?= htmlspecialchars(str_cut_link($D->att->title,30)) ?></b> &middot;
				<a href="<?= $C->SITE_URL ?>getfile/pid:<?= $D->p->post_tmp_id ?>/tp:image/<?= htmlspecialchars($D->att->title) ?>" target="_top">
					<?= $D->att->size_original[0] ?>x<?= $D->att->size_original[1] ?>px,
					<?= show_filesize($D->att->filesize) ?>
				</a>
				<!--
				&middot;
				<a href="<?= $C->SITE_URL ?>getfile/pid:<?= $D->p->post_tmp_id ?>/tp:image/<?= htmlspecialchars($D->att->title) ?>" target="_top"><?= $this->lang('post_atchimg_ftr_dwnld') ?></a>
				&middot;
				<a href="<?= $D->p->permalink ?>" target="_top"><?= $this->lang('post_atchftr_permalink') ?></a>
				-->
			</div>
<?php
	}
	elseif( $D->type == 'videoembed' )
	{
?>	
			<div class="flyboxattachment">
				<?= $D->att->embed_code ?>
			</div>
			<div class="flyboxdata">
				<a href="<?= $D->att->orig_url ?>" target="_blank"><?= str_cut_link($D->att->orig_url,55) ?></a>
				<!-- 
				<a href="<?= $D->att->orig_url ?>" target="_blank"><?= $this->lang('post_atchvid_ftr_site') ?></a> &middot;
				<a href="<?= $D->p->permalink ?>" target="_top"><?= $this->lang('post_atchftr_permalink') ?></a>
				-->
			</div>
<?php	
	}
	
	$this->load_template('footer_flybox.php');
	
?>