<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	$db2->query('SELECT 1 FROM users WHERE id="'.$this->user->id.'" AND is_network_admin=1 LIMIT 1');
	if( 0 == $db2->num_rows() ) {
		$this->redirect('dashboard');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/admin.php');
	
	$D->page_title	= $this->lang('admpgtitle_termsofuse', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->tos_content	= '';
	$D->tos_enabled	= FALSE;
	
	if( isset($C->TERMSPAGE_ENABLED) && $C->TERMSPAGE_ENABLED==1 ) {
		$D->tos_enabled	= TRUE;
	}
	if( isset($C->TERMSPAGE_CONTENT) ) {
		$D->tos_content	= trim(stripslashes($C->TERMSPAGE_CONTENT));
	}
	if( empty($D->tos_content) ) {
		$D->tos_enabled	= FALSE;
	}
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	$D->okmsg	= '';
	if( isset($_POST['tos_content']) ) {
		$D->submit	= TRUE;
		$D->tos_content	= trim(stripslashes($_POST['tos_content']));
		$D->tos_enabled	= isset($_POST['tos_enabled'])&&$_POST['tos_enabled']==1;
		if( empty($D->tos_content) && $D->tos_enabled ) {
			$D->error	= TRUE;
			$D->errmsg	= 'admtrms_err_txt';
			$D->tos_enabled	= FALSE;
		}
		else {
			$db2->query('REPLACE INTO settings SET word="TERMSPAGE_ENABLED", value="'.($D->tos_enabled?1:0).'" ');
			$db2->query('REPLACE INTO settings SET word="TERMSPAGE_CONTENT", value="'.$db2->e($D->tos_content).'" ');
			$C->TERMSPAGE_ENABLED	= $D->tos_enabled?1:0;
			$C->TERMSPAGE_CONTENT	= $D->tos_content;
			$D->okmsg	= $D->tos_enabled ? 'admtrms_ok_txt2' : 'admtrms_ok_txt1';
		}
	}
	
	$this->load_template('admin_termsofuse.php');
	
?>