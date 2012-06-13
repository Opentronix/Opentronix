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
	
	require_once( $C->INCPATH.'helpers/func_images.php' );
	
	$D->page_title	= $this->lang('admpgtitle_themes', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->hdr_show_logo		= $C->HDR_SHOW_LOGO;
	$D->hdr_custom_logo	= empty($C->HDR_CUSTOM_LOGO) ? '' : ('attachments/'.$this->network->id.'/'.$C->HDR_CUSTOM_LOGO);
	$D->hdr_show_favicon	= $C->HDR_SHOW_FAVICON;
	$D->hdr_custom_favicon	= empty($C->HDR_CUSTOM_FAVICON) ? '' : ('attachments/'.$this->network->id.'/'.$C->HDR_CUSTOM_FAVICON);
	
	$D->theme	= $this->_set_template();
	
	$D->changetheme_flag	= FALSE;
	$D->changetheme_warn	= FALSE;
	$D->themes	= array();
	$pt	= $C->INCPATH.'../themes/';
	$fp	= opendir($pt);
	while($d = readdir($fp)) {
		if( $d=='.' || $d=='..' ) { continue; }
		if( ! is_dir($pt.$d) ) { continue; }
		if( ! file_exists($pt.$d.'/theme.php') ) { continue; }
		$current_theme	= FALSE;
		@include( $pt.$d.'/theme.php' );
		if( $current_theme ) {
			$D->themes[$d]	= $current_theme;
		}
	}
	closedir($fp);
	if( isset($D->themes['default']) ) {
		$tmp	= array('default'=>$D->themes['default']);
		foreach($D->themes as $k=>$v) {
			if( $k == 'default' ) { continue; }
			$tmp[$k]	= $v;
		}
		$D->themes	= $tmp;
	}
	
	if( isset($_POST['set_theme']) && $_POST['set_theme']!=$C->THEME && isset($D->themes[$_POST['set_theme']]) ) {
		$C->THEME		= $_POST['set_theme'];
		$C->THEMEOBJ	= $D->themes[$_POST['set_theme']];
		$db2->query('REPLACE INTO settings SET word="THEME", value="'.$db2->e($C->THEME).'" ');
		$D->changetheme_flag	= TRUE;
		$ok	= FALSE;
		if( $D->hdr_show_logo == 1 ) {
			$ok	= TRUE;
		}
		elseif( $D->hdr_show_logo == 2 ) {
			if( isset($C->{'HDR_CUSTOM_LOGO_'.$C->THEME}) && !empty($C->{'HDR_CUSTOM_LOGO_'.$C->THEME}) ) {
				$fn	= $C->{'HDR_CUSTOM_LOGO_'.$C->THEME};
				if( file_exists($C->IMG_DIR.'attachments/'.$this->network->id.'/'.$fn) ) {
					$ok	= TRUE;
					$db2->query('REPLACE INTO settings SET word="HDR_CUSTOM_LOGO", value="'.$db2->e($fn).'" ');
					$D->hdr_custom_logo	= 'attachments/'.$this->network->id.'/'.$fn;
					$D->changetheme_warn	= TRUE;
				}
			}
			if( !$ok && !empty($D->hdr_custom_logo) ) {
				$fn	= 'logo_'.time().rand(100000,999999).'.png';
				$ok	= networkbranding_logo_resize($C->IMG_DIR.$D->hdr_custom_logo, $C->IMG_DIR.'attachments/'.$this->network->id.'/'.$fn, intval($C->THEMEOBJ->logo_height));
				if( $ok ) {
					$db2->query('REPLACE INTO settings SET word="HDR_CUSTOM_LOGO", value="'.$db2->e($fn).'" ');
					$db2->query('REPLACE INTO settings SET word="HDR_CUSTOM_LOGO_'.$db2->e($fn).'", value="" ');
					$D->hdr_custom_logo	= 'attachments/'.$this->network->id.'/'.$fn;
					$D->changetheme_warn	= TRUE;
				}
			}
		}
		if( ! $ok ) {
			$db2->query('REPLACE INTO settings SET word="HDR_SHOW_LOGO", value="1" ');
			$db2->query('REPLACE INTO settings SET word="HDR_CUSTOM_LOGO", value="" ');
			$db2->query('REPLACE INTO settings SET word="HDR_CUSTOM_LOGO_'.$db2->e($C->THEME).'", value="" ');
			$D->hdr_show_logo		= 1;
			$D->hdr_custom_logo	= '';
			$D->changetheme_warn	= TRUE;
		}
		$this->network->load_network_settings($db2);
		$this->_set_template();
	}
	
	$this->load_template('admin_themes.php');
	
?>