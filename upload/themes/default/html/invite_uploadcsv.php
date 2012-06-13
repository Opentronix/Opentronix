<?php
	
	$this->load_template('header.php');
	
?>
		<div id="invcenter">
			<h2><?= $this->lang('invite_title') ?></h2>			
			<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
				<a href="<?= $C->SITE_URL ?>invite"><b><?= $this->lang('os_invite_tab_colleagues') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/parsemail"><b><?= $this->lang('os_invite_tab_parsemail') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/uploadcsv" class="onhtab"><b><?= $this->lang('os_invite_tab_uploadcsv') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/personalurl"><b><?= $this->lang('os_invite_tab_personalurl') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/sentinvites"><b><?= $this->lang('os_invite_tab_sentinvites') ?></b></a>
			</div>
			<div class="invinfo">
				<?= $this->lang('os_invite_txt_uploadcsv', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?>
			</div>

			<?php if( $D->error ) { ?>
			<?= errorbox($this->lang('inv_uplfile_error'), $this->lang($D->errmsg), TRUE, 'margin-bottom:5px;') ?>
			<?php } ?>
			
			<div class="greygrad">
				<div class="greygrad2">
					<div class="greygrad3" style="padding-bottom:0px;">
						<form method="post" action="" enctype="multipart/form-data">
							<table id="setform" cellspacing="5">
								<tr>
									<td class="setparam"><?= $this->lang('inv_uplfile_finp') ?></td>
									<td><input type="file" class="setinp" name="uplfile" value="" /></td>
								</tr>
								<tr>
									<td></td>
									<td><input type="submit" value="<?= $this->lang('inv_uplfile_fbtn') ?>" style="padding:4px; font-weight:bold;" /></td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>
		</div>
<?php
	
	$this->load_template('footer.php');
	
?>