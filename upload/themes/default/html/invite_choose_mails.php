<?php
	
	$this->load_template('header.php');
	
?>
		<script type="text/javascript">
			function js_chkbxfunc() {
				var inps	= document.f.getElementsByTagName("INPUT");
				for(var i=0; i<inps.length; i++) {
					if( inps[i].type != "checkbox" ) {
						continue;
					}
					if( ! inps[i].name.match(/^emails/) ) {
						continue;
					}
					if( inps[i].checked ) {
						document.f.sbmbtn.disabled	= false;
						return;
					}
				}
				document.f.sbmbtn.disabled	= true;
			}
			function js_chkbxfunc_sel(sdf) {
				var inps	= document.f.getElementsByTagName("INPUT");
				for(var i=0; i<inps.length; i++) {
					if( inps[i].type != "checkbox" ) {
						continue;
					}
					if( ! inps[i].name.match(/^emails/) ) {
						continue;
					}
					inps[i].checked	= sdf.checked;
				}
				js_chkbxfunc();
			}
		</script>
		<div id="invcenter">
			<h2><?= $this->lang('invite_title') ?></h2>			
			<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
				<a href="<?= $C->SITE_URL ?>invite"><b><?= $this->lang('os_invite_tab_colleagues') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/parsemail" class="<?= $this->request[1]=='parsemail'?'onhtab':'' ?>"><b><?= $this->lang('os_invite_tab_parsemail') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/uploadcsv" class="<?= $this->request[1]=='uploadcsv'?'onhtab':'' ?>"><b><?= $this->lang('os_invite_tab_uploadcsv') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/personalurl"><b><?= $this->lang('os_invite_tab_personalurl') ?></b></a>
				<a href="<?= $C->SITE_URL ?>invite/sentinvites"><b><?= $this->lang('os_invite_tab_sentinvites') ?></b></a>
			</div>
			<div class="invinfo">
				<?= $this->lang('inv_prsml_gmail_ok_ttl') ?>
			</div>
			<div class="greygrad">
				<div class="greygrad2">
					<div class="greygrad3" style="padding-bottom:0px;">
						<form name="f" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>/get:loaded">
							<table width="100%" id="invtable">
								<tr>
									<td width="40"><input type="checkbox" onclick="js_chkbxfunc_sel(this);" onfocus="this.blur();" checked="checked" /></td>
									<td width="300"><b><?= $this->lang('inv_prsml_gmail_ok_eml') ?></b></td>
									<td><b><?= $this->lang('inv_prsml_gmail_ok_nm') ?></b></td>
								</tr>
								<tr><td style="padding:0px; height:1px; background-color:#ccc;" colspan="3"></td></tr>
								<?php foreach( $D->parsed_mails as $k=>$v ) { ?>
								<tr>
									<td><input type="checkbox" name="emails[]" value="<?= htmlspecialchars($k) ?>" checked="checked" onclick="js_chkbxfunc();" onchange="js_chkbxfunc();" /></td>
									<td><b><?= htmlspecialchars($k) ?></b></td>
									<td><?= htmlspecialchars($v) ?></td>
								</tr>
								<tr><td style="padding:0px; height:1px; background-color:#eee;" colspan="4"></td></tr>
								<?php } ?>
								<tr>
									<td colspan="3" style="padding-top:10px;">
										<input type="submit" name="sbmbtn" value="<?= $this->lang('inv_prsml_gmail_ok_btn') ?>" style="padding:4px; font-weight:bold;" />
									</td>
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