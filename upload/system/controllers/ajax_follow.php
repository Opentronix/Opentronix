<?php
	
	if( !$this->network->id ) {
		echo 'ERROR';
		return;
	}
	if( !$this->user->is_logged ) {
		echo 'ERROR';
		return;
	}
	
	$type	= TRUE;
	if( isset($_POST['type']) && ($_POST['type']=='on' || $_POST['type']=='off') ) {
		$type	= $_POST['type']=='off' ? FALSE : TRUE;
	}
	else {
		echo 'ERROR';
		return;
	}
	
	if( isset($_POST['username']) )
	{
		$u	= $this->network->get_user_by_username($_POST['username']);
		if( ! $u ) {
			echo 'ERROR';
			return;
		}
		if( ! $this->user->follow($u->id, $type) ) {
			echo 'ERROR';
			return;
		}
		echo 'OK';
		return;
	}
	elseif( isset($_POST['groupname']) )
	{
		$g	= $this->network->get_group_by_name($_POST['groupname']);
		if( ! $g ) {
			echo 'ERROR';
			return;
		}
		if( ! $this->user->follow_group($g->id, $type) ) {
			echo 'ERROR';
			return;
		}
		echo 'OK';
		return;
	}
	
	return;
	
?>