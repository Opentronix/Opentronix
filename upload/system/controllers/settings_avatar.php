<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/settings.php');
	
	require_once( $C->INCPATH.'helpers/func_images.php' );
	
	$D->page_title	= $this->lang('settings_avatar_pagetitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->u	= & $this->user;
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	$send_notif	= FALSE;
	if( isset($_FILES['avatar']) && is_uploaded_file($_FILES['avatar']['tmp_name']) ) {
		$D->submit	= TRUE;
		$f	= (object) $_FILES['avatar'];
		list($w, $h, $tp) = getimagesize($f->tmp_name);
		if( $w==0 || $h==0 ) {
			$D->error	= TRUE;
			$D->errmsg	= 'st_avatar_err_invalidfile';
		}
		elseif( $tp!=IMAGETYPE_GIF && $tp!=IMAGETYPE_JPEG && $tp!=IMAGETYPE_PNG ) {
			$D->error	= TRUE;
			$D->errmsg	= 'st_avatar_err_invalidformat';
		}
		elseif( $w<$C->AVATAR_SIZE || $h<$C->AVATAR_SIZE ) {
			$D->error	= TRUE;
			$D->errmsg	= 'st_avatar_err_toosmall';
		}
		else {
			$fn	= time().rand(100000,999999).'.png';
			$res	= copy_avatar($f->tmp_name, $fn);
			if( ! $res) {
				$D->error	= TRUE;
				$D->errmsg	= 'st_avatar_err_cantcopy';
			}
		}
		if( ! $D->error ) {
			$old	= $this->user->info->avatar;
			if( $old != $C->DEF_AVATAR_USER ) {
				rm( $C->IMG_DIR.'avatars/'.$old );
				rm( $C->IMG_DIR.'avatars/thumbs1/'.$old );
				rm( $C->IMG_DIR.'avatars/thumbs2/'.$old );
				rm( $C->IMG_DIR.'avatars/thumbs3/'.$old );
			}
			$db2->query('UPDATE users SET avatar="'.$db2->escape($fn).'" WHERE id="'.$this->user->id.'" LIMIT 1');
			$this->user->info->avatar	= $fn;
			$this->network->get_user_by_id($this->user->id, TRUE);
			$send_notif	= TRUE;
		}
	}
	elseif( $this->param('del') == 'current' ) {
		$old	= $this->user->info->avatar;
		if( $old != $C->DEF_AVATAR_USER ) {
			rm( $C->IMG_DIR.'avatars/'.$old );
			rm( $C->IMG_DIR.'avatars/thumbs1/'.$old );
			rm( $C->IMG_DIR.'avatars/thumbs2/'.$old );
			rm( $C->IMG_DIR.'avatars/thumbs3/'.$old );
			$db2->query('UPDATE users SET avatar="" WHERE id="'.$this->user->id.'" LIMIT 1');
			$this->user->info->avatar	= $C->DEF_AVATAR_USER;
			$this->network->get_user_by_id($this->user->id, TRUE);
			$D->msg	= 'deleted';
			$send_notif	= TRUE;
		}
	}
	
	if( $send_notif ) {
		$n	= intval( $this->network->get_user_notif_rules($this->user->id)->ntf_them_if_i_edt_pictr );
		if( $n == 1 ) {
			$this->load_langfile('inside/notifications.php');
			$this->load_langfile('email/notifications.php');
			$followers	= array_keys($this->network->get_user_follows($this->user->id)->followers);
			foreach($followers as $uid) {
				$send_post	= FALSE;
				$send_mail	= FALSE;
				$n	= intval( $this->network->get_user_notif_rules($uid)->ntf_me_if_u_edt_pictr );
				if( $n == 2 ) { $send_post = TRUE; } elseif( $n == 3 ) { $send_mail = TRUE; } elseif( $n == 1 ) { $send_post = TRUE; $send_mail = TRUE; }
				if( $send_post ) {
					$lng	= array('#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'"><span class="mpost_mentioned">@</span>'.$this->user->info->username.'</a>');
					$this->network->send_notification_post($uid, 0, 'msg_ntf_me_if_u_edt_pictr', $lng, 'replace');
				}
				if( $send_mail ) {
					$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->user->info->username, '#NAME#'=>$this->user->info->fullname, '#A0#'=>$C->SITE_URL.$this->user->info->username);
					$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'" target="_blank">@'.$this->user->info->username.'</a>', '#NAME#'=>$this->user->info->fullname, '#A0#'=>'');
					$subject		= $this->lang('emlsubj_ntf_me_if_u_edt_pictr', $lng_txt);
					$message_txt	= $this->lang('emltxt_ntf_me_if_u_edt_pictr', $lng_txt);
					$message_htm	= $this->lang('emlhtml_ntf_me_if_u_edt_pictr', $lng_htm);
					$this->network->send_notification_email($uid, 'u_edt_pictr', $subject, $message_txt, $message_htm);
				}
			}
		}
	}
	
	list($D->currw, $D->currh) = getimagesize($C->IMG_DIR.'avatars/'.$D->u->info->avatar);
	
	$this->load_template('settings_avatar.php');
	
?>