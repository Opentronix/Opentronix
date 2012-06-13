<?php

	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/settings.php');
	$this->load_langfile('email/changeemail.php');	
	
	$D->page_title	= $this->lang('settings_email_pagetitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
		
	$D->submit			= FALSE;
	$D->error			= FALSE;
	$D->notif			= FALSE;
	$D->new_email_active 	= FALSE;
	$D->errmsg			= '';
	
	$D->new_email		= '';
	$D->new_email_confirm	= '';
	$D->user_pass		= '';
	
	if($this->param('reqid') && $this->param('reqkey'))
	{
		$D->submit	= TRUE;
		
		if(! intval($this->param('reqid')))
		{
			$D->error = TRUE;
			$D->errmsg = $this->lang('st_email_wrong_link');
		}
		else {
			$db2->query('SELECT * FROM email_change_requests WHERE id="'.intval($this->param('reqid')).'" LIMIT 1');
			if(! $obj = $db2->fetch_object() ) 
			{
				$D->error	= TRUE;
				$D->errmsg	= $this->lang('st_email_wrong_link');
			}
			else 
			{
				$D->new_email	= $obj->new_email;
				if($obj->confirm_key != trim($this->param('reqkey')))
				{	
					$D->error = TRUE;
					$D->errmsg = $this->lang('st_email_wrong_conf_id');
		
				}else if($obj->confirm_valid < time())
				{
					$D->error = TRUE;
					$D->errmsg = $this->lang('st_email_wrong_time');
				}else if( $this->network->get_user_by_email($D->new_email) ) 
				{
					$D->error 	= TRUE;
					$D->errmsg 	= $this->lang('st_email_name_repeat', array('#SITE_TITLE#' => $C->SITE_TITLE));
				}
				if(!$D->error)
				{
					$db2->query('UPDATE users SET email="'.$db2->e($D->new_email).'" WHERE id="'.$this->user->id.'" LIMIT 1');
					$this->network->get_user_by_id($this->user->id, TRUE);
					$this->network->get_user_by_email($this->user->info->email, TRUE);
					$this->user->info->email	= $D->new_email;
					$db2->query('DELETE FROM email_change_requests WHERE id="'.intval($this->param('reqid')).'" LIMIT 1');
					$D->new_email		= '';
					$D->new_email_confirm	= '';
					$D->user_pass		= '';
					$D->new_email_active = TRUE;
				}
			}
		}

	}elseif( isset($_POST['new_email'], $_POST['new_email_confirm'], $_POST['user_pass']) ) 
	{
		$D->submit			= TRUE;
		$D->new_email		= mb_strtolower(trim($_POST['new_email']));
		$D->new_email_confirm	= mb_strtolower(trim($_POST['new_email_confirm']));
		$D->user_pass		= trim($_POST['user_pass']);
		
		if(!is_valid_email($D->new_email) || !is_valid_email($D->new_email_confirm))
		{
			$D->error = TRUE;
			$D->errmsg = $this->lang('st_email_address_error');
		}
		elseif($D->new_email != $D->new_email_confirm)
		{
			$D->error = TRUE;
			$D->errmsg = $this->lang('st_email_current_error');
		}
		elseif(empty($D->user_pass) || (md5($D->user_pass) != $this->user->info->password))
		{
			$D->error = TRUE;
			$D->errmsg = $this->lang('st_email_pass_error');
		}
		elseif($this->network->get_user_by_email($D->new_email)) 
		{
			$D->error 	= TRUE;
			$D->errmsg 	= $this->lang('st_email_name_repeat', array('#SITE_TITLE#' => $C->SITE_TITLE));
		}
		
		if( !$D->error )
		{		
			if($C->USERS_EMAIL_CONFIRMATION)
			{
				$D->confirmation_key	= md5(rand().time().rand());
				$db2->query('INSERT INTO email_change_requests(user_id, new_email, confirm_key, confirm_valid) VALUES("'.$this->user->id.'", "'.$db2->e($D->new_email).'", "'.$D->confirmation_key.'", "'.(time() + (7 * 24 * 60 * 60)).'")');
				$D->confirmation_link	= $C->SITE_URL.'settings/email/reqid:'.$db2->insert_id().'/reqkey:'.$D->confirmation_key;
					
				$subject	= $this->lang('prof_changemail_subject', array('#SITE_TITLE#'=>$C->SITE_TITLE));
				$msgtxt	= $this->load_template('email/changeemail_txt.php', FALSE);
				$msghtml	= $this->load_template('email/changeemail_html.php', FALSE);
				do_send_mail_html($D->new_email, $subject, $msgtxt, $msghtml);
						
				$D->error 	= TRUE;
				$D->notif	= TRUE;
				$D->errmsg = $this->lang('st_email_notif_send', array('#SITE_TITLE#'=>$D->new_email));
			}else
			{
				$db2->query('UPDATE users SET email="'.$db2->e($D->new_email).'" WHERE id="'.$this->user->id.'" LIMIT 1');
				$this->network->get_user_by_id($this->user->id, TRUE);
				$this->network->get_user_by_email($this->user->info->email, TRUE);
				$this->user->info->email	= $D->new_email;
				$D->new_email		= '';
				$D->new_email_confirm	= '';
				$D->user_pass		= '';
				$D->new_email_active = TRUE;
			}
		}
	}	
	$this->load_template('settings_email.php');
?>