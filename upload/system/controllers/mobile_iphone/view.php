<?php
	
	if( !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $C->MOBI_DISABLED ) {
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
		$this->redirect($D->post->permalink.'#comments_last');
	}
	elseif( $this->param('delcomment') ) {
		$tmp	= new postcomment($D->post, $this->param('delcomment'));
		if( $tmp && !$tmp->error && $tmp->if_can_delete() ) {
			$tmp->delete_this_comment();
		}
		$D->post	= new post($post_type, $post_id);
	}
	
	$D->page_title	= ($D->post->post_user->id==0&&$D->post->post_group ? $D->post->post_group->title : $D->post->post_user->username).': '.$D->post->post_message;
	
	$D->post->reset_new_comments();
	
	$D->p	= & $D->post;
	
	$D->cnm		= $D->post->post_commentsnum;
	$D->comments	= $D->post->get_comments();
	
	if( $this->param('at') == 'videoembed' && isset($D->p->post_attached['videoembed']) ) {
		$D->video	= $D->p->post_attached['videoembed'];
		$D->video_w	= $D->video->embed_w;
		$D->video_h	= $D->video->embed_h;
		$c	= $D->video->embed_code;
		$c	= preg_replace('/width\=(\"|\\\')?'.$D->video->embed_w.'(\"|\\\')?/ius', 'width=${1}100%${2}', $c);
		$c	= preg_replace('/height\=(\"|\\\')?'.$D->video->embed_h.'(\"|\\\')?/ius', 'height=${1}100%${2}', $c);
		$c	= preg_replace('/width\:(\s*)'.$D->video->embed_w.'(px)?/ius', 'width:100%', $c);
		$c	= preg_replace('/height\:(\s*)'.$D->video->embed_h.'(px)?/ius', 'height:100%', $c);
		$D->video->embed_code	= $c;
	}
	
	$this->load_template('mobile_iphone/view.php');
	
?>