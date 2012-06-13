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
	
	$D->page_title	= ($D->post->post_user->id==0&&$D->post->post_group ? $D->post->post_group->title : $D->post->post_user->username).': '.$D->post->post_message;
	
	$D->p	= & $D->post;
	
	if( ! isset($D->p->post_attached['videoembed']) ) {
		$this->redirect( $D->p->permalink );
	}
	
	$D->video		= $D->post->post_attached['videoembed'];
	
	$w	= 300;
	if( $D->video->embed_w > $w ) {
		$h	= round($w * $D->video->embed_h / $D->video->embed_w);
		$c	= $D->video->embed_code;
		$c	= preg_replace('/width\=(\"|\\\')?'.$D->video->embed_w.'(\"|\\\')?/ius', 'width=${1}'.$w.'${2}', $c);
		$c	= preg_replace('/height\=(\"|\\\')?'.$D->video->embed_h.'(\"|\\\')?/ius', 'height=${1}'.$h.'${2}', $c);
		$c	= preg_replace('/width\:(\s*)'.$D->video->embed_w.'(px)?/ius', 'width:'.$w.'${2}', $c);
		$c	= preg_replace('/height\:(\s*)'.$D->video->embed_h.'(px)?/ius', 'height:'.$h.'${2}', $c);
		$D->video->embed_w	= $w;
		$D->video->embed_h	= $h;
		$D->video->embed_code	= $c;
	}
	
	$this->load_template('mobile/view_video.php');
	
?>