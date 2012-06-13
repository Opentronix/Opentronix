<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/user.php');
	
	$u	= $this->network->get_user_by_id(intval($this->params->user));
	if( !$u && $this->user->is_logged ) {
		$this->params->user	= $this->user->id;
		$u	= $this->user->info;
	}
	if( !$u ) {
		$this->redirect('dashboard');
	}
	
	$D->page_title	= $u->username.' - '.$C->SITE_TITLE;
	$D->page_favicon	= $C->IMG_URL.'avatars/thumbs2/'.$u->avatar;
	
	if( $u->id == $this->user->id ) {
		$D->rss_feeds	= array(
			array( $C->SITE_URL.'rss/my:dashboard',	$this->lang('rss_mydashboard',array('#USERNAME#'=>$this->user->info->username)), ),
			array( $C->SITE_URL.'rss/my:posts',		$this->lang('rss_myposts',array('#USERNAME#'=>$this->user->info->username)), ),
			array( $C->SITE_URL.'rss/my:private',	$this->lang('rss_myprivate',array('#USERNAME#'=>$this->user->info->username)), ),
			array( $C->SITE_URL.'rss/my:mentions',	$this->lang('rss_mymentions',array('#USERNAME#'=>$this->user->info->username)), ),
			array( $C->SITE_URL.'rss/my:bookmarks',	$this->lang('rss_mybookmarks',array('#USERNAME#'=>$this->user->info->username)), ),
		);
	}
	else {
		$D->rss_feeds	= array(
			array( $C->SITE_URL.'rss/username:'.$u->username,	$this->lang('rss_usrposts',array('#USERNAME#'=>$u->username)), ),
		);
	}
	
	$D->usr	= & $u;
	$D->is_my_profile	= $u->id==$this->user->id;
	$D->i_follow_him	= $this->user->is_logged ? $this->user->if_follow_user($u->id) : FALSE;
	
	$tabs	= array('updates', 'info', 'coleagues', 'groups');
	$D->tab	= 'updates';
	if( $this->param('tab') && in_array($this->param('tab'), $tabs) ) {
		$D->tab	= $this->param('tab');
	}
	
	$D->usr_website	= '';
	$D->usr_pemail	= '';
	$D->usr_wphone	= '';
	$D->usr_pphone	= '';
	$db2->query('SELECT website, work_phone, personal_phone, personal_email FROM users_details WHERE user_id="'.$u->id.'" LIMIT 1');
	if($tmp = $db->fetch_object()) {
		$D->usr_website	= stripslashes($tmp->website);
		$D->usr_pemail	= stripslashes($tmp->personal_email);
		$D->usr_wphone	= stripslashes($tmp->work_phone);
		$D->usr_pphone	= stripslashes($tmp->personal_phone);
	}
	if( $D->usr_website=='http://' || $D->usr_website=='https://' || $D->usr_website=='ftp://' ) {
		$D->usr_website	= '';
	}
	
	$D->post_tags	= array();
	$not_in_groups	= array();
	$r	= $this->db2->query('SELECT id FROM groups WHERE is_public=0');
	while($tmp = $this->db2->fetch_object()) {
		$not_in_group[]	= $tmp->id;
	}
	$not_in_groups	= count($not_in_groups)>0 ? ('AND group_id NOT IN('.implode(', ', $not_in_groups).')') : '';
	$D->post_tags		= $this->network->get_recent_posttags('AND user_id="'.$u->id.'" '.$not_in_groups, 10);
	
	$D->some_followers	= array();
	$tmp	= array_keys($this->network->get_user_follows($u->id)->followers);
	shuffle($tmp);
	if( count($tmp) > 0 ) {
		foreach($tmp as $tmpu) {
			$tmpu	= $this->network->get_user_by_id($tmpu);
			if( ! $tmpu ) {
				continue;
			}
			$D->some_followers[]	= $tmpu;
			if( count($D->some_followers) == 7 ) {
				break;
			}
		}
	}
	
	$D->num_groups	= 0;
	$groups	= array_keys($this->network->get_user_follows($u->id)->follow_groups);
	$not_in_groups	= array();
	if( !$this->user->is_logged || !$this->user->info->is_network_admin ) {
		$r	= $db2->query('SELECT id FROM groups WHERE is_public=0');
		while($obj = $db2->fetch_object($r)) {
			$obj->id	= intval($obj->id);
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
	$groups	= array_diff($groups, $not_in_groups);
	$D->num_groups	= count($groups);
	
	if($D->tab == 'updates') {
		$not_in_groups	= count($not_in_groups)==0 ? '' : ('AND p.group_id NOT IN('.implode(', ', $not_in_groups).')');
		$D->posts_html	= '';
		$D->filter	= 'all';
		$tmp	= array('all', 'videos', 'images', 'links', 'files');
		if( $this->param('filter') && in_array($this->param('filter'), $tmp) ) {
			$D->filter	= $this->param('filter');
		}
		$tmp	= array('videos'=>'videoembed', 'images'=>'image', 'links'=>'link', 'files'=>'file');
		if($D->filter == 'all') {
			$q1	= 'SELECT COUNT(p.id) FROM posts p WHERE p.user_id="'.$u->id.'" '.$not_in_groups;
			$q2	= 'SELECT p.*, "public" AS `type` FROM posts p WHERE p.user_id="'.$u->id.'" '.$not_in_groups.' ORDER BY p.id DESC ';
		}
		else {
			$q1	= 'SELECT COUNT(p.id) FROM posts p, posts_attachments a WHERE p.id=a.post_id AND p.user_id="'.$u->id.'" AND a.type="'.$tmp[$D->filter].'" '.$not_in_groups;
			$q2	= 'SELECT p.*, "public" AS `type` FROM posts p, posts_attachments a WHERE p.id=a.post_id AND p.user_id="'.$u->id.'" AND a.type="'.$tmp[$D->filter].'" '.$not_in_groups.' ORDER BY p.id DESC ';
		}
		$D->num_results	= $db2->fetch_field($q1);
		if( 0 == $D->num_results ) {
			$arr	= array('#USERNAME#'=>$u->username, '#SITE_TITLE#'=>htmlspecialchars($C->OUTSIDE_SITE_TITLE), '#A1#'=>'<a href="javascript:;" onclick="postform_open();">', '#A2#'=>'</a>', );
			$lngkey_ttl	= $D->is_my_profile ? 'noposts_myprofile_ttl' : 'noposts_usrprofile_ttl';
			$lngkey_txt	= $D->is_my_profile ? 'noposts_myprofile_txt' : 'noposts_usrprofile_txt';
			if($D->filter != 'all') {
				$lngkey_ttl	.= '_filter';
				$lngkey_txt	.= '_filter';
			}
			$D->noposts_box_title	= $this->lang($lngkey_ttl, $arr);
			$D->noposts_box_text	= $this->lang($lngkey_txt, $arr);
			$D->posts_html	= $this->load_template('noposts_box.php', FALSE);
		}
		else {
			$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_POSTS);
			$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
			$D->pg	= min($D->pg, $D->num_pages);
			$D->pg	= max($D->pg, 1);
			$from	= ($D->pg - 1) * $C->PAGING_NUM_POSTS;
			$res	= $db2->query($q2.'LIMIT '.$from.', '.$C->PAGING_NUM_POSTS);
			ob_start();
			while($obj = $db->fetch_object($res)) {
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
				$D->parsedpost_attlink_maxlen	= 75;
				$D->parsedpost_attfile_maxlen	= 71;
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
			$D->paging_url	= $C->SITE_URL.$u->username.'/filter:'.$D->filter.'/pg:';
			if( $D->num_pages > 1 && !$this->param('onlypost') ) {
				$this->load_template('paging_posts.php');
			}
			$D->posts_html	= ob_get_contents();
			ob_end_clean();
		}
		if( $this->param('from') == 'ajax' ) {
			echo 'OK:';
			echo $D->posts_html;
			exit;
		}
	}
	elseif($D->tab == 'coleagues') {
		$filters	= array('ifollow', 'followers');
		$D->filter	= 'ifollow';
		if( $this->param('filter') && in_array($this->param('filter'), $filters) ) {
			$D->filter	= $this->param('filter');
		}
		$tmp	= $this->network->get_user_follows($D->usr->id);
		$D->fnums	= array('ifollow'=>count($tmp->follow_users), 'followers'=>count($tmp->followers));
		$tmp	= $D->filter=='ifollow' ? $tmp->follow_users : $tmp->followers;
		$D->num_results	= count($tmp);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$tmp	= array_keys(array_slice($tmp, $from, $C->PAGING_NUM_USERS, TRUE));
		$usrs	= array();
		foreach($tmp as $sdf) {
			if($sdf = $this->network->get_user_by_id($sdf)) {
				$usrs[]	= $sdf;
			}
		}
		$D->users_html	= '';
		if( count($usrs) > 0 ) {
			ob_start();
			foreach($usrs as $tmp) {
				$D->u	= $tmp;
				$this->load_template('single_user.php');
			}
			$D->paging_url	= $C->SITE_URL.$D->usr->username.'/tab:coleagues/filter:'.$D->filter.'/pg:';
			if( $D->num_pages > 1 ) {
				$this->load_template('paging_users.php');
			}
			$D->users_html	= ob_get_contents();
			ob_end_clean();
			unset($tmp, $sdf, $usrs, $D->u);
		}
		else {
			$arr	= array('#USERNAME#'=>$u->username);
			$lngkey_ttl	= $D->filter=='ifollow' ? 'nousrs_subtab1_ttl' : 'nousrs_subtab2_ttl';
			$lngkey_txt	= $D->filter=='ifollow' ? 'nousrs_subtab1_txt' : 'nousrs_subtab2_txt';
			if( $D->is_my_profile ) {
				$lngkey_ttl	.= '_me';
				$lngkey_txt .= '_me';
			}
			$D->noposts_box_title	= $this->lang($lngkey_ttl, $arr);
			$D->noposts_box_text	= $this->lang($lngkey_txt, $arr);
			$D->users_html	= $this->load_template('noposts_box.php', FALSE);
		}
		$D->filter1_title	= 'usr_coleagues_subtab1';
		$D->filter2_title	= 'usr_coleagues_subtab2';
		if( $D->usr->id == $this->user->id ) {
			$D->filter1_title	= 'usr_coleagues_subtab1_me';
			$D->filter2_title	= 'usr_coleagues_subtab2_me';
		}
		$D->filter1_title	= $this->lang($D->filter1_title, array('#USERNAME#'=>$D->usr->username));
		$D->filter2_title	= $this->lang($D->filter2_title, array('#USERNAME#'=>$D->usr->username));
	}
	elseif($D->tab == 'info') {
		$D->i	= new stdClass;
		$D->i->ims	= array();
		$D->i->prs	= array();
		$db2->query('SELECT * FROM users_details WHERE user_id="'.$u->id.'" LIMIT 1');
		if($obj = $db2->fetch_object()) {
			unset($obj->user_id);
			foreach($obj as $k=>$v) {
				$v	= stripslashes($v);
				if( substr($k,0,5) == 'prof_' ) {
					if( preg_match('/^(.*)\#\#\#(.*)$/iu', $v, $m) ) {
						if( empty($m[1]) ) {
							$m[1] = $u->fullname;
						}
						$D->i->$k	= array($m[2], $m[1]);
						$D->i->prs[$k]	= $D->i->$k;
					}
				}
				else {
					$D->i->$k	= $v;
					if( substr($k,0,3) == 'im_' && !empty($v) ) {
						$D->i->ims[$k]	= $D->i->$k;
					}
				}
			}
		}
		$D->birthdate	= '';
		$bd_day	= intval(substr($u->birthdate,8,2));
		$bd_month	= intval(substr($u->birthdate,5,2));
		$bd_year	= intval(substr($u->birthdate,0,4));
		if( $bd_day>0 && $bd_month>0 && $bd_year>0 ) {
			$D->birthdate	= mktime(0, 0, 1, $bd_month, $bd_day, $bd_year);
			$D->birthdate	= strftime($this->lang('usr_info_birthdate_dtformat'), $D->birthdate);
		}
		$D->date_register		= strftime($this->lang('usr_info_birthdate_dtformat'), $u->reg_date);
		$D->date_lastlogin	= '';
		$tmp	= intval($db->fetch_field('SELECT lastclick_date FROM users WHERE id="'.$u->id.'" LIMIT 1'));
		if( $tmp > 0 ) {
			$D->date_lastlogin	= strftime($this->lang('usr_info_aboutme_lgndtfrmt'), $tmp);
		}
	}
	elseif($D->tab=='groups' && $D->num_groups>0) {
		$D->num_results	= $D->num_groups;
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_GROUPS);
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_GROUPS;
		$tmp	= array_slice($groups, $from, $C->PAGING_NUM_GROUPS);
		$grps	= array();
		foreach($tmp as $sdf) {
			if($sdf = $this->network->get_group_by_id($sdf)) {
				$grps[]	= $sdf;
			}
		}
		$D->groups_html	= '';
		ob_start();
		foreach($grps as $tmp) {
			$D->g	= $tmp;
			$this->load_template('single_group.php');
		}
		$D->paging_url	= $C->SITE_URL.$D->usr->username.'/tab:groups/pg:';
		if( $D->num_pages > 1 ) {
			$this->load_template('paging_groups.php');
		}
		$D->groups_html	= ob_get_contents();
		ob_end_clean();
		unset($tmp, $sdf, $grps, $D->g);
		$D->groups_title	= $this->lang($D->is_my_profile?'usr_groups_title_me':'usr_groups_title', array('#USERNAME#'=>$D->usr->username));
	}
	
	$this->load_template('user.php');
	
?>