<?php
	
	if( !$this->network->id ) {
		echo 'ERROR';
		return;
	}
	if( !$this->user->is_logged ) {
		echo 'ERROR';
		return;
	}
	
	if( isset($_POST['postid'], $_POST['commentid']) && preg_match('/^(public|private)_([0-9]+)$/', $_POST['postid'], $m) )
	{
		$c	= new postcomment( new post($m[1], $m[2]), $_POST['commentid'] );
		if( $c->error ) {
			echo 'ERROR';
			return;
		}
		if( ! $c->if_can_delete() ) {
			echo 'ERROR';
			return;
		}
		$c->delete_this_comment();
		echo 'OK';
		return;
	}
	
	echo 'ERROR';
	return;
	
?>