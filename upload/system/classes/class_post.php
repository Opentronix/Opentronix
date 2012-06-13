<?php
	
	class post
	{
		private $network;
		private $user;
		private $cache;
		private $db1;
		private $db2;
		public $post_id;
		public $post_type;
		public $post_tmp_id;
		public $post_api_id;
		public $post_user;
		public $post_to_user;
		public $post_group;
		public $post_message;
		public $post_mentioned;
		public $post_attached;
		public $post_posttags;
		public $post_date;
		public $post_comments;
		public $post_commentsnum;
		public $permalink;
		public $is_system_post	= FALSE;
		public $is_feed_post	= FALSE;
		public $error	= FALSE;
		public $tmp;
		
		public function __construct($type, $load_id=FALSE, $load_obj=FALSE)
		{
			global $C;
			$this->tmp	= new stdClass;
			$this->network	= & $GLOBALS['network'];
			$this->user		= & $GLOBALS['user'];
			$this->cache	= & $GLOBALS['cache'];
			$this->db1	= & $GLOBALS['db1'];
			$this->db2	= & $GLOBALS['db2'];
			$type	= $type=='private' ? 'private' : 'public';
			if( ! $this->network->id ) {
				$this->error	= TRUE;
				return;
			}
			if( $load_id ) {
				$id	= intval($load_id);
				$r	= $this->db2->query('SELECT * FROM '.($type=='private'?'posts_pr':'posts').' WHERE id="'.$id.'" LIMIT 1', FALSE);
				if( ! $obj = $this->db2->fetch_object($r) ) {
					$this->error	= TRUE;
					return;
				}
			}
			elseif( $load_obj ) {
				$obj	= $load_obj;
				$id	= intval($obj->id);
				if( ! $id ) {
					$this->error	= TRUE;
					return;
				}
			}
			else {
				$this->error	= TRUE;
				return;
			}
			if( $type=='private' && !$this->user->is_logged ) {
				$this->error	= TRUE;
				return;
			}
			if( $type=='private' && $this->user->id!=$obj->user_id && $this->user->id!=$obj->to_user ) {
				$this->error	= TRUE;
				return;
			}
			if( $type=='private' && $this->user->id==$obj->to_user && $obj->is_recp_del==1 ) {
				$this->error	= TRUE;
				return;
			}
			$g	= FALSE;
			if( $type == 'public' && $obj->group_id>0 ) {
				$g	= $this->network->get_group_by_id($obj->group_id);
				if( $g ) {
					if( !$g->is_public && !$this->user->is_logged ) {
						$this->error	= TRUE;
						return;
					}
					if( !$g->is_public && !$this->user->info->is_network_admin ) {
						$i	= $this->network->get_group_invited_members($g->id);
						if( !$i || !in_array(intval($this->user->id),$i) ) {
							$this->error	= TRUE;
							return;
						}
					}
				}
				if( ! $g ) {
					$g	= $this->network->get_deleted_group_by_id($obj->group_id);
				}
				if( ! $g ) {
					$this->error	= TRUE;
					return;
				}
			}
			$u1	= FALSE;
			if( $obj->api_id == 2 ) {
				$this->is_feed_post	= TRUE;
				if( $obj->user_id == 0 ) {
					$u1	= (object) array('id'=>0);
				}
				else {
					$u1	= $this->network->get_user_by_id($obj->user_id);
					if( ! $u1 ) {
						$this->error	= TRUE;
						return;
					}
				}
			}
			elseif( $obj->user_id == 0 ) {
				$u1	= (object) array('id'=>0);
				$this->is_system_post	= TRUE;
			}
			else {
				$u1	= $this->network->get_user_by_id($obj->user_id);
				if( ! $u1 ) {
					$this->error	= TRUE;
					return;
				}
			}
			$u2	= FALSE;
			if( $type == 'private' ) {
				$u2	= $this->network->get_user_by_id($obj->to_user);
				if( ! $u2 ) {
					$this->error	= TRUE;
					return;
				}
			}
			$this->post_id		= intval($obj->id);
			$this->post_api_id	= intval($obj->api_id);
			$this->post_user		= &$u1;
			$this->post_to_user	= &$u2;
			$this->post_group		= &$g;
			$this->post_message	= stripslashes($obj->message);
			$this->post_date		= intval($obj->date);
			$this->post_mentioned	= array();
			$this->post_attached	= array();
			$this->post_posttags	= array();
			$this->post_comments	= array();
			$this->post_commentsnum	= 0;
			if( $obj->mentioned > 0 ) {
				$r	= $this->db2->query('SELECT user_id FROM '.($type=='private'?'posts_pr_mentioned':'posts_mentioned').' WHERE post_id="'.$obj->id.'" LIMIT '.$obj->mentioned, FALSE);
				while($o = $this->db2->fetch_object($r)) {
					if( $u = $this->network->get_user_by_id($o->user_id) ) {
						$this->post_mentioned[]	= array($u->username, $u->fullname);
					}
				}
			}
			if( $obj->attached > 0 ) {
				$r	= $this->db2->query('SELECT id, type, data FROM '.($type=='private'?'posts_pr_attachments':'posts_attachments').' WHERE post_id="'.$obj->id.'" LIMIT '.$obj->attached, FALSE);
				while($o = $this->db2->fetch_object($r)) {
					$o->data	= unserialize(stripslashes($o->data));
					$o->data->attachment_id	= $o->id;
					$this->post_attached[stripslashes($o->type)]	= $o->data;
				}
			}
			if( $obj->posttags > 0 ) {
				if( preg_match_all('/\#([א-תÀ-ÿ一-龥а-яa-z0-9\-_]{1,50})/iu', $this->post_message, $matches, PREG_PATTERN_ORDER) ) {
					foreach($matches[1] as $tg) {
						$this->post_posttags[]	= trim($tg);
					}
					$this->post_posttags	= array_unique($this->post_posttags);
				}
			}
			if( $obj->comments > 0 ) {
				$r	= $this->db2->query('SELECT * FROM '.($type=='private'?'posts_pr_comments':'posts_comments').' WHERE post_id="'.$obj->id.'" ORDER BY id ASC', FALSE);
				while($o = $this->db2->fetch_object($r)) {
					$tmp	= new postcomment($this, FALSE, $o);
					if( $tmp->error ) {
						continue;
					}
					$this->post_comments[]	= $tmp;
				}
				$this->post_commentsnum	= count($this->post_comments);
			}
			$this->post_type		= $type;
			$this->post_tmp_id	= $type.'_'.$this->post_id;
			$this->permalink		= $C->SITE_URL.'view/'.($type=='private'?'priv':'post').':'.$this->post_id;
			if( $this->is_system_post ) {
				$this->permalink	= $C->SITE_URL;
				$tmp	= unserialize($this->post_message);
				if( !$tmp || !is_object($tmp) || !isset($tmp->lang_key) || !isset($tmp->lang_params) ) {
					$this->error	= TRUE;
					return;
				}
				global $page;
				$page->load_langfile('inside/notifications.php');
				$this->post_message	= $page->lang($tmp->lang_key, $tmp->lang_params);
				if( empty($this->post_message) ) {
					$this->error	= TRUE;
					return;
				}
				$this->tmp->syspost_about_user	= FALSE;
				if( $tmp->from_user_id ) {
					$this->tmp->syspost_about_user	= $this->network->get_user_by_id($tmp->from_user_id);
				}
			}
			return TRUE;
		}
		
		public function is_post_faved()
		{
			if( isset($this->tmp->is_post_faved) ) {
				return $this->tmp->is_post_faved;
			}
			if( $this->error ) {
				$this->tmp->is_post_faved	= FALSE;
				return FALSE;
			}
			if( $this->is_system_post ) {
				$this->tmp->is_post_faved	= FALSE;
				return FALSE;
			}
			if( ! $this->user->is_logged ) {
				$this->tmp->is_post_faved	= FALSE;
				return FALSE;
			}
			if( ! $favs = $this->get_post_favs() ) {
				$this->tmp->is_post_faved	= FALSE;
				return FALSE;
			}
			$this->tmp->is_post_faved = in_array(intval($this->user->id), $favs);
			return $this->tmp->is_post_faved;
		}
		
		public function get_post_favs($force_refresh=FALSE)
		{
			if( $this->error ) {
				return FALSE;
			}
			if( $this->is_system_post ) {
				return FALSE;
			}
			$cachekey	= 'n:'.$this->network->id.',post_favs:'.$this->post_type.':'.$this->post_id;
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				return $data;
			}
			$data	= array();
			$r	= $this->db2->query('SELECT user_id FROM post_favs WHERE post_type="'.$this->post_type.'" AND post_id="'.$this->post_id.'" ', FALSE);
			while($o = $this->db2->fetch_object($r)) {
				$data[]	= intval($o->user_id);
			}
			$this->cache->set($cachekey, $data, $GLOBALS['C']->CACHE_EXPIRE);
			return $data;
		}
		
		public function fave_post($state=TRUE)
		{
			if( $this->error ) {
				return FALSE;
			}
			if( $this->is_system_post ) {
				return FALSE;
			}
			if( ! $this->user->is_logged ) {
				return FALSE;
			}
			$b	= $this->is_post_faved();
			$u	= intval($this->user->id);
			if( $b && !$state ) {
				$this->db2->query('DELETE FROM post_favs WHERE user_id="'.$u.'" AND post_type="'.$this->post_type.'" AND post_id="'.$this->post_id.'" LIMIT 1', FALSE);
			}
			elseif( !$b && $state ) {
				$this->db2->query('INSERT INTO post_favs SET user_id="'.$u.'", post_type="'.$this->post_type.'", post_id="'.$this->post_id.'", date="'.time().'" ', FALSE);
			}
			$this->get_post_favs(TRUE);
			return TRUE;
		}
		
		public function parse_text()
		{
			global $C;
			if( $this->error ) {
				return FALSE;
			}
			if( $this->is_system_post ) {
				if( $C->API_ID == 1 ) {
					if( substr($C->DOMAIN, 0, 2) == 'm.' ) {
						$s	= preg_replace('/^m\./i', '', $C->DOMAIN);
						$this->post_message	= str_replace($s, $C->DOMAIN, $this->post_message);
					}
					elseif( preg_match('/\/m(\/|$)/', $_SERVER['REQUEST_URI']) ) {
						$tmp	= preg_replace('/\/m(\/|$)/', '', $C->SITE_URL);
						$tmp	= rtrim($tmp,'/').'/';
						$this->post_message	= str_replace($tmp, $C->SITE_URL, $this->post_message);
					}
				}
				return $this->post_message;
			}
			$message	= htmlspecialchars($this->post_message);
			if( FALSE!==strpos($message,'http://') || FALSE!==strpos($message,'http://') || FALSE!==strpos($message,'ftp://') ) {
				$message	= preg_replace('#(^|\s)((http|https|ftp)://\w+[^\s\[\]]+)#ie', 'post::_postparse_build_link("\\2", "\\1")', $message);
			}
			if( count($this->post_mentioned) > 0 ) {
				$tmp	= array();
				foreach($this->post_mentioned as $i=>$v) {
					$tmp[$i]	= mb_strlen($v[0]);
				}
				arsort($tmp);
				$tmp2	= array();
				foreach($tmp as $i=>$v) {
					$tmp2[]	= $this->post_mentioned[$i];
				}
				foreach($tmp2 as $u) {
					$txt	= '<a href="'.$C->SITE_URL.$u[0].'" title="'.htmlspecialchars($u[1]).'"><span class="post_mentioned"><b>@</b>'.$u[0].'</span></a>';
					$message	= preg_replace('/(^|\s)\@'.preg_quote($u[0]).'/ius', '$1'.$txt, $message);
				}
			}
			if( count($this->post_posttags) > 0 ) {
				$tmp	= array();
				foreach($this->post_posttags as $i=>$v) {
					$tmp[$i]	= mb_strlen($v);
				}
				arsort($tmp);
				$tmp2	= array();
				foreach($tmp as $i=>$v) {
					$tmp2[]	= $this->post_posttags[$i];
				}
				foreach($tmp2 as $tag) {
					$txt	= '<a href="'.$C->SITE_URL.'search/posttag:%23'.$tag.'" title="'.$tag.'"><span class="post_tag"><b>#</b>'.$tag.'</span></a>';
					$message	= preg_replace('/(^|\s)\#'.preg_quote($tag).'/ius', '$1'.$txt, $message);
				}
			}
			foreach($C->POST_ICONS as $k=>$v) {
				$txt	= '<img src="'.$C->IMG_URL.'icons/'.$v.'" class="post_smiley" alt="'.$k.'" title="'.$k.'" />';
				$message	= str_replace($k, $txt, $message);
			}
			return $message;
		}
		
		public static function parse_date($timestamp, $return_words='auto', $return_dt_format='%b %e %Y, %H:%M')
		{
			if( $return_words == FALSE ) {
				return strftime($return_dt_format, $timestamp);
			}
			$time	= time() - $timestamp;
			$h	= floor($time / 3600);
			$time	-= $h * 3600;
			$m	= floor($time / 60);
			$time	-= $m * 60;
			$s	= $time;
			if( $return_words === 'auto' && $h >= 12 ) {
				return strftime($return_dt_format, $timestamp);
			}
			$txt	= '##BEFORE## ';
			if( $h > 0 ) {
				$txt	.= $h;
				$txt	.= $h==1 ? ' ##HOUR##' : ' ##HOURS##';
			}
			if( $h >= 3 ) {
				$txt	.= ' ##AGO##';
				return post::_parse_date_replace_strings($txt);
			}
			if( $m > 0 ) {
				if( $h > 0 ) {
					$txt	.= ' ##AND## ';
				}
				$txt	.= $m;
				$txt	.= $m==1 ? ' ##MIN##' : ' ##MINS##';
				if( $h > 0 ) {
					$txt	.= ' ##AGO##';
					return post::_parse_date_replace_strings($txt);
				}
			}
			if( $h==0 && $m==0 ) {
				if( $s == 0 ) {
					return post::_parse_date_replace_strings('##NOW##');
				}
				$txt	.= $s;
				$txt	.= $s==1 ? ' ##SEC##' : ' ##SECS##';
			}
			$txt	.= ' ##AGO##';
			return post::_parse_date_replace_strings($txt);
		}
		public static function _parse_date_replace_strings($txt='')
		{
			global $page;
			$tmp	= array (
				'##BEFORE##'	=> $page->lang('posttime_before'),
				'##HOUR##'		=> $page->lang('posttime_hour'),
				'##HOURS##'		=> $page->lang('posttime_hours'),
				'##MIN##'		=> $page->lang('posttime_min'),
				'##MINS##'		=> $page->lang('posttime_mins'),
				'##SEC##'		=> $page->lang('posttime_sec'),
				'##SECS##'		=> $page->lang('posttime_secs'),
				'##AND##'		=> $page->lang('posttime_and'),
				'##AGO##'		=> $page->lang('posttime_ago'),
				'##NOW##'		=> $page->lang('posttime_now'),
			);
			$txt	= str_replace(array_keys($tmp), array_values($tmp), $txt);
			$txt	= trim($txt);
			$txt	= str_replace(' ', '&nbsp;', $txt);
			return $txt;
		}
		
		public function parse_group($cutstr=20)
		{
			if( $this->error ) {
				return FALSE;
			}
			if( ! $this->post_group ) {
				return '';
			}
			if( $this->post_group->is_deleted ) {
				return $GLOBALS['page']->lang('postgroup_in').'&nbsp;<a title="'.$GLOBALS['page']->lang('postgroup_del').' '.$this->post_group->title.'">'.str_cut($this->post_group->title,intval($cutstr)).'</a>';
			}
			return $GLOBALS['page']->lang('postgroup_in').'&nbsp;<a href="'.$GLOBALS['C']->SITE_URL.$this->post_group->groupname.'" title="'.$this->post_group->title.'">'.str_cut($this->post_group->title,intval($cutstr)).'</a>';
		}
		
		public static function parse_api($api_id=0)
		{
			if( $api_id == 0 ) {
				return '';
			}
			if( ! $api = $GLOBALS['network']->get_posts_api($api_id) ) {
				return '';
			}
			return $GLOBALS['page']->lang('postapi_via').'&nbsp;'.$api->name;
		}
		
		public function show_share_link()
		{
			global $C;
			$p		= $this;
			$lnktxt	= $GLOBALS['page']->lang('postsharelink');
			if( $p->post_type == 'public' && (!$p->post_group || $p->post_group->is_public) ) {
				$html	= '
						&middot;
						<span class="post_share_external">
							<a href="javascript:;" onfocus="this.blur();" id="extshare_link_'.$p->post_tmp_id.'" onclick="extshare_openbox(\''.$p->post_tmp_id.'\');" onmouseout="extshare_closebox(\''.$p->post_tmp_id.'\');">'.$lnktxt.'</a>
							<div class="post_share_dropbox" id="extshare_tmpbox_'.$p->post_tmp_id.'" style="display:none;">';
				if( isset($C->FACEBOOK_API_KEY) && !empty($C->FACEBOOK_API_KEY) ) {
					$html	.= '			
								<a class="shr_fb" href="javascript:;" onclick="FB.Connect.showShareDialog(\''.htmlspecialchars($p->permalink).'\');">Facebook</a>';
				}
				else {
					$html	.= '			
								<a class="shr_fb" href="http://www.facebook.com/sharer.php?u='.urlencode($p->permalink).'&t='.urlencode(htmlspecialchars($p->post_user->fullname.': '.$p->post_message)).'">Facebook</a>';
				}
				$html	.= '
								<a class="shr_tw" href="'.$C->SITE_URL.'twitter-share/?url='.urlencode($p->permalink).'&status='.urlencode(htmlspecialchars($p->post_user->fullname.': '.$p->post_message)).'" target="_blank">Twitter</a>
								<a class="shr_bz" href="http://www.google.com/reader/link?url='.urlencode($p->permalink).'&title='.urlencode(htmlspecialchars($p->post_user->fullname.': '.$p->post_message)).'&srcURL='.urlencode($p->permalink).'" target="_blank">Google Buzz</a>
								<a class="shr_dl" href="javascript:;" onclick="window.open(\'http://delicious.com/save?v=5&noui&jump=close&url=\'+encodeURIComponent(\''.$p->permalink.'\')+\'&title=\'+encodeURIComponent(\''.str_replace("'","\'",htmlspecialchars($p->post_user->fullname.': '.$p->post_message)).'\'), \'delicious\',\'toolbar=no,width=550,height=550\'); return false;">Delicious</a>
							</div>
						</span>';
				return $html;
			}
			return '';
		}
		
		public function if_can_edit()
		{	
			global $C;
			if( $this->error ) {
				return FALSE;
			}
			if( ! $this->user->is_logged ) {
				return FALSE;
			}
			if( $this->is_feed_post ) {
				return FALSE;
			}
			if( $this->is_system_post ) {
				return FALSE;
			}
			if( $this->user->id == $this->post_user->id ) {
				return TRUE;
			}
			return FALSE;
		}
		
		public function if_can_delete()
		{
			global $C;
			if( $this->error ) {
				return FALSE;
			}
			if( ! $this->user->is_logged ) {
				return FALSE;
			}
			if( $this->post_type=='private' && $this->post_to_user->id==$this->user->id ) {
				return TRUE;
			}
			if( $this->if_can_edit() ) {
				return TRUE;
			}
			if( $this->is_system_post && !$this->post_group ) {
				return TRUE;
			}
			if( $this->user->info->is_network_admin == 1 ) {
				return TRUE;
			}
			if( $this->post_type=='public' && $this->post_group ) {
				$currentpage	= $GLOBALS['page']->request[0];
				if( $currentpage=='group' || $currentpage=='ajax' ) {
					$gid	= intval($this->post_group->id);
					$r	= $this->db2->query('SELECT id FROM groups_admins WHERE group_id="'.$gid.'" AND user_id="'.$this->user->id.'" LIMIT 1', FALSE);
					if( 0 < $this->db2->num_rows($r) ) {
						return TRUE;
					}
				}
			}
			return FALSE;
		}
		
		public function delete_this_post()
		{
			global $C;
			if( ! $this->if_can_delete() ) {
				return FALSE;
			}
			if( $this->is_system_post ) {
				if( $this->post_type=='private' && $this->post_to_user->id==$this->user->id ) {
					$this->db2->query('DELETE FROM posts_pr WHERE id="'.$this->post_id.'" LIMIT 1', FALSE);
					$this->error	= TRUE;
					return TRUE;
				}
				if( $this->post_type=='public' && $this->post_group ) {
					$this->db2->query('DELETE FROM post_userbox WHERE post_id="'.$this->post_id.'" ', FALSE);
					$this->db2->query('DELETE FROM post_userbox_feeds WHERE post_id="'.$this->post_id.'" ', FALSE);
					$this->db2->query('DELETE FROM posts WHERE id="'.$this->post_id.'" LIMIT 1', FALSE);
					$this->error	= TRUE;
					return TRUE;
				}
				if( $this->post_type=='public' && !$this->post_group ) {
					$this->db2->query('DELETE FROM post_userbox WHERE user_id="'.$this->user->id.'" AND post_id="'.$this->post_id.'" LIMIT 1', FALSE);
					$this->db2->query('DELETE FROM post_userbox_feeds WHERE user_id="'.$this->user->id.'" AND post_id="'.$this->post_id.'" LIMIT 1', FALSE);
					$r	= $this->db2->query('SELECT user_id FROM post_userbox WHERE post_id="'.$this->post_id.'" LIMIT 1', FALSE);
					if( 0 == $this->db2->num_rows($r) ) {
						$r	= $this->db2->query('SELECT user_id FROM post_userbox_feeds WHERE post_id="'.$this->post_id.'" LIMIT 1', FALSE);
						if( 0 == $this->db2->num_rows($r) ) {
							$this->db2->query('DELETE FROM posts WHERE id="'.$this->post_id.'" LIMIT 1', FALSE);
						}
					}
					$this->error	= TRUE;
					return TRUE;
				}
				if( $this->user->is_network_admin ) {
					if($this->post_type == 'public') {
						$this->db2->query('DELETE FROM post_userbox WHERE post_id="'.$this->post_id.'" ', FALSE);
						$this->db2->query('DELETE FROM post_userbox_feeds WHERE post_id="'.$this->post_id.'" ', FALSE);
					}
					$this->db2->query('DELETE FROM '.($this->post_type=='private'?'posts_pr':'posts').' WHERE id="'.$this->post_id.'" LIMIT 1', FALSE);
					$this->error	= TRUE;
					return TRUE;
				}
			}
			if( $this->post_type=='private' && $this->post_to_user->id==$this->user->id ) {
				$this->fave_post(FALSE);
				$this->db2->query('UPDATE posts_pr SET is_recp_del=1 WHERE id="'.$this->post_id.'" LIMIT 1');
				$this->error	= TRUE;
				return TRUE;
			}
			foreach($this->post_comments as $c) {
				$c->delete_this_comment();
			}
			if($this->post_type == 'public') {
				$this->db2->query('DELETE FROM post_userbox WHERE post_id="'.$this->post_id.'" ', FALSE);
				$this->db2->query('DELETE FROM post_userbox_feeds WHERE post_id="'.$this->post_id.'" ', FALSE);
			}
			$this->db2->query('DELETE FROM post_favs WHERE post_type="'.$this->post_type.'" AND post_id="'.$this->post_id.'" ', FALSE);
			$this->db2->query('DELETE FROM '.($this->post_type=='private'?'posts_pr_mentioned':'posts_mentioned').' WHERE post_id="'.$this->post_id.'" ', FALSE);
			$this->db2->query('DELETE FROM '.($this->post_type=='private'?'posts_pr':'posts').' WHERE id="'.$this->post_id.'" LIMIT 1', FALSE);
			$this->db2->query('DELETE FROM '.($this->post_type=='private'?'posts_pr_comments_watch':'posts_comments_watch').' WHERE post_id="'.$this->post_id.'" ', FALSE);
			$this->db2->query('DELETE FROM '.($this->post_type=='private'?'posts_pr_attachments':'posts_attachments').' WHERE post_id="'.$this->post_id.'" ', FALSE);
			$at_dir	= $C->IMG_DIR.'attachments/'.$this->network->id.'/';
			foreach($this->post_attached as $tp=>$at) {
				foreach($at as $k=>$v) {
					if( substr($k,0,5) != 'file_' ) {
						continue;
					}
					rm($at_dir.$v);
				}
			}
			if( $this->post_type=='public' ) {
				$this->db2->query('UPDATE users SET num_posts=num_posts-1 WHERE id="'.$this->post_user->id.'" LIMIT 1');
				if( $this->post_group ) {
					$this->db2->query('UPDATE groups SET num_posts=num_posts-1 WHERE id="'.$this->post_group->id.'" LIMIT 1');
				}
			}
			$this->error	= TRUE;
			return TRUE;
		}
		
		public static function _postparse_build_link($url, $before='')
		{
			$after	= '';
			if( preg_match('/(javascript|vbscript)/', $url) ) {
				return $before.$url.$after;
			}
			if( preg_match('/([\.,\?]|&#33;)$/', $url, $matches) ) {
				$after	.= $matches[1];
				$url	= preg_replace('/([\.,\?]|&#33;)$/', '', $url);
			}
			$txt	= $url;
			if( strlen($txt) > 60 ) {
				$txt	= substr($txt, 0, 45).'...'.substr($txt, -10);
			}
			return $before.'<a href="'.$url.'" title="'.$url.'" target="_blank" rel="nofollow">'.$txt.'</a>'.$after;
		}
		
		public function if_new_comments()
		{
			if( $this->error ) {
				return 0;
			}
			if( ! $this->user->is_logged ) {
				return 0;
			}
			static $loaded	= array();
			if( ! isset($loaded[$this->post_tmp_id]) ) {
				$r	= $this->db2->query('SELECT newcomments FROM '.($this->post_type=='private'?'posts_pr_comments_watch':'posts_comments_watch').' WHERE post_id="'.$this->post_id.'" AND user_id="'.$this->user->id.'" LIMIT 1', FALSE);
				if( ! $obj = $this->db2->fetch_object($r) ) {
					$loaded[$this->post_tmp_id]	= 0;
				}
				else {
					$loaded[$this->post_tmp_id]	= intval($obj->newcomments);
				}
			}
			return $loaded[$this->post_tmp_id];
		}
		public function reset_new_comments()
		{
			if( $this->error ) {
				return FALSE;
			}
			if( ! $this->user->is_logged ) {
				return FALSE;
			}
			$this->db2->query('UPDATE '.($this->post_type=='private'?'posts_pr_comments_watch':'posts_comments_watch').' SET newcomments=0 WHERE post_id="'.$this->post_id.'" AND user_id="'.$this->user->id.'" LIMIT 1', FALSE);
		}
		public function get_comments()
		{
			return $this->post_comments;
		}
		public function get_last_comments()
		{
			global $C;
			return array_reverse( array_slice( array_reverse($this->post_comments), 0, $C->POST_LAST_COMMENTS ) );
		}
	}
	
?>