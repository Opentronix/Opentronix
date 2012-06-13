<?php
	
	if( $this->user->is_logged ) {
		$this->redirect('dashboard');
	}
	
	if( isset($_SESSION['TWITTER_CONNECTED']) && $_SESSION['TWITTER_CONNECTED'] && $_SESSION['TWITTER_CONNECTED']->id ) {
		$uid	= intval($_SESSION['TWITTER_CONNECTED']->id);
		$db2->query('SELECT email, password FROM users WHERE twitter_uid<>"" AND twitter_uid="'.$uid.'" LIMIT 1');
		if($tmp = $db2->fetch_object()) {
			if( $this->user->login(stripslashes($tmp->email), stripslashes($tmp->password)) ) {
				$this->redirect($C->SITE_URL.'dashboard');
			}
		}
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('outside/global.php');
	$this->load_langfile('inside/dashboard.php');
	$this->load_langfile('outside/home.php');
	
	$D->page_title	= $this->lang('os_home_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	$D->intro_ttl	= $this->lang('os_welcome_ttl', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	$D->intro_txt	= $this->lang('os_welcome_txt', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	if( isset($C->HOME_INTRO_TTL) && !empty($C->HOME_INTRO_TTL) ) {
		$D->page_title	= strip_tags($C->SITE_TITLE.' - '.$C->HOME_INTRO_TTL);
		$D->intro_ttl	= $C->HOME_INTRO_TTL;
	}
	if( isset($C->HOME_INTRO_TXT) && !empty($C->HOME_INTRO_TXT) ) {
		$D->intro_txt	= $C->HOME_INTRO_TXT;
	}
	
	$filters	= array('all', 'videos', 'images', 'links', 'files');
	$filter	= 'all';
	if( $this->param('filter') && in_array($this->param('filter'), $filters) ) {
		$filter	= $this->param('filter');
	}
	$at_tmp	= array('videos'=>'videoembed', 'images'=>'image', 'links'=>'link', 'files'=>'file');
	
	$not_in_groups	= '';
	if( !$this->user->is_logged || !$this->user->info->is_network_admin ) {
		$not_in_groups	= array();
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
			if( ! $this->user->is_logged ) {
				$not_in_groups[]	= $obj->id;
				continue;
			}
			$m	= $this->network->get_group_members($g->id);
			if( ! isset($m[$this->user->id]) ) {
				$not_in_groups[]	= $obj->id;
			}
		}
		$not_in_groups	= count($not_in_groups)>0 ? ('AND p.group_id NOT IN('.implode(', ', $not_in_groups).')') : '';
	}
	
	if($filter == 'all') {
		$q1	= 'SELECT COUNT(p.id) FROM posts p WHERE p.user_id<>0 AND p.api_id<>2 '.$not_in_groups;
		$q2	= 'SELECT p.*, "public" AS `type` FROM posts p WHERE p.user_id<>0 AND p.api_id<>2 '.$not_in_groups.' ORDER BY p.id DESC ';
	}
	else {
		$q1	= 'SELECT COUNT(p.id) FROM posts p, posts_attachments a WHERE p.id=a.post_id AND p.user_id<>0 AND p.api_id<>2 '.$not_in_groups.' AND a.type="'.$at_tmp[$filter].'" ';
		$q2	= 'SELECT p.*, "public" AS `type` FROM posts p, posts_attachments a WHERE p.id=a.post_id AND p.user_id<>0 AND p.api_id<>2 '.$not_in_groups.' AND a.type="'.$at_tmp[$filter].'" ORDER BY p.id DESC ';
	}
	
	$D->filter		= $filter;
	$D->num_results	= 0;
	$D->num_pages	= 0;
	$D->pg		= 1;
	$D->posts_html	= '';
	
	$D->num_results	= $db2->fetch_field($q1);
	$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_POSTS);
	$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
	$D->pg	= min($D->pg, $D->num_pages);
	$D->pg	= max($D->pg, 1);
	$from	= ($D->pg - 1) * $C->PAGING_NUM_POSTS;
	$res	= $db2->query($q2.'LIMIT '.$from.', '.$C->PAGING_NUM_POSTS);
	ob_start();
	while($obj = $db2->fetch_object($res)) {
		$D->p	= new post($obj->type, FALSE, $obj);
		if( $D->p->error ) {
			continue;
		}
		if( $this->param('from')=='ajax' && $this->param('onlypost')!="" && $this->param('onlypost')!=$D->p->post_tmp_id ) {
			continue;
		}
		if( $this->param('from')=='ajax' && $this->param('opencomments')!="" && $this->param('opencomments')==$D->p->post_tmp_id ) {
			$D->p->comments_open	= TRUE;
		}
		$D->post_show_slow	= FALSE;
		if( $this->param('from')=='ajax' && isset($_POST['lastpostdate']) && $D->p->post_date>intval($_POST['lastpostdate']) ) {
			$D->post_show_slow	= TRUE;
		}
		$D->parsedpost_attlink_maxlen	= 52;
		$D->parsedpost_attfile_maxlen	= 48;
		if( isset($D->p->post_attached['image']) ) {
			$D->parsedpost_attlink_maxlen	-= 10;
			$D->parsedpost_attfile_maxlen	-= 12;
		}
		if( isset($D->p->post_attached['videoembed']) ) {
			$D->parsedpost_attlink_maxlen	-= 10;
			$D->parsedpost_attfile_maxlen	-= 12;
		}
		$this->load_template('single_post.php');
	}
	unset($D->p);
	$D->paging_url	= $C->SITE_URL.'home/filter:'.$filter.'/pg:';
	if( $D->num_pages>1 && !$this->param('onlypost') ) {
		$this->load_template('paging_posts.php');
	}
	$D->posts_html	= ob_get_contents();
	ob_end_clean();
	
	if( $this->param('from') == 'ajax' )
	{
		echo 'OK:';
		echo $D->posts_html;
		exit;
	}
	
	$D->last_online	= array();
	$num	= 6;
	$time	= 5*60;
	$r	= $db2->query('SELECT id, lastclick_date FROM users WHERE active=1 ORDER BY lastclick_date DESC LIMIT '.($num+1));
	while($o = $db2->fetch_object($r)) {
		if( $o->lastclick_date < time()-$time ) {
			break;
		}
		$D->last_online[]	= $this->network->get_user_by_id($o->id);
	}
	$D->last_online	= array_slice($D->last_online, 0, $num);
	
	$D->post_tags	= array();
	$not_in_groups	= array();
	$r	= $this->db2->query('SELECT id FROM groups WHERE is_public=0');
	while($tmp = $this->db2->fetch_object()) {
		$not_in_group[]	= $tmp->id;
	}
	$not_in_groups	= count($not_in_groups)>0 ? ('AND group_id NOT IN('.implode(', ', $not_in_groups).')') : '';
	$D->post_tags	= $this->network->get_recent_posttags($not_in_groups, 10);
	
	$this->load_template('home.php');
	
?>