<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/group.php');
	$this->load_langfile('inside/group_invite.php');
	
	
	$g	= $this->network->get_group_by_id(intval($this->params->group));
	if( ! $g ) {
		$this->redirect('groups');
	}
	if( $g->is_private ) {
		$u	= $this->network->get_group_members($g->id);
		if( !$u || !isset($u[$this->user->id]) ) {
			$this->redirect('dashboard');
		}
	}
	
	$D->g	= & $g;
	$D->i_am_member	= $this->user->if_follow_group($g->id);
	$D->i_am_admin	= FALSE;
	if( $D->i_am_member ) {
		$D->i_am_admin	= $db->fetch('SELECT id FROM groups_admins WHERE group_id="'.$g->id.'" AND user_id="'.$this->user->id.'" LIMIT 1') ? TRUE : FALSE;
	}
	if( !$D->i_am_admin && $this->user->info->is_network_admin==1 ) {
		$D->i_am_admin	= TRUE;
	}
	$D->i_can_invite	= $D->i_am_admin || ($D->i_am_member && $g->is_public);
	
	if( ! $D->i_can_invite ) {
		$this->redirect($C->SITE_URL.$g->groupname);
	}
	
	$D->page_title	= $this->lang('os_grpinv_pagetitle', array('#GROUP#'=>$g->title, '#SITE_TITLE#'=>$C->SITE_TITLE));
	$D->page_favicon	= $C->IMG_URL.'avatars/thumbs2/'.$g->avatar;
	
	$tmp	= $this->network->get_user_follows($this->user->id);
	$tmp	= array_keys($tmp->followers);
	foreach($tmp as &$v) { $v = intval($v); }
	$tmp2	= array_keys($this->network->get_group_members($g->id));
	foreach($tmp2 as &$v) { $v = intval($v); }
	$tmp	= array_diff($tmp, $tmp2);
	$tmp	= array_diff($tmp, array(intval($this->user->id)));
	if( 0 == count($tmp) ) {
		$this->redirect($C->SITE_URL.$g->groupname);
	}
	$data	= array();
	foreach($tmp as $tmp2) {
		$tmp2	= $this->network->get_user_by_id($tmp2);
		if( ! $tmp2 ) { continue; }
		$data[$tmp2->id]	= $tmp2;
	}
	unset($tmp, $tmp2);
	
	if( isset($_POST['invite_users']) )
	{
		$users	= trim($_POST['invite_users'], ',');
		$users	= str_replace(',,', ',', $users);
		$users	= explode(',', $users);
		$this->load_langfile('inside/notifications.php');
		$this->load_langfile('email/notifications.php');
		foreach($users as $uid) {
			if( ! isset($data[$uid]) ) {
				continue;
			}
			$db->query('INSERT INTO groups_private_members SET group_id="'.$g->id.'", user_id="'.$uid.'", invited_by="'.$this->user->id.'", invited_date="'.time().'" ');
			
			$send_post	= FALSE;
			$send_mail	= FALSE;
			$n	= intval( $this->network->get_user_notif_rules($uid)->ntf_me_if_u_invit_me_grp );
			if( $n == 2 ) { $send_post = TRUE; } elseif( $n == 3 ) { $send_mail = TRUE; } elseif( $n == 1 ) { $send_post = TRUE; $send_mail = TRUE; }
			if( $send_post ) {
				$lng	= array('#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'"><span class="mpost_mentioned">@</span>'.$this->user->info->username.'</a>', '#GROUP#'=>'<a href="'.$C->SITE_URL.$g->groupname.'" title="'.$g->title.'">'.$g->title.'</a>');
				$this->network->send_notification_post($uid, 0, 'msg_ntf_me_if_u_invit_me_grp', $lng, 'replace');
			}
			if( $send_mail ) {
				$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->user->info->username, '#NAME#'=>$this->user->info->fullname, '#GROUP#'=>$g->title, '#A0#'=>$C->SITE_URL.$g->groupname);
				$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'" target="_blank">@'.$this->user->info->username.'</a>', '#NAME#'=>$this->user->info->fullname, '#GROUP#'=>'<a href="'.$C->SITE_URL.$g->groupname.'" title="'.$g->title.'" target="_blank">'.$g->title.'</a>');
				$subject		= $this->lang('emlsubj_ntf_me_if_u_invit_me_grp', $lng_txt);
				$message_txt	= $this->lang('emltxt_ntf_me_if_u_invit_me_grp', $lng_txt);
				$message_htm	= $this->lang('emlhtml_ntf_me_if_u_invit_me_grp', $lng_htm);
				$this->network->send_notification_email($uid, 'u_invit_me_grp', $subject, $message_txt, $message_htm);
			}
		}
		$this->network->get_group_invited_members($g->id, TRUE);
		$this->redirect( $C->SITE_URL.$g->groupname.'/msg:invited' );
	}
	
	$D->nobody	= FALSE;
	if( 0 == count($data) ) {
		$D->nobody	= TRUE;
	}
	
	$D->members	= array();
	foreach($data as $o) {
		$D->members[]	= array (
			intval($o->id),
			$o->username,
			$o->fullname,
			$o->position,
			$o->avatar,
			0, // selected
		);
	}
	
	$this->load_template('group_invite.php');
	
?>