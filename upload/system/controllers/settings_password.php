<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/settings.php');
	
	$D->page_title	= $this->lang('settings_password_pagetitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	$D->pass_old	= '';
	$D->pass_new	= '';
	$D->pass_new2	= '';
	
	if( isset($_POST['pass_old'], $_POST['pass_new'], $_POST['pass_new2']) ) {
		$D->submit	= TRUE;
		$D->pass_old	= trim($_POST['pass_old']);
		$D->pass_new	= trim($_POST['pass_new']);
		$D->pass_new2	= trim($_POST['pass_new2']);
		if( empty($D->pass_old) || md5($D->pass_old)!=$db2->fetch_field('SELECT password FROM users WHERE id="'.$this->user->id.'" LIMIT 1') ) {
			$D->error	= TRUE;
			$D->errmsg	= 'st_password_err_current';
		}
		elseif( mb_strlen($D->pass_new)<5 ) {
			$D->error	= TRUE;
			$D->errmsg	= 'st_password_err_newshort';
		}
		elseif( $D->pass_new != $D->pass_new2 ) {
			$D->error	= TRUE;
			$D->errmsg	= 'st_password_err_missmatch';
		}
		else {
			$pass	= md5($D->pass_new);
			$db2->query('UPDATE users SET password="'.$db2->e($pass).'" WHERE id="'.$this->user->id.'" LIMIT 1');
			$this->user->info->password	= $pass;
			$this->network->get_user_by_id($this->user->id, TRUE);
			$D->pass_old	= '';
			$D->pass_new	= '';
			$D->pass_new2	= '';
		}
	}
	
	$this->load_template('settings_password.php');
	
?>