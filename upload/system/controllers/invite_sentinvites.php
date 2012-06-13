<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/invite.php');
	
	$D->page_title	= $this->lang('os_invite_ttl_sentinvites', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#OUTSIDE_SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE));
	
	$D->invites	= array();
	
	$r	= $db2->query('SELECT * FROM users_invitations WHERE user_id="'.$this->user->id.'" ORDER BY id DESC');
	while($tmp = $db2->fetch_object($r)) {
		$obj	= new stdClass;
		$obj->fullname	= stripslashes($tmp->recp_name);
		$obj->date		= strftime($tmp->date);
		$obj->email		= stripslashes($tmp->recp_email);
		$obj->is_accepted	= FALSE;
		$obj->username	= '';
		$obj->avatar	= '';
		if( $tmp->recp_is_registered && $tmp->recp_user_id ) {
			$obj->is_accepted	= TRUE;
			$u	= $this->network->get_user_by_id($tmp->recp_user_id);
			if( $u ) {
				$obj->username	= $u->username;
				$obj->avatar	= $u->avatar;
				$obj->fullname	= $u->fullname;
			}
		}
		$D->invites[]	= $obj;
	}
	
	$this->load_template('invite_sentinvites.php');
	
?>