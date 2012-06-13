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
	
	$D->page_title	= $this->lang('admpgtitle_networkbranding', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->hdr_show_logo		= $C->HDR_SHOW_LOGO;
	$D->hdr_custom_logo	= empty($C->HDR_CUSTOM_LOGO) ? '' : ('attachments/'.$this->network->id.'/'.$C->HDR_CUSTOM_LOGO);
	$D->hdr_show_favicon	= $C->HDR_SHOW_FAVICON;
	$D->hdr_custom_favicon	= empty($C->HDR_CUSTOM_FAVICON) ? '' : ('attachments/'.$this->network->id.'/'.$C->HDR_CUSTOM_FAVICON);
	
	$D->theme	= $this->_set_template();
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	if( isset($_POST['sbm']) ) {
		$D->submit	= TRUE;
		$D->hdr_show_logo	= 1;
		if( isset($_POST['hdr_show_logo']) && in_array(intval($_POST['hdr_show_logo']),array(0,1,2)) ) {
			$D->hdr_show_logo	= intval($_POST['hdr_show_logo']);
			if( $D->hdr_show_logo != 2 ) {
				$db2->query('REPLACE INTO settings SET word="HDR_SHOW_LOGO", value="'.$D->hdr_show_logo.'" ');
			}
			else {
				$f	= FALSE;
				if( isset($_FILES['custom_logo']) && is_uploaded_file($_FILES['custom_logo']['tmp_name']) ) {
					$f	= (object) $_FILES['custom_logo'];
				}
				if( !empty($C->HDR_CUSTOM_LOGO) && !$f ) {
					$db2->query('REPLACE INTO settings SET word="HDR_SHOW_LOGO", value="2" ');
				}
				elseif( empty($C->HDR_CUSTOM_LOGO) && !$f ) {
					$D->error	= TRUE;
					$D->errmsg	= 'admbrnd_frm_err_invalidfile';
				}
				else {
					list($w, $h, $tp) = getimagesize($f->tmp_name);
					if( $w==0 || $h==0 ) {
						$D->error	= TRUE;
						$D->errmsg	= 'admbrnd_frm_err_invalidfile';
					}
					elseif( $tp!=IMAGETYPE_GIF && $tp!=IMAGETYPE_JPEG && $tp!=IMAGETYPE_PNG ) {
						$D->error	= TRUE;
						$D->errmsg	= 'admbrnd_frm_err_invalidformat';
					}
					elseif( $w < $C->LOGO_HEIGHT ) {
						$D->error	= TRUE;
						$D->errmsg	= 'admbrnd_frm_err_toosmall';
					}
					else {
						$path	= $C->IMG_DIR.'attachments/'.$this->network->id.'/';
						$fn	= 'logo_'.time().rand(100000,999999).'.png';
						networkbranding_logo_resize($f->tmp_name, $path.$fn, $C->LOGO_HEIGHT);
						if( ! file_exists($path.$fn) ) {
							$D->error	= TRUE;
							$D->errmsg	= 'admbrnd_frm_err_cantcopy';
						}
						else {
							$db2->query('REPLACE INTO settings SET word="HDR_SHOW_LOGO", value="2" ');
							$db2->query('REPLACE INTO settings SET word="HDR_CUSTOM_LOGO", value="'.$db2->e($fn).'" ');
							$db2->query('REPLACE INTO settings SET word="HDR_CUSTOM_LOGO_'.$db2->e($C->THEME).'", value="'.$db2->e($fn).'" ');
							$D->hdr_custom_logo	= 'attachments/'.$this->network->id.'/'.$fn;
						}
					}
				} 
			}
		}
		$D->hdr_show_favicon	= 1;
		if( isset($_POST['hdr_show_favicon']) && in_array(intval($_POST['hdr_show_favicon']),array(0,1,2)) ) {
			$D->hdr_show_favicon	= intval($_POST['hdr_show_favicon']);
			if( $D->hdr_show_favicon != 2 ) {
				$db2->query('REPLACE INTO settings SET word="HDR_SHOW_FAVICON", value="'.$D->hdr_show_favicon.'" ');
			}
			else {
				$f	= FALSE;
				if( isset($_FILES['custom_favicon']) && is_uploaded_file($_FILES['custom_favicon']['tmp_name']) ) {
					$f	= (object) $_FILES['custom_favicon'];
				}
				if( !empty($C->HDR_CUSTOM_FAVICON) && !$f ) {
					$db2->query('REPLACE INTO settings SET word="HDR_SHOW_FAVICON", value="2" ');
				}
				elseif( empty($C->HDR_CUSTOM_FAVICON) && !$f ) {
					$D->error	= TRUE;
					$D->errmsg	= 'admbrnd_frm_err_ficn_invalidfile';
				}
				else {
					list($w, $h, $tp) = getimagesize($f->tmp_name);
					if( $w==0 || $h==0 ) {
						$D->error	= TRUE;
						$D->errmsg	= 'admbrnd_frm_err_ficn_invalidfile';
					}
					elseif( $tp!=IMAGETYPE_GIF && $tp!=IMAGETYPE_PNG && $tp!=IMAGETYPE_ICO ) {
						$D->error	= TRUE;
						$D->errmsg	= 'admbrnd_frm_err_ficn_invalidformat';
					}
					elseif( $w!=16 || $h!=16 ) {
						$D->error	= TRUE;
						$D->errmsg	= 'admbrnd_frm_err_ficn_badsize';
					}
					else {
						$path	= $C->IMG_DIR.'attachments/'.$this->network->id.'/';
						$fn	= 'favicon_'.time().rand(100000,999999).'.ico';
						copy( $f->tmp_name, $path.$fn );
						if( ! file_exists($path.$fn) ) {
							$D->error	= TRUE;
							$D->errmsg	= 'admbrnd_frm_err_ficn_cantcopy';
						}
						else {
							chmod($path.$fn, 0777);
							if( !empty($C->HDR_CUSTOM_FAVICON) ) {
								rm( $path.$C->HDR_CUSTOM_FAVICON );
							}
							$db2->query('REPLACE INTO settings SET word="HDR_SHOW_FAVICON", value="2" ');
							$db2->query('REPLACE INTO settings SET word="HDR_CUSTOM_FAVICON", value="'.$db2->e($fn).'" ');
							$D->hdr_custom_favicon	= 'attachments/'.$this->network->id.'/'.$fn;
						}
					}
				} 
			}
		}
		$this->network->load_network_settings($db2);
	}
	
	$this->load_template('admin_networkbranding.php');
	
?>