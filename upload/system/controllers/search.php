<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/search.php');
	
	if( $this->param('saved') ) {
		$tmp	= $db2->e(trim($this->param('saved')));
		$db2->query('SELECT search_url FROM searches WHERE user_id="'.$this->user->id.'" AND search_key="'.$tmp.'" LIMIT 1');
		if( $tmp = $db2->fetch_object() ) {
			$this->redirect( stripslashes($tmp->search_url) );
		}
	}
	if( $this->param('usertag') ) {
		$tmp	= str_replace('/', '', urldecode($this->param('usertag')));
		$this->redirect( $C->SITE_URL.'search/tab:users/s:'.urlencode(trim($tmp)) );
	}
	if( $this->param('posttag') ) {
		$tmp	= str_replace('/', '', urldecode($this->param('posttag')));
		$this->redirect( $C->SITE_URL.'search/tab:posts/s:'.urlencode(trim($tmp)) );
	}
	if( isset($_POST['lookin']) && $_POST['lookin']=='posts' && isset($_POST['lookfor'], $_POST['puser'], $_POST['pgroup'], $_POST['pdate1'], $_POST['pdate2']) ) {
		$lookfor	= str_replace('/', '', $_POST['lookfor']);
		$puser	= preg_replace('/[^a-z0-9-\_]/iu', '', $_POST['puser']);
		$pgroup	= preg_replace('/[^ا-یא-תÀ-ÿ一-龥а-яa-z0-9\-\.\s]/iu', '', $_POST['pgroup']);
		$ptypes	= isset($_POST['ptype']) ? implode(',', preg_replace('/[^a-z]/iu','',$_POST['ptype'])) : '';
		$pdate1	= trim(implode(',', preg_replace('/[^0-9]/iu','',$_POST['pdate1'])), ',');
		$pdate2	= trim(implode(',', preg_replace('/[^0-9]/iu','',$_POST['pdate2'])), ',');
		if( ! preg_match('/^[0-9]{1,2}\,[0-9]{1,2}\,[0-9]{4}$/', $pdate1) ) { $pdate1 = ''; }
		if( ! preg_match('/^[0-9]{1,2}\,[0-9]{1,2}\,[0-9]{4}$/', $pdate2) ) { $pdate2 = ''; }
		$url	= $C->SITE_URL.'search/tab:posts/ptypes:'.$ptypes.'/';
		if( ! empty($puser) ) { $url .= 'puser:'.$puser.'/'; }
		if( ! empty($pgroup) ) { $url .= 'pgroup:'.urlencode($pgroup).'/'; }
		if( ! empty($pdate1) ) { $url .= 'pdate1:'.$pdate1.'/'; }
		if( ! empty($pdate2) ) { $url .= 'pdate2:'.$pdate2.'/'; }
		$url	.= 's:'.urlencode(trim($lookfor));
		$this->redirect($url);
	}
	if( isset($_POST['lookin'], $_POST['lookfor']) && $_POST['lookin']=='posts' ) {
		$tmp	= str_replace('/', '', $_POST['lookfor']);
		$this->redirect( $C->SITE_URL.'search/tab:'.trim($_POST['lookin']).'/ptypes:link,image,video,file,comments/s:'.urlencode(trim($tmp)) );
	}
	if( isset($_POST['lookin'], $_POST['lookfor']) ) {
		$tmp	= str_replace('/', '', $_POST['lookfor']);
		$this->redirect( $C->SITE_URL.'search/tab:'.trim($_POST['lookin']).'/s:'.urlencode(trim($tmp)) );
	}
	
	$tabs	= array('posts', 'users', 'groups');
	$D->tab	= 'posts';
	if( $this->param('tab') && in_array($this->param('tab'), $tabs) ) {
		$D->tab	= $this->param('tab');
	}
	
	$D->search_string	= urldecode($this->param('s'));
	$D->search_string	= preg_replace('/\s+/us', ' ', $D->search_string);
	$D->search_string	= trim($D->search_string);
	
	$D->page_title	= $this->lang('srch_title_'.$D->tab, array('#SITE_TITLE#'=>$C->SITE_TITLE));
	$D->search_title	= $this->lang( (empty($D->search_string)?'srch_title2_':'srch_title3_').$D->tab, array('#STRING#'=>htmlspecialchars(str_cut($D->search_string,30))));
	
	$D->num_results	= 0;
	$D->num_pages	= 0;
	$D->num_per_page	= 0;
	$D->pg	= 1;
	$D->posts_html	= '';
	$D->users_html	= '';
	$D->groups_html	= '';
	
	if( $D->tab=='users' && !empty($D->search_string) )
	{
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
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		if( 0 == $D->num_results ) {
			$D->noposts_box_title	= $this->lang('srch_noresult_users_ttl');
			$D->noposts_box_text	= $this->lang('srch_noresult_users_txt');
			$D->users_html	= $this->load_template('noposts_box.php', FALSE);
		}
		else {
			$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
			$tmp	= array_slice($uids, $from, $C->PAGING_NUM_USERS);
			ob_start();
			foreach($tmp as $u) {
				$u	= $this->network->get_user_by_id($u);
				if( ! $u ) { continue; }
				$D->u	= $u;
				$this->load_template('single_user.php');
			}
			$D->paging_url	= $C->SITE_URL.'search/tab:users/s:'.urlencode($D->search_string).'/pg:';
			if( $D->num_pages > 1 ) {
				$this->load_template('paging_users.php');
			}
			$D->users_html	= ob_get_contents();
			ob_end_clean();
		}
	}
	elseif( $D->tab=='groups' && !empty($D->search_string) )
	{
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
				if( ! $this->user->is_logged ) {
					$not_in_groups[]	= $obj->id;
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
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		if( 0 == $D->num_results ) {
			$D->noposts_box_title	= $this->lang('srch_noresult_groups_ttl');
			$D->noposts_box_text	= $this->lang('srch_noresult_groups_txt');
			$D->groups_html	= $this->load_template('noposts_box.php', FALSE);
		}
		else {
			$from	= ($D->pg - 1) * $C->PAGING_NUM_GROUPS;
			$tmp	= array_slice($gids, $from, $C->PAGING_NUM_GROUPS);
			ob_start();
			foreach($tmp as $u) {
				$g	= $this->network->get_group_by_id($u);
				if( ! $g ) { continue; }
				$D->g	= $g;
				$this->load_template('single_group.php');
			}
			$D->paging_url	= $C->SITE_URL.'search/tab:group/s:'.urlencode($D->search_string).'/pg:';
			if( $D->num_pages > 1 ) {
				$this->load_template('paging_group.php');
			}
			$D->groups_html	= ob_get_contents();
			ob_end_clean();
		}
	}
	elseif( $D->tab == 'posts' )
	{
		$D->can_be_saved	= FALSE;
		$D->error	= FALSE;
		$D->errmsg	= '';
		$puser	= trim( preg_replace('/[^a-z0-9-\_]/iu', '', $this->param('puser')) );
		$pgroup	= trim( preg_replace('/[^ا-یא-תÀ-ÿ一-龥а-яa-z0-9\-\.\s]/iu', '', urldecode($this->param('pgroup'))) );
		$ptypes	= 'link,image,video,file,comments';
		if( isset($this->params->ptypes) ) {
			$ptypes	= trim( preg_replace('/[^a-z\,]/', '', $this->params->ptypes) );
		}
		$ptypes	= explode(',', $ptypes);
		foreach($ptypes as $k=>$v) {
			if( $v!='link' && $v!='image' && $v!='file' && $v!='video' && $v!='comments' ) { unset($ptypes[$k]); }
		}
		$pdate1	= trim( preg_replace('/[^0-9\,]/iu', '', $this->param('pdate1')) );
		if( ! preg_match('/^[0-9]{1,2}\,[0-9]{1,2}\,[0-9]{4}$/', $pdate1) ) { $pdate1 = ''; }
		$pdate2	= trim( preg_replace('/[^0-9\,]/iu', '', $this->param('pdate2')) );
		if( ! preg_match('/^[0-9]{1,2}\,[0-9]{1,2}\,[0-9]{4}$/', $pdate2) ) { $pdate2 = ''; }
		
		$D->box_expanded	= (object) array('type'=>FALSE, 'author'=>FALSE, 'group'=>FALSE, 'date'=>FALSE);
		if( count($ptypes) != 5 ) {
			$D->box_expanded->type		= TRUE;
		}
		if( ! empty($puser) ) {
			$D->box_expanded->author	= TRUE;
		}
		if( ! empty($pgroup) ) {
			$D->box_expanded->group	= TRUE;
		}
		if( ! empty($pdate1) || ! empty($pdate2) ) {
			$D->box_expanded->date		= TRUE;
		}
		
		$D->form_user	= $puser;
		$D->form_group	= $pgroup;
		$D->form_type	= array();
		foreach($ptypes as $v) {
			$D->form_type[$v]	= 1;
		}
		$D->form_date1		= array('d'=>'', 'm'=>'', 'y'=>'');
		$D->form_date2		= array('d'=>'', 'm'=>'', 'y'=>'');
		if( ! empty($pdate1) ) {
			list($D->form_date1['d'], $D->form_date1['m'], $D->form_date1['y']) = explode(',', $pdate1);
		}
		if( ! empty($pdate2) ) {
			list($D->form_date2['d'], $D->form_date2['m'], $D->form_date2['y']) = explode(',', $pdate2);
		}
		$D->form_date1_days	= array('');
		for($i=1; $i<=31; $i++) { $D->form_date1_days[] = $i; }
		$D->form_date1_months	= array('');
		for($i=1; $i<=12; $i++) { $D->form_date1_months[] = $i; }
		$D->form_date1_years	= array();
		$tmp	= intval($db2->fetch_field('SELECT FROM_UNIXTIME(date,"%Y") FROM posts ORDER BY id ASC LIMIT 1'));
		if( ! $tmp ) { $tmp = intval(date('Y')); }
		for($i=$tmp; $i<=intval(date('Y')); $i++) {
			$D->form_date1_years[]	= $i;
		}
		$D->form_date1_years[]	= '';
		$D->form_date1_years	= array_reverse($D->form_date1_years);
		$D->form_date2_days	= $D->form_date1_days;
		$D->form_date2_months	= $D->form_date1_months;
		$D->form_date2_years	= $D->form_date1_years;
		
		if( ! empty($D->search_string) ) {
			$D->error	= FALSE;
			$D->errmsg	= '';
			$u	= FALSE;
			$g	= FALSE;
			if( !$D->error && !empty($puser) ) {
				if( ! $u = $this->network->get_user_by_username($puser) ) {
					$D->error	= TRUE;
					$D->errmsg	= $this->lang('srch_noresult_posts_invusr', array('#USERNAME#'=>'<b>'.htmlspecialchars($puser).'</b>'));
				}
			}
			if( !$D->error && !empty($pgroup) ) {
				if( ! $g = $this->network->get_group_by_name($pgroup) ) {
					$D->error	= TRUE;
					$D->errmsg	= $this->lang('srch_noresult_posts_invgrp', array('#GROUP#'=>'<b>'.htmlspecialchars($pgroup).'</b>'));
				}
			}
			if( !$D->error && $g && $g->is_private ) {
				if( ! in_array(intval($this->user->id), $this->network->get_group_invited_members($g->id)) ) {
					$g	= FALSE;
					$D->error	= TRUE;
					$D->errmsg	= $this->lang('srch_noresult_posts_invgrp', array('#GROUP#'=>'<b>'.htmlspecialchars($pgroup).'</b>'));
				}
			}
			$t1	= FALSE;
			$t2	= FALSE;
			if( !$D->error && (!empty($pdate1) || !empty($pdate2)) ) {
				if( ! empty($pdate1) ) {
					list($d,$m,$y) = explode(',', $pdate1);
					$t1	= mktime(0, 0, 1, $m, $d, $y);
					if( $t1 > time() ) {
						$D->error	= TRUE;
						$D->errmsg	= $this->lang('srch_noresult_posts_invdt');
					}
				}
				if( ! empty($pdate2) ) {
					list($d,$m,$y) = explode(',', $pdate2);
					$t2	= mktime(23, 59, 59, $m, $d, $y);
				}
				if( !$D->error && $t1 && $t2 && $t1>$t2 ) {
					$D->error	= TRUE;
					$D->errmsg	= $this->lang('srch_noresult_posts_invdt');
				}
			}
			if( !$D->error ) {
				$D->can_be_saved	= TRUE;
				$D->search_saved	= FALSE;
				$search_key	= md5($D->search_string."\n".serialize($ptypes)."\n".$puser."\n".$pgroup."\n".serialize($pdate1)."\n".serialize($pdate2));
				$search_key	= $db2->escape($search_key);
				$db2->query('SELECT id FROM searches WHERE user_id="'.$this->user->id.'" AND search_key="'.$search_key.'" LIMIT 1');
				if($obj = $db2->fetch_object()) {
					$D->search_saved	= $obj->id;
				}
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
				$search_in_comments	= FALSE;
				if( $tmpci = array_search('comments', $ptypes) ) {
					$search_in_comments	= TRUE;
					unset($ptypes[$tmpci]);
					$ptypes	= array_values($ptypes);
				}
				if( $search_in_comments ) {
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
				}
				$in_where	.= ')';
				if( $u ) {
					$in_where	.= ' AND user_id="'.$u->id.'"';
				}
				if( $g ) {
					$in_where	.= ' AND group_id="'.$g->id.'"';
				}
				else {
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
							if( ! $this->user->is_logged ) {
								$not_in_groups[]	= $obj->id;
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
				}
				if( $t1 && $t2 ) {
					$in_where	.= ' AND date BETWEEN "'.$t1.'" AND "'.$t2.'"';
				}
				elseif( $t1 ) {
					$in_where	.= ' AND date>="'.$t1.'"';
				}
				elseif( $t2 ) {
					$in_where	.= ' AND date<="'.$t2.'"';
				}
				if( count($ptypes) == 0 ) {
					$in_where	.= ' AND attached=0';
				}
				elseif( count($ptypes) < 4 ) {
					$not_in_att	= array();
					$tmp	= array_flip($ptypes);
					if( ! isset($tmp['link']) ) { $not_in_att[] = '"link"'; }
					if( ! isset($tmp['image']) ) { $not_in_att[] = '"image"'; }
					if( ! isset($tmp['video']) ) { $not_in_att[] = '"videoembed"'; }
					if( ! isset($tmp['file']) ) { $not_in_att[] = '"file"'; }
					$in_where	.= ' AND (attached=0 OR id NOT IN( SELECT DISTINCT post_id FROM posts_attachments WHERE `type`';
					if( count($not_in_att) == 1 ) {
						$in_where	.= '='.reset($not_in_att);
					}
					else {
						$in_where	.= ' IN('.implode(', ', $not_in_att).')';
					}
					$in_where	.= ' ))';
				}
				$D->num_results	= $db2->fetch_field('SELECT COUNT(id) FROM posts WHERE '.$in_where);
				$tmp_url	= trim($_SERVER['REQUEST_URI'], '/');
				$tmp_url	= preg_replace('/(^|\/)pg\:[^\/]*/iu', '', $tmp_url);
				$tmp_url	= preg_replace('/(^|\/)from\:[^\/]*/iu', '', $tmp_url);
				$tmp_url	= preg_replace('/(^|\/)r\:[^\/]*/iu', '', $tmp_url);
				$tmp_url	= preg_replace('/\/+/', '/', $tmp_url);
				$tmp_url	= '/'.trim($tmp_url, '/');
				$D->ajax_url	= str_replace('/search/', '/from:ajax/search/', $tmp_url);
				$D->paging_url	= $tmp_url.'/pg:';
				
				if( $this->param('from')=='ajax' && isset($_POST['savesearch']) ) {
					if( $_POST['savesearch']=='on' && !$D->search_saved ) {
						$db2->query('INSERT INTO searches SET user_id="'.$this->user->id.'", search_key="'.$search_key.'", search_string="'.$db2->e($D->search_string).'", search_url="'.$db2->e($tmp_url).'", added_date="'.time().'", total_hits=1, last_results="'.$D->num_results.'" ');
						echo 'OK:'.intval($db2->insert_id());
						return;
					}
					elseif( $_POST['savesearch']=='off' && $D->search_saved ) {
						$db2->query('DELETE FROM searches WHERE id="'.$D->search_saved.'" LIMIT 1');
						echo 'OK:0';
						return;
					}
					echo 'ERROR';
					return;
				}
				
				if( $D->num_results == 0 ) {
					$D->error	= TRUE;
					$D->errmsg	= $this->lang('srch_noresult_posts_def');
				}
				else {
					$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_POSTS);
					$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
					$D->pg	= min($D->pg, $D->num_pages);
					$D->pg	= max($D->pg, 1);
					$from	= ($D->pg - 1) * $C->PAGING_NUM_POSTS;
					$res	= $db2->query('SELECT *, "public" AS `type` FROM posts WHERE '.$in_where.' ORDER BY id DESC LIMIT '.$from.', '.$C->PAGING_NUM_POSTS);
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
					if( $D->num_pages > 1 && !$this->param('onlypost') ) {
						$this->load_template('paging_posts.php');
					}
					$D->posts_html	= ob_get_contents();
					ob_end_clean();
				}
				if( $D->search_saved ) {
					$db2->query('UPDATE searches SET total_hits=total_hits+1, last_results="'.$D->num_results.'" WHERE id="'.$D->search_saved.'" LIMIT 1');
				}
				if( $this->param('from') == 'ajax' ) {
					echo 'OK:';
					echo $D->posts_html;
					exit;
				}
			}
		}
		$D->saved_searches	= array();
		$db2->query('SELECT id, search_key, search_string FROM searches WHERE user_id="'.$this->user->id.'" ORDER BY id DESC');
		while($tmp = $db2->fetch_object()) {
			$tmp->search_key		= stripslashes($tmp->search_key);
			$tmp->search_string	= stripslashes($tmp->search_string);
			$D->saved_searches[$tmp->id]	= $tmp;
		}
		
		if( $D->can_be_saved && !$this->user->is_logged ) {
			$D->can_be_saved	= FALSE;
		}
	}
	
	
	$this->load_template('search.php');
	
?>