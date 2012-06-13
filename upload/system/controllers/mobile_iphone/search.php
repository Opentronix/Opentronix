<?php
	
	if( !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $this->network->id && $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
	
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/search.php');
	
	if( $this->param('usertag') ) {
		$tmp	= str_replace('/', '', urldecode($this->param('usertag')));
		$this->redirect( $C->SITE_URL.'search/lookin:users/lookfor:'.urlencode(trim($tmp)) );
	}
	if( $this->param('posttag') ) {
		$tmp	= str_replace('/', '', urldecode($this->param('posttag')));
		$this->redirect( $C->SITE_URL.'search/lookin:posts/lookfor:'.urlencode(trim($tmp)) );
	}
	if( $this->param('saved') ) {
		$tmp	= $db2->e(trim($this->param('saved')));
		$db2->query('SELECT search_url FROM searches WHERE user_id="'.$this->user->id.'" AND search_key="'.$tmp.'" LIMIT 1');
		if( $tmp = $db2->fetch_object() ) {
			$tmp	= trim(urldecode(stripslashes($tmp->search_url)));
			if( preg_match('/\/s\:(.*)($|\|)/iu', $tmp, $m) ) {
				$this->redirect( $C->SITE_URL.'search/lookin:posts/lookfor:'.urlencode(trim($m[1])) );
			}
		}
	}
	if( isset($_POST['lookin'], $_POST['lookfor']) ) {
		$tmp1	= trim(str_replace('/', '', $_POST['lookin']));
		$tmp2	= trim(str_replace('/', '', $_POST['lookfor']));
		$this->redirect( $C->SITE_URL.'search/lookin:'.$tmp1.'/lookfor:'.urlencode($tmp2) );
	}
	
	$lookins	= array('posts', 'users', 'groups');
	$D->lookin	= 'posts';
	if( $this->param('lookin') && in_array($this->param('lookin'), $lookins) ) {
		$D->lookin	= $this->param('lookin');
	}
	
	$D->search_string	= $this->param('lookfor') ? urldecode($this->param('lookfor')) : '';
	$D->search_string	= preg_replace('/\s+/us', ' ', $D->search_string);
	$D->search_string	= trim($D->search_string);
	
	$D->page_title	= $this->lang('search_title_'.$D->lookin, array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	if( $D->lookin=='users' && !empty($D->search_string) )
	{
		$D->num_results	= 0;
		$D->num_pages	= 0;
		$D->num_per_page	= 0;
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->users_html	= '';
		$uids	= array();
		$tmp	= $db2->e($D->search_string);
		$db2->query('SELECT id FROM users WHERE active=1 AND (username="'.$tmp.'" OR fullname="'.$tmp.'" OR position="'.$tmp.'") ORDER BY username ASC');
		while($o = $db2->fetch_object()) {
			$uids[]	= intval($o->id);
		}
		$tmp	= str_replace(array('%','_'), array('\%','\_'), $db2->e($D->search_string));
		$db2->query('SELECT id FROM users WHERE active=1 AND (username LIKE "%'.$tmp.'%" OR fullname LIKE "%'.$tmp.'%" OR position LIKE "%'.$tmp.'%") ORDER BY username ASC');
		while($o = $db2->fetch_object()) {
			$uids[]	= intval($o->id);
		}
		$db2->query('SELECT id FROM users WHERE tags REGEXP "(^|\,| )'.$db2->e(preg_quote($D->search_string)).'($|\,)" ORDER BY username ASC');
		while($o = $db2->fetch_object()) {
			$uids[]	= intval($o->id);
		}
		//if( 0 == count($uids) ) {
			$tmp	= str_replace(array('%','_'), array('\%','\_'), $db2->e($D->search_string));
			$db2->query('SELECT id FROM users WHERE active=1 AND about_me LIKE "%'.$tmp.'%" ORDER BY username ASC, num_followers DESC');
			while($o = $db2->fetch_object()) {
				$uids[]	= intval($o->id);
			}
		//}
		$uids	= array_unique($uids);
		$D->num_results	= count($uids);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		if( 0 < $D->num_results ) {
			$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
			$tmp	= array_slice($uids, $from, $C->PAGING_NUM_USERS);
			$i	= 0;
			ob_start();
			foreach($tmp as $u) {
				$u	= $this->network->get_user_by_id($u);
				if( ! $u ) { continue; }
				$D->u	= $u;
				$D->u->list_index	= $i++;
				$this->load_template('mobile_iphone/single_user.php');
			}
			$D->users_html	= ob_get_contents();
			ob_end_clean();
		}
	}
	elseif( $D->lookin=='groups' && !empty($D->search_string) ) {
		$D->num_results	= 0;
		$D->num_pages	= 0;
		$D->num_per_page	= 0;
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->groups_html	= '';
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
		$gids	= array();
		$tmp	= $db2->e($D->search_string);
		$db2->query('SELECT id FROM groups WHERE (groupname="'.$tmp.'" OR title="'.$tmp.'") '.$not_in_groups.' ORDER BY title ASC, num_followers DESC');
		while($o = $db2->fetch_object()) {
			$gids[]	= intval($o->id);
		}
		$tmp	= str_replace(array('%','_'), array('\%','\_'), $db2->e($D->search_string));
		$db2->query('SELECT id FROM groups WHERE (groupname LIKE "%'.$tmp.'%" OR title LIKE "%'.$tmp.'%") '.$not_in_groups.' ORDER BY title ASC, num_followers DESC');
		while($o = $db2->fetch_object()) {
			$gids[]	= intval($o->id);
		}
		//if( 0 == count($gids) ) {
			$db2->query('SELECT id FROM groups WHERE about_me LIKE "%'.$tmp.'%" '.$not_in_groups.' ORDER BY title ASC, num_followers DESC');
			while($o = $db2->fetch_object()) {
				$gids[]	= intval($o->id);
			}
		//}
		$gids	= array_unique($gids);
		$D->num_results	= count($gids);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_GROUPS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		if( 0 < $D->num_results ) {
			$from	= ($D->pg - 1) * $C->PAGING_NUM_GROUPS;
			$tmp	= array_slice($gids, $from, $C->PAGING_NUM_GROUPS);
			$i	= 0;
			ob_start();
			foreach($tmp as $u) {
				$g	= $this->network->get_group_by_id($u);
				if( ! $g ) { continue; }
				$D->g	= $g;
				$D->g->list_index	= $i++;
				$this->load_template('mobile_iphone/single_group.php');
			}
			$D->groups_html	= ob_get_contents();
			ob_end_clean();
		}
	}
	elseif( $D->lookin=='posts' && !empty($D->search_string) ) {
		$D->num_results	= 0;
		$D->start_from	= 0;
		$D->posts_html	= '';
		$D->error	= FALSE;
		$D->errmsg	= '';
		if( !$D->error ) {
			$in_where	= '(api_id=2 OR user_id<>0)';
			$tmp	= str_replace(array('%','_'), array('\%','\_'), $db2->e($D->search_string));
			if( $tmp != '#' ) {
				$tmp	= preg_replace('/^\#/', '', $tmp);
			}
			$in_where	.= ' AND (message LIKE "%'.$tmp.'%"';
			if( mb_strlen($D->search_string)>=3 && FALSE!==strpos($D->search_string,' ') ) {
				$tmp	= preg_replace('/[^ا-یא-תÀ-ÿ一-龥а-яa-z0-9\s]/iu', '', $tmp);
				$tmp	= $db2->e($tmp);
				$tmp	= preg_replace('/\s+/iu', ' ', $tmp);
				$tmp	= preg_replace('/(^|\s)/iu', ' +', $tmp);
				$tmp	= trim($tmp);
				$in_where	.= ' OR MATCH(message) AGAINST("'.$tmp.'" IN BOOLEAN MODE)';
			}
			$in_where2	= '';
			$tmp	= str_replace(array('%','_'), array('\%','\_'), $db2->e($D->search_string));
			if( $tmp != '#' ) {
				$tmp	= preg_replace('/^\#/', '', $tmp);
			}
			$in_where2	.= 'message LIKE "%'.$tmp.'%"';
			if( mb_strlen($D->search_string)>=3 && FALSE!==strpos($D->search_string,' ') ) {
				$tmp	= preg_replace('/[^ا-یא-תÀ-ÿ一-龥а-яa-z0-9\s]/iu', '', $tmp);
				$tmp	= $db2->e($tmp);
				$tmp	= preg_replace('/\s+/iu', ' ', $tmp);
				$tmp	= preg_replace('/(^|\s)/iu', ' +', $tmp);
				$tmp	= trim($tmp);
				$in_where2	.= ' OR MATCH(message) AGAINST("'.$tmp.'" IN BOOLEAN MODE)';
			}
			$tmppids	= array();
			$db2->query('SELECT post_id FROM posts_comments WHERE '.$in_where2);
			while($tmp = $db2->fetch_object()) {
				$tmppids[]	= $tmp->post_id;
			}
			if( 1 == count($tmppids) ) {
				$in_where	.= ' OR id='.reset($tmppids);
			}
			elseif( 1 < count($tmppids) ) {
				$in_where	.= ' OR id IN('.implode(', ', $tmppids).')';
			}
			$in_where	.= ')';
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
			if( count($not_in_groups) == 1 ) {
				$in_where	.= ' AND group_id!="'.reset($not_in_groups).'"';
			}
			elseif( count($not_in_groups) > 1 ) {
				$in_where	.= ' AND group_id NOT IN('.implode(', ', $not_in_groups).')';
			}
			//if( preg_match('/^\#[а-яa-z0-9\-_]{1,50}$/iu', $D->search_string) ) {
			//	$in_where	.= ' AND posttags>0';
			//}
			$D->num_results	= $db2->fetch_field('SELECT COUNT(id) FROM posts WHERE '.$in_where);
			if( 0 < $D->num_results ) {
				$D->start_from	= $this->param('start_from') ? intval($this->param('start_from')) : 0;
				$D->start_from	= max($D->start_from, 0);
				$D->start_from	= min($D->start_from, $D->num_results);
				$res	= $db2->query('SELECT *, "public" AS `type` FROM posts WHERE '.$in_where.' ORDER BY id DESC LIMIT '.$D->start_from.', '.$C->PAGING_NUM_POSTS);
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
				$D->posts_html	= ob_get_contents();
				ob_end_clean();
			}
			if( $this->param('from') == 'ajax' ) {
				echo 'OK:'.$D->posts_number.':';
				echo $D->posts_html;
				exit;
			}
		}
	}
	
	$D->saved_searches	= array();
	if( $D->lookin == 'posts' ) {
		$db2->query('SELECT id, search_key, search_string FROM searches WHERE user_id="'.$this->user->id.'" ORDER BY id DESC');
		while($tmp = $db2->fetch_object()) {
			$tmp->search_key		= stripslashes($tmp->search_key);
			$tmp->search_string	= stripslashes($tmp->search_string);
			$D->saved_searches[$tmp->id]	= $tmp;
		}
	}
	
	$this->load_template('mobile_iphone/search.php');
	
?>