<?php
		
	$this->load_template('header.php');
	
?>
		<div class="ttl" style="margin-bottom:10px;">
			<div class="ttl2">
				<h3><?= $this->lang('signup_subtitle', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?></h3>
				<?php if( $D->steps > 1 ) { ?>
				<div id="postfilter"><span><?= $this->lang('signup_step') ?> <?= $C->USERS_EMAIL_CONFIRMATION ? 2 : 1 ?> / <?= $D->steps ?></span></div>
				<?php } ?>
			</div>
		</div>
		
		<?php if($D->error) { ?>
			<?= errorbox($this->lang('signup_step2_error'), $this->lang($D->errmsg,$D->errmsg_lngkeys)); ?>
		<?php } ?>
		<form method="post" action="">
			<table id="regform" cellspacing="5" style="margin-bottom:10px;">
				<?php if( preg_match('/\@proxymail\.facebook\.com$/iu', $D->email) ) { ?>
				<?php } elseif( $C->USERS_EMAIL_CONFIRMATION ) { ?>
				<tr>
					<td class="regparam" style="padding:5px; padding-top:7px;"><?= $this->lang('signup_step2_form_email') ?></td>
					<td class="confirmedmail">
						<b><?= $D->email ?> <img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/greencheck.gif" alt="" /></b>
						<?= $this->lang('signup_step2_email_confirmed') ?>
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_email') ?></td>
					<td><input type="text" name="email" value="<?= htmlspecialchars($D->email) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<?php } ?>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_fullname') ?></td>
					<td><input type="text" name="fullname" value="<?= htmlspecialchars($D->fullname) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_username') ?></td>
					<td><input type="text" name="username" value="<?= htmlspecialchars($D->username) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_password') ?></td>
					<td><input type="password" name="password" value="<?= htmlspecialchars($D->password) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<tr>
					<td class="regparam"><?= $this->lang('signup_step2_form_password2') ?></td>
					<td><input type="password" name="password2" value="<?= htmlspecialchars($D->password2) ?>" autocomplete="off" class="reginp" /></td>
				</tr>
				<?php if( ! $C->USERS_EMAIL_CONFIRMATION ) { ?>
				<tr>
					<td class="regparam" style="padding-top:13px;"><?= $this->lang('signup_step2_form_captcha') ?></td>
					<td>
						<input type="hidden" name="captcha_key" value="<?= $D->captcha_key ?>" />
						<?= $D->captcha_html ?><br />
						<input type="text" maxlength="20" name="captcha_word" value="" autocomplete="off" class="reginp" style="width:168px; margin-top:5px;" />
					</td>
				</tr>
				<?php } ?>
				<?php if( $D->terms_of_use ) { ?>
				<tr>
					<td></td>
					<td>
						<input type="checkbox" name="accept_terms" value="1" <?= $D->accept_terms?'checked="checked"':'' ?> style="float:left;" />
						<div style="float:left; margin-left:5px; margin-top:3px;"><?= $this->lang('signup_step2_form_terms',array('#SITE_TITLE#'=>$C->SITE_TITLE,'#A2#'=>'</a>','#A1#'=>'<a href="'.$C->SITE_URL.'terms" target="_blank">')) ?></div>
						<div class="klear"></div>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td></td>
					<td><input type="submit" value="<?= $this->lang('signup_step2_form_submit') ?>" style="padding:4px; font-weight:bold;" /></td>
				</tr>
			</table>
		</form>
		<?php if( isset($C->FACEBOOK_API_KEY) && !empty($C->FACEBOOK_API_KEY) ) { ?>
			<script type="text/javascript">
				function func_fbconnected() {
					document.getElementById("fblnkdiv").style.display = "block";
					FB.Connect.showPermissionDialog("email");
					try{ document.getElementById("fbbtndiv").style.display = "none"; } catch(e) {}
					try{ document.getElementById("twitterconnect").style.display = "none"; } catch(e) {}
				}
				function func_fbconnected2() {
					api = FB.Facebook.apiClient;
					api.users_getInfo( [api.get_session().uid], ['name', 'username', 'about_me', 'birthday_date', 'pic_big', 'profile_url', 'website', 'proxied_email'], function(result) {
						var inurl	= "";
						if(result && result.length && result.length == 1) {
							var uinfo	= result[0];
							inurl	+= "fb_uid="+api.get_session().uid;
							for(var k in uinfo) {
								inurl	+= "&fb["+k+"]="+encodeURIComponent(uinfo[k]);
							}
						}
						window.location.href	= "<?= $C->SITE_URL ?>signin/js:fbconnect/?"+inurl;
					} );
				}
				function func_fbnotconnected() {
					try{ document.getElementById("fbbtndiv").style.display = "block"; } catch(e) {}
				}
			</script>
			<div id="fblnkdiv" style="display:none; margin-top:5px; line-height:1.4;">
				<a href="<?= $C->SITE_URL ?>signin/fbinfo:<?= $this->param('fbinfo') ?>"><b>&raquo;</b> <?= $this->lang('signup_fb_differentuser') ?></a><br />
				<a href="javascript:;" onclick="FB.Facebook.apiClient.revokeAuthorization(null,function(){window.location.href='<?= $C->SITE_URL ?>signin/signout:ok';});"><b>&raquo;</b> <?= $this->lang('signup_fb_disconnect') ?></a>
			</div>
			<div id="fbbtndiv" style="display:none; float:left; margin-left:5px; margin-top:6px;">
				<fb:login-button onlogin="func_fbconnected2();"></fb:login-button>
			</div>
		<?php } ?>
		<?php if( isset($C->TWITTER_CONSUMER_KEY,$C->TWITTER_CONSUMER_SECRET) && !empty($C->TWITTER_CONSUMER_KEY) && !empty($C->TWITTER_CONSUMER_SECRET) && $this->param('get')!='twitter' ) { ?>
		<div style="margin-top:5px; float:left;">
			<a id="twitterconnect" href="<?= $C->SITE_URL ?>twitter-connect?backto=<?= $C->SITE_URL ?>signin/get:twitter" title="Twitter Connect" style="float:left; margin-left:5px; margin-top:1px;"><b>Twitter</b></a>
		</div>
		<?php } ?>
		
<?php
	
	$this->load_template('footer.php');
	
?>