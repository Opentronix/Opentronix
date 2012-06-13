<?php
	
	if( !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
	
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/dashboard.php');
	
	$shows		= array('all', '@me', 'private', 'commented', 'bookmarks', 'everybody');
	$tabnums		= array('all', '@me', 'private', 'commented');
	
	$D->show_feeds_tab	= $db2->fetch_field('SELECT post_id FROM post_userbox_feeds WHERE user_id="'.$this->user->id.'" LIMIT 1') ? TRUE : FALSE;
	if($D->show_feeds_tab) {
		$shows[]	= 'feeds';
		$tabnums[]	= 'feeds';
	}
	
	$tabnums	= $this->network->get_dashboard_tabstate($this->user->id, $tabnums);
	
	$show	= 'all';
	if( $this->param('show') && in_array($this->param('show'), $shows) ) {
		$show	= $this->param('show');
	}
	$D->show		= $show;
	
	if( isset($tabnums[$show]) ) {
		$tabnums[$show]	= 0;
	}
	$D->tabnums	= $tabnums;
	
	$not_in_groups	= '';
	if(!$this->user->info->is_network_admin && ($show == '@me' || $show == 'everybody')) {
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
	switch($show)
	{
		case 'all':
			$q1	= 'SELECT COUNT(post_id) FROM post_userbox WHERE user_id="'.$this->user->id.'" ';
			$q2	= 'SELECT p.*, "public" AS `type` FROM post_userbox b LEFT JOIN posts p ON p.id=b.post_id WHERE b.user_id="'.$this->user->id.'" ORDER BY p.id DESC ';
			break;
		
		case 'feeds':
			$q1	= 'SELECT COUNT(post_id) FROM post_userbox_feeds WHERE user_id="'.$this->user->id.'" ';
			$q2	= 'SELECT p.*, "public" AS `type` FROM post_userbox_feeds b LEFT JOIN posts p ON p.id=b.post_id WHERE b.user_id="'.$this->user->id.'" ORDER BY p.id DESC ';
			break;
		
		case '@me':
			$q1	= 'SELECT COUNT(p.id) FROM posts p INNER JOIN (SELECT post_id FROM posts_mentioned WHERE user_id="'.$this->user->id.'" UNION SELECT p.post_id FROM posts_comments p, posts_comments_mentioned c WHERE c.comment_id = p.id AND c.user_id ="'.$this->user->id.'") x ON x.post_id=p.id '.$not_in_groups;
			$q2	= 'SELECT p.*, "public" AS `type` FROM posts p INNER JOIN (SELECT post_id FROM posts_mentioned WHERE user_id="'.$this->user->id.'" UNION SELECT p.post_id FROM posts_comments p, posts_comments_mentioned c WHERE c.comment_id = p.id AND c.user_id ="'.$this->user->id.'") x ON x.post_id=p.id '.$not_in_groups.' ORDER BY p.id DESC ';
			break;
		
		case 'commented':
			$q1	= '
				SELECT
				(SELECT COUNT(post_id) FROM posts_comments_watch w,posts p WHERE w.user_id="'.$this->user->id.'" AND w.post_id = p.id AND p.comments>0 )
				+
				(SELECT COUNT(post_id) FROM posts_pr_comments_watch w, posts_pr p WHERE w.user_id="'.$this->user->id.'" AND w.post_id = p.id AND p.comments>0)';
			$q2	= '
				(SELECT p.id, p.api_id, p.user_id, p.group_id, "0" AS to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, "0" AS is_recp_del, "public" AS `type`, p.date_lastcomment AS cdt FROM posts_comments_watch w LEFT JOIN posts p ON p.id=w.post_id WHERE w.user_id="'.$this->user->id.'" AND p.comments>0)
				UNION
				(SELECT p.id, p.api_id, p.user_id, "0" AS group_id, p.to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, p.is_recp_del, "private" AS `type`, p.date_lastcomment AS cdt FROM posts_pr_comments_watch w LEFT JOIN posts_pr p ON p.id=w.post_id WHERE w.user_id="'.$this->user->id.'" AND p.comments>0 AND (p.user_id="'.$this->user->id.'" OR (p.to_user="'.$this->user->id.'" && p.is_recp_del=0) ) )
				ORDER BY cdt DESC ';
			break;
			
		case 'everybody':
			$q1	= 'SELECT COUNT(p.id) FROM posts p WHERE p.api_id IN(0,1,3) AND p.user_id<>0 '.$not_in_groups;
			$q2	= 'SELECT p.*, "public" AS `type` FROM posts p WHERE p.api_id IN(0,1,3) AND p.user_id<>0 '.$not_in_groups.' ORDER BY p.id DESC ';
			break;
			
		case 'bookmarks':
			$q1	= 'SELECT COUNT(post_id) FROM post_favs WHERE user_id="'.$this->user->id.'"';
			$q2	= '
				(SELECT p.id, p.api_id, p.user_id, p.group_id, "0" AS to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, "0" AS is_recp_del, "public" AS `type`, f.id AS fid FROM post_favs f LEFT JOIN posts p ON p.id=f.post_id WHERE f.user_id="'.$this->user->id.'" AND f.post_type="public")
				UNION
				(SELECT p.id, p.api_id, p.user_id, "0" AS group_id, p.to_user, p.message, p.mentioned, p.attached, p.posttags, p.comments, p.date, p.ip_addr, p.is_recp_del, "private" AS `type`, f.id AS fid FROM post_favs f LEFT JOIN posts_pr p ON p.id=f.post_id WHERE f.user_id="'.$this->user->id.'" AND f.post_type="private")
				ORDER BY fid DESC ';
			break;
			
		case 'private':
			$q1	= 'SELECT COUNT(id) FROM posts_pr WHERE (user_id="'.$this->user->id.'" OR (to_user="'.$this->user->id.'" AND is_recp_del=0))';
			$q2	= 'SELECT *, "private" AS `type` FROM posts_pr WHERE (user_id="'.$this->user->id.'" OR (to_user="'.$this->user->id.'" AND is_recp_del=0)) ORDER BY date_lastcomment DESC, id DESC ';
			break;
		
		case 'group':
			$q1	= 'SELECT COUNT(p.id) FROM posts p WHERE group_id="'.$onlygroup->id.'"';
			$q2	= 'SELECT p.*, "public" AS `type` FROM posts p WHERE group_id="'.$onlygroup->id.'" ORDER BY p.id DESC ';
			break;
	}
	
	$D->num_results	= 0;
	$D->start_from	= 0;
	$D->posts_html	= '';
	
	if( $q1!='' && $q2!='' ) {
		$D->num_results	= $db2->fetch_field($q1);
		$D->start_from	= $this->param('start_from') ? intval($this->param('start_from')) : 0;
		$D->start_from	= max($D->start_from, 0);
		$D->start_from	= min($D->start_from, $D->num_results);
		$res	= $db2->query($q2.'LIMIT '.$D->start_from.', '.$C->PAGING_NUM_POSTS);
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
		unset($D->p);
		$D->posts_html	= ob_get_contents();
		ob_end_clean();
	}
	
	if( $this->param('from') == 'ajax' ) {
		echo 'OK:'.$D->posts_number.':';
		echo $D->posts_html;
		exit;
	}
	
	if( $show=='all' || $show=='@me' || $show=='private' || $show=='commented' || $show=='feeds' ) {
		$this->network->reset_dashboard_tabstate($this->user->id, $show);
	}
	
	$this->load_template('mobile_iphone/dashboard.php');
	
?>