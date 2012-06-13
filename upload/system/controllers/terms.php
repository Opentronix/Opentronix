<?php
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('outside/terms.php');
	
	$D->page_title	= $this->lang('terms_pgtitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	if( !isset($C->TERMSPAGE_ENABLED) || $C->TERMSPAGE_ENABLED!=1 ) {
		$this->redirect('home');
	}
	if( !isset($C->TERMSPAGE_CONTENT) || empty($C->TERMSPAGE_CONTENT) ) {
		$this->redirect('home');
	}
	
	$D->terms	= trim(stripslashes($C->TERMSPAGE_CONTENT));
	
	$this->load_template('terms.php');
	
?>