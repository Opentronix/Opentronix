<?php
	
	if( $this->user->is_logged ) {
		$this->redirect('dashboard');
	}
	if( $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
	
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/home.php');
	
	$D->page_title	= $this->lang('home_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->is_network	= TRUE;
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	$D->email		= '';
	$D->password	= '';
	$D->rememberme	= TRUE;
	
	if( isset($_POST['email'], $_POST['password']) ) {
		$D->submit	= TRUE;
		$D->email		= trim($_POST['email']);
		$D->password	= trim($_POST['password']);
		$D->rememberme	= isset($_POST['rememberme']) && $_POST['rememberme']==1;
		if( empty($D->email) || empty($D->password) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'home_form_errmsg';
		}
		else {
			if( $this->user->is_logged ) {
				$this->user->logout();
			}
			$res	= $this->user->login($D->email, md5($D->password), $D->rememberme);
			if( ! $res ) {
				$D->error	= TRUE;
				$D->errmsg	= 'home_form_errmsg';
			}
			else {
				$this->redirect($C->SITE_URL.'dashboard');
			}
		}
	}
	
	$this->load_template('mobile_iphone/home.php');
	
?>