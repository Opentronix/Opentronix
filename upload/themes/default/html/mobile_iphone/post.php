<?php
	
	$this->load_template('mobile_iphone/header.php');
	
?>
		<script type="text/javascript">
			pferr_user1	= "<?= $this->lang('iphone_np_jserr_user1') ?>";
			pferr_user2	= "<?= $this->lang('iphone_np_jserr_user2') ?>";
			pferr_user3	= "<?= $this->lang('iphone_np_jserr_user3') ?>";
			pferr_msg	= "<?= $this->lang('iphone_np_jserr_msg') ?>";
			pf_attachdel_confirm	= "<?= $this->lang('iphone_np_atch_delete') ?>";
			current_usr	= "<?= $this->user->info->username ?>";
			pf_msg_max_length	= <?= $C->POST_MAX_SYMBOLS ?>;
		</script>
		<form name="pf" method="post" action="" onsubmit="pf_submit(); return false;">
			<input type="hidden" name="post_temp_id" value="<?= $D->post_temp_id ?>" />
			<input type="hidden" name="sharewith" value="<?= htmlspecialchars($D->pf_sharewith) ?>" />
			<input type="hidden" name="attached_link" value="0" />
			<input type="hidden" name="attached_image" value="0" />
			<input type="hidden" name="attached_videoembed" value="0" />
			<input type="hidden" name="attached_file" value="0" />
			<div id="postpage">
				<?php if( $D->error ) { ?>
					<div class="alert red"><?= $this->lang('iphone_newpost_error') ?></div>
				<?php } ?>
				<div id="sharewith">
					<a href="javascript:;" onclick="np_sharewith();" id="swdropper" style="<?= $D->pf_sharewith=='user'?'display:none':'' ?>"><strong><span id="sharewith_sel"><?= $D->pf_sharewith=='all' ? $this->lang('iphone_np_sharewith_all') : htmlspecialchars($D->pf_sharewithx) ?></span></strong><small></small></a>
					<div id="swinput" style="<?= $D->pf_sharewith=='user'?'':'display:none' ?>">
						<div id="swinput2">
							<div id="swinput3">
								<input type="text" name="sharewith_inp" value="<?= $D->pf_sharewith=='user'?htmlspecialchars($D->pf_sharewithx):'' ?>" />
								<a href="javascript:;" onclick="np_sharewith();" id="swinputdropper"></a>
							</div>
						</div>
					</div>
					<b><?= $this->lang('iphone_np_sharewith') ?></b>
					<div id="swdropmenu" style="display:none;">
						<div id="swdropmenu2">
							<a href="javascript:;" onclick="np_sharewith_all('<?= $this->lang('iphone_np_sharewith_all') ?>');"><?= $this->lang('iphone_np_sharewith_all') ?><small></small></a>
							<?php foreach($D->menu_groups as $g) { ?>
							<a href="javascript:;" onclick="np_sharewith_group('<?= htmlspecialchars($g->title) ?>');"><?= htmlspecialchars($g->title) ?><small></small></a>
							<?php } ?>
							<a href="javascript:;" onclick="np_sharewith_user('');" style="border-bottom:0px;"><?= $this->lang('iphone_np_sharewith_usr') ?> <span>&middot; <?= $this->lang('iphone_np_sharewith_usrd') ?></span></a>
						</div>
					</div>
				</div>
				<div id="txtr">
					<textarea name="message" onblur="pf_textarea_blur()" style="-webkit-border-radius: 3px;"><?= htmlspecialchars($D->pf_message) ?></textarea>
				</div>
				<div id="attbtns">
					<? if( $C->ATTACH_LINK_DISABLED==1 ) { echo '<div style="display:none;">'; }  ?>
					<a href="javascript:;" id="attbtn_link" onclick="pf_attachbox_open('link')" style="width:23%;" class="ab_first"><strong><?= $this->lang('iphone_np_atchbtn_link') ?></strong></a>
					<? if( $C->ATTACH_LINK_DISABLED==1 ) { echo '</div>'; }  ?>
					<? if( $C->ATTACH_IMAGE_DISABLED==1 ) { echo '<div style="display:none;">'; }  ?>
					<a href="javascript:;" id="attbtn_image" onclick="pf_attachbox_open('image')" style="width:29%;"><strong><?= $this->lang('iphone_np_atchbtn_image') ?></strong></a>
					<? if( $C->ATTACH_IMAGE_DISABLED==1 ) { echo '</div>'; }  ?>
					<? if( $C->ATTACH_VIDEO_DISABLED==1 ) { echo '<div style="display:none;">'; }  ?>
					<a href="javascript:;" id="attbtn_videoembed" onclick="pf_attachbox_open('videoembed')" style="width:26%;"><strong><?= $this->lang('iphone_np_atchbtn_video') ?></strong></a>
					<? if( $C->ATTACH_VIDEO_DISABLED==1 ) { echo '</div>'; }  ?>
					<? if( $C->ATTACH_FILE_DISABLED==1 ) { echo '<div style="display:none;">'; }  ?>
					<a href="javascript:;" id="attbtn_file" onclick="pf_attachbox_open('file')" style="width:22%;" class="ab_last"><strong><?= $this->lang('iphone_np_atchbtn_file') ?></strong></a>
					<? if( $C->ATTACH_FILE_DISABLED==1 ) { echo '</div>'; }  ?>
				</div>
				<a href="javascript:;" onclick="pf_submit();" id="updatebtn"><strong><b><?= $this->lang('iphone_np_submit') ?></b></strong></a>
			</div>
			<div id="pf_attachbox_container" style="display:none;">
				<div id="blackoverlay2"></div>
				<div id="postattacher">
					<div id="pf_attach_link" style="display:none;">
						<div id="postattacher2">
							<h2><?= $this->lang('iphone_np_link_title') ?></h2>
							<div id="patt">
								<input type="text" id="atchinp_link" value="" />
							</div>
							<a href="javascript:;" onclick="pf_attachbox_submit()" id="attachbtn"><b><?= $this->lang('iphone_np_link_button') ?></b></a>
							<div class="postattach_loader"></div>
							<a href="javascript:;" onclick="pf_attachbox_close()" id="closeattach"></a>
						</div>
					</div>
					<div id="pf_attach_link_on" style="display:none;">
						<div id="postattacher2">
							<h2><?= $this->lang('iphone_np_link_title_on') ?></h2>
							<div class="curratt">
								<b id="atchinp_link_on"></b>
								<div class="currattmask"></div>
							</div>
							<a href="javascript:;" onclick="pf_attachbox_delete()" id="reattachbtn"><b><?= $this->lang('iphone_np_link_button_on') ?></b></a>
							<a href="javascript:;" onclick="pf_attachbox_close()" id="closeattach"></a>
						</div>
					</div>
					<div id="pf_attach_file" style="display:none;">
						<div id="postattacher2">
							<h2><?= $this->lang('iphone_np_file_title') ?></h2>
							<div id="patt">
								<input type="file" id="atchinp_file" value="" />
							</div>
							<a href="javascript:;" onclick="pf_attachbox_submit()" id="attachbtn"><b><?= $this->lang('iphone_np_file_button') ?></b></a>
							<div class="postattach_loader"></div>
							<a href="javascript:;" onclick="pf_attachbox_close()" id="closeattach"></a>
						</div>
					</div>
					<div id="pf_attach_file_on" style="display:none;">
						<div id="postattacher2">
							<h2><?= $this->lang('iphone_np_file_title_on') ?></h2>
							<div class="curratt">
								<b id="atchinp_file_on"></b>
								<div class="currattmask"></div>
							</div>
							<a href="javascript:;" onclick="pf_attachbox_delete()" id="reattachbtn"><b><?= $this->lang('iphone_np_file_button_on') ?></b></a>
							<a href="javascript:;" onclick="pf_attachbox_close()" id="closeattach"></a>
						</div>
					</div>
					<div id="pf_attach_videoembed" style="display:none;">
						<div id="postattacher2">
							<h2><?= $this->lang('iphone_np_videoem_title') ?></h2>
							<div id="patt">
								<input type="text" id="atchinp_videoembed" value="" />
							</div>
							<a href="javascript:;" onclick="pf_attachbox_submit()" id="attachbtn"><b><?= $this->lang('iphone_np_videoem_button') ?></b></a>
							<div class="postattach_loader"></div>
							<a href="javascript:;" onclick="pf_attachbox_close()" id="closeattach"></a>
						</div>
					</div>
					<div id="pf_attach_videoembed_on" style="display:none;">
						<div id="postattacher2">
							<h2><?= $this->lang('iphone_np_videoem_title_on') ?></h2>
							<div class="curratt">
								<b id="atchinp_videoembed_on"></b>
								<div class="currattmask"></div>
							</div>
							<a href="javascript:;" onclick="pf_attachbox_delete()" id="reattachbtn"><b><?= $this->lang('iphone_np_videoem_button_on') ?></b></a>
							<a href="javascript:;" onclick="pf_attachbox_close()" id="closeattach"></a>
						</div>
					</div>
					<div id="pf_attach_image" style="display:none;">
						<div id="attabs">
							<a href="javascript:;" onclick="pf_attachbox_imgtab('upl')" id="pf_attach_image_lnk_upl"><b><?= $this->lang('iphone_np_image_tab_upl') ?></b></a>
							<a href="javascript:;" onclick="pf_attachbox_imgtab('url')" id="pf_attach_image_lnk_url"><b><?= $this->lang('iphone_np_image_tab_url') ?></b></a>
						</div>
						<div id="pf_attach_image_upl" style="display:none;">
							<div id="postattacher2">
								<h2><?= $this->lang('iphone_np_image_title_upl') ?></h2>
								<div id="patt">
									<input type="file" id="atchinp_image_upl" value="" />
								</div>
								<a href="javascript:;" onclick="pf_attachbox_submit()" id="attachbtn"><b><?= $this->lang('iphone_np_image_button') ?></b></a>
								<div class="postattach_loader"></div>
								<a href="javascript:;" onclick="pf_attachbox_close()" id="closeattach"></a>
							</div>
						</div>
						<div id="pf_attach_image_url" style="display:none;">
							<div id="postattacher2">
								<h2><?= $this->lang('iphone_np_image_title_url') ?></h2>
								<div id="patt">
									<input type="text" id="atchinp_image_url" value="" />
								</div>
								<a href="javascript:;" onclick="pf_attachbox_submit()" id="attachbtn"><b><?= $this->lang('iphone_np_image_button') ?></b></a>
								<div class="postattach_loader"></div>
								<a href="javascript:;" onclick="pf_attachbox_close()" id="closeattach"></a>
							</div>
						</div>
					</div>
					<div id="pf_attach_image_on" style="display:none;">
						<div id="postattacher2">
							<h2><?= $this->lang('iphone_np_image_title_on') ?></h2>
							<div class="curratt">
								<b id="atchinp_image_on"></b>
								<div class="currattmask"></div>
							</div>
							<a href="javascript:;" onclick="pf_attachbox_delete()" id="reattachbtn"><b><?= $this->lang('iphone_np_image_button_on') ?></b></a>
							<a href="javascript:;" onclick="pf_attachbox_close()" id="closeattach"></a>
						</div>
					</div>
				</div>
			</div>
		</form>
		<script type="text/javascript">
			setTimeout( function() {
				pf_validate(document.pf.message);
				pf_validate_advanced(document.pf.message);
			}, 1000 );	
		</script>
<?php
	
	$this->load_template('mobile_iphone/footer.php');
	
?>