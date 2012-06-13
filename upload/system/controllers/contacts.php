<?php
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('outside/contacts.php');
	
	require_once( $C->INCPATH.'helpers/func_captcha.php' );
	
	$D->page_title	= $this->lang('contacts_pgtitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	
	$D->fullname	= '';
	$D->email		= '';
	$D->message		= '';
	if( $this->user->is_logged ) {
		$D->fullname	= $this->user->info->fullname;
		$D->email		= $this->user->info->email;
	}
	
	$D->captcha_key	= '';
	$D->captcha_word	= '';
	$D->captcha_html	= '';
	list($D->captcha_word, $D->captcha_html)	= generate_captcha(5);
	$D->captcha_key	= md5($D->captcha_word.time().rand());
	$_SESSION['captcha_'.$D->captcha_key]	= $D->captcha_word;
	
	if( isset($_POST['sbm']) ) {
		$D->submit	= TRUE;
		$D->fullname	= trim($_POST['fullname']);
		$D->email		= trim($_POST['email']);
		$D->message		= trim($_POST['message']);
		if( empty($D->fullname) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'cntf_err_fullname';
		}
		elseif( empty($D->email) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'cntf_err_email1';
		}
		elseif( ! is_valid_email($D->email) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'cntf_err_email2';
		}
		elseif( empty($D->message) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'cntf_err_message';
		}
		elseif( !isset($_POST['captcha_key'],$_POST['captcha_word']) || !isset($_SESSION['captcha_'.$_POST['captcha_key']]) || $_SESSION['captcha_'.$_POST['captcha_key']]!=strtolower($_POST['captcha_word']) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'cntf_err_captcha';
		}
		else {
			$sender	= $D->fullname.' <'.$D->email.'>';
			$recipient	= $C->SYSTEM_EMAIL;
			$subject	= $C->OUTSIDE_SITE_TITLE.' - '.$this->lang('cnt_frm_sbj');
			$message	= $D->message;
			
			do_send_mail($recipient, $subject, $message, $sender);
			
			$D->fullname	= '';
			$D->email		= '';
			$D->subject		= '';
			$D->message		= '';
		}
	}
	
	$this->load_template('contacts.php');
	
	cleanup_captcha_files();
	
?>