<?php
	
	if( ! $this->network->id ) {
		$this->redirect('home');
	}
	
	if( ! $C->MOBI_DISABLED ) {
		$this->redirect( $this->user->is_logged ? 'dashboard' : 'home' );
	}
	
	if( $this->user->is_logged ) {
		$this->user->logout();
	}
	
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/mobidisabled.php');
	
	$this->load_template('mobile/mobidisabled.php');
	
?>