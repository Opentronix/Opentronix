<?php
	
	if( !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
	
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/groups.php');
	
	$D->shows	= array('my', 'all');
	$D->show	= 'my';
	if( $this->param('show') && in_array($this->param('show'),$D->shows) ) {
		$D->show	= $this->param('show');
	}
	
	$D->page_title	= $this->lang('groups_page_title_'.$D->show, array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$not_in_groups	= array();
	if( ! $this->user->info->is_network_admin ) {
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
			if( ! in_array(intval($this->user->id), $m) ) {
				$not_in_groups[]	= $obj->id;
				continue;
			}
		}
	}
	$not_in_groups	= count($not_in_groups)>0 ? ('AND id NOT IN('.implode(', ', $not_in_groups).')') : '';
	
	$D->num_results	= 0;
	$D->num_pages	= 0;
	$D->pg		= 1;
	$D->groups_html	= '';
	
	if( $this->param('pg') ) {
		$D->pg	= intval($this->param('pg'));
		$D->pg	= max(1, $D->pg);
	}
	
	$D->nums	= array();
	$D->nums['my']	= count($this->network->get_user_follows($this->user->id)->follow_groups);
	$D->nums['all']	= $db2->fetch_field('SELECT COUNT(id) FROM groups WHERE 1 '.$not_in_groups);
	
	$tmp	= array();
	if( $D->show == 'my' ) {
		$D->num_results	= count($this->network->get_user_follows($this->user->id)->follow_groups);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_GROUPS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_GROUPS;
		$tmp	= array_keys(array_slice($this->network->get_user_follows($this->user->id)->follow_groups, $from, $C->PAGING_NUM_GROUPS, TRUE));
	}
	else {
		$D->num_results	= $db2->fetch_field('SELECT COUNT(id) FROM groups WHERE 1 '.$not_in_groups);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_GROUPS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_GROUPS;
		$db2->query('SELECT id FROM groups WHERE 1 '.$not_in_groups.' ORDER BY title ASC, id ASC LIMIT '.$from.', '.$C->PAGING_NUM_GROUPS);
		while($o = $db2->fetch_object()) {
			$tmp[]	= $o->id;
		}
	}
	
	$g	= array();
	foreach($tmp as $sdf) {
		if($sdf = $this->network->get_group_by_id($sdf)) {
			$g[]	= $sdf;
		}
	}
	
	if( count($g) > 0 ) {
		ob_start();
		$i	= 0;
		foreach($g as $tmp) {
			$D->g	= $tmp;
			$D->g->list_index	= $i++;
			$this->load_template('mobile_iphone/single_group.php');
		}
		$D->groups_html	= ob_get_contents();
		ob_end_clean();
	}
	
	$this->load_template('mobile_iphone/groups.php');
	
?>