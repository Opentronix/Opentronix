<?php
	
	require_once($C->INCPATH.'conf_embed.php');
	require_once($C->INCPATH.'helpers/func_images.php');
	
	class newpost
	{
		private $network	= FALSE;
		private $id		= FALSE;
		private $user	= FALSE;
		private $api_id		= 0;
		private $to_user		= FALSE;
		private $group		= FALSE;
		private $message		= '';
		private $mentioned	= array();
		private $attached		= array();
		private $posttags		= 0;
		private $db1;
		private $db2;
		private $cache;
		
		public function __construct()
		{
			global $C;
			$this->cache	= & $GLOBALS['cache'];
			$this->db1		= & $GLOBALS['db1'];
			$this->db2		= FALSE;
			$this->api_id	= $C->API_ID;
			$n	= & $GLOBALS['network'];
			if( $n->id ) {
				$u	= & $GLOBALS['user'];
				if($u->is_logged && $u->network->id==$n->id) {
					$this->network	= $n;
					$this->user		= $u->info;
					$this->db2	= & $GLOBALS['db2'];
				}
			}
			$this->id	= FALSE;
		}
		
		public function set_user($network_id, $user_id)
		{
			$this->network	= FALSE;
			$this->user		= FALSE;
			$n	= new network;
			if( ! $n->LOAD_by_id($network_id) ) {
				return FALSE;
			}
			if( ! $u = $n->get_user_by_id($user_id) ) {
				return FALSE;
			}
			$this->network	= $n;
			$this->user		= $u;
			return TRUE;
		}
		
		public function set_user_advanced($network, $user)
		{
			$this->network	= $network;
			$this->user		= $user;
			return TRUE;
		}
		
		public function load_post($type, $id, $network_id=FALSE, $user_id=FALSE)
		{
			if( $network_id && $user_id ) {
				$this->set_user($network_id, $user_id);
			}
			if( ! $this->user->id ) {
				return FALSE;
			}
			$this->id		= FALSE;
			$this->api_id	= 0;
			$this->to_user	= FALSE;
			$this->group	= FALSE;
			$this->message	= '';
			$this->mentioned	= array();
			$this->attached	= array();
			$this->posttags	= 0;
			$db2	= & $this->network->db2;
			$db2->query('SELECT * FROM '.($type=='private'?'posts_pr':'posts').' WHERE id="'.intval($id).'" AND user_id="'.$this->user->id.'" LIMIT 1');
			if( ! $obj = $db2->fetch_object() ) {
				return FALSE;
			}
			$this->id	= $obj->id;
			$this->api_id	= $obj->api_id;
			$this->to_user	= $type=='private' ? $this->network->get_user_by_id($obj->to_user) : FALSE;
			$this->group	= !isset($obj->group_id)||$obj->group_id==0 ? FALSE : $this->network->get_group_by_id($obj->group_id);
			$this->message	= stripslashes($obj->message);
			$this->posttags	= $obj->posttags;
			$this->mentioned	= array();
			if( $obj->mentioned > 0 ) {
				$db2->query('SELECT user_id FROM '.($type=='private'?'posts_pr_mentioned':'posts_mentioned').' WHERE post_id="'.$obj->id.'" LIMIT '.$obj->mentioned);
				while($o = $db2->fetch_object()) {
					$this->mentioned[]	= $o->user_id;
				}
			}
			if( $obj->attached > 0 ) {
				$db2->query('SELECT id, type, data FROM '.($type=='private'?'posts_pr_attachments':'posts_attachments').' WHERE post_id="'.$obj->id.'" LIMIT '.$obj->attached);
				while($o = $db2->fetch_object()) {
					$o->data	= unserialize(stripslashes($o->data));
					$o->data->attachment_id	= $o->id;
					$this->attached[stripslashes($o->type)]	= $o->data;
				}
			}
			return TRUE;
		}
		
		public function set_api_id($api_id)
		{
			if( $this->id ) {
				return FALSE;
			}
			$this->api_id	= intval($api_id);
			return TRUE;
		}
		
		public function set_to_user($user_id)
		{
			if( $this->id ) {
				return FALSE;
			}
			if( ! $user_id ) {
				return FALSE;
			}
			if( $user_id == $this->user->id ) {
				return FALSE;
			}
			if( ! $u = $this->network->get_user_by_id($user_id) ) {
				return FALSE;
			}
			$this->to_user	= $u;
			$this->group	= FALSE;
			return TRUE;
		}
		
		public function set_group_id($group_id)
		{
			if( $this->id ) {
				return FALSE;
			}
			if( ! $group_id ) {
				return FALSE;
			}
			if( ! $g = $this->network->get_group_by_id($group_id) ) {
				return FALSE;
			}
			if( !$g->is_public && $this->user->id>0 && !$this->user->is_network_admin ) {
				$users	= $this->network->get_group_invited_members($g->id);
				if( !$users || !in_array(intval($this->user->id),$users) ) {
					return FALSE;
				}
			}
			$this->group	= $g;
			$this->to_user	= FALSE;
			return TRUE;
		}
		
		public function set_message($message)
		{
			if( empty($message) ) {
				return FALSE;
			}
			global $C;
			$message	= mb_substr($message, 0, $C->POST_MAX_SYMBOLS);
			$this->message	= $message;
			
			$this->mentioned	= array();
			if( preg_match_all('/\@([a-zA-Z0-9\-_]{3,30})/u', $message, $matches, PREG_PATTERN_ORDER) ) {
				foreach($matches[1] as $unm) {
					if( $usr = $this->network->get_user_by_username($unm) ) {
						$this->mentioned[]	= $usr->id;
					}
				}
			}
			$this->mentioned	= array_unique($this->mentioned);
			
			$this->posttags	= array();
			if( preg_match_all('/\#([א-תÀ-ÿ一-龥а-яa-z0-9\-_]{1,50})/iu', $message, $matches, PREG_PATTERN_ORDER) ) {
				foreach($matches[1] as $tg) {
					$this->posttags[]	= mb_strtolower(trim($tg));
				}
			}
			$this->posttags	= count( array_unique($this->posttags) );
		}
		
		public function attach_link($link)
		{
			if( isset($this->attached['link']) ) {
				unset($this->attached['link']);
			}
			if( ! preg_match('/^(ftp|http|https):\/\//', $link) ) {
				$link	= 'http://'.$link;
			}
			if( ! preg_match('/^(ftp|http|https):\/\/((([a-z0-9.-]+\.)+[a-z]{2,4})|([0-9\.]{1,4}){4})(\/([a-zа-я0-9-_\—\:%\.\?\!\=\+\&\/\#\~\;\,\@]+)?)?$/iu', $link) ) {
				return FALSE;
			}
			return $this->attached['link'] = (object) array(
				'link'	=> $link,
				'hits'	=> 0,
			);
		}
		
		public function attach_image($input, $orig_filename='')
		{
			global $C;
			if( isset($this->attached['image']) ) {
				unset($this->attached['image']);
			}
			$types	= array (
				IMAGETYPE_GIF	=> 'gif',
				IMAGETYPE_JPEG	=> 'jpg',
				IMAGETYPE_PNG	=> 'png',
			);
			if( preg_match('/^(http|https|ftp)\:\/\//u', $input) ) {
				$tmp	= $C->TMP_DIR.'tmp'.md5(time().rand()).'.'.pathinfo($input,PATHINFO_EXTENSION);
				$res	= my_copy($input, $tmp);
				if( ! $res ) {
					return FALSE;
				}
				chmod($tmp, 0777);
				$input	= $tmp;
			}
			list($w, $h, $tp)	= @getimagesize($input);
			if( $w == 0 || $h == 0 ) {
				return FALSE;
			}
			if( ! isset($types[$tp]) ) {
				return FALSE;
			}
			$fn	= time().rand(100000,999999);
			$data	= (object) array (
				'in_tmpdir'	=> TRUE,
				'title'	=> $orig_filename,
				'file_original'	=> $fn.'_orig.'.$types[$tp],
				'file_preview'	=> $fn.'_large.'.$types[$tp],
				'file_thumbnail'	=> $fn.'_thumb.'.$types[$tp],
				'size_original'	=> '',
				'size_preview'	=> '',
				'filesize'	=> 0,
				'hits'	=> 0,
			);
			$data	= copy_attachment_image($input, $data); 
			if( ! $data ) {
				return FALSE;
			}
			return $this->attached['image'] = $data;
		}
		
		public function attach_videoembed($video)
		{
			global $C;
			if( isset($this->attached['videoembed']) ) {
				unset($this->attached['videoembed']);
			}
			$data	= (object) array (
				'in_tmpdir'	=> TRUE,
				'src_site'		=> '',
				'src_id'		=> '',
				'title'		=> '',
				'file_thumbnail'	=> time().rand(100000,999999).'_thumb.gif',
				'embed_code'	=> '',
				'embed_w'		=> '',
				'embed_h'		=> '',
				'orig_url'		=> '',
				'hits'	=> 0,
			);
			$S	= $C->NEWPOST_EMBEDVIDEO_SOURCES;
			foreach($S as $k=>$obj) {
				if( preg_match($obj->src_url_pattern, $video, $matches) ) {
					$data->src_id	= $matches[$obj->src_url_matchnum];
					$data->src_site	= $k;
					break;
				}
				elseif( preg_match($obj->src_emb_pattern, $video, $matches) ) {
					$data->src_id	= $matches[$obj->src_emb_matchnum];
					$data->src_site	= $k;
					break;
				}
			}
			if( empty($data->src_site) || empty($data->src_id) ) {
				return FALSE;
			}
			$S	= $S[$data->src_site];
			$data->embed_w	= $S->embed_w;
			$data->embed_h	= $S->embed_h;
			$data->embed_code	= str_replace('###ID###', $data->src_id, $S->embed_code);
			$data->orig_url	= str_replace('###ID###', $data->src_id, $S->insite_url);
			if( ! empty($S->embed_thumb) ) {
				$tmp	= str_replace('###ID###', $data->src_id, $S->embed_thumb);
				if( my_copy($tmp, $C->TMP_DIR.$data->file_thumbnail) ) {
					$res	= copy_attachment_videoimg($C->TMP_DIR.$data->file_thumbnail, $C->TMP_DIR.$data->file_thumbnail, $C->ATTACH_VIDEO_THUMBSIZE);
					if( ! $res ) {
						rm($C->TMP_DIR.$data->file_thumbnail);
					}
				}
			}
			if( ! file_exists($C->TMP_DIR.$data->file_thumbnail) ) {
				$data->file_thumbnail	= '';
			}
			return $this->attached['videoembed'] = $data;
		}
		
		public function attach_file($source, $filename)
		{
			global $C;
			if( isset($this->attached['file']) ) {
				unset($this->attached['file']);
			}
			if( ! file_exists($source) ) {
				return FALSE;
			}
			$ext	= '';
			$pos	= strpos($filename, '.');
			if( FALSE !== $pos ) {
				$ext	= '.'.mb_strtolower(mb_substr($filename,$pos+1));
			}
			$data	= (object) array (
				'in_tmpdir'	=> TRUE,
				'title'		=> $filename,
				'file_original'	=> time().rand(100000,999999).$ext,
				'filesize'	=> 0,
				'hits'	=> 0,
			);
			copy($source, $C->TMP_DIR.$data->file_original);
			if( ! file_exists($C->TMP_DIR.$data->file_original) ) {
				return FALSE;
			}
			chmod($C->TMP_DIR.$data->file_original, 0777);
			$data->filesize	= filesize($C->TMP_DIR.$data->file_original);
			return $this->attached['file'] = $data;
		}
		
		public function attach_richtext($richtext)
		{
		}
		
		public function save()
		{
			if( empty($this->message) ) {
				return FALSE;
			}
			global $C;
			$db2		= & $this->network->db2;
			$is_private	= $this->to_user ? TRUE : FALSE;
			$db_api_id		= intval($this->api_id);
			$db_user_id		= intval($this->user->id);
			$db_group_id	= $this->group ? intval($this->group->id) : 0;
			$db_to_user		= $this->to_user ? intval($this->to_user->id) : 0;
			$db_message		= $db2->escape($this->message);
			$db_mentioned	= count($this->mentioned);
			$db_attached	= count($this->attached);
			$db_posttags	= intval($this->posttags);
			$db_date		= time();
			$db_ip_addr		= ip2long($_SERVER['REMOTE_ADDR']);
			if( ! $this->id )
			{
				if( $is_private ) {
					$db2->query('INSERT INTO posts_pr SET api_id="'.$db_api_id.'", user_id="'.$db_user_id.'", to_user="'.$db_to_user.'", message="'.$db_message.'", mentioned="'.$db_mentioned.'", posttags="'.$db_posttags.'", attached="'.$db_attached.'", date="'.$db_date.'", date_lastcomment="'.$db_date.'", ip_addr="'.$db_ip_addr.'" ');
				}
				else {
					$db2->query('INSERT INTO posts SET api_id="'.$db_api_id.'", user_id="'.$db_user_id.'", group_id="'.$db_group_id.'", message="'.$db_message.'", mentioned="'.$db_mentioned.'", posttags="'.$db_posttags.'", attached="'.$db_attached.'", date="'.$db_date.'", date_lastcomment="'.$db_date.'", ip_addr="'.$db_ip_addr.'" ');
				}
				if( ! $id = $db2->insert_id() ) {
					return FALSE;
				}
				$this->attachments_copy_files();
				foreach($this->attached as $k=>$v) {
					$db2->query('INSERT INTO '.($is_private?'posts_pr_attachments':'posts_attachments').' SET post_id="'.$id.'", type="'.$db2->escape($k).'", data="'.$db2->escape(serialize($v)).'" ');
				}
				foreach($this->mentioned as $uid) {
					$db2->query('INSERT INTO '.($is_private?'posts_pr_mentioned':'posts_mentioned').' SET post_id="'.$id.'", user_id="'.intval($uid).'" ');
				}
				if( ! $is_private ) {
					$q	= array();
					$q2	= array();
					if( $this->user->id > 0 ) {
						$q[]	= '("'.$this->user->id.'", "'.$id.'")';
					}
					if($this->user->id > 0) {
						$u	= $this->network->get_user_follows($this->user->id)->followers;
						foreach($u as $k=>$v) {
							if( !$this->group || $this->group->is_public ) {
								$q[]	= '("'.$k.'", "'.$id.'")';
								$q2[]	= array($k, $this->api_id==2 ? 'feeds' : 'all');
							}
						}
					}
					if( $this->group ) {
						$u	= $this->network->get_group_members($this->group->id);
						if($u) {
							foreach($u as $k=>$v) {
								$q[]	= '("'.$k.'", "'.$id.'")';
								if( $k != $this->user->id ) {
									$q2[]	= array($k, $this->api_id==2 ? 'feeds' : 'all');
								}
							}
						}
						$q	= array_unique($q);					}
					if( count($q) > 0 ) {
						$q	= implode(', ', $q);
						$db2->query('INSERT INTO '.($this->api_id==2 ? 'post_userbox_feeds' : 'post_userbox').' (user_id, post_id) VALUES '.$q);
					}
					if( count($q2) > 0 ) {
						$tmpu	= array();
						foreach($q2 as $tmptmp) {
							$tmpu[$tmptmp[0]][$tmptmp[1]]	= 1;
						}
						foreach($tmpu as $tmpuid=>$tmptabs) {
							foreach($tmptabs as $tmptab=>$tmpnum) {
								$this->network->set_dashboard_tabstate($tmpuid, $tmptab, $tmpnum);
							}
						}
					}
					if( $this->user->id > 0 ) {
						$db2->query('UPDATE users SET num_posts=num_posts+1, lastpost_date="'.time().'" WHERE id="'.$db_user_id.'" LIMIT 1');
					}
					if( $this->user->id > 0 && count($this->mentioned) > 0 ) {
						$notify	= array();
						if( !$this->group || $this->group->is_public ) {
							foreach($this->mentioned as $uid) {
								if( $uid == $this->user->id ) {
									continue;
								}
								$notify[]	= $uid;
							}
						}
						else {
							$grpmem	= $this->network->get_group_members($this->group->id);
							foreach($this->mentioned as $uid) {
								if( ! isset($grpmem[$uid]) ) {
									continue;
								}
								if( $uid == $this->user->id ) {
									continue;
								}
								$notify[]	= $uid;
							}
						}
						$notify	= array_unique($notify);
						global $page;
						$permalink	= $C->SITE_URL.'view/post:'.$id;
						$page->load_langfile('email/notifications.php');
						foreach($notify as $uid) {
							$this->network->set_dashboard_tabstate($uid, '@me', 1);
							
							$n	= intval( $this->network->get_user_notif_rules($uid)->ntf_me_if_u_posts_qme );
							if( $n==1 || $n==3 ) {
								$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->user->username, '#NAME#'=>$this->user->fullname, '#A0#'=>$permalink, '#A1#'=>'', '#A2#'=>'');
								$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->username.'" title="'.htmlspecialchars($this->user->fullname).'" target="_blank">@'.$this->user->username.'</a>', '#NAME#'=>$this->user->fullname, '#A1#'=>'<a href="'.$permalink.'" target="_blank">', '#A2#'=>'</a>', '#A0#'=>'');
								$subject		= $page->lang('emlsubj_ntf_me_if_u_posts_qme', $lng_txt);
								$message_txt	= $page->lang('emltxt_ntf_me_if_u_posts_qme', $lng_txt);
								$message_htm	= $page->lang('emlhtml_ntf_me_if_u_posts_qme', $lng_htm);
								$this->network->send_notification_email($uid, 'u_posts_qme', $subject, $message_txt, $message_htm);
							}
						}
					}
					if( $this->group ) {
						$db2->query('UPDATE groups SET num_posts=num_posts+1 WHERE id="'.$db_group_id.'" LIMIT 1');
					}
					if( $this->user->id > 0 ) {
						$db2->query('INSERT INTO '.($is_private?'posts_pr_comments_watch':'posts_comments_watch').' SET user_id="'.$this->user->id.'", post_id="'.$id.'", newcomments=0');
					}
					if( $C->RPC_PINGS_ON && $this->user->id>0 && $this->api_id!=2 && (!$this->group || $this->group->is_public) ) {
						include_once($C->INCPATH.'classes/IXR_Library.inc.php');
						$myBlogName	= $this->user->username.' - '.$C->SITE_TITLE;
						$myBlogUrl	= $C->SITE_URL.$this->user->username;
						$myBlogUpdateUrl	= $C->SITE_URL.$this->user->username;
						$myBlogRSSFeedUrl	= $C->SITE_URL.'rss/username:'.$this->user->username;
						foreach($C->RPC_PINGS_SERVERS as $server) {
							$client	= new IXR_Client($server);
							$client->timeout	= 0;
							$client->useragent	.= ' -- '.$C->SITE_TITLE;
							$client->debug	= false;
							$client->query( 'weblogUpdates.extendedPing', $myBlogName, $myBlogUrl, $myBlogUpdateUrl, $myBlogRSSFeedUrl );
						}
					}
				}
				else {
					$db2->query('UPDATE users SET lastpost_date="'.time().'" WHERE id="'.$db_user_id.'" LIMIT 1');
					$this->network->set_dashboard_tabstate($db_to_user, 'private', 1);
					
					$n	= intval( $this->network->get_user_notif_rules($this->to_user->id)->ntf_me_if_u_posts_prvmsg );
					if( $n==1 || $n==3 ) {
						global $C, $page;
						$page->load_langfile('email/notifications.php');
						$permalink	= $C->SITE_URL.'view/priv:'.$id;
						$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->user->username, '#NAME#'=>$this->user->fullname, '#A0#'=>$permalink, '#A1#'=>'', '#A2#'=>'');
						$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->username.'" title="'.htmlspecialchars($this->user->fullname).'" target="_blank">@'.$this->user->username.'</a>', '#NAME#'=>$this->user->fullname, '#A1#'=>'<a href="'.$permalink.'" target="_blank">', '#A2#'=>'</a>', '#A0#'=>'');
						$subject		= $page->lang('emlsubj_ntf_me_if_u_posts_prvmsg', $lng_txt);
						$message_txt	= $page->lang('emltxt_ntf_me_if_u_posts_prvmsg', $lng_txt);
						$message_htm	= $page->lang('emlhtml_ntf_me_if_u_posts_prvmsg', $lng_htm);
						$this->network->send_notification_email($this->to_user->id, 'u_posts_prvmsg', $subject, $message_txt, $message_htm);
					}
					$db2->query('INSERT INTO '.($is_private?'posts_pr_comments_watch':'posts_comments_watch').' SET user_id="'.$this->user->id.'", post_id="'.$id.'", newcomments=0');
					$db2->query('INSERT INTO '.($is_private?'posts_pr_comments_watch':'posts_comments_watch').' SET user_id="'.$this->to_user->id.'", post_id="'.$id.'", newcomments=0');
				}
				$db2->query('UPDATE applications SET total_posts=total_posts+1 WHERE id="'.$db_api_id.'" LIMIT 1');
			}
			else
			{
				$id	= $this->id;
				$db2->query('UPDATE '.($is_private?'posts_pr':'posts').' SET message="'.$db_message.'", mentioned="'.$db_mentioned.'", attached="'.$db_attached.'", posttags="'.$db_posttags.'", date_lastedit="'.time().'" WHERE id="'.$this->id.'" LIMIT 1');
				$db2->query('DELETE FROM '.($is_private?'posts_pr_attachments':'posts_attachments').' WHERE post_id="'.$this->id.'" ');
				$this->attachments_copy_files();
				foreach($this->attached as $k=>$v) {
					$db2->query('INSERT INTO '.($is_private?'posts_pr_attachments':'posts_attachments').' SET post_id="'.$id.'", type="'.$db2->escape($k).'", data="'.$db2->escape(serialize($v)).'" ');
				}
				$mentioned1	= array();
				$db2->query('SELECT user_id FROM '.($is_private?'posts_pr_mentioned':'posts_mentioned').' WHERE post_id="'.$this->id.'" ');
				while($sdf = $db2->fetch_object()) {
					$mentioned1[]	= intval($sdf->user_id);
				}
				$db2->query('DELETE FROM '.($is_private?'posts_pr_mentioned':'posts_mentioned').' WHERE post_id="'.$this->id.'" ');
				$mentioned2	= array();
				foreach($this->mentioned as $uid) {
					$db2->query('INSERT INTO '.($is_private?'posts_pr_mentioned':'posts_mentioned').' SET post_id="'.$id.'", user_id="'.intval($uid).'" ');
					$mentioned2[]	= intval($uid);
				}
				$new_mentioned	= array_diff($mentioned2, $mentioned1);
				foreach($new_mentioned as $uid) {
					if( $is_private ) {
						continue;
					}
					$this->network->set_dashboard_tabstate($uid, '@me', 1);
					// ...
				}
			}
			
			return $id ? ($id.($is_private?'_private':'_public')) : FALSE;
		}
		
		private function attachments_copy_files()
		{
			global $C;
			$dir	= $C->IMG_DIR.'attachments/'.$this->network->id.'/';
			if( ! is_dir($dir) ) {
				mkdir($dir, 0777);
			}
			foreach($this->attached as &$at) {
				if( !isset($at->in_tmpdir) || !$at->in_tmpdir ) {
					continue;
				}
				foreach($at as $k=>$v) {
					if( substr($k,0,5) != 'file_' ) {
						continue;
					}
					if( empty($v) ) {
						continue;
					}
					rename($C->TMP_DIR.$v, $dir.$v);
					chmod($dir.$v, 0777);
				}
				unset($at->in_tmpdir);
			}
			return TRUE;
		}
		
		public function get_attached()
		{
			return $this->attached;
		}
		public function set_attached($at)
		{
			return $this->attached = $at;
		}
	}
	
?>