<?php
	
	$this->load_template('header.php');
	
	?>
	<script type="text/javascript">
		function js_fix_url() {
			window.location.href	= window.location.href.replace("signout:ok", "");
		}
		<?php if( $D->allow_fb_connect && isset($C->FACEBOOK_API_KEY) && !empty($C->FACEBOOK_API_KEY) ) { ?>
			function func_fbconnected() {
				<?php if( $this->param('signout') == 'ok' ) { ?>
				FB.Facebook.apiClient.revokeAuthorization(null, js_fix_url);
				return;
				<?php } ?>
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
				<?php if( $this->param('signout') == 'ok' ) { ?>
				js_fix_url();
				<?php } else { ?>
				document.getElementById("fbcntbtndiv").style.display	= "block";
				<?php } ?>
			}
		<?php } elseif( $this->param('signout')=='ok' && isset($C->FACEBOOK_API_KEY) && !empty($C->FACEBOOK_API_KEY) ) { ?>
			function func_fbconnected() {
				FB.Facebook.apiClient.revokeAuthorization(null, js_fix_url);
			}
			function func_fbnotconnected() {
				js_fix_url();
			}
		<?php } elseif( $this->param('signout') == 'ok' ) { ?>
			js_fix_url();
		<?php } ?>
	</script>
	
			<?php if($D->error) { ?>
				<?= errorbox($this->lang('signin_form_error'), $this->lang($D->errmsg)); ?>
			<?php } elseif($this->param('pass')=='changed') { ?>
				<?= okbox($this->lang('signinforg_alldone_ttl'), $this->lang('signinforg_alldone_txt')) ?>
			<?php } ?>
			<div id="poblicpage_login">
				<form method="post" action="">
					<table id="regform" cellspacing="5">
						<tr>
							<td></td>
							<td>
								<b><?= $this->lang('signin_form_caption') ?></b>
								<a id="forgotpass" href="<?= $C->SITE_URL ?>signin/forgotten"><?= $this->lang('signin_form_forgotten') ?></a>
							</td>
						</tr>
						<tr>
							<td class="regparam"><?= $this->lang('signin_form_email') ?></td>
							<td><input type="text" name="email" value="<?= htmlspecialchars($D->email) ?>" tabindex="1" maxlength="100" class="reginp" /></td>
						</tr>
						<tr>
							<td class="regparam"><?= $this->lang('signin_form_password') ?></td>
							<td><input type="password" name="password" value="<?= htmlspecialchars($D->password) ?>" tabindex="2" maxlength="100" class="reginp" /></td>
						</tr>
						<tr>
							<td></td>
							<td valign="middle">
								<input type="submit" value="<?= $this->lang('signin_form_submit') ?>" tabindex="4" style="float:left; padding:4px; font-weight:bold;" />
								<label style="margin:0px; padding:0px; margin-left:10px; margin-top:7px; float:left; clear:none;">
									<input type="checkbox" name="rememberme" value="1" <?= $D->rememberme==1?'checked="checked"':'' ?> tabindex="3" />
									<span style="padding:2px; padding-left:5px;"><?= $this->lang('signin_form_rem') ?></span>
								</label>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div id="poblicpage_info">
				<h2><?= $this->lang('signin_reg_title', array('#SITE_TITLE#'=>$C->SITE_TITLE)) ?></h2>
				<?= $this->lang('os_signin_reg_txt_comp', array('#COMPANY#'=>$C->COMPANY, '#NUM_MEMBERS#'=>$D->num_members, '#NUM_POSTS#'=>$D->num_posts)) ?>
				<div id="joinnow">
					<a href="<?= $C->SITE_URL ?>signup" class="bluebtn1" style="margin-top:0px;"><b><?= $this->lang('signin_reg_button') ?></b></a>
					
					<?php if( $D->allow_fb_connect ) { ?>
					<div style="float:left; margin-top:5px; margin-left:10px; margin-right:5px;"><?= $this->lang('signin_or_connect') ?></div>
					<div style="float:left; margin-top:1px;">
						<div id="fbcntbtndiv" style="display:none; float:left; margin-left:5px;;">
							<?php if( isset($C->FACEBOOK_API_KEY) && !empty($C->FACEBOOK_API_KEY) ) { ?>
							<div style="float:left; margin-top:1px;" title="Facebook Connect">
								<fb:login-button onlogin="func_fbconnected();"></fb:login-button>
							</div>
							<?php } ?>
						</div>
						<?php if( isset($C->TWITTER_CONSUMER_KEY,$C->TWITTER_CONSUMER_SECRET) && !empty($C->TWITTER_CONSUMER_KEY) && !empty($C->TWITTER_CONSUMER_SECRET) ) { ?>
						<a id="twitterconnect" href="<?= $C->SITE_URL ?>twitter-connect?backto=<?= $C->SITE_URL ?>signin/get:twitter" title="Twitter Connect" style="margin-left:5px; margin-top:1px;"><b>Twitter</b></a>
						<?php } ?>
					</div>
					<?php } ?>
					
				</div>
			</div>
			<div class="klear"></div>
<?php
	
	$this->load_template('footer.php');
	
?>