<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/group.php');
	
	require_once( $C->INCPATH.'helpers/func_images.php' );
	
	$g	= $this->network->get_group_by_id(intval($this->params->group));
	if( ! $g ) {
		$this->redirect('dashboard');
	}
	if( $g->is_private && !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $g->is_private && !$this->user->info->is_network_admin ) {
		$u	= $this->network->get_group_invited_members($g->id);
		if( !$u || !in_array(intval($this->user->id),$u) ) {
			$this->redirect('dashboard');
		}
	}
	
	$D->page_title	= $g->title.' - '.$C->SITE_TITLE;
	$D->page_favicon	= $C->IMG_URL.'avatars/thumbs2/'.$g->avatar;
	
	$D->g	= & $g;
	$D->i_am_member	= $this->user->if_follow_group($g->id);
	$D->i_am_admin	= FALSE;
	if( $D->i_am_member ) {
		$D->i_am_admin	= $db->fetch('SELECT id FROM groups_admins WHERE group_id="'.$g->id.'" AND user_id="'.$this->user->id.'" LIMIT 1') ? TRUE : FALSE;
	}
	if( !$D->i_am_admin && $this->user->is_logged && $this->user->info->is_network_admin==1 ) {
		$D->i_am_admin	= TRUE;
	}
	
	$D->i_can_invite	= $D->i_am_admin || ($D->i_am_member && $g->is_public);
	$D->i_can_invite	= $this->user->is_logged && $D->i_can_invite;
	
	if( $D->i_can_invite ) {
		$tmp	= $this->network->get_user_follows($this->user->id);
		$tmp	= array_keys($tmp->followers);
		foreach($tmp as &$v) { $v = intval($v); }
		$tmp2	= array_keys($this->network->get_group_members($g->id));
		foreach($tmp2 as &$v) { $v = intval($v); }
		$tmp	= array_diff($tmp, $tmp2);
		$tmp	= array_diff($tmp, array(intval($this->user->id)));
		if( ! count($tmp) ) {
			$D->i_can_invite	= FALSE;
		}
	}
	
	if( $this->param('act')=='join' || $this->param('act')=='leave' ) {
		if( $this->param('act')=='join' && !$D->i_am_member ) {
			$this->user->follow_group($g->id, TRUE);
		}
		elseif( $this->param('act')=='leave' && $D->i_am_member ) {
			$this->user->follow_group($g->id, FALSE);
		}
		$tmp_url	= $C->SITE_URL.$g->groupname;
		if( $this->param('tab') ) {
			$tmp_url	.= '/tab:'.$this->param('tab');
		}
		if( $this->param('subtab') ) {
			$tmp_url	.= '/subtab:'.$this->param('subtab');
		}
		if( $this->param('filter') ) {
			$tmp_url	.= '/filter:'.$this->param('filter');
		}
		if( $this->param('pg') ) {
			$tmp_url	.= '/pg:'.$this->param('pg');
		}
		$tmp_url	.= '/msg:'.$this->param('act');
		$this->redirect($tmp_url);
	}
	
	if( $D->i_am_member ) {
		$D->rss_feeds	= array(
			array( $C->SITE_URL.'rss/groupname:'.$g->groupname,	$this->lang('rss_grpposts',array('#GROUP#'=>$g->title)), ),
		);
	}
	
	$tabs	= array('updates', 'members');
	if( $D->i_am_admin ) { $tabs[] = 'settings'; }
	$D->tab	= 'updates';
	if( $this->param('tab') && in_array($this->param('tab'), $tabs) ) {
		$D->tab	= $this->param('tab');
	}
	$D->subtab	= '';
	$subtabs	= array();
	if( $D->tab == 'settings' ) {
		$subtabs = array('main', 'admins', 'rssfeeds');
		if( $g->is_private ) {
			$subtabs[]	= 'privmembers';
		}
		$subtabs[]	= 'delgroup';
		$D->subtab	= 'main';
	}
	if( $this->param('subtab') && in_array($this->param('subtab'), $subtabs) ) {
		$D->subtab	= $this->param('subtab');
	}
	
	$D->num_members	= 0;
	$D->some_members	= array();
	$tmp	= $this->network->get_group_members($g->id);
	$D->num_members	= count($tmp);
	if( $this->user->is_logged ) {
		$D->some_members	= array_intersect(array_keys($tmp), array_keys($this->network->get_user_follows($this->user->id)->follow_users));
	}
	if( count($D->some_members)<6 ) {
		foreach($D->some_members as $v) {
			unset($tmp[$v]);
		}
		$tmp	= array_keys($tmp);
		shuffle($tmp);
		$tmp	= array_slice($tmp, 0, 6-count($D->some_members));
		foreach($tmp as $k) {
			$D->some_members[]	= $k;
		}
	}
	shuffle($D->some_members);
	foreach($D->some_members as $k=>$v) {
		$D->some_members[$k]	= $this->network->get_user_by_id($v);
		if( ! $D->some_members[$k] ) { unset($D->some_members[$k]); }
	}
	
	$D->num_admins	= 0;
	$D->some_admins	= array();
	$tmp	= array();
	$db2->query('SELECT user_id FROM groups_admins WHERE group_id="'.$g->id.'" ');
	while($i = $db2->fetch_object()) {
		$tmp[intval($i->user_id)]	= 1;
	}
	$D->num_admins	= count($tmp);
	if( $this->user->is_logged ) {
		$D->some_admins	= array_intersect(array_keys($tmp), array_keys($this->network->get_user_follows($this->user->id)->follow_users));
	}
	if( count($D->some_admins)<3 ) {
		foreach($D->some_admins as $v) {
			unset($tmp[$v]);
		}
		$tmp	= array_keys($tmp);
		shuffle($tmp);
		$tmp	= array_slice($tmp, 0, 3-count($D->some_admins));
		foreach($tmp as $k) {
			$D->some_admins[]	= $k;
		}
	}
	shuffle($D->some_admins);
	foreach($D->some_admins as $k=>$v) {
		$D->some_admins[$k]	= $this->network->get_user_by_id($v);
		if( ! $D->some_admins[$k] ) { unset($D->some_admins[$k]); }
	}
	
	$D->post_tags	= $this->network->get_recent_posttags('AND group_id="'.$g->id.'"', 10);
	$D->about_me	= nl2br(htmlspecialchars($D->g->about_me));
	if( FALSE!==strpos($D->about_me,'http://') || FALSE!==strpos($D->about_me,'http://') || FALSE!==strpos($D->about_me,'ftp://') ) {
		$D->about_me	= preg_replace('#(^|\s)((http|https|ftp)://\w+[^\s\[\]]+)#ie', 'post::_postparse_build_link("\\2", "\\1")', $D->about_me);
	}
	
	if( $D->tab == 'updates' )
	{
		$D->posts_html	= '';
		$D->filter	= 'all';
		$tmp	= array('all', 'videos', 'images', 'links', 'files');
		if( $this->param('filter') && in_array($this->param('filter'), $tmp) ) {
			$D->filter	= $this->param('filter');
		}
		$tmp	= array('videos'=>'videoembed', 'images'=>'image', 'links'=>'link', 'files'=>'file');
		if($D->filter == 'all') {
			$q1	= 'SELECT COUNT(id) FROM posts WHERE group_id="'.$g->id.'"';
			$q2	= 'SELECT *, "public" AS `type` FROM posts WHERE group_id="'.$g->id.'" ORDER BY id DESC ';
		}
		else {
			$q1	= 'SELECT COUNT(p.id) FROM posts p, posts_attachments a WHERE p.id=a.post_id AND p.group_id="'.$g->id.'" AND a.type="'.$tmp[$D->filter].'" ';
			$q2	= 'SELECT p.*, "public" AS `type` FROM posts p, posts_attachments a WHERE p.id=a.post_id AND p.group_id="'.$g->id.'" AND a.type="'.$tmp[$D->filter].'" ORDER BY p.id DESC ';
		}
		$D->num_results	= $db2->fetch_field($q1);
		if( 0 == $D->num_results ) {
			$arr	= array('#GROUP#'=>htmlspecialchars($g->title), '#SITE_TITLE#'=>htmlspecialchars($C->OUTSIDE_SITE_TITLE), '#A1#'=>'<a href="javascript:;" onclick="postform_open(({groupname:\''.htmlspecialchars($g->title).'\'}));">', '#A2#'=>'</a>', );
			$lngkey_ttl	= 'noposts_group_ttl';
			$lngkey_txt	= 'noposts_group_txt';		
			if( $D->filter != 'all' ) {
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
			$D->paging_url	= $C->SITE_URL.$g->groupname.'/filter:'.$D->filter.'/pg:';
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
		$D->group_posts_title	= $this->lang('group_title_updates', array('#GROUP#'=>htmlspecialchars($g->title)));
	}
	elseif( $D->tab == 'members' )
	{
		$D->page_title	= $this->lang('group_pagetitle_members', array('#GROUP#'=>htmlspecialchars($g->title), '#SITE_TITLE#'=>$C->SITE_TITLE));
		$filters	= array('all', 'admins');
		$D->filter	= 'all';
		if( $this->param('filter') && in_array($this->param('filter'), $filters) ) {
			$D->filter	= $this->param('filter');
		}
		$tmp	= array();
		if( $D->filter == 'all' ) {
			$tmp	= array_keys($this->network->get_group_members($g->id));
		}
		elseif( $D->filter == 'admins' ) {
			$db2->query('SELECT user_id FROM groups_admins WHERE group_id="'.$g->id.'" ORDER BY id ASC');
			while($o = $db2->fetch_object()) {
				$tmp[]	= intval($o->user_id);
			}
		}
		$D->num_results	= count($tmp);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$tmp	= array_slice($tmp, $from, $C->PAGING_NUM_USERS, TRUE);
		$usrs	= array();
		foreach($tmp as $sdf) {
			if($sdf = $this->network->get_user_by_id($sdf)) {
				$usrs[]	= $sdf;
			}
		}
		$D->users_html	= '';
		ob_start();
		foreach($usrs as $tmp) {
			$D->u	= $tmp;
			$this->load_template('single_user.php');
		}
		$D->paging_url	= $C->SITE_URL.$g->groupname.'/tab:members/filter:'.$D->filter.'/pg:';
		if( $D->num_pages > 1 ) {
			$this->load_template('paging_users.php');
		}
		$D->users_html	= ob_get_contents();
		ob_end_clean();
		unset($tmp, $sdf, $usrs, $D->u);
	}
	elseif( $D->tab == 'settings' && $D->i_am_admin )
	{
		$D->num_rssfeeds	= $db->fetch_field('SELECT COUNT(id) FROM groups_rssfeeds WHERE is_deleted=0 AND group_id="'.$g->id.'" ');
		if( $D->subtab == 'main' ) {
			$D->page_title	= $this->lang('group_pagetitle_settings', array('#GROUP#'=>htmlspecialchars($g->title), '#SITE_TITLE#'=>$C->SITE_TITLE));
			$D->submit	= FALSE;
			$D->error	= FALSE;
			$D->errmsg	= '';
			$D->form_title		= $g->title;
			$D->form_groupname	= $g->groupname;
			$D->form_description	= $g->about_me;
			$D->form_type		= $g->is_private ? 'private' : 'public';
			if( isset($_POST['sbm']) ) {
				$D->submit	= TRUE;
				$D->form_title		= trim($_POST['form_title']);
				$D->form_groupname	= trim($_POST['form_groupname']);
				$D->form_description	= trim($_POST['form_description']);
				$D->form_type		= trim($_POST['form_type']);
				if( isset($_FILES['form_avatar']) && is_uploaded_file($_FILES['form_avatar']['tmp_name']) ) {
					$f	= (object) $_FILES['form_avatar'];
					list($w, $h, $tp) = getimagesize($f->tmp_name);
					if( $w==0 || $h==0 ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_setterr_avatar_invalidfile';
					}
					elseif( $tp!=IMAGETYPE_GIF && $tp!=IMAGETYPE_JPEG && $tp!=IMAGETYPE_PNG ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_setterr_avatar_invalidformat';
					}
					elseif( $w<$C->AVATAR_SIZE || $h<$C->AVATAR_SIZE ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_setterr_avatar_toosmall';
					}
					else {
						$fn	= time().rand(100000,999999).'.png';
						$res	= copy_avatar($f->tmp_name, $fn);
						if( ! $res) {
							$D->error	= TRUE;
							$D->errmsg	= 'group_setterr_avatar_cantcopy';
						}
						else {
							$old	= $g->avatar;
							if( $old != $C->DEF_AVATAR_GROUP ) {
								rm( $C->IMG_DIR.'avatars/'.$old );
								rm( $C->IMG_DIR.'avatars/thumbs1/'.$old );
								rm( $C->IMG_DIR.'avatars/thumbs2/'.$old );
								rm( $C->IMG_DIR.'avatars/thumbs3/'.$old );
							}
							$db2->query('UPDATE groups SET avatar="'.$db2->escape($fn).'" WHERE id="'.$g->id.'" LIMIT 1');
							$D->page_favicon	= $C->IMG_URL.'avatars/thumbs2/'.$fn;
						}
					}
				}
				if( $D->form_type=='public' && $g->is_private ) {
					$db2->query('UPDATE groups SET is_public=1 WHERE id="'.$g->id.'" LIMIT 1');
				}
				elseif( $D->form_type=='private' && $g->is_public ) {
					$db2->query('UPDATE groups SET is_public=0 WHERE id="'.$g->id.'" LIMIT 1');
					$tmp1	= array_keys($this->network->get_group_members($g->id));
					$tmp2	= $this->network->get_group_invited_members($g->id);
					$tmp	= array_diff($tmp1, $tmp2);
					foreach($tmp as $uid) {
						$db2->query('INSERT INTO groups_private_members SET group_id="'.$g->id.'", user_id="'.$uid.'", invited_by="'.$this->user->id.'", invited_date="'.time().'" ');
					}
					$tmp	= $this->network->get_group_invited_members($g->id, TRUE);
					unset($tmp, $tmp1, $tmp2);
				}
				$D->form_description	= mb_substr($D->form_description, 0, $C->POST_MAX_SYMBOLS);
				$db2->query('UPDATE groups SET about_me="'.$db2->e($D->form_description).'" WHERE id="'.$g->id.'" LIMIT 1');
				if( mb_strlen($D->form_title)<3 || mb_strlen($D->form_title)>30 ) {
					$D->error	= TRUE;
					$D->errmsg	= 'group_setterr_title_length';
				}
				elseif( preg_match('/[^ا-یא-תÀ-ÿ一-龥а-яa-z0-9\-\.\s]/iu', $D->form_title) ) {
					$D->error	= TRUE;
					$D->errmsg	= 'group_setterr_title_chars';
				}
				elseif( $D->form_title != $g->title ) {
					$db2->query('SELECT id FROM groups WHERE (groupname="'.$db2->e($D->form_title).'" OR title="'.$db2->e($D->form_title).'") AND id<>"'.$g->id.'" LIMIT 1');
					if( $db2->num_rows() > 0 ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_setterr_title_exists';
					}
					else {
						$db2->query('UPDATE groups SET title="'.$db2->e($D->form_title).'" WHERE id="'.$g->id.'" LIMIT 1');
						$D->page_title	= $D->form_title.' - '.$C->SITE_TITLE;
						$this->user->get_top_groups(1, TRUE);
					}
				}
				if( mb_strlen($D->form_groupname)<3 || mb_strlen($D->form_groupname)>30 ) {
					$D->error	= TRUE;
					$D->errmsg	= 'group_setterr_name_length';
				}
				elseif( ! preg_match('/^[a-z0-9\-\_]{3,30}$/iu', $D->form_groupname) ) {
					$D->error	= TRUE;
					$D->errmsg	= 'group_setterr_name_chars';
				}
				elseif( $D->form_groupname != $g->groupname ) {
					$db2->query('SELECT id FROM groups WHERE (groupname="'.$db2->e($D->form_groupname).'" OR title="'.$db2->e($D->form_groupname).'") AND id<>"'.$g->id.'" LIMIT 1');
					if( $db2->num_rows() > 0 ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_setterr_name_exists';
					}
					else {
						$db2->query('SELECT id FROM users WHERE username="'.$db2->e($D->form_groupname).'" LIMIT 1');
						if( $db2->num_rows() > 0 ) {
							$D->error	= TRUE;
							$D->errmsg	= 'group_setterr_name_existsu';
						}
						elseif( file_exists($C->INCPATH.'controllers/'.$D->form_groupname.'.php') ) {
							$D->error	= TRUE;
							$D->errmsg	= 'group_setterr_name_existss';
						}
						else {
							$db2->query('UPDATE groups SET groupname="'.$db2->e($D->form_groupname).'" WHERE id="'.$g->id.'" LIMIT 1');
							$this->network->get_group_by_name($g->groupname, TRUE);
							$this->network->get_group_by_name($D->form_groupname, TRUE);
							$this->user->get_top_groups(1, TRUE);
						}
					}
				}
				$g	= $this->network->get_group_by_id($g->id, TRUE);
			}
		}
		elseif( $D->subtab == 'admins' ) {
			$D->page_title	= $this->lang('group_pagetitle_settings_admins', array('#GROUP#'=>htmlspecialchars($g->title), '#SITE_TITLE#'=>$C->SITE_TITLE));
			$D->admins		= array();
			$D->jsmembers	= array();
			$r	= $db2->query('SELECT user_id FROM groups_admins WHERE group_id="'.$g->id.'" ');
			while($tmp = $db2->fetch_object($r)) {
				$tmp	= $this->network->get_user_by_id($tmp->user_id);
				if( $tmp ) {
					$D->admins[$tmp->id]	= $tmp;
				}
			}
			foreach($this->network->get_group_members($g->id) as $k=>$v) {
				$tmp	= $this->network->get_user_by_id($k);
				if( $tmp ) {
					$D->jsmembers[]	= $tmp->username;
				}
			}
			if( isset($_POST['admins']) ) {
				$admins	= trim($_POST['admins']);
				$admins	= trim($admins, ',');
				$admins	= trim($admins);
				$admins	= explode(',', $admins);
				$members	= $this->network->get_group_members($g->id);
				$ids	= array();
				if( isset($D->admins[$this->user->id]) || !$this->user->info->is_network_admin ) {
					$ids[]	= intval($this->user->id);
				}
				foreach($admins as $a) {
					$a	= trim($a);
					if( empty($a) ) { continue; }
					$a	= $this->network->get_user_by_username($a);
					if( ! $a ) { continue; }
					if( ! isset($members[$a->id]) ) { continue; }
					$ids[]	= intval($a->id);
				}
				$ids	= array_unique($ids);
				$this->db2->query('DELETE FROM groups_admins WHERE group_id="'.$g->id.'" ');
				foreach($ids as $a) {
					$this->db2->query('INSERT INTO groups_admins SET group_id="'.$g->id.'", user_id="'.$a.'" ');
				}
				$this->redirect( $C->SITE_URL.$D->g->groupname.'/tab:settings/subtab:admins/msg:admsaved' );
			}
		}
		elseif( $D->subtab == 'privmembers' && $g->is_private ) {
			$D->page_title	= $this->lang('group_pagetitle_settings_privmembers', array('#GROUP#'=>htmlspecialchars($g->title), '#SITE_TITLE#'=>$C->SITE_TITLE));
			$D->cannot_be_removed	= array();
			$D->can_be_removed	= array();
			$r	= $db2->query('SELECT user_id FROM groups_admins WHERE group_id="'.$g->id.'" ');
			while($tmp = $db2->fetch_object($r)) {
				$tmp	= $this->network->get_user_by_id($tmp->user_id);
				if( $tmp ) {
					$tmp->id	= intval($tmp->id);
					$D->cannot_be_removed[$tmp->id]	= $tmp;
				}
			}
			$r	= $db2->query('SELECT user_id FROM groups_private_members WHERE group_id="'.$g->id.'" ');
			while($tmp = $db2->fetch_object($r)) {
				$tmp	= $this->network->get_user_by_id($tmp->user_id);
				if( $tmp ) {
					$tmp->id	= intval($tmp->id);
					if( isset($D->cannot_be_removed[$tmp->id]) ) {
						continue;
					}
					$D->can_be_removed[$tmp->id]	= $tmp;
				}
			}
			arsort($D->cannot_be_removed);
			arsort($D->can_be_removed);
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
					$a	= intval($a->id);
					if( isset($D->can_be_removed[$a]) ) {
						$ids[$a]	= 'keep';
					}
				}
				$remove	= array();
				foreach($D->can_be_removed as $u) {
					if( ! isset($ids[$u->id]) ) {
						$remove[]	= $u->id;
					}
				}
				foreach($remove as $u) {
					$db2->query('DELETE FROM groups_private_members WHERE group_id="'.$g->id.'" AND user_id="'.$u.'" ');
					$tmp	= $this->network->get_group_members($g->id);
					if( isset($tmp[$u]) ) {
						$this->db2->query('DELETE FROM groups_followed WHERE user_id="'.$u.'" AND group_id="'.$g->id.'" ');
						$this->db2->query('UPDATE groups SET num_followers=num_followers-1 WHERE id="'.$g->id.'" LIMIT 1');
						$this->db2->query('DELETE FROM post_userbox WHERE user_id="'.$u.'" AND post_id IN(SELECT id FROM posts WHERE group_id="'.$g->id.'" )');
						$this->db2->query('DELETE FROM post_userbox_feeds WHERE user_id="'.$u.'" AND post_id IN(SELECT id FROM posts WHERE group_id="'.$g->id.'" )');
					}
					$nothing	= $this->network->get_user_follows($u, TRUE);
					$nothing	= $this->network->get_user_by_id($u, TRUE);
				}
				$nothing	= $this->network->get_group_invited_members($g->id, TRUE);
				$nothing	= $this->network->get_group_members($g->id, TRUE);
				$nothing	= $this->network->get_group_by_id($g->id, TRUE);
				$this->redirect( $C->SITE_URL.$D->g->groupname.'/tab:settings/subtab:privmembers/msg:mmbsaved' );
			}
		}
		elseif( $D->subtab == 'rssfeeds' ) {
			$D->page_title	= $this->lang('group_pagetitle_settings_rssfeeds', array('#GROUP#'=>htmlspecialchars($g->title), '#SITE_TITLE#'=>$C->SITE_TITLE));
			$D->submit	= FALSE;
			$D->error	= FALSE;
			$D->errmsg	= '';
			$D->newfeed_url		= '';
			$D->newfeed_filter	= '';
			$D->newfeed_auth_req	= FALSE;
			$D->newfeed_auth_msg	= FALSE;
			$D->newfeed_username	= '';
			$D->newfeed_password	= '';
			if( isset($_POST['sbm']) ) {
				$D->submit	= TRUE;
				$D->newfeed_url		= trim($_POST['newfeed_url']);
				$D->newfeed_filter	= trim( mb_strtolower($_POST['newfeed_filter']) );
				$D->newfeed_filter	= preg_replace('/[^\,ا-یא-תÀ-ÿ一-龥а-яa-z0-9-\_\.\#\s]/iu', '', $D->newfeed_filter);
				$D->newfeed_filter	= preg_replace('/\s+/ius', ' ', $D->newfeed_filter);
				$D->newfeed_filter	= preg_replace('/(\s)*(\,)+(\s)*/iu', ',', $D->newfeed_filter);
				$D->newfeed_filter	= trim( trim($D->newfeed_filter, ',') );
				$D->newfeed_filter	= str_replace(',', ', ', $D->newfeed_filter);
				$D->newfeed_username	= isset($_POST['newfeed_username']) ? trim($_POST['newfeed_username']) : '';
				$D->newfeed_password	= isset($_POST['newfeed_password']) ? trim($_POST['newfeed_password']) : '';
				if( empty($D->newfeed_url) ) {
					$D->error	= TRUE;
					$D->errmsg	= 'group_feedsett_err_feed';
				}
				$f	= '';
				if( !$D->error ) {
					$f	= new rssfeed($D->newfeed_url);
					$auth	= $f->check_if_requires_auth();
					if( $f->error ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_feedsett_err_feed';
					}
					elseif( $auth ) {
						$D->newfeed_auth_req	= TRUE;
					}
					else {
						$f->read();
						if( $f->error ) {
							$D->error	= TRUE;
							$D->errmsg	= 'group_feedsett_err_feed';
						}
					}
				}
				if( !$D->error && $D->newfeed_auth_req && !empty($D->newfeed_username) && !empty($D->newfeed_password) ) {
					$f->set_userpwd($D->newfeed_username.':'.$D->newfeed_password);
					$auth	= $f->check_if_requires_auth();
					if( $f->error || $auth ) {
						$D->error	= TRUE;
						$D->errmsg	= 'group_feedsett_err_auth';
					}
					else {
						$f->read();
						if( $f->error ) {
							$D->error	= TRUE;
							$D->errmsg	= 'group_feedsett_err_feed';
						}
					}
				}
				if( !$D->error && $f->is_read ) {
					$f->fetch();
					$lastdate	= $f->get_lastitem_date();
					if( ! $lastdate ) {
						$lastdate	= time();
					}
					$title	= $f->title;
					if( empty($title) ) {
						$title	= preg_replace('/^(http|https|ftp)\:\/\//iu', '', $D->newfeed_url);
					}
					$title	= $this->db2->e($title);
					$usrpwd	= $D->newfeed_auth_req ? ($D->newfeed_username.':'.$D->newfeed_password) : '';
					$usrpwd	= $this->db2->e($usrpwd);
					$keywords	= str_replace(', ', ',', $D->newfeed_filter);
					$keywords	= $this->db2->e($keywords);
					$this->db2->query('SELECT id FROM groups_rssfeeds WHERE is_deleted=0 AND group_id="'.$g->id.'" AND feed_url="'.$this->db2->e($D->newfeed_url).'" AND feed_userpwd="'.$usrpwd.'" AND filter_keywords="'.$keywords.'" LIMIT 1');
					if( 0 == $this->db2->num_rows() ) {
						$this->db2->query('INSERT INTO groups_rssfeeds SET is_deleted=0, group_id="'.$g->id.'", feed_url="'.$this->db2->e($D->newfeed_url).'", feed_title="'.$title.'", feed_userpwd="'.$usrpwd.'", filter_keywords="'.$keywords.'", date_added="'.time().'", date_last_post=0, date_last_crawl="'.time().'", date_last_item="'.$lastdate.'", added_by_user="'.$this->user->id.'" ');
					}
					$this->redirect($C->SITE_URL.$g->groupname.'/tab:settings/subtab:rssfeeds/msg:added');
				}
				if( !$D->error && $D->newfeed_auth_req && (empty($D->newfeed_username) || empty($D->newfeed_password)) ) {
					$D->newfeed_auth_msg	= TRUE;
				}
			}
			$D->feeds	= array();
			$this->db2->query('SELECT id, feed_url, feed_title, filter_keywords FROM groups_rssfeeds WHERE is_deleted=0 AND group_id="'.$g->id.'" ORDER BY id ASC');
			while($obj = $this->db2->fetch_object()) {
				$obj->feed_url		= stripslashes($obj->feed_url);
				$obj->feed_title		= stripslashes($obj->feed_title);
				$obj->filter_keywords	= stripslashes($obj->filter_keywords);
				$obj->filter_keywords	= str_replace(',', ', ', $obj->filter_keywords);
				$D->feeds[$obj->id]	= $obj;
			}
			if( $this->param('delfeed') && isset($D->feeds[$this->param('delfeed')]) ) {
				$this->db2->query('UPDATE groups_rssfeeds SET is_deleted=1 WHERE id="'.intval($this->param('delfeed')).'" AND is_deleted=0 AND group_id="'.$g->id.'" LIMIT 1');
				$this->redirect($C->SITE_URL.$g->groupname.'/tab:settings/subtab:rssfeeds/msg:deleted');
			}
		}
		elseif( $D->subtab == 'delgroup' ) {
			$D->page_title	= $this->lang('group_pagetitle_settings_delgroup', array('#GROUP#'=>htmlspecialchars($g->title), '#SITE_TITLE#'=>$C->SITE_TITLE));
			$D->submit	= FALSE;
			$D->error	= FALSE;
			$D->errmsg	= '';
			$D->f_postsact	= '';
			if( isset($_POST['sbm']) ) {
				$D->submit	= TRUE;
				$D->f_postsact	= isset($_POST['postsact']) ? trim($_POST['postsact']) : '';
				$D->password	= trim($_POST['password']);
				if( $g->is_private ) {
					$D->f_postsact	= 'del';
				}
				if( $D->f_postsact!='keep' && $D->f_postsact!='del' ) {
					$D->f_postsact	= '';
					$D->error		= TRUE;
					$D->errmsg		= 'group_del_f_err_posts';
				}
				if( !$D->error && md5($D->password)!=$this->user->info->password ) {
					$D->error		= TRUE;
					$D->errmsg		= 'group_del_f_err_passwd';
				}
				if( !$D->error ) {
					ini_set('max_execution_time', 10*60*60);
					if( $D->f_postsact == 'del' ) {
						$r	= $db2->query('SELECT * FROM posts WHERE group_id="'.$g->id.'" ORDER BY id ASC');
						while($obj = $db2->fetch_object($r)) {
							$p	= new post('public', FALSE, $obj);
							if( $p->error ) { continue; }
							$p->delete_this_post();
						}
						$r	= $db2->query('SELECT id FROM groups_rssfeeds WHERE group_id="'.$g->id.'" ');
						while($obj = $db2->fetch_object($r)) {
							$db2->query('DELETE FROM groups_rssfeeds_posts WHERE rssfeed_id="'.$obj->id.'" ');
						}
						$db2->query('DELETE FROM groups_rssfeeds WHERE group_id="'.$g->id.'" ');
					}
					$r	= $db2->query('SELECT * FROM posts WHERE user_id="0" AND group_id="'.$g->id.'" ORDER BY id ASC');
					while($obj = $db2->fetch_object($r)) {
						$p	= new post('public', FALSE, $obj);
						if( $p->error ) { continue; }
						$p->delete_this_post();
					}
					$f	= array_keys($this->network->get_group_members($g->id));
					$db2->query('DELETE FROM groups_followed WHERE group_id="'.$g->id.'" ');
					$db2->query('DELETE FROM groups_private_members WHERE group_id="'.$g->id.'" ');
					$db2->query('DELETE FROM groups_admins WHERE group_id="'.$g->id.'" ');
					$db2->query('UPDATE groups_rssfeeds SET is_deleted=1 WHERE group_id="'.$g->id.'" ');
					foreach($f as $uid) {
						$this->network->get_user_follows($uid, TRUE);
					}
					$db2->query('INSERT INTO groups_deleted (id, groupname, title, is_public) SELECT id, groupname, title, is_public FROM groups WHERE id="'.$g->id.'" LIMIT 1');
					$db2->query('DELETE FROM groups WHERE id="'.$g->id.'" LIMIT 1');
					$this->network->get_group_by_id($g->id, TRUE);
					$av	= $g->avatar;
					if( $av != $C->DEF_AVATAR_GROUP ) {
						rm( $C->IMG_DIR.'avatars/'.$av );
						rm( $C->IMG_DIR.'avatars/thumbs1/'.$av );
						rm( $C->IMG_DIR.'avatars/thumbs2/'.$av );
						rm( $C->IMG_DIR.'avatars/thumbs3/'.$av );
					}
					$this->redirect( $C->SITE_URL.'groups/msg:deleted' );
				}
			}
		}
	}
	
	$this->load_template('group.php');
	
?>