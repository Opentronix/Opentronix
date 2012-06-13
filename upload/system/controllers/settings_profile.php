<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/settings.php');
	
	$D->page_title	= $this->lang('settings_profile_pagetitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->menu_bdate_d	= array();
	$D->menu_bdate_m	= array();
	$D->menu_bdate_y	= array();
	if( $this->user->info->birthdate == '0000-00-00' ) {
		$D->menu_bdate_d[0]	= '';
		$D->menu_bdate_m[0]	= '';
		$D->menu_bdate_y[0]	= '';
	}
	for($i=1; $i<=31; $i++) {
		$D->menu_bdate_d[$i]	= $i;
	}
	for($i=1; $i<=12; $i++) {
		$D->menu_bdate_m[$i]	= strftime('%B', mktime(0,0,1,$i,1,2009));
	}
	for($i=intval(date('Y')); $i>=1900; $i--) {
		$D->menu_bdate_y[$i]	= $i;
	}
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	
	$D->name		= $this->user->info->fullname;
	$D->location	= $this->user->info->location;
	$D->gender		= $this->user->info->gender;
	$D->aboutme		= $this->user->info->about_me;
	$D->tags		= implode(', ', $this->user->info->tags);
	$D->bdate_d		= 0;
	$D->bdate_m		= 0;
	$D->bdate_y		= 0;
	if( $this->user->info->birthdate != '0000-00-00' ) {
		$D->bdate_d		= intval(substr($this->user->info->birthdate,8,2));
		$D->bdate_m		= intval(substr($this->user->info->birthdate,5,2));
		$D->bdate_y		= intval(substr($this->user->info->birthdate,0,4));
	}
	
	$u	= $this->user->info;
	
	$tmphash	= md5($u->fullname.$u->location.$u->birthdate.$u->gender.$u->about_me.serialize($u->tags));
	
	if( isset($_POST['sbm']) ) {
		$D->submit	= TRUE;
		$D->name		= trim($_POST['name']);
		$D->name		= strip_tags($D->name);
		$D->location	= trim($_POST['location']);
		$D->gender		= isset($_POST['gender']) ? trim($_POST['gender']) : '';
		$D->aboutme		= trim($_POST['aboutme']);
		$D->tags		= trim($_POST['tags']);
		$D->bdate_d		= intval($_POST['bdate_d']);
		$D->bdate_m		= intval($_POST['bdate_m']);
		$D->bdate_y		= intval($_POST['bdate_y']);
		if( $D->gender!='m' && $D->gender!='f' ) {
			$D->gender	= '';
		}
		if( !isset($D->menu_bdate_m[$D->bdate_m]) || !isset($D->menu_bdate_d[$D->bdate_d]) || !isset($D->menu_bdate_y[$D->bdate_y]) ) {
			$D->bdate_m	= 0;
			$D->bdate_d	= 0;
			$D->bdate_y	= 0;
		}
		if( $D->bdate_d==0 || $D->bdate_m==0 || $D->bdate_y==0 ) {
			$D->bdate_m	= 0;
			$D->bdate_d	= 0;
			$D->bdate_y	= 0;
			$birthdate	= '0000-00-00';
		}
		else {
			$birthdate	= $D->bdate_y.'-'.str_pad($D->bdate_m,2,0,STR_PAD_LEFT).'-'.str_pad($D->bdate_d,2,0,STR_PAD_LEFT);
		}
		$D->tags	= str_replace(array("\n","\r"), ',', $D->tags);
		$D->tags	= preg_replace('/\,+/ius', ',', $D->tags);
		$D->tags	= explode(',', $D->tags);
		foreach($D->tags as $k=>$v) {
			$v	= trim($v);
			if( FALSE == preg_match('/^[ا-یא-תÀ-ÿ一-龥а-яa-z0-9\-\_\.\s\+]{2,}$/iu', $v) ) {
				unset($D->tags[$k]);
				continue;
			}
			$D->tags[$k]	= $v;
		}
		$D->tags	= implode(', ', $D->tags);
		
		$db2->query('UPDATE users SET fullname="'.$db2->e($D->name).'", about_me="'.$db2->e($D->aboutme).'", tags="'.$db2->e($D->tags).'", gender="'.$db2->e($D->gender).'", birthdate="'.$db2->e($birthdate).'", location="'.$db2->e($D->location).'" WHERE id="'.$this->user->id.'" LIMIT 1');
		
		$this->user->sess['LOGGED_USER']	= $this->network->get_user_by_id($this->user->id, TRUE);
		$this->user->info	= & $this->user->sess['LOGGED_USER'];
		
		$u	= $this->user->info;
		$tmphash2	= md5($u->fullname.$u->location.$u->birthdate.$u->gender.$u->about_me.serialize($u->tags));
		if( $tmphash != $tmphash2 ) {
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
	
	$this->load_template('settings_profile.php');
	
?>