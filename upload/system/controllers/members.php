<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/members.php');
	
	if( $this->user->is_logged ) {
		$tabs		= array('all', 'ifollow', 'followers', 'admins');
	}
	else {
		$tabs		= array('all', 'admins');
	}
	$tab		= 'all';
	if( $this->param('tab') && in_array($this->param('tab'), $tabs) ) {
		$tab	= $this->param('tab');
	}
	
	$D->tab		= $tab;
	$D->page_title	= $this->lang('members_page_title_'.$tab, array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->tabnums	= array();
	$D->tabnums['all']		= $db2->fetch_field('SELECT COUNT(id) FROM users WHERE active=1');
	$D->tabnums['admins']		= $db2->fetch_field('SELECT COUNT(id) FROM users WHERE active=1 AND is_network_admin=1');
	if( $this->user->is_logged ) {
		$followtmp	= $this->network->get_user_follows($this->user->id);
		$D->tabnums['ifollow']		= count($followtmp->follow_users);
		$D->tabnums['followers']	= count($followtmp->followers);
	}
	
	$D->num_results	= 0;
	$D->num_pages	= 0;
	$D->pg		= 1;
	$D->users_html	= '';
	
	$tmp	= array();
	if( $tab == 'all' ) {
		$D->num_results	= $D->tabnums['all'];
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$db2->query('SELECT id FROM users WHERE active=1 ORDER BY id DESC LIMIT '.$from.', '.$C->PAGING_NUM_USERS);
		while($o = $db2->fetch_object()) {
			$tmp[]	= $o->id;
		}
	}
	elseif( $tab == 'ifollow' ) {
		$D->num_results	= $D->tabnums['ifollow'];
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$tmp	= array_keys(array_slice($followtmp->follow_users, $from, $C->PAGING_NUM_USERS, TRUE));
	}
	elseif( $tab == 'followers' ) {
		$D->num_results	= $D->tabnums['followers'];
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$tmp	= array_keys(array_slice($followtmp->followers, $from, $C->PAGING_NUM_USERS, TRUE));
	}
	elseif( $tab == 'admins' ) {
		$D->num_results	= $D->tabnums['admins'];
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$db2->query('SELECT id FROM users WHERE active=1 AND is_network_admin=1 ORDER BY id DESC LIMIT '.$from.', '.$C->PAGING_NUM_USERS);
		while($o = $db2->fetch_object()) {
			$tmp[]	= $o->id;
		}
	}
	
	$u	= array();
	foreach($tmp as $sdf) {
		if($sdf = $this->network->get_user_by_id($sdf)) {
			$u[]	= $sdf;
		}
	}
	ob_start();
	foreach($u as $tmp) {
		$D->u	= $tmp;
		$this->load_template('single_user.php');
	}
	$D->paging_url	= $C->SITE_URL.'members/tab:'.$tab.'/pg:';
	if( $D->num_pages > 1 ) {
		$this->load_template('paging_users.php');
	}
	$D->users_html	= ob_get_contents();
	ob_end_clean();
	
	unset($followtmp, $tmp, $sdf, $u, $D->u);
	
	$D->leftcol_title	= $this->lang('os_members_left_title_'.$tab);
	$D->leftcol_text	= $this->lang('os_members_left_text_'.$tab.($D->num_results==1||$D->num_results==0?$D->num_results:''), array('#NUM#'=>$D->num_results,'#SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE));
	
	$this->load_template('members.php');
	
?>