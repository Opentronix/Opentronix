<?php

	$this->user->write_pageview();

	$hdr_search	= ($this->request[0]=='members' ? 'users' : ($this->request[0]=='groups' ? 'groups' : ($this->request[0]=='search' ? $D->tab : 'posts') ) );

	$this->load_langfile('inside/header.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<title><?= htmlspecialchars($D->page_title) ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link href="<?= $C->SITE_URL ?>themes/default/css/inside.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript" src="<?= $C->SITE_URL ?>themes/default/js/inside.js"></script>
		<script type="text/javascript" src="<?= $C->SITE_URL ?>themes/default/js/inside_autocomplete.js"></script>
		<script type="text/javascript" src="<?= $C->SITE_URL ?>themes/default/js/inside_postform.js"></script>
		<script type="text/javascript" src="<?= $C->SITE_URL ?>themes/default/js/inside_posts.js"></script>
		<script type="text/javascript" src="<?= $C->SITE_URL ?>themes/default/js/swfobject.js"></script>
		<base href="<?= $C->SITE_URL ?>" />
		<script type="text/javascript"> var siteurl = "<?= $C->SITE_URL ?>"; </script>

		<?php if( isset($D->page_favicon) ) { ?>
		<link href="<?= $D->page_favicon ?>" type="image/x-icon" rel="shortcut icon" />
		<?php } elseif( $C->HDR_SHOW_FAVICON == 1 ) { ?>
		<link href="<?= $C->SITE_URL.'themes/default/imgs/favicon.ico' ?>" type="image/x-icon" rel="shortcut icon" />
		<?php } elseif( $C->HDR_SHOW_FAVICON == 2 ) { ?>
		<link href="<?= $C->IMG_URL.'attachments/'.$this->network->id.'/'.$C->HDR_CUSTOM_FAVICON ?>" type="image/x-icon" rel="shortcut icon" />
		<?php } ?>
		<?php if(isset($D->rss_feeds)) { foreach($D->rss_feeds as &$f) { ?>
		<link rel="alternate" type="application/atom+xml" title="<?= $f[1] ?>" href="<?= $f[0] ?>" />
		<?php }} ?>
		<?php if( $this->user->is_logged && $this->user->info->js_animations == "0" ) { ?>
		<script type="text/javascript"> disable_animations = true; </script>
		<?php } ?>
		<?php if( $this->user->is_logged && $this->user->sess['total_pageviews'] == 1 ) { ?>
		<script type="text/javascript"> pf_autoopen = true; </script>
		<?php } ?>
		<?php if( isset($C->FACEBOOK_API_KEY) && !empty($C->FACEBOOK_API_KEY) ) { ?>
		<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_US" type="text/javascript"></script>
		<script type="text/javascript">
			FB_RequireFeatures(["Api","Connect"], function() {
				//FB.FBDebug.logLevel=1;
				FB.Facebook.init('<?= $C->FACEBOOK_API_KEY ?>', '<?= $C->SITE_URL ?>xd_receiver.htm');
				<?php if( !$this->user->is_logged && $this->request[0]!='signin' && $this->request[0]!='signup' ) { ?>
				function hrd_func_fbconnected() {
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
				FB.Connect.ifUserConnected( hrd_func_fbconnected, function(){try{func_fbnotconnected();}catch(e){};} );
				<?php } else { ?>
				FB.Connect.ifUserConnected( function(){try{func_fbconnected();}catch(e){};}, function(){try{func_fbnotconnected();}catch(e){};} );
				<?php } ?>
			});
		</script>
		<?php } ?>
		<?php if( $this->lang('global_html_direction') == 'rtl' ) { ?>
		<style type="text/css"> #site { direction:rtl; } </style>
		<?php } ?>
	</head>
	<body>
		<div id="site">
			<div id="wholesite">
				<div id="toprow" class="<?= $this->request[0]=='dashboard'||$this->request[0]=='home'? 'specialhomelink' : '' ?>">
					<div id="toprow2">
						<?php if( $C->HDR_SHOW_LOGO==2 && !empty($C->HDR_CUSTOM_LOGO) ) { ?>
						<a href="<?= $C->SITE_URL ?>dashboard" id="logolink_custom" title="<?= htmlspecialchars($C->SITE_TITLE) ?>"><img src="<?= $C->IMG_URL.'attachments/'.$this->network->id.'/'.$C->HDR_CUSTOM_LOGO ?>" alt="<?= htmlspecialchars($C->SITE_TITLE) ?>" /></a>
						<?php } else { ?>
						<a href="<?= $C->SITE_URL ?>dashboard" id="logolink" title="<?= htmlspecialchars($C->SITE_TITLE) ?>"><strong><?= htmlspecialchars($C->SITE_TITLE) ?></strong></a>
						<?php } ?>
						<div id="userstuff">
							<?php if( $this->user->is_logged ) { ?>
							<div id="avatar"><img src="<?= $C->IMG_URL ?>avatars/thumbs2/<?= $this->user->info->avatar ?>" alt="" /></div>
							<a href="<?= $C->SITE_URL ?><?= $this->user->info->username ?>" id="username"><span><?= $this->user->info->username ?></span></a>
							<div id="userlinks">
								<a href="<?= $C->SITE_URL ?>settings"><b><?= $this->lang('hdr_nav_settings') ?></b></a>
								<a href="<?= $C->SITE_URL ?>signout"><b><?= $this->lang('hdr_nav_signout') ?></b></a>
							</div>
							<?php } else { ?>
							<div id="userlinks">
								<a href="<?= $C->SITE_URL ?>signin"><b><?= $this->lang('hdr_nav_signin') ?></b></a>
								<a href="<?= $C->SITE_URL ?>signup"><b><?= $this->lang('hdr_nav_signup') ?></b></a>
							</div>
							<?php } ?>
							<div id="topsearch">
								<form name="search_form" method="post" action="<?= $C->SITE_URL ?>search">
									<input type="hidden" name="lookin" value="<?= $hdr_search ?>" />
									<div id="searchbtn"><input type="submit" value="<?= $this->lang('hdr_search_submit') ?>" /></div>
									<div class="searchselect">
										<a id="search_drop_lnk" href="javascript:;" onfocus="this.blur();" onclick="try{msgbox_close();}catch(e){}; dropdiv_open('search_drop_menu1');"><?= $this->lang('hdr_search_'.$hdr_search) ?></a>
										<div id="search_drop_menu1" class="searchselectmenu" style="display:none;">
											<a href="javascript:;" onclick="hdr_search_settype('posts',this.innerHTML);dropdiv_close('search_drop_menu1');" onfocus="this.blur();"><?= $this->lang('hdr_search_posts') ?></a>
											<a href="javascript:;" onclick="hdr_search_settype('users',this.innerHTML);dropdiv_close('search_drop_menu1');" onfocus="this.blur();"><?= $this->lang('hdr_search_users') ?></a>
											<a href="javascript:;" onclick="hdr_search_settype('groups',this.innerHTML);dropdiv_close('search_drop_menu1');" onfocus="this.blur();" style="border-bottom:0px;"><?= $this->lang('hdr_search_groups') ?></a>
										</div>
									</div>
									<div id="searchinput"><input type="text" name="lookfor" value="<?= isset($D->search_string)?htmlspecialchars($D->search_string):'' ?>" rel="autocomplete" autocompleteoffset="-6,4" /></div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div id="nethdr1">
					<div id="nethdr2">
						<div id="netnav" class="specialhomelink">
							<a href="<?= $C->SITE_URL ?>dashboard" class="<?= $this->request[0]=='dashboard'||$this->request[0]=='home'?'onnettab ':'' ?>homelink"><span><b><?= $this->lang('hdr_nav_home') ?></b></span></a>
							<a href="<?= $C->SITE_URL ?>members" class="<?= $this->request[0]=='members'?'onnettab':'' ?>"><span><b><?= $this->lang('hdr_nav_users') ?></b></span></a>
							<a href="<?= $C->SITE_URL ?>groups" class="<?= $this->request[0]=='groups'?'onnettab':'' ?>"><span><b><?= $this->lang('hdr_nav_groups') ?></b></span></a>
							<?php if( $this->user->is_logged && $this->user->info->is_network_admin == 1 ) { ?>
							<a href="<?= $C->SITE_URL ?>admin" class="<?= $this->request[0]=='admin'?'onnettab':'' ?>"><span><b><?= $this->lang('hdr_nav_admin') ?></b></span></a>
							<?php } ?>
						</div>
					</div>
				</div>
				<div id="slim_msgbox" style="display:none;">
					<strong id="slim_msgbox_msg"></strong>
					<a href="javascript:;" onclick="msgbox_close('slim_msgbox'); this.blur();" onfocus="this.blur();"><b><?= $this->lang('pf_msg_okbutton') ?></b></a>
				</div>
				<?php if( $this->user->is_logged ) { ?>
				<div id="postform" style="display:none;">
					<form name="post_form" action="" method="post" onsubmit="return false;">
						<div id="pf_posting" style="display:none;">
							<img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/loading.gif" alt="" /><b><?= $this->lang('pf_msg_posting') ?></b>
						</div>
						<div id="pf_loading" style="display:none;">
							<img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/loading.gif" alt="" /><b><?= $this->lang('pf_msg_loading') ?></b>
						</div>
						<div id="pf_postedok" style="display:none;">
							<strong id="pf_postedok_msg"></strong>
							<a href="javascript:;" onclick="postform_topmsg_close();" onfocus="this.blur();"><b><?= $this->lang('pf_msg_okbutton') ?></b></a>
						</div>
						<div id="pf_postederror" style="display:none;">
							<strong id="pf_postederror_msg"></strong>
							<a href="javascript:;" onclick="postform_topmsg_close();" onfocus="this.blur();"><b><?= $this->lang('pf_msg_okbutton') ?></b></a>
						</div>
						<div id="pf_mainpart" style="display:none;">
							<script type="text/javascript">
								pf_msg_max_length	= <?= $C->POST_MAX_SYMBOLS ?>;
								pf_close_confirm	= "<?= $this->lang('pf_confrm_close') ?>";
								pf_rmatch_confirm	= "<?= $this->lang('pf_confrm_rmat') ?>";
							</script>
							<div id="pfhdr">
								<div id="pfhdrleft">
									<b id="pf_title_newpost"><?= $this->lang('pf_title_newmsg') ?></b>
									<b id="pf_title_edtpost" style="display:none;"><?= $this->lang('pf_title_edtmsg') ?></b>
									<div id="sharewith_user" class="pmuser" style="display:none;">
										<strong><?= $this->lang('pf_title_newmsg_usr') ?></strong> <input type="text" name="username" value="" rel="autocomplete" autocompleteoffset="0,3" autocompleteafter="d.post_form.message.focus(); postform_sharewith_user(d.post_form.username.value);" onblur="postform_bgcheck_username();" />
										<a href="javascript:;" onclick="dropdiv_open('updateoptions',-2);" onfocus="this.blur();"></a>
									</div>
									<div id="sharewith_group" class="pmuser" style="display:none;">
										<strong><?= $this->lang('pf_title_newmsg_grp') ?></strong> <input type="text" name="groupname" value="" rel="autocomplete" autocompleteoffset="0,3" autocompleteafter="d.post_form.message.focus(); postform_sharewith_group(d.post_form.groupname.value);" onblur="postform_bgcheck_groupname();" />
										<a href="javascript:;" onclick="dropdiv_open('updateoptions',-2);" onfocus="this.blur();"></a>
									</div>
									<div id="sharewith" onclick="dropdiv_open('updateoptions',-2);">
										<a href="javascript:;" id="selectedupdateoption" onfocus="this.blur();"><span defaultvalue="<?= $this->lang('os_pf_title_newmsg_all') ?>"></span><b></b></a>
										<div id="updateoptions" style="display:none;">
											<a href="javascript:;" onclick="postform_sharewith_all('<?= $this->lang('os_pf_title_newmsg_all') ?>');"><?= $this->lang('os_pf_title_newmsg_all') ?></a>
											<?php if( $this->request[0]=='user' && $this->params->user && $this->params->user!=$this->user->id && $tmp=$this->network->get_user_by_id($this->params->user) ) { ?>
											<a href="javascript:;" onclick="postform_sharewith_user('<?= htmlspecialchars($tmp->username) ?>');" onfocus="this.blur();" title="<?= htmlspecialchars($u->fullname) ?>"><?= htmlspecialchars(str_cut($tmp->username, 30)) ?></a>
											<?php } ?>
											<?php foreach($this->user->get_top_groups(10) as $g) { ?>
											<a href="javascript:;" onclick="postform_sharewith_group('<?= htmlspecialchars($g->title) ?>');" onfocus="this.blur();" title="<?= htmlspecialchars($g->title) ?>"><?= htmlspecialchars(str_cut($g->title, 30)) ?></a>
											<?php } ?>
											<!--
											<a href="javascript:;" onclick="postform_sharewith_findgroup();"><?= $this->lang('pf_title_newmsg_mngrp') ?></a>
											-->
											<a href="javascript:;" onclick="postform_sharewith_finduser();" style="border-bottom:0px;"><?= $this->lang('pf_title_newmsg_mnusr') ?></a>
										</div>
									</div>
								</div>
								<div id="pfhdrright">
									<a href="javascript:;" onclick="postform_close_withconfirm();" onfocus="this.blur();"></a>
									<small><?= $this->lang('pf_cnt_symbols_bfr') ?><span id="pf_chars_counter"><?= $C->POST_MAX_SYMBOLS ?></span><?= $this->lang('pf_cnt_symbols_aftr') ?></small>
								</div>
							</div>
							<textarea name="message" tabindex="1" rel="autocomplete" autocompleteoffset="0,3"></textarea>
							<div id="pfattach">
								<? if( $C->ATTACH_LINK_DISABLED==1 ) { echo '<div style="display:none;">'; }  ?>
								<a href="javascript:;" class="attachbtn" onclick="postform_attachbox_open('link', 96); this.blur();" id="attachbtn_link" tabindex="3"><b><?= $this->lang('pf_attachtab_link') ?></b></a>
								<? if( $C->ATTACH_LINK_DISABLED==1 ) { echo '</div>'; }  ?>
								<div id="attachok_link" class="attachok" style="display:none;"><span><b><?= $this->lang('pf_attached_link') ?></b> <em id="attachok_link_txt"></em> <a href="javascript:;" class="removeattachment" onclick="postform_attach_remove('link');" onfocus="this.blur();"></a></span></div>
								<? if( $C->ATTACH_IMAGE_DISABLED==1 ) { echo '<div style="display:none;">'; }  ?>
								<a href="javascript:;" class="attachbtn" onclick="postform_attachbox_open('image', 131); this.blur();" id="attachbtn_image" tabindex="3"><b><?= $this->lang('pf_attachtab_image') ?></b></a>
								<? if( $C->ATTACH_IMAGE_DISABLED==1 ) { echo '</div>'; }  ?>
								<div id="attachok_image" class="attachok" style="display:none;"><span><b><?= $this->lang('pf_attached_image') ?></b> <em id="attachok_image_txt"></em> <a href="javascript:;" class="removeattachment" onclick="postform_attach_remove('image');" onfocus="this.blur();"></a></span></div>
								<? if( $C->ATTACH_VIDEO_DISABLED==1 ) { echo '<div style="display:none;">'; }  ?>
								<a href="javascript:;" class="attachbtn" onclick="postform_attachbox_open('videoembed', 96); this.blur();" id="attachbtn_videoembed" tabindex="3"><b><?= $this->lang('pf_attachtab_videmb') ?></b></a>
								<? if( $C->ATTACH_VIDEO_DISABLED==1 ) { echo '</div>'; }  ?>
								<div id="attachok_videoembed" class="attachok" style="display:none;"><span><b><?= $this->lang('pf_attached_videmb') ?></b> <em id="attachok_videoembed_txt"></em> <a href="javascript:;" class="removeattachment" onclick="postform_attach_remove('videoembed');" onfocus="this.blur();"></a></span></div>
								<? if( $C->ATTACH_FILE_DISABLED==1 ) { echo '<div style="display:none;">'; }  ?>
								<a href="javascript:;" class="attachbtn" onclick="postform_attachbox_open('file', 96); this.blur();" id="attachbtn_file" tabindex="3"><b><?= $this->lang('pf_attachtab_file') ?></b></a>
								<? if( $C->ATTACH_FILE_DISABLED==1 ) { echo '</div>'; }  ?>
								<div id="attachok_file" class="attachok" style="display:none;"><span><b><?= $this->lang('pf_attached_file') ?></b> <em id="attachok_file_txt"></em> <a href="javascript:;" class="removeattachment" onclick="postform_attach_remove('file');" onfocus="this.blur();"></a></span></div>
								<a href="javascript:;" id="postbtn" onclick="postform_submit();" tabindex="2"><b id="postbtn_newpost"><?= $this->lang('pf_submit_newmsg') ?></b><b id="postbtn_edtpost" style="display:none;"><?= $this->lang('pf_submit_edtmsg') ?></b></a>
							</div>
						</div>
						<div id="attachbox" style="display:none;">
							<div id="attachboxhdr"></div>
							<div id="attachboxcontent">
								<div id="attachboxcontent_link" style="display:none;">
									<a href="javascript:;" class="closeattachbox" onclick="postform_attachbox_close();" onfocus="this.blur();"></a>
									<div class="attachform">
										<small id="attachboxtitle_link" defaultvalue="<?= $this->lang('pf_attachbx_ttl_link') ?>"></small>
										<input type="text" name="atch_link" value="" style="width:800px;" onpaste="postform_attach_pastelink(event,this,postform_attach_submit);" onkeyup="postform_attach_pastelink(event,this,postform_attach_submit);" />
									</div>
									<div id="attachboxcontent_link_ftr" class="submitattachment">
										<img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/loading.gif" alt="" style="margin-bottom:2px;" />
										<a href="javascript:;" class="submitattachmentbtn" onclick="postform_attach_submit();" onfocus="this.blur();"><b><?= $this->lang('pf_attachbtn_link') ?></b></a>
										<div class="orcancel"><?= $this->lang('pf_attachbtn_or') ?> <a href="javascript:;" onclick="postform_attachbox_close();" onfocus="this.blur();"><?= $this->lang('pf_attachbtn_orclose') ?></a></div>
									</div>
								</div>
								<div id="attachboxcontent_image" style="display:none;">
									<div class="litetabs">
										<a href="javascript:;" class="closeattachbox" onclick="postform_attachbox_close();" onfocus="this.blur();"></a>
										<a href="javascript:;" onclick="postform_attachimage_tab('upl');" id="attachform_img_upl_btn" class="onlitetab" onfocus="this.blur();"><b><?= $this->lang('pf_attachimg_tabupl') ?></b></a>
										<a href="javascript:;" onclick="postform_attachimage_tab('url');" id="attachform_img_url_btn" class="" onfocus="this.blur();"><b><?= $this->lang('pf_attachimg_taburl') ?></b></a>
									</div>
									<div class="attachform">
										<div id="attachform_img_upl_div">
											<small id="attachboxtitle_image_upl" defaultvalue="<?= $this->lang('pf_attachbx_ttl_imupl') ?>"></small>
											<input type="file" name="atch_image_upl" value="" size="50" />
										</div>
										<div id="attachform_img_url_div" style="display:none;">
											<small id="attachboxtitle_image_url" defaultvalue="<?= $this->lang('pf_attachbx_ttl_imurl') ?>"></small>
											<input type="text" name="atch_image_url" value="" style="width:800px;" onpaste="postform_attach_pastelink(event,this,postform_attach_submit);" onkeyup="postform_attach_pastelink(event,this,postform_attach_submit);" />
										</div>
									</div>
									<div id="attachboxcontent_image_ftr" class="submitattachment">
										<img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/loading.gif" alt="" style="margin-bottom:2px;" />
										<a href="javascript:;" class="submitattachmentbtn" onclick="postform_attach_submit();" onfocus="this.blur();"><b><?= $this->lang('pf_attachbtn_image') ?></b></a>
										<div class="orcancel"><?= $this->lang('pf_attachbtn_or') ?> <a href="javascript:;" onclick="postform_attachbox_close();" onfocus="this.blur();"><?= $this->lang('pf_attachbtn_orclose') ?></a></div>
									</div>
								</div>
								<div id="attachboxcontent_videoembed" style="display:none;">
									<a href="javascript:;" class="closeattachbox" onclick="postform_attachbox_close();" onfocus="this.blur();"></a>
									<div class="attachform">
										<small id="attachboxtitle_videoembed" defaultvalue="<?= $this->lang('pf_attachbx_ttl_videm') ?>"></small>
										<input type="text" name="atch_videoembed" value="" style="width:800px;" onpaste="postform_attach_pastelink(event,this,postform_attach_submit);" onkeyup="postform_attach_pastelink(event,this,postform_attach_submit);" />
									</div>
									<div id="attachboxcontent_videoembed_ftr" class="submitattachment">
										<img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/loading.gif" alt="" style="margin-bottom:2px;" />
										<a href="javascript:;" class="submitattachmentbtn" onclick="postform_attach_submit();" onfocus="this.blur();"><b><?= $this->lang('pf_attachbtn_videmb') ?></b></a>
										<div class="orcancel"><?= $this->lang('pf_attachbtn_or') ?> <a href="javascript:;" onclick="postform_attachbox_close();" onfocus="this.blur();"><?= $this->lang('pf_attachbtn_orclose') ?></a></div>
									</div>
								</div>
								<div id="attachboxcontent_file" style="display:none;">
									<a href="javascript:;" class="closeattachbox" onclick="postform_attachbox_close();" onfocus="this.blur();"></a>
									<div class="attachform">
										<small id="attachboxtitle_file" defaultvalue="<?= $this->lang('pf_attachbx_ttl_file') ?>"></small>
										<input type="file" name="atch_file" value="" size="50" />
									</div>
									<div id="attachboxcontent_file_ftr" class="submitattachment">
										<img src="<?= $C->SITE_URL.'themes/'.$C->THEME ?>/imgs/loading.gif" alt="" style="margin-bottom:2px;" />
										<a href="javascript:;" class="submitattachmentbtn" onclick="postform_attach_submit();" onfocus="this.blur();"><b><?= $this->lang('pf_attachbtn_file') ?></b></a>
										<div class="orcancel"><?= $this->lang('pf_attachbtn_or') ?> <a href="javascript:;" onclick="postform_attachbox_close();" onfocus="this.blur();"><?= $this->lang('pf_attachbtn_orclose') ?></a></div>
									</div>
								</div>
							</div>
							<div id="attachboxftr"></div>
						</div>
					</form>
				</div>
				<?php } ?>
				<div id="pagebody">
					<?php if( $this->param('installed')=='ok' ) { ?>
						<?= okbox($this->lang('opentronix_install_ok_ttl'), $this->lang('opentronix_install_ok_txt',array('#VER#'=>$C->VERSION))) ?>
					<?php } ?>
