<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/settings.php');
	
	require_once($C->INCPATH.'helpers/func_externalprofiles.php');
	
	$D->page_title	= $this->lang('settings_contacts_pagetitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->submit	= FALSE;
	$D->errmsg1	= '';
	$D->errmsg2	= '';
	$D->errmsg3	= '';
	
	$D->i	= (object) array (
		'website'	=> '',
		'im_skype'	=> '',
		'im_icq'	=> '',
		'im_gtalk'	=> '',
		'im_msn'	=> '',
		'im_yahoo'	=> '',
		'im_aim'	=> '',
		'im_jabber'	=> '',
		'prof_linkedin'	=> '',
		'prof_facebook'	=> '',
		'prof_twitter'	=> '',
		'prof_flickr'	=> '',
		'prof_friendfeed'	=> '',
		'prof_delicious'	=> '',
		'prof_digg'	=> '',
		'prof_myspace'	=> '',
		'prof_orcut'	=> '',
		'prof_youtube'	=> '',
		'prof_mixx'	=> '',
		'prof_edno23'	=> '',
		'prof_favit'	=> '',
	);
	$db2->query('SELECT * FROM users_details WHERE user_id="'.$this->user->id.'" LIMIT 1');
	if($obj = $db->fetch_object()) {
		unset($obj->user_id);
		foreach($obj as $k=>$v) {
			if( substr($k,0,5)=='prof_' && !empty($v) ) {
				if( preg_match('/\#\#\#(.*)$/', $v, $m) ) {
					$D->i->$k	= stripslashes($m[1]);
				}
			}
			else {
				$D->i->$k	= stripslashes($v);
			}
		}
	}
	
	$tmphash	= md5(serialize($D->i));
	
	if( isset($_POST['sbm']) ) {
		$D->submit	= TRUE;
		foreach($D->i as $k=>$v) {
			$D->i->$k	= isset($_POST[$k]) ? trim($_POST[$k]) : '';
		}
		
		$update_fields	= array();
		
		if( $D->i->website == 'http://' ) {
			$D->i->website	= '';
		}
		if( !empty($D->i->website) && !preg_match('/^((http|ftp|https):\/\/)?([a-z0-9.-]+\.)+[a-z]{2,4}(\/([a-z0-9-_\/]+)?)?$/iu', $D->i->website) ) {
			$D->errmsg1	= 'st_cnt_error_website';
		}
		else {
			if( ! preg_match('/^(http|ftp|https):\/\//iu', $D->i->website) ) {
				$D->i->website	= 'http://'.$D->i->website;
			}
			$update_fields['website']	= $D->i->website;
		}
		
		// not bad idea to validate messengers... some day ;)
		$update_fields['im_skype']	= $D->i->im_skype;
		$update_fields['im_icq']	= $D->i->im_icq;
		$update_fields['im_gtalk']	= $D->i->im_gtalk;
		$update_fields['im_msn']	= $D->i->im_msn;
		$update_fields['im_yahoo']	= $D->i->im_yahoo;
		$update_fields['im_jabber']	= $D->i->im_jabber;
		
		$tmp	= array();
		foreach($D->i as $k=>$v) {
			if( ! preg_match('/^prof_(.*)$/i', $k, $m) ) {
				continue;
			}
			$tmp[]	= $m[1];
		}
		$tmp	= array_reverse($tmp);
		foreach($tmp as $site) {
			$valfunc	= 'validate_'.$site.'_profile_url';
			if( !empty($D->i->{'prof_'.$site}) && !$m=$valfunc($D->i->{'prof_'.$site}) ) {
				$D->errmsg3	= 'st_cnt_error_'.$site;
			}
			else {
				$update_fields['prof_'.$site]	= empty($D->i->{'prof_'.$site}) ? '' : ($m[1].'###'.$m[0]);
				$D->i->{'prof_'.$site}		= empty($D->i->{'prof_'.$site}) ? '' : $m[0];
			}
		}
		
		if( count($update_fields) > 0 ) {
			$insql	= array();
			foreach($update_fields as $k=>$v) {
				$insql[]	= '`'.$k.'`="'.$db2->e($v).'"';
			}
			$insql	= implode(', ', $insql);
			$db2->query('REPLACE INTO users_details SET user_id="'.$this->user->id.'", '.$insql);
			$this->user->sess['cdetails']	= $this->db2->fetch('SELECT * FROM users_details WHERE user_id="'.$this->user->id.'" LIMIT 1');
			
			if( $tmphash != md5(serialize($D->i)) ) {
				
				$n	= intval( $this->network->get_user_notif_rules($this->user->id)->ntf_them_if_i_edt_profl );
				if( $n == 1 ) {
					$this->load_langfile('inside/notifications.php');
					$this->load_langfile('email/notifications.php');
					$followers	= array_keys($this->network->get_user_follows($this->user->id)->followers);
					foreach($followers as $uid) {
						$send_post	= FALSE;
						$send_mail	= FALSE;
						$n	= intval( $this->network->get_user_notif_rules($uid)->ntf_me_if_u_edt_profl );
						if( $n == 2 ) { $send_post = TRUE; } elseif( $n == 3 ) { $send_mail = TRUE; } elseif( $n == 1 ) { $send_post = TRUE; $send_mail = TRUE; }
						if( $send_post ) {
							$lng	= array('#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'"><span class="mpost_mentioned">@</span>'.$this->user->info->username.'</a>');
							$this->network->send_notification_post($uid, 0, 'msg_ntf_me_if_u_edt_profl', $lng, 'replace');
						}
						if( $send_mail ) {
							$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->user->info->username, '#NAME#'=>$this->user->info->fullname, '#A0#'=>$C->SITE_URL.$this->user->info->username);
							$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'" target="_blank">@'.$this->user->info->username.'</a>', '#NAME#'=>$this->user->info->fullname, '#A0#'=>'');
							$subject		= $this->lang('emlsubj_ntf_me_if_u_edt_profl', $lng_txt);
							$message_txt	= $this->lang('emltxt_ntf_me_if_u_edt_profl', $lng_txt);
							$message_htm	= $this->lang('emlhtml_ntf_me_if_u_edt_profl', $lng_htm);
							$this->network->send_notification_email($uid, 'u_edt_profl', $subject, $message_txt, $message_htm);
						}
					}
				}
			}
		}
	}
	
	$this->load_template('settings_contacts.php');
	
?>