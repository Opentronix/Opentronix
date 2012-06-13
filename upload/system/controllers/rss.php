<?php
	
	/**
	 *	
	 * Some feeds require an username/password authentication, for other feeds is only optional
	 * 
	 * for example:
	 *
	 *  /rss/my:dashboard - this feed is based on the current user, so auth is requred
	 *  /rss/my:private - this feed is based on the current user, so auth is requred
	 *  
	 *  /rss/username:Somebody - if no User/pass is provided, feed will contain only Somebody's public posts,
	 *  else feed will provide also Somebody's posts from private groups that User is a member of.
	 * 	 	
	 */
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/rss.php');
	
	header('Content-type: application/xml; charset=UTF-8');
	
	if( ! $this->network->id ) {
		echo '<'.'?xml version="1.0" encoding="UTF-8"?'.">\n";
		echo '<feed xml:lang="en-US" xmlns="http://www.w3.org/2005/Atom">'."\n";
		echo '</feed>'."\n";
		return;
	}
	
	$rss_title		= '';
	$rss_altlink	= '';
	$q		= '';
	$q_limit	= $C->PAGING_NUM_POSTS;
	
	if( $this->param('my')=='dashboard' ) {
		_feed_auth_required();
		$rss_title	= $this->lang('rss_mydashboard', array('#USERNAME#'=>$this->user->info->username));
		$rss_altlink	= $C->SITE_URL.'dashboard';
		$q	= 'SELECT p.*, "public" AS `type` FROM post_userbox b LEFT JOIN posts p ON p.id=b.post_id WHERE b.user_id="'.$this->user->id.'" AND (p.api_id=2 OR p.user_id<>0) ORDER BY p.id DESC LIMIT '.$q_limit;
	}
	elseif( $this->param('my')=='posts' ) {
		_feed_auth_required();
		$rss_title	= $this->lang('rss_myposts', array('#USERNAME#'=>$this->user->info->username));
		$rss_altlink	= $C->SITE_URL.'dashboard/tab:myposts';
		$q	= 'SELECT *, "public" AS `type` FROM posts WHERE user_id="'.$this->user->id.'" ORDER BY id DESC LIMIT '.$q_limit;
	}
	elseif( $this->param('my')=='mentions' ) {
		_feed_auth_required();
		$rss_title	= $this->lang('rss_mymentions', array('#USERNAME#'=>$this->user->info->username));
		$rss_altlink	= $C->SITE_URL.'dashboard/tab:@me';
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
				$m	= $this->network->get_group_members($g->id);
				if( ! isset($m[$this->user->id]) ) {
					$not_in_groups[]	= $obj->id;
				}
			}
		}
		$not_in_groups	= count($not_in_groups)>0 ? ('AND p.group_id NOT IN('.implode(', ', $not_in_groups).')') : '';
		$q	= 'SELECT p.*, "public" AS `type` FROM posts p, posts_mentioned m WHERE p.id=m.post_id '.$not_in_groups.' AND m.user_id="'.$this->user->id.'" ORDER BY p.id DESC LIMIT '.$q_limit;
	}
	elseif( $this->param('my')=='bookmarks' ) {
		_feed_auth_required();
		$rss_title	= $this->lang('rss_mybookmarks', array('#USERNAME#'=>$this->user->info->username));
		$rss_altlink	= $C->SITE_URL.'dashboard/tab:bookmarks';
		$q	= '
			(SELECT p.id, p.api_id, p.user_id, p.group_id, "0" AS to_user, p.message, p.mentioned, p.attached, p.posttags, p.date, p.ip_addr, "0" AS is_recp_del, "public" AS `type`, f.id AS fid FROM post_favs f LEFT JOIN posts p ON p.id=f.post_id WHERE f.user_id="'.$this->user->id.'" AND f.post_type="public")
			UNION
			(SELECT p.id, p.api_id, p.user_id, "0" AS group_id, p.to_user, p.message, p.mentioned, p.attached, p.posttags, p.date, p.ip_addr, p.is_recp_del, "private" AS `type`, f.id AS fid FROM post_favs f LEFT JOIN posts_pr p ON p.id=f.post_id WHERE f.user_id="'.$this->user->id.'" AND f.post_type="private")
			ORDER BY fid DESC LIMIT '.$q_limit;
	}
	elseif( $this->param('my')=='private' ) {
		_feed_auth_required();
		$rss_title	= $this->lang('rss_myprivate', array('#USERNAME#'=>$this->user->info->username));
		$rss_altlink	= $C->SITE_URL.'dashboard/tab:private';
		$q	= 'SELECT *, "private" AS `type` FROM posts_pr WHERE (user_id="'.$this->user->id.'" OR (to_user="'.$this->user->id.'" AND is_recp_del=0)) ORDER BY id DESC LIMIT '.$q_limit;
	}
	elseif( $this->param('all')=='posts' ) {
		_feed_auth_optional();
		$rss_title	= $this->lang('rss_allposts');
		$rss_altlink	= $C->SITE_URL.'dashboard/tab:everybody';
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
			if( $this->user->is_logged ) {
				if( $this->user->info->is_network_admin ) {
					continue;
				}
				$m	= $this->network->get_group_members($g->id);
				if( isset($m[$this->user->id]) ) {
					continue;
				}
			}
			$not_in_groups[]	= $obj->id;
		}
		$not_in_groups	= count($not_in_groups)>0 ? ('AND p.group_id NOT IN('.implode(', ', $not_in_groups).')') : '';
		$q	= 'SELECT p.*, "public" AS `type` FROM posts p WHERE (user_id<>0 OR group_id<>0) '.$not_in_groups.' ORDER BY p.id DESC LIMIT '.$q_limit;
	}
	elseif( $this->param('username') ) {
		_feed_auth_optional();
		$u	= $this->network->get_user_by_username($this->param('username'));
		if( $u ) {
			$rss_title	= $this->lang('rss_usrposts', array('#USERNAME#'=>$u->username));
			$rss_altlink	= $C->SITE_URL.$u->username;
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
				if( $this->user->is_logged ) {
					if( $this->user->info->is_network_admin ) {
						continue;
					}
					$m	= $this->network->get_group_members($g->id);
					if( isset($m[$this->user->id]) ) {
						continue;
					}
				}
				$not_in_groups[]	= $obj->id;
			}
			$not_in_groups	= count($not_in_groups)>0 ? ('AND group_id NOT IN('.implode(', ', $not_in_groups).')') : '';
			$q	= 'SELECT *, "public" AS `type` FROM posts WHERE user_id="'.$u->id.'" '.$not_in_groups.' ORDER BY id DESC LIMIT '.$q_limit;
		}
	}
	elseif( $this->param('groupname') ) {
		_feed_auth_optional();
		$g	= $this->network->get_group_by_name($this->param('groupname'));
		if( $g ) {
			$rss_title	= $this->lang('rss_grpposts', array('#GROUP#'=>htmlspecialchars($g->title)));
			$rss_altlink	= $C->SITE_URL.$g->groupname;
			$has_access	= TRUE;
			if( $g->is_private ) {
				if( ! $this->user->is_logged ) {
					$has_access	= FALSE;
				}
				elseif( ! $this->user->info->is_network_admin ) {
					$m	= $this->network->get_group_members($g->id);
					if( ! isset($m[$this->user->id]) ) {
						$has_access	= FALSE;
					}
				}
			}
			if( $has_access ) {
				$q	= 'SELECT *, "public" AS `type` FROM posts WHERE group_id="'.$g->id.'" AND (api_id=2 OR user_id<>0) ORDER BY id DESC LIMIT '.$q_limit;
			}
		}
	}
	
	$rss_dtupd	= 0;
	$posts	= array();
	if( ! empty($q) ) {
		$r		= $this->db2->query($q);
		while($obj = $this->db2->fetch_object($r)) {
			$p	= new post($obj->type, FALSE, $obj);
			if( $p->error ) {
				continue;
			}
			$posts[]	= $p;
			$rss_dtupd	= max($rss_dtupd, $p->post_date);
		}
	}
	
	echo '<'.'?xml version="1.0" encoding="UTF-8"?'.">\n";
	echo '<feed xml:lang="en-US" xmlns="http://www.w3.org/2005/Atom">'."\n";
	echo "\t<title>".$rss_title." - ".$C->SITE_TITLE."</title>\n";
	echo "\t<id>tag:".$C->DOMAIN.','.md5($_SERVER['REQUEST_URI'])."</id>\n";
	echo "\t".'<link href="'.$_SERVER['REQUEST_URI'].'" rel="self" />'."\n";
	echo "\t".'<link href="'.$rss_altlink.'" rel="alternate" type="text/html" />'."\n";
	echo "\t<subtitle>".$rss_title." - ".$C->SITE_TITLE."</subtitle>\n";
	echo "\t<updated>".date('c',$rss_dtupd)."</updated>\n";
	foreach($posts as &$p)
	{
		if( $p->post_user->id == 0 && $p->post_group ) {
			$title	= $p->post_group->title;
		}
		else {
			$title	= $p->post_user->username;
			if( $p->post_type == 'private' ) {
				$title	.= ' » '.$p->post_to_user->username;
			}
		}
		$title	.= ': '.$p->post_message;
		$title	= preg_replace('/\s+/s', ' ', $title);
		
		$content	= '<div style="line-height:1.3; padding:5px;">';
		if( $p->post_user->id == 0 && $p->post_group ) {
			$content	.= '<img src="'.$C->IMG_URL.'/avatars/thumbs2/'.$p->post_group->avatar.'" width="16" height="16" alt="" border="0" /> <strong>'.$p->post_group->title.'</strong>';
		}
		else {
			$content	.= '<img src="'.$C->IMG_URL.'/avatars/thumbs2/'.$p->post_user->avatar.'" width="16" height="16" alt="" border="0" /> <strong>'.$p->post_user->username.'</strong>';
		}
		if( $p->post_type == 'private' ) {
			$content	.= '<img src="'.$C->IMG_URL.'/avatars/thumbs2/'.$p->post_to_user->avatar.'" width="16" height="16" alt="" border="0" /> <strong>'.$p->post_to_user->username.'</strong>';
		}
		$content	.= ':<br />'.$p->parse_text().'<br />';
		if( isset($p->post_attached['link']) ) {
			$content	.= '<a href="'.htmlspecialchars($p->post_attached['link']->link).'" target="_blank">'.htmlspecialchars($p->post_attached['link']->link).'</a><br />';
		}
		if( isset($p->post_attached['file']) ) {
			$content	.= '<a href="'.$C->SITE_URL.'getfile/pid:'.$p->post_tmp_id.'/'.htmlspecialchars($p->post_attached['file']->title).'" target="_blank">'.htmlspecialchars($p->post_attached['file']->title).'</a><br />';
		}
		if( isset($p->post_attached['image']) ) {
			$content	.= '<a href="'.$C->IMG_URL.'attachments/'.$this->network->id.'/'.$p->post_attached['image']->file_preview.'" target="_blank"><img src="'.$C->IMG_URL.'attachments/'.$this->network->id.'/'.$p->post_attached['image']->file_thumbnail.'" width="60" height="60" alt="'.htmlspecialchars($p->post_attached['image']->title).'" border="0" /></a><br />';
		}
		if( isset($p->post_attached['videoembed']) ) {
			$content	.= '<a href="'.$p->post_attached['videoembed']->orig_url.'" target="_blank"><img src="'.$C->IMG_URL.'attachments/'.$this->network->id.'/'.$p->post_attached['videoembed']->file_thumbnail.'" width="60" height="60" alt="'.htmlspecialchars($p->post_attached['videoembed']->orig_url).'" border="0" /></a><br />';
		}
		$content	.= '</div>';
		$content	= preg_replace('/\s+/s', ' ', $content);
		
		echo "\t<entry>\n";
		echo "\t\t<title>".htmlspecialchars($title)."</title>\n";
		echo "\t\t<id>tag:".$C->DOMAIN.",".date('c',$p->post_date).",".md5($p->permalink)."</id>\n";
		echo "\t\t".'<link rel="alternate" type="text/html" href="'.$p->permalink.'" />'."\n";
		echo "\t\t".'<content type="html">'.htmlspecialchars($content).'</content>'."\n";
		echo "\t\t<published>".date('c',$p->post_date)."</published>\n";
		echo "\t\t<updated>".date('c',$p->post_date)."</updated>\n";
		echo "\t</entry>\n";
	}
	echo '</feed>'."\n";
	return;
	
	/************************************************************/
	/************************************************************/
	/************************************************************/
	/************************************************************/
	
	function _feed_auth_optional()
	{
		return FALSE;
		/*
		global $user, $C;
		if( $user->is_logged ) {
			return TRUE;
		}
		if( isset($_SERVER['PHP_AUTH_USER']) ) {
			$username	= trim($_SERVER['PHP_AUTH_USER']);
			$password	= trim($_SERVER['PHP_AUTH_PW']);
			if( empty($username) && empty($password) ) {
				return FALSE;
			}
			if( $user->login($username, md5($password)) ) {
				return TRUE;
			}
		}
		header('WWW-Authenticate: Basic realm="'.$C->SITE_TITLE.' Authentication"');
		header('HTTP/1.0 401 Unauthorized');
		return FALSE;
		*/
	}
	
	function _feed_auth_required()
	{
		global $user, $C;
		if( $user->is_logged ) {
			return TRUE;
		}
		if( isset($_SERVER['PHP_AUTH_USER']) ) {
			$username	= trim($_SERVER['PHP_AUTH_USER']);
			$password	= trim($_SERVER['PHP_AUTH_PW']);
			if( $user->login($username, md5($password)) ) {
				return TRUE;
			}
		}
		header('WWW-Authenticate: Basic realm="'.$C->SITE_TITLE.' Authentication"');
		header('HTTP/1.0 401 Unauthorized');
		echo '<'.'?xml version="1.0" encoding="UTF-8"?'.">\n";
		echo '<feed xml:lang="en-US" xmlns="http://www.w3.org/2005/Atom">'."\n";
		echo '</feed>'."\n";
		exit;
	}
	
?>