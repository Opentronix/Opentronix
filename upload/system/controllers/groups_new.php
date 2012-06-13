<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/group.php');
	$this->load_langfile('inside/groups_new.php');
	
	$D->page_title	= $this->lang('newgroup_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	$D->form_title		= '';
	$D->form_groupname	= '';
	$D->form_description	= '';
	$D->form_type		= 'public';
	
	if( isset($_POST['sbm']) ) {
		$D->submit	= TRUE;
		$D->form_title		= trim($_POST['form_title']);
		$D->form_groupname	= trim($_POST['form_groupname']);
		$D->form_description	= mb_substr(trim($_POST['form_description']), 0, $C->POST_MAX_SYMBOLS);
		$D->form_type		= trim($_POST['form_type'])=='private' ? 'private' : 'public';
		
		if( mb_strlen($D->form_title)<3 || mb_strlen($D->form_title)>30 ) {
			$D->error	= TRUE;
			$D->errmsg	= 'group_setterr_title_length';
		}
		elseif( preg_match('/[^ا-یא-תÀ-ÿ一-龥а-яa-z0-9\-\.\s]/iu', $D->form_title) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'group_setterr_title_chars';
		}
		else {
			$db2->query('SELECT id FROM groups WHERE (groupname="'.$db2->e($D->form_title).'" OR title="'.$db2->e($D->form_title).'") LIMIT 1');
			if( $db2->num_rows() > 0 ) {
				$D->error	= TRUE;
				$D->errmsg	= 'group_setterr_title_exists';
			}
		}
		
		if( ! $D->error ) {
			if( mb_strlen($D->form_groupname)<3 || mb_strlen($D->form_groupname)>30 ) {
				$D->error	= TRUE;
				$D->errmsg	= 'group_setterr_name_length';
			}
			elseif( ! preg_match('/^[a-z0-9\-\_]{3,30}$/iu', $D->form_groupname) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'group_setterr_name_chars';
			}
			else {
				$db2->query('SELECT id FROM groups WHERE (groupname="'.$db2->e($D->form_groupname).'" OR title="'.$db2->e($D->form_groupname).'") LIMIT 1');
				if( $db2->num_rows() > 0 ) {
					$D->error	= TRUE;
					$D->errmsg	= 'group_setterr_name_exists';
				}
				else {
					$db2->query('SELECT id FROM users WHERE username="'.$db2->e($D->form_groupname).'" LIMIT 1');
					if( $db2->num_rows() > 0 ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_setterr_name_existsu';
					}
					elseif( file_exists($C->INCPATH.'controllers/'.strtolower($D->form_groupname).'.php') ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_setterr_name_existss';
					}
					elseif( file_exists($C->INCPATH.'controllers/mobile/'.strtolower($D->form_groupname).'.php') ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_setterr_name_existss';
					}
				}
			}
		}
		
		if( ! $D->error ) {
			$db2->query('INSERT INTO groups SET groupname="'.$db2->e($D->form_groupname).'", title="'.$db2->e($D->form_title).'", about_me="'.$db2->e($D->form_description).'", is_public="'.($D->form_type=='public'?1:0).'" ');
			
			$g	= $this->network->get_group_by_id( intval($db2->insert_id()) );
			
			$db2->query('INSERT INTO groups_admins SET group_id="'.$g->id.'", user_id="'.$this->user->id.'" ');
			if( $g->is_private ) {
				$db2->query('INSERT INTO groups_private_members SET group_id="'.$g->id.'", user_id="'.$this->user->id.'", invited_by="'.$this->user->id.'", invited_date="'.time().'" ');
			}
			
			if( $g->is_public ) {
				$n	= intval( $this->network->get_user_notif_rules($this->user->id)->ntf_them_if_i_create_grp );
				if( $n == 1 ) {
					$this->load_langfile('inside/notifications.php');
					$this->load_langfile('email/notifications.php');
					$followers	= array_keys($this->network->get_user_follows($this->user->id)->followers);
					foreach($followers as $uid) {
						$send_post	= FALSE;
						$send_mail	= FALSE;
						$n	= intval( $this->network->get_user_notif_rules($uid)->ntf_me_if_u_creates_grp );
						if( $n == 2 ) { $send_post = TRUE; } elseif( $n == 3 ) { $send_mail = TRUE; } elseif( $n == 1 ) { $send_post = TRUE; $send_mail = TRUE; }
						if( $send_post ) {
							$lng	= array('#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'"><span class="mpost_mentioned">@</span>'.$this->user->info->username.'</a>', '#GROUP#'=>'<a href="'.$C->SITE_URL.$g->groupname.'" title="'.$g->title.'">'.$g->title.'</a>');
							$this->network->send_notification_post($uid, 0, 'msg_ntf_me_if_u_creates_grp', $lng, 'replace');
						}
						if( $send_mail ) {
							$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->user->info->username, '#NAME#'=>$this->user->info->fullname, '#GROUP#'=>$g->title, '#A0#'=>$C->SITE_URL.$g->groupname);
							$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'" target="_blank">@'.$this->user->info->username.'</a>', '#NAME#'=>$this->user->info->fullname, '#GROUP#'=>'<a href="'.$C->SITE_URL.$g->groupname.'" title="'.$g->title.'" target="_blank">'.$g->title.'</a>');
							$subject		= $this->lang('emlsubj_ntf_me_if_u_creates_grp', $lng_txt);
							$message_txt	= $this->lang('emltxt_ntf_me_if_u_creates_grp', $lng_txt);
							$message_htm	= $this->lang('emlhtml_ntf_me_if_u_creates_grp', $lng_htm);
							$this->network->send_notification_email($uid, 'u_creates_grp', $subject, $message_txt, $message_htm);
						}
					}
				}
			}
			$this->user->follow_group($g->id);
			$this->network->get_group_by_id($g->groupname, TRUE);
			$this->network->get_group_by_id($g->title, TRUE);
			$this->redirect( $C->SITE_URL.$g->groupname.'/msg:created' );
		}
	}
	
	$this->load_template('groups_new.php');
	
?>