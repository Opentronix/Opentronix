<?php
	
	$this->load_template('header.php');
	
?>
		<script type="text/javascript">
			inv_lines	= 0;
			function invform_line_add() {
				var tb	= d.getElementById("invite_table");
				if( !tb ){ return; }
				var tr	= tb.getElementsByTagName("TR");
				if( !tr || tr.length == 0 ) { return; }
				tr	= tr[0];
				tr	= tr.cloneNode(true);
				var i;
				var inp	= tr.getElementsByTagName("INPUT");
				for(i=0; i<inp.length; i++) {
					inp[i].value	= "";
				}
				inp	= tr.getElementsByTagName("SELECT");
				for(i=0; i<inp.length; i++) {
					inp[i].selectedIndex	= 0;
				}
				tb.appendChild(tr);
				inv_lines	++;
			}
			function invform_line_del(confirm_msg) {
				if( inv_lines <= 1 ) { return; }
				var tb	= d.getElementById("invite_table");
				if( !tb ){ return; }
				var tr	= tb.getElementsByTagName("TR");
				if( !tr || tr.length == 0 ) { return; }
				var i, inp;
				for(i=tr.length-1; i>=0; i--) {
					inp	= tr[i].getElementsByTagName("INPUT");
					if(inp.length==2 && inp[0].value=="" && inp[1].value=="") {
						tb.removeChild(tr[i]);
						inv_lines	--;
						return;
					}
				}
				if(confirm_msg && !confirm(confirm_msg)) {
					return;
				}
				tb.removeChild(tr[tr.length-1]);
				inv_lines	--;
			}
		</script>
		<div id="invcenter">
			<h2><?= $this->lang('invite_title') ?></h2>			
			<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
				<a href="<?= $C->SITE_URL ?>invite" class="onhtab"><b><?= $this->lang('os_invite_tab_colleagues') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/parsemail"><b><?= $this->lang('os_invite_tab_parsemail') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/uploadcsv"><b><?= $this->lang('os_invite_tab_uploadcsv') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/personalurl"><b><?= $this->lang('os_invite_tab_personalurl') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/sentinvites"><b><?= $this->lang('os_invite_tab_sentinvites') ?></b></a>
			</div>
		<?php if( $D->status=='okbox' || $D->status=='msgbox' ) { $f = $D->status; ?>
			<?= $f($D->mtitle, $D->message, FALSE) ?>
		<?php } else { ?>
			<div class="invinfo">
				<?= $this->lang('os_invite_txt_colleagues', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#OUTSIDE_SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE)) ?>
			</div>
			<?php if( $D->status=='errorbox' ) { ?>
			<?= errorbox($D->mtitle, $D->message, TRUE, 'margin-bottom:5px;') ?>
			<?php } ?>
			<div class="greygrad">
				<div class="greygrad2">
					<div class="greygrad3" style="padding-bottom:0px;">
						<form method="post" action="">
							<table id="setform" cellspacing="5">
								<tr>
									<td></td>
									<td><b style="font-size:14px;"><?= $this->lang('inv_clg_form_title') ?></b></td>
								</tr>
								<tbody id="invite_table" class="invtbl_emldmns_<?= count($C->EMAIL_DOMAINS) ?>">
								<?php foreach($D->formdata as $obj) { ?>
								<script type="text/javascript">
									inv_lines ++; 
								</script>
								<tr>
									<td class="setparam" style="text-align:right;"><?= $this->lang('inv_clg_form_name') ?></td>
									<td><input type="text" name="name[]" value="<?= htmlspecialchars($obj->name) ?>" class="setinp" style="width:200px; padding:3px;" maxlength="100" /></td>
									<td class="setparam" style="text-align:right; width:80px;"><?= $this->lang('inv_clg_form_email') ?></td>
									<td><input type="text" name="email[]" value="<?= htmlspecialchars($obj->email) ?>" class="setinp" style="width:280px; padding:3px;" maxlength="100" /></td>
								</tr>
								<?php } ?>
								</tbody>
								<tr>
									<td></td>
									<td>
										<a href="javascript:;" onclick="invform_line_add();" onfocus="this.blur();" class="addaline"><?= $this->lang('inv_clg_form_lnadd') ?></a>
										<a href="javascript:;" onclick="invform_line_del();" onfocus="this.blur();" class="remaline"><?= $this->lang('inv_clg_form_lndel') ?></a>
									</td>
								</tr>	
								<tr>
									<td></td>
									<td><input type="submit" value="<?= $this->lang('inv_clg_form_submit') ?>" style="padding:4px; font-weight:bold;"/></td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
		</div>
<?php
	
	$this->load_template('footer.php');
	
?>