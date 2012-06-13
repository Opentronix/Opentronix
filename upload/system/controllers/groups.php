<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/groups.php');
	
	if( ! $this->user->is_logged ) {
		$tabs	= array('all');
		$D->tab	= 'all';
	}
	else {
		$tabs	= array('all', 'my');
		$D->tab	= 'my';
		if( count($this->network->get_user_follows($this->user->id)->follow_groups) == 0 ) {
			$tabs	= array('my', 'all');
			$D->tab	= 'all';
		}
		if( $this->param('tab') && in_array($this->param('tab'), $tabs) ) {
			$D->tab	= $this->param('tab');
		}
	}
	
	$D->page_title	= $this->lang('groups_page_title_'.$D->tab, array('#COMPANY#'=>$C->COMPANY, '#SITE_TITLE#'=>$C->SITE_TITLE));
	
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
			if( !$this->user->is_logged || !in_array(intval($this->user->id),$m) ) {
				$not_in_groups[]	= $obj->id;
				continue;
			}
		}
	}
	$not_in_groups	= count($not_in_groups)>0 ? ('AND id NOT IN('.implode(', ', $not_in_groups).')') : '';
	
	$D->tabnums	= array();
	$D->tabnums['all']	= $db2->fetch_field('SELECT COUNT(id) FROM groups WHERE 1 '.$not_in_groups);
	$D->tabnums['my']		= $this->user->is_logged ? count($this->network->get_user_follows($this->user->id)->follow_groups) : '';
	
	$D->num_results	= 0;
	$D->num_pages	= 0;
	$D->pg		= 1;
	$D->groups_html	= '';
	
	$tmp	= array();
	if( $D->tab == 'all' )
	{
		$D->num_results	= $D->tabnums['all'];
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_GROUPS);
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_GROUPS;
		$db2->query('SELECT id FROM groups WHERE 1 '.$not_in_groups.' ORDER BY title ASC, id ASC LIMIT '.$from.', '.$C->PAGING_NUM_GROUPS);
		while($o = $db2->fetch_object()) {
			$tmp[]	= $o->id;
		}
	}
	elseif( $D->tab == 'my' )
	{
		$D->num_results	= $D->tabnums['my'];
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_GROUPS);
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_GROUPS;
		$tmp	= array_keys(array_slice($this->network->get_user_follows($this->user->id)->follow_groups, $from, $C->PAGING_NUM_GROUPS, TRUE));
	}
	
	if( 0 == $D->num_results ) {
		$arr	= array('#SITE_TITLE#'=>htmlspecialchars($C->OUTSIDE_SITE_TITLE));
		$D->noposts_box_title	= $this->lang('nogroups_box_ttl_'.$D->tab, $arr);
		$D->noposts_box_text	= $this->lang('nogroups_box_txt_'.$D->tab, $arr);
		$D->groups_html	= $this->load_template('noposts_box.php', FALSE);
	}
	else {
		$g	= array();
		foreach($tmp as $sdf) {
			if($sdf = $this->network->get_group_by_id($sdf)) {
				$g[]	= $sdf;
			}
		}
		ob_start();
		foreach($g as $tmp) {
			$D->g	= $tmp;
			$this->load_template('single_group.php');
		}
		$D->paging_url	= $C->SITE_URL.'groups/tab:'.$D->tab.'/pg:';
		if( $D->num_pages > 1 ) {
			$this->load_template('paging_groups.php');
		}
		$D->groups_html	= ob_get_contents();
		ob_end_clean();
		unset($tmp, $sdf, $g, $D->g);
	}
	
	$this->load_template('groups.php');
	
?>