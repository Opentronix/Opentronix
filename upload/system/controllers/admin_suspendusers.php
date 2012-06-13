<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	$db2->query('SELECT 1 FROM users WHERE id="'.$this->user->id.'" AND is_network_admin=1 LIMIT 1');
	if( 0 == $db2->num_rows() ) {
		$this->redirect('dashboard');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/admin.php');
	
	$D->page_title	= $this->lang('admpgtitle_suspendusers', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->users	= array();
	$r	= $db2->query('SELECT id FROM users WHERE active=0 ORDER BY fullname ASC, username ASC');
	while($tmp = $db2->fetch_object($r)) {
		if($sdf = $this->network->get_user_by_id($tmp->id)) {
			$D->users[]	= $sdf;
		}
	}
	
	if( isset($_POST['admins']) ) {
		$admins	= trim($_POST['admins']);
		$admins	= trim($admins, ',');
		$admins	= trim($admins);
		$admins	= explode(',', $admins);
		$ids	= array();
		foreach($admins as $a) {
			$a	= trim($a);
			if( empty($a) ) { continue; }
			$a	= $this->network->get_user_by_username($a);
			if( ! $a ) { continue; }
			$ids[]	= intval($a->id);
		}
		$ids	= array_unique($ids);
		$this->db2->query('UPDATE users SET active=1');
		if( count($ids) ) {
			$this->db2->query('UPDATE users SET active=0 WHERE id IN('.implode(', ', $ids).') ');
		}
		foreach($ids as $a) {
			$this->network->get_user_by_id($a, TRUE);
		}
		foreach($D->users as $sdf) {
			if( in_array(intval($sdf->id), $ids) ) {
				continue;
			}
			$this->network->get_user_by_id($sdf->id, TRUE);
		}
		$this->redirect( $C->SITE_URL.'admin/suspendusers/msg:suspsaved' );
	}
	
	$this->load_template('admin_suspendusers.php');
	
?>