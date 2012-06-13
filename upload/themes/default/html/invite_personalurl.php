<?php
	
	$this->load_template('header.php');
	
?>
		<div id="invcenter">
			<h2><?= $this->lang('invite_title') ?></h2>			
			<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
				<a href="<?= $C->SITE_URL ?>invite"><b><?= $this->lang('os_invite_tab_colleagues') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/parsemail"><b><?= $this->lang('os_invite_tab_parsemail') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/uploadcsv"><b><?= $this->lang('os_invite_tab_uploadcsv') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/personalurl" class="onhtab"><b><?= $this->lang('os_invite_tab_personalurl') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/sentinvites"><b><?= $this->lang('os_invite_tab_sentinvites') ?></b></a>
			</div>
			<div class="invinfo">
				<?= $this->lang('os_invite_txt_personalurl', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#OUTSIDE_SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE)) ?>
			</div>
			<script type="text/javascript">
				function select_text(obj) {
				}
			</script>
			<div class="greygrad">
				<div class="greygrad2">
					<div class="greygrad3" style="padding-bottom:0px;">
						<b style="display:block; padding-left:8px;"><?= $this->lang('inv_plnk_yourlink') ?></b>
						<div id="invitelink" onmousedown="select_text(this);"><?= $D->invitation_link ?></div>
					</div>
				</div>
			</div>
		</div>
<?php
	
	$this->load_template('footer.php');
	
?>