<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/invite.php');
	
	$D->page_title	= $this->lang('os_invite_ttl_personalurl', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#OUTSIDE_SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE));
	
	$code	= '';
	$db1->query('SELECT code FROM invitation_codes WHERE user_id="'.$this->user->id.'" LIMIT 1');
	if( $tmp = $db1->fetch_object() ) {
		$code	= $tmp->code;
	}
	else {
		$code	= md5(time().rand(0,9999999));
		$db1->query('INSERT INTO invitation_codes SET code="'.$code.'", user_id="'.$this->user->id.'" ');
	}
	
	$D->invitation_link	= $C->SITE_URL.'signup/invited:'.$code;
	
	$this->load_template('invite_personalurl.php');
	
?>