<?php
	
	$this->load_template('header.php');
	
	if( $D->tab != 'posts' ) {
?>
					<div id="invcenter">
						<h2><?= $D->search_title ?></h2>			
						<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
							<a href="<?= $C->SITE_URL ?>search/tab:posts/s:<?= urlencode($D->search_string) ?>" class="<?= $D->tab=='posts'?'onhtab':'' ?>"><b><?= $this->lang('srch_tab_posts') ?></b></a>
							<a href="<?= $C->SITE_URL ?>search/tab:users/s:<?= urlencode($D->search_string) ?>" class="<?= $D->tab=='users'?'onhtab':'' ?>"><b><?= $this->lang('srch_tab_users') ?></b></a>
							<a href="<?= $C->SITE_URL ?>search/tab:groups/s:<?= urlencode($D->search_string) ?>" class="<?= $D->tab=='groups'?'onhtab':'' ?>"><b><?= $this->lang('srch_tab_groups') ?></b></a>
						</div>
						<?php if( empty($D->search_string)) { ?>
						<form method="post" action="<?= $C->SITE_URL ?>search">
							<input type="hidden" name="lookin" value="<?= $D->tab ?>" />
							<strong style="display:block;padding:5px;padding-left:9px;"><?= $this->lang('srch_tab_defform_txt') ?></strong>
							<div class="greygrad">
								<div class="greygrad2">
									<div class="greygrad3" style="padding:3px;">
										<table id="setform" cellspacing="5">
											<tr>
												<td>
													<input type="text" name="lookfor" value="<?= htmlspecialchars($D->search_string) ?>" class="setinp" style="width:250px;" maxlength="255" />
													<input type="submit" value="<?= $this->lang('srch_tab_defform_btn') ?>" style="padding:4px; font-weight:bold;"/>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</form>
						<?php } ?>
						<div id="grouplist" class="groupspage">
							<?php if( $D->tab == 'users' ) { ?>
								<?= $D->users_html ?>
							<?php } elseif( $D->tab == 'groups' ) { ?>
								<?= $D->groups_html ?>
							<?php } ?>
						</div>
					</div>
					<script type="text/javascript">
						window.onload	= function() {
							document.search_form.lookfor.focus();
						};
					</script>
<?php } else { ?>
					<div id="invcenter">
						<h2 id="sttl"><?= $D->search_title ?></h2>
						<?php if( $D->can_be_saved ) { ?>
						<a id="savesearch" href="javascript:;" onclick="save_search_on('<?= $D->ajax_url ?>');" style="display:<?= $D->search_saved?'none':'block' ?>;" onfocus="this.blur();"><b><?= $this->lang('srch_posts_save_add') ?></b></a>
						<a id="remsearch" href="javascript:;" onclick="save_search_off('<?= $D->ajax_url ?>', '<?= $this->lang('srch_posts_save_delc') ?>');" style="display:<?= $D->search_saved?'block':'none' ?>;" onfocus="this.blur();"><b><?= $this->lang('srch_posts_save_del') ?></b></a>
						<?php } ?>
						<div id="searchbostsleft">
							<div class="htabs" style="margin-bottom:6px; margin-top:0px;">
								<a href="<?= $C->SITE_URL ?>search/tab:posts/s:<?= urlencode($D->search_string) ?>" class="<?= $D->tab=='posts'?'onhtab':'' ?>"><b><?= $this->lang('srch_tab_posts') ?></b></a>
								<a href="<?= $C->SITE_URL ?>search/tab:users/s:<?= urlencode($D->search_string) ?>" class="<?= $D->tab=='users'?'onhtab':'' ?>"><b><?= $this->lang('srch_tab_users') ?></b></a>
								<a href="<?= $C->SITE_URL ?>search/tab:groups/s:<?= urlencode($D->search_string) ?>" class="<?= $D->tab=='groups'?'onhtab':'' ?>"><b><?= $this->lang('srch_tab_groups') ?></b></a>
							</div>
							<div id="searchresultspost">
								<?php if( $D->error ) { ?>
								<?= msgbox($this->lang('srch_noresult_posts_ttl'), $D->errmsg, FALSE) ?>
								<?php } ?>
								<div id="posts_html">
									<?= $D->posts_html ?>
								</div>
							</div>
						</div>
						<div id="searchbostsright">
							<div class="ttl" style="margin-bottom:3px;"><div class="ttl2"><h3><?= $this->lang('srch_posts_filterbox') ?></h3></div></div>
							<form method="post" name="psrch" action="<?= $C->SITE_URL ?>search">
								<input type="hidden" name="lookin" value="posts" />
								<strong><?= $this->lang('srch_posts_string') ?></strong>
								<div class="greygrad">
									<div class="greygrad2">
										<div class="greygrad3">
											<input type="text" name="lookfor" value="<?= htmlspecialchars($D->search_string) ?>" maxlength="100" />
										</div>
									</div>
								</div>
								<a href="javascript:;" onclick="srchposts_togglefilt('type');" id="srchposts_droplnk_type" class="sdropper<?= $D->box_expanded->type?' dropppped':'' ?>" onfocus="this.blur();"><?= $this->lang('srch_posts_ptype') ?></a>
								<div class="greygrad" id="srchposts_dropbox_type" style="display:<?= $D->box_expanded->type?'block':'none' ?>;">
									<div class="greygrad2">
										<div class="greygrad3">				
											<label><input type="checkbox" name="ptype[]" value="link" <?= isset($D->form_type['link'])?'checked="checked"':'' ?> /><span><?= $this->lang('srch_posts_ptp_link') ?></span></label>
											<label><input type="checkbox" name="ptype[]" value="image" <?= isset($D->form_type['image'])?'checked="checked"':'' ?> /><span><?= $this->lang('srch_posts_ptp_image') ?></span></label>
											<label><input type="checkbox" name="ptype[]" value="video" <?= isset($D->form_type['video'])?'checked="checked"':'' ?> /><span><?= $this->lang('srch_posts_ptp_video') ?></span></label>
											<label><input type="checkbox" name="ptype[]" value="file" <?= isset($D->form_type['file'])?'checked="checked"':'' ?> /><span><?= $this->lang('srch_posts_ptp_file') ?></span></label>
											<label><input type="checkbox" name="ptype[]" value="comments" <?= isset($D->form_type['comments'])?'checked="checked"':'' ?> /><span><?= $this->lang('srch_posts_ptp_comments') ?></span></label>
										</div>
									</div>
								</div>
								<a href="javascript:;" onclick="srchposts_togglefilt('author');" id="srchposts_droplnk_author" class="sdropper<?= $D->box_expanded->author?' dropppped':'' ?>" onfocus="this.blur();"><?= $this->lang('srch_posts_user') ?></a>				 
								<div class="greygrad" id="srchposts_dropbox_author" style="display:<?= $D->box_expanded->author?'block':'none' ?>;">
									<div class="greygrad2">
										<div class="greygrad3">
											<input type="text" name="puser" value="<?= htmlspecialchars($D->form_user) ?>" maxlength="100" rel="autocomplete" autocompleteoffset="0,3" />
										</div>
									</div>
								</div>
								<a href="javascript:;" onclick="srchposts_togglefilt('group');" id="srchposts_droplnk_group" class="sdropper<?= $D->box_expanded->group?' dropppped':'' ?>" onfocus="this.blur();"><?= $this->lang('srch_posts_group') ?></a>
								<div class="greygrad" id="srchposts_dropbox_group" style="display:<?= $D->box_expanded->group?'block':'none' ?>;">
									<div class="greygrad2">
										<div class="greygrad3">
											<input type="text"name="pgroup" value="<?= htmlspecialchars($D->form_group) ?>" maxlength="100" rel="autocomplete" autocompleteoffset="0,3" />
										</div>
									</div>
								</div>
								<a href="javascript:;" onclick="srchposts_togglefilt('date');" id="srchposts_droplnk_date" class="sdropper<?= $D->box_expanded->date?' dropppped':'' ?>" onfocus="this.blur();"><?= $this->lang('srch_posts_date') ?></a>
								<div class="greygrad" id="srchposts_dropbox_date" style="display:<?= $D->box_expanded->date?'block':'none' ?>;">
									<div class="greygrad2">
										<div class="greygrad3" style="padding:5px;">
											<table>
												<tr>
													<td align="right"><?= $this->lang('srch_posts_dt_from') ?></td>
													<td>
														<select name="pdate1[d]">
															<?php foreach($D->form_date1_days as $i) { ?>
															<option value="<?= $i ?>" <?= $i==$D->form_date1['d']?'selected="selected"':'' ?>><?= $i ?></option>
															<?php } ?>
														</select>
													</td>
													<td>
														<select name="pdate1[m]">
															<?php foreach($D->form_date1_months as $i) { ?>
															<option value="<?= $i ?>" <?= $i==$D->form_date1['m']?'selected="selected"':'' ?>><?= empty($i) ? '' : str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
															<?php } ?>
														</select>
													</td>
													<td>
														<select name="pdate1[y]">
															<?php foreach($D->form_date1_years as $i) { ?>
															<option value="<?= $i ?>" <?= $i==$D->form_date1['y']?'selected="selected"':'' ?>><?= $i ?></option>
															<?php } ?>
														</select>
													</td>
												</tr>
												<tr>
													<td align="right"><?= $this->lang('srch_posts_dt_to') ?></td>
													<td>
														<select name="pdate2[d]">
															<?php foreach($D->form_date2_days as $i) { ?>
															<option value="<?= $i ?>" <?= $i==$D->form_date2['d']?'selected="selected"':'' ?>><?= $i ?></option>
															<?php } ?>
														</select>
													</td>
													<td>
														<select name="pdate2[m]">
															<?php foreach($D->form_date2_months as $i) { ?>
															<option value="<?= $i ?>" <?= $i==$D->form_date2['m']?'selected="selected"':'' ?>><?= empty($i) ? '' : str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
															<?php } ?>
														</select>
													</td>
													<td>
														<select name="pdate2[y]">
															<?php foreach($D->form_date2_years as $i) { ?>
															<option value="<?= $i ?>" <?= $i==$D->form_date2['y']?'selected="selected"':'' ?>><?= $i ?></option>
															<?php } ?>
														</select>
													</td>
												</tr>
											</table>
										</div>
									</div>
								</div>
								<div class="greygrad">
									<div class="greygrad2">
										<div class="greygrad3">
											<input type="submit" value="<?= $this->lang('srch_posts_submit') ?>" style="padding:2px; font-weight:bold;"/>
										</div>
									</div>
								</div>
							</form>
							<?php if( count($D->saved_searches) > 0 ) { ?>
							<div class="ttl" style="margin-bottom:5px;"><div class="ttl2"><h3><?= $this->lang('stch_posts_saved') ?></h3></div></div>
							<div class="taglist">
								<?php foreach($D->saved_searches as $id=>$tmp) { ?>
								<a href="<?= $C->SITE_URL ?>search/saved:<?= $tmp->search_key ?>" class="<?= $id==$D->search_saved?'ontag':'' ?>"><?= preg_replace('/^\#/', '<small>#</small>', htmlspecialchars(str_cut($tmp->search_string,23))) ?></a>
								<?php } ?>
							</div>
							<?php } ?>
						</div>
					</div>
					<script type="text/javascript">
						window.onload	= function() {
							document.psrch.lookfor.focus();
						};
					</script>
<?php
	}
	
	$this->load_template('footer.php');
	
?>