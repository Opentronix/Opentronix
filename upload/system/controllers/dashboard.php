<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('home');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/dashboard.php');
	
	$D->page_title	= $this->lang('dashboard_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	if( $this->param('from')=='ajax' && isset($_POST['toggle_whattodo']) ) {
		$tmp	= intval($_POST['toggle_whattodo'])==0 ? 1 : 0;
		$this->db2->query('UPDATE users SET dbrd_whattodo_closed="'.$tmp.'" WHERE id="'.$this->user->id.'" LIMIT 1');
		$this->user->info->dbrd_whattodo_closed	= $tmp;
		$this->network->get_user_by_id($this->user->id, TRUE);
		echo 'OK';
		exit;
	}
	if( $this->param('from')=='ajax' && isset($_POST['toggle_grpmenu']) ) {
		$tmp	= intval($_POST['toggle_grpmenu'])==0 ? 1 : 0;
		$this->db2->query('UPDATE users SET dbrd_groups_closed="'.$tmp.'" WHERE id="'.$this->user->id.'" LIMIT 1');
		$this->user->info->dbrd_groups_closed	= $tmp;
		$this->network->get_user_by_id($this->user->id, TRUE);
		echo 'OK';
		exit;
	}
	if( $this->param('from')=='ajax' && isset($_POST['hide_wrongaddr_warning']) ) {
		setcookie('wrongaddr_warning_'.md5($C->DOMAIN), 'off', time()+7*24*60*60, '/', cookie_domain());
		echo 'OK';
		exit;
	}
	$D->groupsmenu_active	= $this->user->info->dbrd_groups_closed==1 ? FALSE : TRUE;
	
	$D->rss_feeds	= array(
		array( $C->SITE_URL.'rss/my:dashboard',	$this->lang('rss_mydashboard',array('#USERNAME#'=>$this->user->info->username)), ),
		array( $C->SITE_URL.'rss/my:posts',		$this->lang('rss_myposts',array('#USERNAME#'=>$this->user->info->username)), ),
		array( $C->SITE_URL.'rss/my:private',	$this->lang('rss_myprivate',array('#USERNAME#'=>$this->user->info->username)), ),
		array( $C->SITE_URL.'rss/my:mentions',	$this->lang('rss_mymentions',array('#USERNAME#'=>$this->user->info->username)), ),
		array( $C->SITE_URL.'rss/my:bookmarks',	$this->lang('rss_mybookmarks',array('#USERNAME#'=>$this->user->info->username)), ),
		array( $C->SITE_URL.'rss/all:posts',	$this->lang('rss_everybody',array('#SITE_TITLE#'=>$C->SITE_TITLE)), ),
	);
	
	$tabs		= array('all', '@me', 'private', 'commented', 'bookmarks', 'everybody', 'group');
	$filters	= array('all', 'videos', 'images', 'links', 'files');
	$at_tmp	= array('videos'=>'videoembed', 'images'=>'image', 'links'=>'link', 'files'=>'file');
	
	$D->show_feeds_tab	= $db2->fetch_field('SELECT post_id FROM post_userbox_feeds WHERE user_id="'.$this->user->id.'" LIMIT 1') ? TRUE : FALSE;
	if($D->show_feeds_tab) {
		$tabs[]	= 'feeds';
	}
	
	$tab	= 'all';
	if( $this->param('tab') && in_array($this->param('tab'), $tabs) ) {
		$tab	= $this->param('tab');
	}
	$filter	= 'all';
	if( $this->param('filter') && in_array($this->param('filter'), $filters) ) {
		$filter	= $this->param('filter');
	}
	
	$privtabs	= array('all', 'inbox', 'sent', 'usr');
	$privtab	= 'all';
	$privusr	= FALSE;
	if($tab=='private' && $this->param('privtab') && in_array($this->param('privtab'),$privtabs)) {
		$privtab	= $this->param('privtab');
	}
	if($tab=='private' && $privtab=='usr' && $this->param('usr') && $this->param('usr')!=$this->user->info->username) {
		$privusr	= $this->network->get_user_by_username($this->param('usr'));
	}
	
	$onlygroup	= FALSE;
	if($tab=='group' && $this->param('g')) {
		$onlygroup	= $this->network->get_group_by_name($this->param('g'));
		if( ! $onlygroup ) {
			$tab	= 'all';
			$onlygroup	= FALSE;
		}
		elseif( ! isset( $this->network->get_user_follows($this->user->id)->follow_groups[$onlygroup->id] ) ) {
			$tab	= 'all';
			$onlygroup	= FALSE;
		}
	}
	
	$not_in_groups	= '';
	if( !$this->user->info->is_network_admin && ($tab == '@me' || $tab == 'everybody')) {
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
			$m	= $this->network->get_group_members($g->id);
			if( ! isset($m[$this->user->id]) ) {
				$not_in_groups[]	= $obj->id;
			}
		}
		$not_in_groups	= count($not_in_groups)>0 ? ('AND p.group_id NOT IN('.implode(', ', $not_in_groups).')') : '';
	}
	
	$q1	= '';
	$q2	= '';
	switch($tab)
	{
		case 'all':
			if($filter == 'all') {
				$q1	= 'SELECT COUNT(post_id) FROM post_userbox WHERE user_id="'.$this->user->id.'" ';
				$q2	= 'SELECT p.*, "public" AS `type` FROM post_userbox b LEFT JOIN posts p ON p.id=b.post_id WHERE b.user_id="'.$this->user->id.'" ORDER BY p.id DESC ';
			}
			else {
				$q1	= 'SELECT COUNT(b.post_id) FROM post_userbox b, posts_attachments a FORCE INDEX (post_type_IDX) WHERE b.post_id=a.post_id AND a.type="'.$at_tmp[$filter].'" AND b.user_id="'.$this->user->id.'" ';
				$q2	= 'SELECT p.*, "public" AS `type` FROM post_userbox b LEFT JOIN posts p ON p.id=b.post_id, posts_attachments a WHERE b.post_id=a.post_id AND a.type="'.$at_tmp[$filter].'" AND b.user_id="'.$this->user->id.'" ORDER BY p.id DESC ';
			}
			break;
			
		case 'feeds':
			if($filter == 'all') {
				$q1	= 'SELECT COUNT(post_id) FROM post_userbox_feeds WHERE user_id="'.$this->user->id.'" ';
				$q2	= 'SELECT p.*, "public" AS `type` FROM post_userbox_feeds b LEFT JOIN posts p ON p.id=b.post_id WHERE b.user_id="'.$this->user->id.'" ORDER BY p.id DESC ';
			}
			else {
				$q1	= 'SELECT COUNT(b.post_id) FROM post_userbox_feeds b, posts_attachments a FORCE INDEX (post_type_IDX) WHERE b.post_id=a.post_id AND a.type="'.$at_tmp[$filter].'" AND b.user_id="'.$this->user->id.'" ';
				$q2	= 'SELECT p.*, "public" AS `type` FROM post_userbox_feeds b LEFT JOIN posts p ON p.id=b.post_id, posts_attachments a WHERE b.post_id=a.post_id AND a.type="'.$at_tmp[$filter].'" AND b.user_id="'.$this->user->id.'" ORDER BY p.id DESC ';
			}
			break;
		
		case '@me':
			if($filter == 'all') {
				$q1	= 'SELECT COUNT(p.id) FROM posts p INNER JOIN (SELECT post_id FROM posts_mentioned WHERE user_id="'.$this->user->id.'" UNION SELECT p.post_id FROM posts_comments p, posts_comments_mentioned c WHERE c.comment_id = p.id AND c.user_id ="'.$this->user->id.'") x ON x.post_id=p.id '.$not_in_groups;
				$q2	= 'SELECT p.*, "public" AS `type` FROM posts p INNER JOIN (SELECT post_id FROM posts_mentioned WHERE user_id="'.$this->user->id.'" UNION SELECT p.post_id FROM posts_comments p, posts_comments_mentioned c WHERE c.comment_id = p.id AND c.user_id ="'.$this->user->id.'") x ON x.post_id=p.id '.$not_in_groups.' ORDER BY p.id DESC ';
			}
			else {
				$q1	= 'SELECT COUNT(p.id) FROM posts p JOIN posts_attachments a FORCE INDEX (post_type_IDX) ON p.id=a.post_id AND a.type="'.$at_tmp[$filter].'" INNER JOIN (SELECT post_id FROM posts_mentioned WHERE user_id="'.$this->user->id.'" UNION SELECT p.post_id FROM posts_comments p, posts_comments_mentioned c WHERE c.comment_id = p.id AND c.user_id ="'.$this->user->id.'") x ON x.post_id=p.id '.$not_in_groups.' ';
				$q2	= 'SELECT p.*, "public" AS `type` FROM posts p JOIN posts_attachments a ON p.id=a.post_id AND a.type="'.$at_tmp[$filter].'" INNER JOIN (SELECT post_id FROM posts_mentioned WHERE user_id="'.$this->user->id.'" UNION SELECT p.post_id FROM posts_comments p, posts_comments_mentioned c WHERE c.comment_id = p.id AND c.user_id ="'.$this->user->id.'") x ON x.post_id=p.id '.$not_in_groups.' ORDER BY p.id DESC ';
			}
			break;
		
		case 'commented':
			if($filter == 'all') {
				$q1	= '
					SELECT
					(SELECT COUNT(post_id) FROM posts_comments_watch WHERE user_id="'.$this->user->id.'" AND post_id IN(SELECT id FROM posts WHERE comments>0) )
					+
					(SELECT COUNT(post_id) FROM posts_pr_comments_watch WHERE user_id="'.$this->user->id.'" AND post_id IN(SELECT id FROM posts_pr WHERE comments>0) )';
				$q2	= '
					(SELECT p.id, p.api_id, p.user_id, p.group_id, "0" AS to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, "0" AS is_recp_del, "public" AS `type`, p.date_lastcomment AS cdt FROM posts_comments_watch w LEFT JOIN posts p ON p.id=w.post_id WHERE w.user_id="'.$this->user->id.'" AND p.comments>0)
					UNION
					(SELECT p.id, p.api_id, p.user_id, "0" AS group_id, p.to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, p.is_recp_del, "private" AS `type`, p.date_lastcomment AS cdt FROM posts_pr_comments_watch w LEFT JOIN posts_pr p ON p.id=w.post_id WHERE w.user_id="'.$this->user->id.'" AND p.comments>0 AND (p.user_id="'.$this->user->id.'" OR (p.to_user="'.$this->user->id.'" && p.is_recp_del=0) ) )
					ORDER BY cdt DESC ';
			}
			else {
				$q1	= '
					SELECT
					(SELECT COUNT(w.post_id) FROM posts_comments_watch w, posts_attachments a WHERE w.post_id=a.post_id AND w.user_id="'.$this->user->id.'" AND a.type="'.$at_tmp[$filter].'")
					+
					(SELECT COUNT(w.post_id) FROM posts_pr_comments_watch w, posts_pr_attachments a WHERE w.post_id=a.post_id AND w.user_id="'.$this->user->id.'" AND a.type="'.$at_tmp[$filter].'")';
				$q2	= '
					(SELECT p.id, p.api_id, p.user_id, p.group_id, "0" AS to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, "0" AS is_recp_del, "public" AS `type`, p.date_lastcomment AS cdt FROM posts_comments_watch w LEFT JOIN posts p ON p.id=w.post_id, posts_attachments a WHERE w.post_id=a.post_id AND w.user_id="'.$this->user->id.'" AND a.type="'.$at_tmp[$filter].'" AND p.comments>0)
					UNION
					(SELECT p.id, p.api_id, p.user_id, "0" AS group_id, p.to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, p.is_recp_del, "private" AS `type`, p.date_lastcomment AS cdt FROM posts_pr_comments_watch w LEFT JOIN posts_pr p ON p.id=w.post_id, posts_pr_attachments a WHERE w.post_id=a.post_id AND w.user_id="'.$this->user->id.'" AND a.type="'.$at_tmp[$filter].'" AND p.comments>0 AND (p.user_id="'.$this->user->id.'" OR (p.to_user="'.$this->user->id.'" && p.is_recp_del=0) ) )
					ORDER BY cdt DESC ';
			}
			break;
		
		case 'everybody':
			if($filter == 'all') {
				$q1	= 'SELECT COUNT(p.id) FROM posts p WHERE p.user_id<>0 AND p.api_id<>2 '.$not_in_groups;
				$q2	= 'SELECT p.*, "public" AS `type` FROM posts p WHERE p.user_id<>0 AND p.api_id<>2 '.$not_in_groups.' ORDER BY p.id DESC ';
			}
			else {
				$q1	= 'SELECT COUNT(p.id) FROM posts p, posts_attachments a WHERE p.id=a.post_id AND p.user_id<>0 AND p.api_id<>2 '.$not_in_groups.' AND a.type="'.$at_tmp[$filter].'" ';
				$q2	= 'SELECT p.*, "public" AS `type` FROM posts p, posts_attachments a WHERE p.id=a.post_id AND p.user_id<>0 AND p.api_id<>2 '.$not_in_groups.' AND a.type="'.$at_tmp[$filter].'" ORDER BY p.id DESC ';
			}
			break;
			
		case 'bookmarks':
			if($filter == 'all') {
				$q1	= 'SELECT COUNT(post_id) FROM post_favs WHERE user_id="'.$this->user->id.'"';
				$q2	= '
					(SELECT p.id, p.api_id, p.user_id, p.group_id, "0" AS to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, "0" AS is_recp_del, "public" AS `type`, f.id AS fid FROM post_favs f LEFT JOIN posts p ON p.id=f.post_id WHERE f.user_id="'.$this->user->id.'" AND f.post_type="public")
					UNION
					(SELECT p.id, p.api_id, p.user_id, "0" AS group_id, p.to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, p.is_recp_del, "private" AS `type`, f.id AS fid FROM post_favs f LEFT JOIN posts_pr p ON p.id=f.post_id WHERE f.user_id="'.$this->user->id.'" AND f.post_type="private")
					ORDER BY fid DESC ';
			}
			else {
				$q1	= '
					SELECT
					(SELECT COUNT(f.post_id) FROM post_favs f, posts_attachments a WHERE f.post_id=a.post_id AND f.user_id="'.$this->user->id.'" AND f.post_type="public" AND a.type="'.$at_tmp[$filter].'")
					+
					(SELECT COUNT(f.post_id) FROM post_favs f, posts_pr_attachments a WHERE f.post_id=a.post_id AND f.user_id="'.$this->user->id.'" AND f.post_type="private" AND a.type="'.$at_tmp[$filter].'")';
				$q2	= '
					(SELECT p.id, p.api_id, p.user_id, p.group_id, "0" AS to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, "0" AS is_recp_del, "public" AS `type`, f.id AS fid FROM post_favs f LEFT JOIN posts p ON p.id=f.post_id, posts_attachments a WHERE f.post_id=a.post_id AND f.user_id="'.$this->user->id.'" AND f.post_type="public" AND a.type="'.$at_tmp[$filter].'")
					UNION
					(SELECT p.id, p.api_id, p.user_id, "0" AS group_id, p.to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, p.is_recp_del, "private" AS `type`, f.id AS fid FROM post_favs f LEFT JOIN posts_pr p ON p.id=f.post_id, posts_pr_attachments a WHERE f.post_id=a.post_id AND f.user_id="'.$this->user->id.'" AND f.post_type="public" AND a.type="'.$at_tmp[$filter].'")
					ORDER BY fid DESC ';
			}
			break;
			
		case 'private':
			
			if($privtab == 'all') {
				if($filter == 'all') {
					$q1	= 'SELECT COUNT(id) FROM posts_pr WHERE (user_id="'.$this->user->id.'" OR (to_user="'.$this->user->id.'" AND is_recp_del=0))';
					$q2	= 'SELECT *, "private" AS `type` FROM posts_pr WHERE (user_id="'.$this->user->id.'" OR (to_user="'.$this->user->id.'" AND is_recp_del=0)) ORDER BY date_lastcomment DESC, id DESC ';
				}
				else {
					$q1	= 'SELECT COUNT(p.id) FROM posts_pr p, posts_pr_attachments a WHERE p.id=a.post_id AND (p.user_id="'.$this->user->id.'" OR (p.to_user="'.$this->user->id.'" AND p.is_recp_del=0)) AND a.type="'.$at_tmp[$filter].'"';
					$q2	= 'SELECT p.*, "private" AS `type` FROM posts_pr p, posts_pr_attachments a WHERE p.id=a.post_id AND (p.user_id="'.$this->user->id.'" OR (p.to_user="'.$this->user->id.'" AND p.is_recp_del=0)) AND a.type="'.$at_tmp[$filter].'" ORDER BY p.date_lastcomment DESC, p.id DESC ';
				}
			}
			if($privtab == 'inbox') {
				if($filter == 'all') {
					$q1	= 'SELECT COUNT(id) FROM posts_pr WHERE to_user="'.$this->user->id.'" AND is_recp_del=0';
					$q2	= 'SELECT *, "private" AS `type` FROM posts_pr WHERE to_user="'.$this->user->id.'" AND is_recp_del=0 ORDER BY date_lastcomment DESC, id DESC ';
				}
				else {
					$q1	= 'SELECT COUNT(p.id) FROM posts_pr p, posts_pr_attachments a WHERE p.id=a.post_id AND p.to_user="'.$this->user->id.'" AND p.is_recp_del=0 AND a.type="'.$at_tmp[$filter].'"';
					$q2	= 'SELECT p.*, "private" AS `type` FROM posts_pr p, posts_pr_attachments a WHERE p.id=a.post_id AND p.to_user="'.$this->user->id.'" AND p.is_recp_del=0 AND a.type="'.$at_tmp[$filter].'" ORDER BY p.date_lastcomment DESC, p.id DESC ';
				}
			}
			elseif($privtab == 'sent') {
				if($filter == 'all') {
					$q1	= 'SELECT COUNT(id) FROM posts_pr WHERE user_id="'.$this->user->id.'"';
					$q2	= 'SELECT *, "private" AS `type` FROM posts_pr WHERE user_id="'.$this->user->id.'" ORDER BY date_lastcomment DESC, id DESC ';
				}
				else {
					$q1	= 'SELECT COUNT(p.id) FROM posts_pr p, posts_pr_attachments a WHERE p.id=a.post_id AND p.user_id="'.$this->user->id.'" AND a.type="'.$at_tmp[$filter].'"';
					$q2	= 'SELECT p.*, "private" AS `type` FROM posts_pr p, posts_pr_attachments a WHERE p.id=a.post_id AND p.user_id="'.$this->user->id.'" AND a.type="'.$at_tmp[$filter].'" ORDER BY p.date_lastcomment DESC, p.id DESC ';
				}
			}
			elseif($privtab == 'usr' && $privusr) {
				if($filter == 'all') {
					$q1	= 'SELECT COUNT(id) FROM posts_pr WHERE ((user_id="'.$this->user->id.'" AND to_user="'.$privusr->id.'") OR (user_id="'.$privusr->id.'" AND to_user="'.$this->user->id.'" AND is_recp_del=0))';
					$q2	= 'SELECT *, "private" AS `type` FROM posts_pr WHERE ((user_id="'.$this->user->id.'" AND to_user="'.$privusr->id.'") OR (user_id="'.$privusr->id.'" AND to_user="'.$this->user->id.'" AND is_recp_del=0)) ORDER BY date_lastcomment DESC, id DESC ';
				}
				else {
					$q1	= 'SELECT COUNT(p.id) FROM posts_pr p, posts_pr_attachments a WHERE p.id=a.post_id AND ((p.user_id="'.$this->user->id.'" AND p.to_user="'.$privusr->id.'") OR (p.user_id="'.$privusr->id.'" AND p.to_user="'.$this->user->id.'" AND p.is_recp_del=0)) AND a.type="'.$at_tmp[$filter].'"';
					$q2	= 'SELECT p.*, "private" AS `type` FROM posts_pr p, posts_pr_attachments a WHERE p.id=a.post_id AND ((p.user_id="'.$this->user->id.'" AND p.to_user="'.$privusr->id.'") OR (p.user_id="'.$privusr->id.'" AND p.to_user="'.$this->user->id.'" AND p.is_recp_del=0)) AND a.type="'.$at_tmp[$filter].'" ORDER BY p.date_lastcomment DESC, p.id DESC ';
				}
			}
			break;
		
		case 'group':
			if($filter == 'all') {
				$q1	= 'SELECT COUNT(p.id) FROM posts p WHERE group_id="'.$onlygroup->id.'"';
				$q2	= 'SELECT p.*, "public" AS `type` FROM posts p WHERE group_id="'.$onlygroup->id.'" ORDER BY p.id DESC ';
			}
			else {
				$q1	= 'SELECT COUNT(p.id) FROM posts p, posts_attachments a WHERE p.id=a.post_id AND group_id="'.$onlygroup->id.'" AND a.type="'.$at_tmp[$filter].'" ';
				$q2	= 'SELECT p.*, "public" AS `type` FROM posts p, posts_attachments a WHERE p.id=a.post_id AND group_id="'.$onlygroup->id.'" AND a.type="'.$at_tmp[$filter].'" ORDER BY p.id DESC ';
			}
			break;
	}
	$D->tab		= $tab;
	$D->filter		= $filter;
	$D->privtab		= $privtab;
	$D->privusr		= $privusr;
	$D->onlygroup	= $onlygroup;
	$D->num_results	= 0;
	$D->num_pages	= 0;
	$D->pg		= 1;
	$D->posts_html	= '';
	
	if( $q1!='' && $q2!='' ) {
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
		if( $tab == 'private' ) {
			$D->paging_url	= $C->SITE_URL.'dashboard/tab:private/privtab:'.$privtab.'/filter:'.$filter.'/pg:';
		}
		elseif( $tab == 'group' ) {
			$D->paging_url	= $C->SITE_URL.'dashboard/tab:group/g:'.$onlygroup->groupname.'/filter:'.$filter.'/pg:';
		}
		else {
			$D->paging_url	= $C->SITE_URL.'dashboard/tab:'.$tab.'/filter:'.$filter.'/pg:';
		}
		if( $D->num_pages>1 && !$this->param('onlypost') ) {
			$this->load_template('paging_posts.php');
		}
		$D->posts_html	= ob_get_contents();
		ob_end_clean();
	}
	
	if( 0 == $D->num_results ) {
		if( ! ($tab=='private' && $privtab=='usr' && !$privusr) ) {
			$arr	= array('#USERNAME#'=>$this->user->info->username, '#SITE_TITLE#'=>htmlspecialchars($C->OUTSIDE_SITE_TITLE), '#A1#'=>'<a href="javascript:;" onclick="postform_open();">', '#A2#'=>'</a>', );
			$lngkey_ttl	= 'noposts_dtb_'.$tab.'_ttl';
			$lngkey_txt	= 'noposts_dtb_'.$tab.'_txt';
			if( $tab == 'private' ) {
				$lngkey_ttl	.= '_'.$privtab;
				$lngkey_txt	.= '_'.$privtab;
				if( $privtab == 'usr' && $privusr ) {
					$arr['#USERNAME2#']	= '<a href="'.$C->SITE_URL.$privusr->username.'">'.$privusr->username.'</a>';
					$arr['#A3#']	= '<a href="javascript:;" onclick="postform_open(({username:\''.$privusr->username.'\'}));">';
					$arr['#A4#']	= '</a>';
				}
			}
			else {
				if( $tab == 'group' ) {
					$arr['#GROUP#']	= '<a href="'.$C->SITE_URL.$onlygroup->groupname.'">'.htmlspecialchars($onlygroup->title).'</a>';
					$arr['#A1#']	= '<a href="javascript:;" onclick="postform_open(({groupname:\''.htmlspecialchars($onlygroup->title).'\'}));">';
					$arr['#A2#']	= '</a>';
				}
				if( $filter != 'all' ) {
					$lngkey_ttl	.= '_filter';
					$lngkey_txt	.= '_filter';
				}
			}
			$D->noposts_box_title	= $this->lang($lngkey_ttl, $arr);
			$D->noposts_box_text	= $this->lang($lngkey_txt, $arr);
			$D->posts_html	= $this->load_template('noposts_box.php', FALSE);
		}
	}
	
	if( $tab=='all' || $tab=='@me' || $tab=='private' || $tab=='commented' || $tab=='feeds' ) {
		$this->network->reset_dashboard_tabstate($this->user->id, $tab);
	}
	
	if( $this->param('from') == 'ajax' )
	{
		echo 'OK:';
		echo $D->posts_html;
		exit;
	}
	
	$D->menu_groups	= $this->user->get_top_groups(15);
	
	$D->tabs_state	= $this->network->get_dashboard_tabstate($this->user->id, array('all','@me','private','commented','feeds'));
	if( isset($D->tabs_state[$tab]) ) {
		$D->tabs_state[$tab]	= 0;
	}
	
	$D->whattodo_active	= FALSE;
	$D->whattodo_minimized	= FALSE;
	$D->whattodo_links	= array();
	$D->whattodo_lines	= array(
		'prof_info'	=> FALSE,
		'cnt_info'	=> FALSE,
		'avatar'	=> FALSE,
		'followu'	=> FALSE,
		'invite'	=> FALSE,
		'followg'	=> FALSE,
	);
	
	$ui	= & $this->user->info;
	if( empty($ui->position) && empty($ui->location) && 0==intval($ui->birthdate) && empty($ui->gender) && empty($ui->about_me) && 0==count($ui->tags) ) {
		$D->whattodo_lines['prof_info']	= TRUE;
		$D->whattodo_links[]	= array($C->SITE_URL.'settings/profile', 'os_dbrd_whattodoo_profile');
	}
	$tmp	= '';
	if( $this->user->sess['cdetails'] ) {
		unset($this->user->sess['cdetails']->user_id);
		foreach($this->user->sess['cdetails'] as $v) { $tmp .= $v; }
	}
	if( empty($tmp) ) {
		$D->whattodo_lines['cnt_info']	= TRUE;
		$D->whattodo_links[]	= array($C->SITE_URL.'settings/contacts', 'os_dbrd_whattodoo_contacts');
	}
	if( $ui->avatar == $C->DEF_AVATAR_USER ) {
		$D->whattodo_lines['avatar']	= TRUE;
		$D->whattodo_links[]	= array($C->SITE_URL.'settings/avatar', 'os_dbrd_whattodoo_avatar');
	}
	if( 0 == count($this->network->get_user_follows($this->user->id)->follow_users) ) {
		$D->whattodo_lines['followu']	= TRUE;
		$D->whattodo_links[]	= array($C->SITE_URL.'members', 'os_dbrd_whattodoo_followusr');
	}
	if( ! $db2->fetch_field('SELECT id FROM users_invitations WHERE user_id="'.$this->user->id.'" LIMIT 1') ) {
		$D->whattodo_lines['invite']	= TRUE;
		$D->whattodo_links[]	= array($C->SITE_URL.'invite', 'os_dbrd_whattodoo_invite');
	}
	if( 0 == count($this->network->get_user_follows($this->user->id)->follow_groups) ) {
		$D->whattodo_lines['followg']	= TRUE;
		$D->whattodo_links[]	= array($C->SITE_URL.'groups', 'os_dbrd_whattodoo_followgrp');
	}
	if( $D->whattodo_lines['prof_info'] || $D->whattodo_lines['cnt_info'] || $D->whattodo_lines['avatar'] || $D->whattodo_lines['followu'] || $D->whattodo_lines['invite'] || $D->whattodo_lines['followg'] ) {
		$D->whattodo_active	= TRUE;
	}
	if( $this->user->info->dbrd_whattodo_closed == 1 ) {
		$D->whattodo_minimized	= TRUE;
	}
	if( $D->whattodo_active && $D->whattodo_minimized && !$D->whattodo_lines['prof_info'] && !$D->whattodo_lines['cnt_info'] && !$D->whattodo_lines['avatar'] && !$D->whattodo_lines['followu'] ) {
		$D->whattodo_active	= FALSE;
	}
	
	$D->last_online	= array();
	$num	= 6;
	$time	= 5*60;
	$r	= $db2->query('SELECT id, lastclick_date FROM users WHERE active=1 ORDER BY lastclick_date DESC LIMIT '.($num+1));
	while($o = $db2->fetch_object($r)) {
		if( $o->lastclick_date < time()-$time ) {
			break;
		}
		if( $o->id == $this->user->id ) {
			continue;
		}
		$D->last_online[]	= $this->network->get_user_by_id($o->id);
	}
	$D->last_online	= array_slice($D->last_online, 0, $num);
	
	$D->saved_searches	= array();
	$db2->query('SELECT id, search_key, search_string FROM searches WHERE user_id="'.$this->user->id.'" ORDER BY id DESC');
	while($tmp = $db2->fetch_object()) {
		$tmp->search_key		= stripslashes($tmp->search_key);
		$tmp->search_string	= stripslashes($tmp->search_string);
		$D->saved_searches[$tmp->id]	= $tmp;
	}
	
	$D->post_tags	= array();
	$not_in_groups	= array();
	$r	= $this->db2->query('SELECT id FROM groups WHERE is_public=0');
	while($tmp = $this->db2->fetch_object()) {
		$not_in_group[]	= $tmp->id;
	}
	$not_in_groups	= count($not_in_groups)>0 ? ('AND group_id NOT IN('.implode(', ', $not_in_groups).')') : '';
	$D->post_tags	= $this->network->get_recent_posttags($not_in_groups, 10);
	
	$this->load_template('dashboard.php');
	
?>