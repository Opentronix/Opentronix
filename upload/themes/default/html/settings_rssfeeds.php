<?php
	
	$this->load_template('header.php');
	
?>
					<div id="settings">
						<div id="settings_left">				
							<div class="ttl" style="margin-right:12px;"><div class="ttl2"><h3><?= $this->lang('settings_menu_title') ?></h3></div></div>
							<div class="sidenav">
								<a href="<?= $C->SITE_URL ?>settings/profile"><?= $this->lang('settings_menu_profile') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/contacts"><?= $this->lang('settings_menu_contacts') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/avatar"><?= $this->lang('settings_menu_avatar') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/password"><?= $this->lang('settings_menu_password') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/email"><?= $this->lang('settings_menu_email') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/system"><?= $this->lang('settings_menu_system') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/notifications"><?= $this->lang('settings_menu_notif') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/rssfeeds" class="onsidenav"><?= $this->lang('settings_menu_rssfeeds') ?></a>
								<a href="<?= $C->SITE_URL ?>settings/delaccount"><?= $this->lang('settings_menu_delaccount') ?></a>
							</div>
						</div>
						<div id="settings_right">
							<div class="ttl" style="margin-bottom:5px;"><div class="ttl2"><h3><?= $this->lang('st_rssfeeds_title') ?></h3></div></div>
							<?php if( $this->param('msg') == 'added' ) { ?>
								<?= okbox($this->lang('st_rssfeeds_ok'), $this->lang('st_rssfeeds_ok_txt', array('#SITE_TITLE#'=>$C->SITE_TITLE)), TRUE, 'margin-bottom:5px;') ?>
							<?php } elseif( $this->param('msg') == 'deleted' ) { ?>
								<?= okbox($this->lang('st_rssfeeds_ok'), $this->lang('st_rssfeeds_okdel_txt'), TRUE, 'margin-bottom:5px;') ?>
							<?php } ?>
							<?php if( count($D->feeds) == 0 ) { ?>
								<?= msgbox($this->lang('st_rssfeeds_nofeeds_ttl'), $this->lang('st_rssfeeds_nofeeds_txt'), FALSE, 'margin-bottom:5px;') ?>
							<?php } else { ?>
							<table id="setform" cellspacing="0" cellpadding="5">
								<tr>
									<td width="110" class="setparam" valign="top"><?= $this->lang('st_rssfeeds_feedslist') ?></td>
									<td width="400">
										<div class="groupfeedslist">
										<?php foreach($D->feeds as $f) { ?>
										<div class="groupfeed">
											<a href="<?= $C->SITE_URL ?>settings/rssfeeds/delfeed:<?= $f->id ?>" onclick="return confirm('<?= $this->lang('st_rssfeeds_feed_delcnf') ?>');" title="<?= $this->lang('st_rssfeeds_feed_delete') ?>" onfocus="this.blur();" class="grpdelbtn"></a>
											<?= htmlspecialchars(str_cut($f->feed_title,35)) ?>				
											<span><a href="<?= htmlspecialchars($f->feed_url) ?>" target="_blank"><?= htmlspecialchars(str_cut_link($f->feed_url,50)) ?></a></span>
											<?php if( !empty($f->filter_keywords) ) { ?>
											<span><?= $this->lang('st_rssfeeds_feed_filter') ?> <?= htmlspecialchars($f->filter_keywords) ?></span>
											<?php } ?>
										</div>
										<?php } ?>
										</div>
									</td>
								</tr>
							</table>		
							<?php } ?>
							<div class="ttl" style="margin-top:10px; margin-bottom:6px;"><div class="ttl2"><h3><?= $this->lang('st_rssfeeds_f_title') ?></h3></div></div>
							<?php if( $D->error ) { ?>
								<?= errorbox($this->lang('st_rssfeeds_err'), $this->lang($D->errmsg), TRUE, 'margin-bottom:5px;') ?>
							<?php } elseif( $D->newfeed_auth_msg ) { ?>
								<?= msgbox($this->lang('st_rssfeeds_pwdreq_ttl'), $this->lang('st_rssfeeds_pwdreq_txt'), TRUE, 'margin-bottom:5px;') ?>
							<?php } ?>
							<form method="post" action="<?= $C->SITE_URL ?>settings/rssfeeds">
								<table id="setform" cellspacing="0" cellpadding="5">
									<tr>
										<td width="110" class="setparam"><?= $this->lang('st_rssfeeds_f_url') ?></td>
										<td><input type="text" class="setinp" name="newfeed_url" value="<?= htmlspecialchars($D->newfeed_url) ?>" maxlength="255" style="width:320px; padding:3px;" /></td>
									</tr>
									<?php if( $D->newfeed_auth_req ) { ?>
									<tr>
										<td class="setparam"><?= $this->lang('st_rssfeeds_f_usr') ?></td>
										<td><input type="text" class="setinp" name="newfeed_username" value="<?= htmlspecialchars($D->newfeed_username) ?>" autocomplete="off" maxlength="255" style="width:320px; padding:3px;" /></td>
									</tr>
									<tr>
										<td class="setparam"><?= $this->lang('st_rssfeeds_f_pwd') ?></td>
										<td><input type="password" class="setinp" name="newfeed_password" value="<?= htmlspecialchars($D->newfeed_password) ?>" autocomplete="off" maxlength="255" style="width:320px; padding:3px;" /></td>
									</tr>
									<?php } ?>
									<tr>
										<td style="padding-bottom:0px;" class="setparam"><?= $this->lang('st_rssfeeds_f_filter') ?></td>
										<td style="padding-bottom:0px;"><input type="text" class="setinp" name="newfeed_filter" value="<?= htmlspecialchars($D->newfeed_filter) ?>" maxlength="255" style="width:320px; padding:3px;" /></td>
									</tr>
									<tr>
										<td style="padding:0px;"></td>
										<td class="setparam" style="text-align:left; font-size:10px; padding-top:2px;"><?= $this->lang('st_rssfeeds_f_filtertxt') ?></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="submit" name="sbm" value="<?= $this->lang('st_rssfeeds_f_submit') ?>" style="padding:4px; font-weight:bold;" /></td>
									</tr>
								</table>
							</form>
										
						</div>
					</div>
<?php
	
	$this->load_template('footer.php');
	
?>