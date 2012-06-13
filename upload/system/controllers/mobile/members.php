<?php
	
	if( !$this->network->id || !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $this->network->id && $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
		
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/members.php');
	
	$shows	= array('following', 'followers', 'everybody');
	$D->show	= 'following';
	if( isset($_GET['show']) && in_array($_GET['show'],$shows) ) {
		$D->show	= $_GET['show'];
	}
	
	$D->page_title	= $this->lang('members_page_title_'.$D->show, array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->num_results	= 0;
	$D->num_pages	= 0;
	$D->pg		= 1;
	$D->users_html	= '';
	
	if( isset($_GET['pg']) ) {
		$D->pg	= intval($_GET['pg']);
		$D->pg	= max(1, $_GET['pg']);
	}
	
	$tmp	= array();
	if( $D->show == 'everybody' ) {
		$D->num_results	= $db2->fetch_field('SELECT COUNT(id) FROM users WHERE active=1');
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$db2->query('SELECT id FROM users WHERE active=1 ORDER BY id DESC LIMIT '.$from.', '.$C->PAGING_NUM_USERS);
		while($o = $db2->fetch_object()) {
			$tmp[]	= $o->id;
		}
	}
	elseif( $D->show == 'following' ) {
		$D->num_results	= count($this->network->get_user_follows($this->user->id)->follow_users);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$tmp	= array_keys(array_slice($this->network->get_user_follows($this->user->id)->follow_users, $from, $C->PAGING_NUM_USERS, TRUE));
	}
	elseif( $D->show == 'followers' ) {
		$D->num_results	= count($this->network->get_user_follows($this->user->id)->followers);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$tmp	= array_keys(array_slice($this->network->get_user_follows($this->user->id)->followers, $from, $C->PAGING_NUM_USERS, TRUE));
	}
	
	$u	= array();
	foreach($tmp as $sdf) {
		if($sdf = $this->network->get_user_by_id($sdf)) {
			$u[]	= $sdf;
		}
	}
	if( count($u) > 0 ) {
		ob_start();
		$i	= 0;
		foreach($u as $tmp) {
			$D->u	= $tmp;
			$D->u->list_index	= $i++;
			$this->load_template('mobile/single_user.php');
		}
		$D->users_html	= ob_get_contents();
		ob_end_clean();
	}
	
	$this->load_template('mobile/members.php');
	
?>