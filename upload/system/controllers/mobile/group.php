<?php
	
	if( !$this->network->id || !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $this->network->id && $C->MOBI_DISABLED ) {
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
	if( $D->i_am_member ) {
		$D->i_am_admin	= $db->fetch('SELECT id FROM groups_admins WHERE group_id="'.$g->id.'" AND user_id="'.$this->user->id.'" LIMIT 1') ? TRUE : FALSE;
	}
	if( !$D->i_am_admin && $this->user->info->is_network_admin==1 ) {
		$D->i_am_admin	= TRUE;
	}
	$D->i_can_invite	= $D->i_am_admin || ($D->i_am_member && $g->is_public);
	
	if( isset($_GET['do_join']) && !$D->i_am_member ) {
		$this->user->follow_group($g->id, TRUE);
		$D->i_am_member	= TRUE;
	}
	elseif( isset($_GET['do_leave']) && $D->i_am_member && $this->user->if_can_leave_group($g->id) ) {
		$this->user->follow_group($g->id, FALSE);
		$D->i_am_member	= FALSE;
	}
	
	$D->num_members	= count($this->network->get_group_members($g->id));
	
	$shows	= array('updates', 'members', 'admins');
	$D->show	= 'updates';
	if( isset($_GET['show']) && in_array($_GET['show'],$shows) ) {
		$D->show	= $_GET['show'];
	}
	
	$D->num_results	= 0;
	$D->num_pages	= 0;
	$D->pg		= isset($_GET['pg']) ? intval($_GET['pg']) : 1;
	$D->posts_html	= '';
	$D->users_html	= '';
	$D->paging_url	= $C->SITE_URL.$g->groupname.'/?show='.$D->show.'&pg=';
	
	if( $D->show == 'updates' )
	{
		$q1	= 'SELECT COUNT(id) FROM posts WHERE group_id="'.$g->id.'"';
		$q2	= 'SELECT *, "public" AS `type` FROM posts WHERE group_id="'.$g->id.'" ORDER BY id DESC ';
		$D->num_results	= $db2->fetch_field($q1);
		if( 0 < $D->num_results ) {
			$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_POSTS);
			$D->pg	= min($D->pg, $D->num_pages);
			$D->pg	= max($D->pg, 1);
			$from	= ($D->pg - 1) * $C->PAGING_NUM_POSTS;
			$res	= $db2->query($q2.'LIMIT '.$from.', '.$C->PAGING_NUM_POSTS);
			$i	= 0;
			ob_start();
			while($obj = $db->fetch_object($res)) {
				$D->p	= new post($obj->type, FALSE, $obj);
				if( $D->p->error ) {
					continue;
				}
				$D->p->list_index	= $i++;
				$this->load_template('mobile/single_post.php');
			}
			unset($D->p);
			$D->posts_html	= ob_get_contents();
			ob_end_clean();
		}
	}
	elseif( $D->show == 'members' )
	{
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
			$this->load_template('mobile/single_user.php');
		}
		$D->users_html	= ob_get_contents();
		ob_end_clean();
		unset($tmp, $sdf, $usrs, $D->u);
	}
	elseif( $D->show == 'admins' )
	{
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
			$this->load_template('mobile/single_user.php');
		}
		$D->users_html	= ob_get_contents();
		ob_end_clean();
		unset($tmp, $sdf, $usrs, $D->u);

	}
	
	$this->load_template('mobile/group.php');
	
?>