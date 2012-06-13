<?php
	
	$this->load_template('header.php');
	
?>
					<div id="invcenter">
						<h2><?= $this->lang('groups_page_ttl2') ?></h2>
						<?php if( $this->param('msg')=='deleted' ) { ?>
						<?= okbox($this->lang('groups_msgbox_deleted_ttl'), $this->lang('groups_msgbox_deleted_txt')) ?>
						<?php } ?>	
						<?php if( $this->user->is_logged ) { ?>
						<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
							<?php if( $D->tabnums['my'] == 0 ) { ?>
							<a href="<?= $C->SITE_URL ?>groups/tab:all" class="<?= $D->tab=='all'?'onhtab':'' ?>"><b><?= $this->lang('groups_page_tab_all') ?> <small>(<?= $D->tabnums['all'] ?>)</small></b></a>
							<a href="<?= $C->SITE_URL ?>groups/tab:my" class="<?= $D->tab=='my'?'onhtab':'' ?>"><b><?= $this->lang('groups_page_tab_my') ?> <small>(<?= $D->tabnums['my'] ?>)</small></b></a>
							<?php } else { ?>
							<a href="<?= $C->SITE_URL ?>groups/tab:my" class="<?= $D->tab=='my'?'onhtab':'' ?>"><b><?= $this->lang('groups_page_tab_my') ?> <small>(<?= $D->tabnums['my'] ?>)</small></b></a>
							<a href="<?= $C->SITE_URL ?>groups/tab:all" class="<?= $D->tab=='all'?'onhtab':'' ?>"><b><?= $this->lang('groups_page_tab_all') ?> <small>(<?= $D->tabnums['all'] ?>)</small></b></a>
							<?php } ?>
							<a href="<?= $C->SITE_URL ?>groups/new" class="newgroupbtn"><b><?= $this->lang('groups_page_tab_add') ?></b></a>
						</div>
						<?php } else { ?>
						<div class="htabs" style="margin:0px; margin-bottom:6px; height:1px;"></div>
						<?php } ?>
						<div id="grouplist" class="groupspage">
							<?= $D->groups_html ?>
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>