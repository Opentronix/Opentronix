<?php
	
	if( !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
	
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/group.php');
	
	$g	= $this->network->get_group_by_id(intval($this->params->group));
	if( ! $g ) {
		$this->redirect('dashboard');
	}
	if( $g->is_private && !$this->user->info->is_network_admin ) {
		$u	= $this->network->get_group_invited_members($g->id);
		if( !$u || !in_array(intval($this->user->id),$u) ) {
			$this->redirect('dashboard');
		}
	}
	
	$D->page_title	= $g->title.' - '.$C->SITE_TITLE;
	
	$D->g	= & $g;
	$D->i_am_member	= $this->user->if_follow_group($g->id);
	$D->i_am_admin	= FALSE;
	$D->i_am_adming	= FALSE;
	if( $D->i_am_member ) {
		$D->i_am_adming	= $db->fetch('SELECT id FROM groups_admins WHERE group_id="'.$g->id.'" AND user_id="'.$this->user->id.'" LIMIT 1') ? TRUE : FALSE;
		$D->i_am_admin	= $D->i_am_adming;
	}
	if( !$D->i_am_admin && $this->user->info->is_network_admin==1 ) {
		$D->i_am_admin	= TRUE;
	}
	$D->i_can_invite	= $D->i_am_admin || ($D->i_am_member && $g->is_public);
	
	$D->g_avatar	= md5($D->g->id.'-'.$D->g->avatar).'.'.pathinfo($D->g->avatar,PATHINFO_EXTENSION);
	if( ! file_exists($C->TMP_DIR.$D->g_avatar) ) {
		require_once($C->INCPATH.'helpers/func_images.php');
		copy_attachment_videoimg($C->IMG_DIR.'avatars/'.$D->g->avatar, $C->TMP_DIR.$D->g_avatar, 100);
	}
	
	$D->num_members	= count($this->network->get_group_members($g->id));
	$D->num_admins	= intval($db->fetch_field('SELECT COUNT(id) FROM groups_admins WHERE group_id="'.$g->id.'" '));
	
	$shows	= array('updates', 'members', 'admins');
	$D->show	= 'updates';
	if( $this->param('show') && in_array($this->param('show'),$shows) ) {
		$D->show	= $this->param('show');
	}
	
	if( $D->show == 'updates' )
	{
		$D->num_results	= 0;
		$D->start_from	= 0;
		$D->posts_html	= '';
		
		$q1	= 'SELECT COUNT(id) FROM posts WHERE group_id="'.$g->id.'"';
		$q2	= 'SELECT *, "public" AS `type` FROM posts WHERE group_id="'.$g->id.'" ORDER BY id DESC ';
		$D->num_results	= $db2->fetch_field($q1);
		if( 0 < $D->num_results ) {
			$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_POSTS);
			$D->start_from	= $this->param('start_from') ? intval($this->param('start_from')) : 0;
			$D->start_from	= max($D->start_from, 0);
			$D->start_from	= min($D->start_from, $D->num_results);
			$res	= $db2->query($q2.'LIMIT '.$D->start_from.', '.$C->PAGING_NUM_POSTS);
			$D->posts_number	= 0;
			ob_start();
			while($obj = $db2->fetch_object($res)) {
				$D->p	= new post($obj->type, FALSE, $obj);
				if( $D->p->error ) {
					continue;
				}
				$D->posts_number	++;
				$D->p->list_index	= $D->posts_number;
				$this->load_template('mobile_iphone/single_post.php');
			}
			unset($D->p);
			$D->posts_html	= ob_get_contents();
			ob_end_clean();
		}
		if( $this->param('from') == 'ajax' ) {
			echo 'OK:'.$D->posts_number.':';
			echo $D->posts_html;
			exit;
		}
	}
	elseif( $D->show == 'members' )
	{
		$D->num_results	= 0;
		$D->num_pages	= 0;
		$D->pg		= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->users_html	= '';
		
		$tmp	= array_keys($this->network->get_group_members($g->id));
		$D->num_results	= count($tmp);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$tmp	= array_slice($tmp, $from, $C->PAGING_NUM_USERS);
		$usrs	= array();
		foreach($tmp as $sdf) {
			if($sdf = $this->network->get_user_by_id($sdf)) {
				$usrs[]	= $sdf;
			}
		}
		$D->users_html	= '';
		$i	= 0;
		ob_start();
		foreach($usrs as $tmp) {
			$D->u	= $tmp;
			$D->u->list_index	= $i++;
			$this->load_template('mobile_iphone/single_user.php');
		}
		$D->users_html	= ob_get_contents();
		ob_end_clean();
		unset($tmp, $sdf, $usrs, $D->u);
	}
	elseif( $D->show == 'admins' )
	{
		$D->num_results	= 0;
		$D->num_pages	= 0;
		$D->pg		= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->users_html	= '';
		
		$db2->query('SELECT user_id FROM groups_admins WHERE group_id="'.$g->id.'" ORDER BY id ASC');
		while($o = $db2->fetch_object()) {
			$tmp[]	= intval($o->user_id);
		}
		$D->num_results	= count($tmp);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$tmp	= array_slice($tmp, $from, $C->PAGING_NUM_USERS);
		$usrs	= array();
		foreach($tmp as $sdf) {
			if($sdf = $this->network->get_user_by_id($sdf)) {
				$usrs[]	= $sdf;
			}
		}
		$D->users_html	= '';
		$i	= 0;
		ob_start();
		foreach($usrs as $tmp) {
			$D->u	= $tmp;
			$D->u->list_index	= $i++;
			$this->load_template('mobile_iphone/single_user.php');
		}
		$D->users_html	= ob_get_contents();
		ob_end_clean();
		unset($tmp, $sdf, $usrs, $D->u);
	}
	
	$this->load_template('mobile_iphone/group.php');
	
?>