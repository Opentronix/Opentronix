<?php
	
	$this->load_template('mobile_iphone/header.php');
	
?>
			<div id="searchpage">	
				<div id="attabs">
					<a href="<?= $C->SITE_URL ?>search/lookin:posts/lookfor:<?= htmlspecialchars(urlencode($D->search_string)) ?>" class="<?= $D->lookin=='posts'?'onattab':'' ?>"><b><?= $this->lang($D->lookin=='posts'?'iphone_srch_posts1':'iphone_srch_posts2') ?></b></a>
					<a href="<?= $C->SITE_URL ?>search/lookin:users/lookfor:<?= htmlspecialchars(urlencode($D->search_string)) ?>" class="<?= $D->lookin=='users'?'onattab':'' ?>"><b><?= $this->lang($D->lookin=='users'?'iphone_srch_users1':'iphone_srch_users2') ?></b></a>
					<a href="<?= $C->SITE_URL ?>search/lookin:groups/lookfor:<?= htmlspecialchars(urlencode($D->search_string)) ?>" class="<?= $D->lookin=='groups'?'onattab':'' ?>"><b><?= $this->lang($D->lookin=='groups'?'iphone_srch_groups1':'iphone_srch_groups2') ?></b></a>
				</div>
				<div id="searchinput">
					<form method="post" action="<?= $C->SITE_URL ?>search">
						<input type="hidden" name="lookin" value="<?= $D->lookin ?>" />
						<span>
							<input type="text" name="lookfor" value="<?= htmlspecialchars($D->search_string) ?>" />
							<input type="submit" value="" id="submitsearch" />
						</span>
					</form>
				</div>
				
				<?php if( ! empty($D->search_string) ) { ?>
				
					<?php if( $D->num_results == 0 ) { ?>
					
						<div id="listpage" style="background-color:#d2d2d2; padding-top:0px;">
							<div class="alert yellow"><?= $this->lang('search_nores') ?></div>
						</div>
						
					<?php } elseif( $D->lookin == 'users' ) { ?>
					
						<div id="listpage" style="background-color:#d2d2d2; padding-top:0px;">
							<div id="prlist">
								<div id="prlist2">
									<div id="prlist3">
										<div id="prlist4">
											<div id="prlist5">
												<div id="prlist6">
													<?= $D->users_html ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php if( $D->num_pages > 1 ) { ?>
							<div id="nextback">
								<div id="nextback2">
									<div id="nextback3">
										<?php if( $D->pg > 1 ) { ?>
										<a href="<?= $C->SITE_URL ?>search/pg:<?= $D->pg-1 ?>/lookin:users/lookfor:<?= htmlspecialchars(urlencode($D->search_string)) ?>" class="nb_back"><?= $this->lang('iphone_paging_back') ?></a>
										<?php } ?>
										<?php if( $D->pg < $D->num_pages ) { ?>
										<a href="<?= $C->SITE_URL ?>search/pg:<?= $D->pg+1 ?>/lookin:users/lookfor:<?= htmlspecialchars(urlencode($D->search_string)) ?>" class="nb_next"><?= $this->lang('iphone_paging_next') ?></a>
										<?php } ?>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
						
					<?php } elseif( $D->lookin == 'groups' ) { ?>
					
						<div id="listpage" style="background-color:#d2d2d2; padding-top:0px;">
							<div id="prlist">
								<div id="prlist2">
									<div id="prlist3">
										<div id="prlist4">
											<div id="prlist5">
												<div id="prlist6">
													<?= $D->groups_html ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php if( $D->num_pages > 1 ) { ?>
							<div id="nextback">
								<div id="nextback2">
									<div id="nextback3">
										<?php if( $D->pg > 1 ) { ?>
										<a href="<?= $C->SITE_URL ?>search/pg:<?= $D->pg-1 ?>/lookin:groups/lookfor:<?= htmlspecialchars(urlencode($D->search_string)) ?>" class="nb_back"><?= $this->lang('iphone_paging_back') ?></a>
										<?php } ?>
										<?php if( $D->pg < $D->num_pages ) { ?>
										<a href="<?= $C->SITE_URL ?>search/pg:<?= $D->pg+1 ?>/lookin:groups/lookfor:<?= htmlspecialchars(urlencode($D->search_string)) ?>" class="nb_next"><?= $this->lang('iphone_paging_next') ?></a>
										<?php } ?>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
						
					<?php } elseif( $D->lookin == 'posts' ) { ?>
					
						<div id="postspage" style="background-color:#d2d2d2; border-top:0px solid; padding-top:0px; ;">
							<div id="posts" style="padding-top:0;">
								<?= $D->posts_html ?>
							</div>
							<?php if( $D->num_results > $D->posts_number ) { ?>
							<div id="loadmore">
								<div id="loadmoreloader" style="display:none;"></div>
								<a id="loadmorelink" href="javascript:;" onclick="load_more_results('posts', <?= $D->posts_number ?>, <?= $D->num_results ?>);"><b><strong><?= $this->lang('iphone_paging_posts') ?></strong></b></a>
							</div>
							<?php } ?>
						</div>
						
					<?php } ?>
					
				<?php } elseif( $D->lookin == 'posts' && count($D->saved_searches) ) {  ?>
					
					<div id="savedsearches">
						<b><?= $this->lang('iphone_saved_searches') ?></b>	
						<?php foreach($D->saved_searches as $s) { ?>
						<a href="<?= $C->SITE_URL ?>search/saved:<?= $s->search_key ?>"><?= htmlspecialchars($s->search_string) ?></a>
						<?php } ?>	
					</div>
					
				<?php }  ?>
			</div>
<?php
	
	$this->load_template('mobile_iphone/footer.php');
	
?>