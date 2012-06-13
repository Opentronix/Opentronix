<?php
	
	if( $this->network->id && $this->user->is_logged ) {
		$this->redirect('dashboard');
	}
	
	$this->load_langfile('outside/global.php');
	$this->load_langfile('outside/signin.php');
	
	$D->page_title	= $this->lang('signinforg_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->have_key	= FALSE;
	
	if( $this->param('key') )
	{
		$D->have_key	= TRUE;
		$D->error_key	= FALSE;
		
		$key	= $this->db2->e(trim($this->param('key')));
		$this->db2->query('SELECT id FROM users WHERE active=1 AND pass_reset_key="'.$key.'" AND pass_reset_valid>="'.time().'" LIMIT 1');
		if( ! $u = $this->db2->fetch_object() ) {
			$D->error_key	= TRUE;
		}
		else {
			$D->submit	= FALSE;
			$D->error	= FALSE;
			$D->errmsg	= '';
			
			if( isset($_POST['pass1'], $_POST['pass2']) ) {
				$pass	= trim($_POST['pass1']);
				if( strlen($pass)<5 ) {
					$D->error	= TRUE;
					$D->errmsg	= 'signinforg_err_passwdlen';
				}
				elseif( $pass != trim($_POST['pass2']) ) {
					$D->error	= TRUE;
					$D->errmsg	= 'signinforg_err_passdiff';
				}
				else {
					$pass	= md5($pass);
					$this->db2->query('UPDATE users SET password="'.$this->db2->e($pass).'", pass_reset_key="", pass_reset_valid="" WHERE id="'.$u->id.'" LIMIT 1');
					$u	= $this->network->get_user_by_id($u->id, TRUE);
					$this->redirect('signin/pass:changed');
				}
			}
		}
	}
	else
	{
		$D->submit	= FALSE;
		$D->error	= FALSE;
		$D->errmsg	= '';
		$D->email	= '';
		
		if( isset($_POST['email']) ) {
			$D->submit	= TRUE;
			$D->email	= strtolower(trim($_POST['email']));
			if( ! is_valid_email($D->email) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'signinforg_err_email';
			}
			$u	= FALSE;
			if( ! $D->error ) {
				$this->db2->query('SELECT id, active FROM users WHERE email="'.$this->db2->e($D->email).'" LIMIT 1');
				if( ! $u = $this->db2->fetch_object() ) {
					$D->error	= TRUE;
					$D->errmsg	= 'signinforg_err_email2';
				}
				elseif( $u->active == "0" ) {
					$D->error	= TRUE;
					$D->errmsg	= 'signinforg_err_banned';
				}
				elseif( ! $D->u = $this->network->get_user_by_id($u->id) ) {
					$D->error	= TRUE;
					$D->errmsg	= 'signinforg_err_email2';
				}
			}
			if( ! $D->error ) {
				if( $this->user->is_logged ) {
					$this->user->logout();
				}
				$key		= md5('akey_'.$D->u->id.'_'.time());
				$valid	= time() + 48*60*60;
				$this->db2->query('UPDATE users SET pass_reset_key="'.$key.'", pass_reset_valid="'.$valid.'" WHERE id="'.$D->u->id.'" LIMIT 1');
				$D->recover_link	= $C->SITE_URL.'signin/forgotten/key:'.$key;
				$this->load_langfile('email/signin.php');
				$subject	= $this->lang('signinforg_email_subject', array('#SITE_TITLE#'=>$C->SITE_TITLE));
				$msgtxt	= $this->load_template('email/signinforg_txt.php', FALSE);
				$msghtml	= $this->load_template('email/signinforg_html.php', FALSE);
				do_send_mail_html($D->email, $subject, $msgtxt, $msghtml);
			}
		}
	}
	
	$this->load_template('signin_forgotten.php');
	
?>