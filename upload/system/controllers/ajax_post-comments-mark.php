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
		$p	= new post($m[1], $m[2]);
		if( $p->error ) {
			echo 'ERROR';
			return;
		}
		$p->reset_new_comments();
		echo 'OK';
		return;
	}
	
	echo 'ERROR';
	return;
	
?>