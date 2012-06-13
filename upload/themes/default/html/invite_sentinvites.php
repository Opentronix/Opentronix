<?php
	
	$this->load_template('header.php');
	
?>
		<div id="invcenter">
			<h2><?= $this->lang('invite_title') ?></h2>			
			<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
				<a href="<?= $C->SITE_URL ?>invite"><b><?= $this->lang('os_invite_tab_colleagues') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/parsemail"><b><?= $this->lang('os_invite_tab_parsemail') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/uploadcsv"><b><?= $this->lang('os_invite_tab_uploadcsv') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/personalurl"><b><?= $this->lang('os_invite_tab_personalurl') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/sentinvites" class="onhtab"><b><?= $this->lang('os_invite_tab_sentinvites') ?></b></a>
			</div>
			<?php if( count($D->invites) == 0 ) { ?>
			<?= msgbox($this->lang('inv_sent_nosent_ttl'), $this->lang('inv_sent_nosent_txt'), FALSE) ?>
			<?php } else { ?>
			<div class="invinfo">
				<?= $this->lang('os_invite_txt_sentinvites', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#OUTSIDE_SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE)) ?>
			</div>
			<div class="greygrad">
				<div class="greygrad2">
					<div class="greygrad3" style="padding-bottom:0px;">
						<table width="100%" id="invtable">
							<tr>
								<td width="18"></td>
								<td><b><?= $this->lang('inv_sent_tbl_name') ?></b></td>
								<td><b><?= $this->lang('inv_sent_tbl_date') ?></b></td>
								<td><b><?= $this->lang('inv_sent_tbl_status') ?></b></td>
							</tr>
							<tr><td style="padding:0px; height:1px; background-color:#ccc;" colspan="4"></td></tr>
							<tr>
							<?php foreach($D->invites as $obj) { ?>
								<td>
									<?php if( !empty($obj->avatar) ) { ?>
									<img src="<?= $C->IMG_URL ?>avatars/thumbs2/<?= $obj->avatar ?>" alt="" />
									<?php } ?>
								</td>
								<td>
									<?php if( ! empty($obj->username) ) { ?>
									<b><a href="<?= $C->SITE_URL ?><?= $obj->username ?>"><?= htmlspecialchars($obj->fullname) ?></a></b> <small>(@<?= $obj->username ?>)</small>
									<?php } else { ?>
									<b><?= htmlspecialchars($obj->fullname) ?></b>
									<?php } ?>
								</td>
								<td><?= strftime($this->lang('inv_sent_date_format'), $obj->date) ?></td>
								<td><?= $this->lang( $obj->is_accepted ? 'inv_sent_status_acptd' : 'inv_sent_status_pndng' ) ?></td>
							</tr>
							<tr><td style="padding:0px; height:1px; background-color:#eee;" colspan="4"></td></tr>
							<?php } ?>
						</table>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
<?php
	
	$this->load_template('footer.php');
	
?>