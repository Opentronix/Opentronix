<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	$db2->query('SELECT 1 FROM users WHERE id="'.$this->user->id.'" AND is_network_admin=1 LIMIT 1');
	if( 0 == $db2->num_rows() ) {
		$this->redirect('dashboard');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/admin.php');
	
	require_once( $C->INCPATH.'helpers/func_languages.php' );
	
	$s	= new stdClass;
	$db2->query('SELECT word, value FROM settings');
	while($o = $db2->fetch_object()) {
		$s->{stripslashes($o->word)}	= stripslashes($o->value);
	}
	
	$D->menu_timezones	= array();
	if( floatval(substr(phpversion(),0,3)) >= 5.2 ) {
		$tmp	= array();
		foreach(DateTimeZone::listIdentifiers() as $v) {
			if( substr($v, 0, 4) == 'Etc/' ) { continue; }
			if( FALSE === strpos($v, '/') ) { continue; }
			$sdf	= new DateTimeZone($v);
			if( ! $sdf ) { continue; }
			$tmp[$v]	= $sdf->getOffset( new DateTime("now", $sdf) );
		}
		asort($tmp);
		foreach($tmp as $k=>$v) {
			$D->menu_timezones[$k]	= str_replace(array('/','_'), array(' / ',' '), $k);
		}
		asort($D->menu_timezones);
	}
	
	$D->menu_languages	= array();
	foreach(get_available_languages(FALSE) as $k=>$v) {
		$D->menu_languages[$k]	= $v->name;
	}
	
	$D->menu_postlength	= array(140, 150, 160, 170, 180, 190, 200);
	
	$D->network_name	= $s->SITE_TITLE;
	$D->system_email	= $s->SYSTEM_EMAIL;
	$D->def_language	= $s->LANGUAGE;
	$D->def_timezone	= isset($s->DEF_TIMEZONE) ? $s->DEF_TIMEZONE : $C->DEF_TIMEZONE;
	$D->post_maxlength	= isset($s->POST_MAX_SYMBOLS) ? $s->POST_MAX_SYMBOLS : $C->POST_MAX_SYMBOLS;
	$D->post_atch_link	= !isset($s->ATTACH_LINK_DISABLED) || $s->ATTACH_LINK_DISABLED==0;
	$D->post_atch_image	= !isset($s->ATTACH_IMAGE_DISABLED) || $s->ATTACH_IMAGE_DISABLED==0;
	$D->post_atch_video	= !isset($s->ATTACH_VIDEO_DISABLED) || $s->ATTACH_VIDEO_DISABLED==0;
	$D->post_atch_file	= !isset($s->ATTACH_FILE_DISABLED) || $s->ATTACH_FILE_DISABLED==0;
	$D->mobi_enabled	= $C->MOBI_DISABLED==0;
	$D->email_confirm	= $s->USERS_EMAIL_CONFIRMATION;
	
	$D->intro_ttl	= isset($C->HOME_INTRO_TTL) ? trim($C->HOME_INTRO_TTL) : '';
	$D->intro_txt	= isset($C->HOME_INTRO_TTL) ? trim($C->HOME_INTRO_TXT) : '';
	if( empty($D->intro_ttl) && empty($D->intro_txt) ) {
		$this->load_langfile('outside/home.php');
		$D->intro_ttl	= $this->lang('os_welcome_ttl', array('#SITE_TITLE#'=>$C->SITE_TITLE));
		$D->intro_txt	= $this->lang('os_welcome_txt', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	}
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	if( isset($_POST['sbm']) ) {
		$D->submit	= TRUE;
		$D->post_atch_link	= isset($_POST['atch_link']) && $_POST['atch_link']==1;
		$D->post_atch_image	= isset($_POST['atch_image']) && $_POST['atch_image']==1;
		$D->post_atch_video	= isset($_POST['atch_video']) && $_POST['atch_video']==1;
		$D->post_atch_file	= isset($_POST['atch_file']) && $_POST['atch_file']==1;
		$D->mobi_enabled		= isset($_POST['mobi_enabled']) && $_POST['mobi_enabled']==1;
		$D->email_confirm		= isset($_POST['email_confirm']) && $_POST['email_confirm']==1;
		if( isset($_POST['post_maxlength']) && in_array(intval($_POST['post_maxlength']),$D->menu_postlength) ) {
			$D->post_maxlength	= intval($_POST['post_maxlength']);
		}
		if( isset($_POST['def_timezone']) && isset($D->menu_timezones[$_POST['def_timezone']]) ) {
			$D->def_timezone	= $_POST['def_timezone'];
		}
		
		if( isset($_POST['def_language']) && isset($D->menu_languages[$_POST['def_language']]) ) {
			$D->def_language	= $_POST['def_language'];
			$old	= $db2->fetch_field('SELECT value FROM settings WHERE word="LANGUAGE" LIMIT 1');
			if( $old != $D->def_language ) {
				$db2->query('REPLACE INTO settings SET word="LANGUAGE", value="'.$db2->e($D->def_language).'" ');
				$db2->query('UPDATE users SET language="'.$db2->e($D->def_language).'" ');
				$this->network->get_user_by_id($this->user->id, TRUE);
			}
		}
		$db2->query('REPLACE INTO settings SET word="DEF_TIMEZONE", value="'.$db2->e($D->def_timezone).'" ');
		$db2->query('REPLACE INTO settings SET word="POST_MAX_SYMBOLS", value="'.$D->post_maxlength.'" ');
		$db2->query('REPLACE INTO settings SET word="ATTACH_LINK_DISABLED", value="'.intval(!$D->post_atch_link).'" ');
		$db2->query('REPLACE INTO settings SET word="ATTACH_IMAGE_DISABLED", value="'.intval(!$D->post_atch_image).'" ');
		$db2->query('REPLACE INTO settings SET word="ATTACH_VIDEO_DISABLED", value="'.intval(!$D->post_atch_video).'" ');
		$db2->query('REPLACE INTO settings SET word="ATTACH_FILE_DISABLED", value="'.intval(!$D->post_atch_file).'" ');
		$db2->query('REPLACE INTO settings SET word="MOBI_DISABLED", value="'.intval(!$D->mobi_enabled).'" ');
		$db2->query('REPLACE INTO settings SET word="USERS_EMAIL_CONFIRMATION", value="'.intval($D->email_confirm).'" ');
		if( isset($_POST['network_name']) ) {
			$D->network_name	= trim($_POST['network_name']);
		}
		if( empty($D->network_name) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'admgnrl_err_netw';
		}
		elseif( preg_match('/[^ا-یא-תÀ-ÿ一-龥а-яa-z0-9\-\.\_\s\!\?]/iu', $D->network_name) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'admgnrl_err_netw2';
		}
		else {
			$db2->query('REPLACE INTO settings SET word="SITE_TITLE", value="'.$db2->e($D->network_name).'" ');
			$db2->query('REPLACE INTO settings SET word="COMPANY", value="'.$db2->e($D->network_name).'" ');
		}
		if( isset($_POST['system_email']) ) {
			$D->system_email	= trim($_POST['system_email']);
		}
		if( empty($D->system_email) || !is_valid_email($D->system_email) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'admgnrl_err_email';
		}
		else {
			$db2->query('REPLACE INTO settings SET word="SYSTEM_EMAIL", value="'.$db2->e($D->system_email).'" ');
		}
		if( !$D->error ) {
			$D->intro_ttl	= isset($_POST['intro_ttl']) ? trim($_POST['intro_ttl']) : '';
			$D->intro_txt	= isset($_POST['intro_txt']) ? trim($_POST['intro_txt']) : '';
			if( empty($D->intro_ttl) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'admgnrl_err_introttl';
			}
			elseif( empty($D->intro_txt) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'admgnrl_err_introtxt';
			}
			else {
				$db2->query('REPLACE INTO settings SET word="HOME_INTRO_TTL", value="'.$db2->e($D->intro_ttl).'" ');
				$db2->query('REPLACE INTO settings SET word="HOME_INTRO_TXT", value="'.$db2->e($D->intro_txt).'" ');
			}
		}
		$this->network->load_network_settings();
	}
	
	$D->page_title	= $this->lang('admpgtitle_general', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$this->load_template('admin_general.php');
	
?>