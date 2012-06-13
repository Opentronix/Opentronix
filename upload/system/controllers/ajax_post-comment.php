<?php
	
	if( !$this->network->id ) {
		echo 'ERROR';
		return;
	}
	if( !$this->user->is_logged ) {
		echo 'ERROR';
		return;
	}
	
	if( isset($_POST['postid']) && preg_match('/^(public|private)_([0-9]+)$/', $_POST['postid'], $m) )
	{
		$msg	= isset($_POST['message']) ? trim($_POST['message']) : '';
		if( empty($msg) ) {
			echo 'ERROR';
			return;
		}
		$c	= new newpostcomment( new post($m[1], $m[2]) );
		if( $c->error ) {
			echo 'ERROR';
			return;
		}
		$c->set_message($msg);
		if( $c->save() ) {
			echo 'OK';
			return;
		}
	}
	
	echo 'ERROR';
	return;
	
?>