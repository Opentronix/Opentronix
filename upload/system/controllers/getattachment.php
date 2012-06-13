<?php
	
	$this->load_langfile('inside/global.php');
	
	if( !$this->network->id ) {
		echo 'ERROR';
		return;
	}
	
	$type		= trim($this->param('tp'));
	$postid	= trim($this->param('pid'));
	
	if( $type!='image' && $type!='videoembed' ) {
		return;
	}
	if( ! preg_match('/^(public|private)_([0-9]+)$/', $postid, $m) ) {
		return;
	}
	
	$D->p	= new post($m[1], $m[2]);
	if( $D->p->error ) {
		return;
	}
	
	$a	= $D->p->post_attached;
	if( ! isset($a[$type]) ) {
		return;
	}
	
	$D->type	= $type;
	$D->att	= $a[$type];
	
	$this->load_template('post_flybox_attachment.php');
	
?>