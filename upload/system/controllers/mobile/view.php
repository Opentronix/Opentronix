<?php
	
	if( !$this->network->id || !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $this->network->id && $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
		
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/view.php');
	
	$post_type	= '';
	$post_id	= '';
	if( $this->param('post') ) {
		$post_type	= 'public';
		$post_id	= intval($this->param('post'));
	}
	elseif( $this->param('priv') ) {
		$post_type	= 'private';
		$post_id	= intval($this->param('priv'));
	}
	else {
		$this->redirect('dashboard');
	}
	
	$D->post	= new post($post_type, $post_id);
	if($D->post->error) {
		$this->redirect('dashboard');
	}
	if($D->post->is_system_post) {
		$this->redirect('dashboard');
	}
	
	if( isset($_POST['message']) ) {
		$c	= new newpostcomment($D->post);
		$c->set_api_id($C->API_ID);
		$c->set_message($_POST['message']);
		$c->save();
		$this->redirect($D->post->permalink.'#comments');
	}
	
	$D->page_title	= ($D->post->post_user->id==0&&$D->post->post_group ? $D->post->post_group->title : $D->post->post_user->username).': '.$D->post->post_message;
	
	$D->post->reset_new_comments();
	
	$D->p	= & $D->post;
	
	$D->cnm		= $D->post->post_commentsnum;
	$D->cpg		= FALSE;
	$D->comments	= array();
	if( $D->cnm > 0 ) {
		$D->cnum_pages	= ceil($D->cnm / $C->PAGING_NUM_COMMENTS);
		if( $D->cnm <= $C->POST_LAST_COMMENTS ) {
			$D->cpg	= 1;
			$D->comments	= $D->post->get_comments();
		}
		elseif( ! $this->param('cpg') ) {
			$D->cpg	= FALSE;
			$D->comments	= $D->post->get_last_comments();
		}
		else {
			$D->cpg	= max(1, intval($this->param('cpg')));
			$D->cpg	= min($D->cpg, $D->cnum_pages);
			$D->comments	= array_slice($D->post->get_comments(), $C->PAGING_NUM_COMMENTS*($D->cpg-1), $C->PAGING_NUM_COMMENTS);
		}
	}
	
	$this->load_template('mobile/view.php');
	
?>