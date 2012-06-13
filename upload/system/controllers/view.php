<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/view.php');
	
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
	$D->page_favicon	= $C->IMG_URL.'avatars/thumbs2/'.($D->post->post_user->id==0&&$D->post->post_group ? $D->post->post_group->avatar : $D->post->post_user->avatar);	
	
	if( $D->post->post_group ) {
		$D->post->post_group->num_members	= count($this->network->get_group_members($D->post->post_group->id));
	}
	
	$D->prevpost	= '';
	$D->nextpost	= '';
	if( $post_type == 'public' && $D->post->post_user->id>0 ) {
		$not_in_groups	= array();
		if( !$this->user->is_logged || !$this->user->info->is_network_admin ) {
			$r	= $db2->query('SELECT id FROM groups WHERE is_public=0');
			while($obj = $db2->fetch_object($r)) {
				$g	= $this->network->get_group_by_id($obj->id);
				if( ! $g ) {
					$not_in_groups[]	= $obj->id;
					continue;
				}
				if( $g->is_public == 1 ) {
					continue;
				}
				$m	= $this->network->get_group_invited_members($g->id);
				if( !$this->user->is_logged || !in_array(intval($this->user->id), $m) ) {
					$not_in_groups[]	= $obj->id;
					continue;
				}
			}
		}
		$not_in_groups	= count($not_in_groups)>0 ? ('AND p.group_id NOT IN('.implode(', ', $not_in_groups).')') : '';
		$db2->query('SELECT * FROM posts WHERE id>"'.$D->post->post_id.'" AND user_id="'.$D->post->post_user->id.'" ORDER BY id ASC LIMIT 1');
		if($tmp = $db2->fetch_object()) {
			$tmp	= new post('public', FALSE, $tmp);
			if( ! $tmp->error ) {
				$D->prevpost	= $tmp->permalink;
			}
		}
		$db2->query('SELECT * FROM posts WHERE id<"'.$D->post->post_id.'" AND user_id="'.$D->post->post_user->id.'" ORDER BY id DESC LIMIT 1');
		if($tmp = $db2->fetch_object()) {
			$tmp	= new post('public', FALSE, $tmp);
			if( ! $tmp->error ) {
				$D->nextpost	= $tmp->permalink;
			}
		}
		unset($tmp, $not_in_groups);
	}
	
	$D->delete_enabled	= $D->post->if_can_delete();
	$D->delete_urlafter	= $post_type=='public' ? $C->SITE_URL.($D->post->post_user->id==0&&$D->post->post_group ? $D->post->post_group->groupname : $D->post->post_user->username).'/tab:updates/msg:deletedpost' : $C->SITE_URL.'dashboard/tab:private/msg:deletedpost';
	
	if( $this->param('from') == 'ajax' ) {
		echo 'OK:';
		$this->load_template('view.php');
		return;
	}
	
	$D->post->reset_new_comments();
	
	$this->load_template('header.php');
	$this->load_template('view.php');
	$this->load_template('footer.php');
	
?>