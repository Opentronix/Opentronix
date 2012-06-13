<?php
	
	require_once($C->INCPATH.'conf_embed.php');
	
	class newpostcomment
	{
		private $network	= FALSE;
		private $id		= FALSE;
		private $user	= FALSE;
		private $post	= FALSE;
		private $api_id		= 0;
		private $message		= '';
		private $mentioned	= array();
		private $posttags		= 0;
		private $db1;
		private $db2;
		private $cache;
		public $error	= FALSE;
		
		public function __construct($post_obj)
		{
			global $C;
			$this->cache	= & $GLOBALS['cache'];
			$this->db1		= & $GLOBALS['db1'];
			$this->db2		= FALSE;
			$this->post		= & $post_obj;
			if( ! $this->post instanceof post ) {
				$this->error	= TRUE;
				return;
			}
			$this->api_id	= $C->API_ID;
			$n	= & $GLOBALS['network'];
			if( $n->id ) {
				$u	= & $GLOBALS['user'];
				if($u->is_logged && $u->network->id==$n->id) {
					$this->network	= $n;
					$this->user		= $u;
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
		
		public function set_api_id($api_id)
		{
			if( $this->id ) {
				return FALSE;
			}
			$this->api_id	= intval($api_id);
			return TRUE;
		}
		
		public function set_message($message)
		{
			if( empty($message) ) {
				return FALSE;
			}
			global $C;
			$this->message	= trim($message);
			
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
		
		public function save()
		{
			if( $this->error ) {
				return FALSE;
			}
			if( ! $this->user->is_logged ) {
				return FALSE;
			}
			if( empty($this->message) ) {
				return FALSE;
			}
			$db2		= & $this->network->db2;
			$is_private	= $this->post->post_type=='private' ? TRUE : FALSE;
			$db_api_id		= intval($this->api_id);
			$db_user_id		= intval($this->user->id);
			$db_message		= $db2->escape($this->message);
			$db_mentioned	= count($this->mentioned);
			$db_posttags	= intval($this->posttags);
			$db_date		= time();
			$db_ip_addr		= ip2long($_SERVER['REMOTE_ADDR']);
			
			$db2->query('INSERT INTO '.($is_private?'posts_pr_comments':'posts_comments').' SET api_id="'.$db_api_id.'", post_id="'.$this->post->post_id.'", user_id="'.$db_user_id.'", message="'.$db_message.'", mentioned="'.$db_mentioned.'", posttags="'.$db_posttags.'", date="'.$db_date.'", ip_addr="'.$db_ip_addr.'"   ');
			if( ! $id = $db2->insert_id() ) {
				return FALSE;
			}
			$db2->query('UPDATE '.($is_private?'posts_pr':'posts').' SET comments=comments+1, date_lastcomment="'.time().'" WHERE id="'.$this->post->post_id.'" LIMIT 1');
			
			foreach($this->mentioned as $uid) {
				$db2->query('INSERT INTO '.($is_private?'posts_pr_comments_mentioned':'posts_comments_mentioned').' SET comment_id="'.$id.'", user_id="'.intval($uid).'" ');
			}
			
			$db2->query('SELECT id, newcomments FROM '.($is_private?'posts_pr_comments_watch':'posts_comments_watch').' WHERE user_id="'.$this->user->id.'" AND post_id="'.$this->post->post_id.'" LIMIT 1');
			if( $sdf = $db2->fetch_object() ) {
				if( $sdf->newcomments <> 0 ) {
					$db2->query('UPDATE '.($is_private?'posts_pr_comments_watch':'posts_comments_watch').' SET newcomments=0 WHERE id="'.$sdf->id.'" LIMIT 1');
				}
			}
			else {
				$db2->query('INSERT INTO '.($is_private?'posts_pr_comments_watch':'posts_comments_watch').' SET user_id="'.$this->user->id.'", post_id="'.$this->post->post_id.'", newcomments=0');
			}
			if( ! $is_private ) {
				$db2->query('SELECT user_id FROM '.($is_private?'posts_pr_comments_watch':'posts_comments_watch').' WHERE user_id<>"'.$this->user->id.'" AND post_id="'.$this->post->post_id.'" ');
				while( $sdf = $db2->fetch_object() ) {
					$this->network->set_dashboard_tabstate($sdf->user_id, 'commented', 1);
				}
				//foreach($this->mentioned as $uid) {
				//	За тези хора трябва да се вкарва ред в posts_pr_comments_watch (ако постът е в достъпна за тях група)
				//	Също така да се известяват по e-mail за @user
				//}
			}
			$db2->query('UPDATE '.($is_private?'posts_pr_comments_watch':'posts_comments_watch').' SET newcomments=newcomments+1 WHERE user_id<>"'.$this->user->id.'" AND post_id="'.$this->post->post_id.'" ');
			
			if( $is_private ) {
				$db2->query('UPDATE posts_pr SET is_recp_del=0 WHERE id="'.$this->post->post_id.'" LIMIT 1');
				$uid	= $this->post->post_to_user->id;
				if( $uid == $this->user->id ) {
					$uid	= $this->post->post_user->id;
				}
				$this->network->set_dashboard_tabstate($uid, 'private', 1);
				
				$n	= intval( $this->network->get_user_notif_rules($uid)->ntf_me_if_u_posts_prvmsg );
				if( $n==1 || $n==3 ) {
					global $C, $page;
					$page->load_langfile('email/notifications.php');
					$permalink	= $this->post->permalink.'#comments';
					$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->user->info->username, '#NAME#'=>$this->user->info->fullname, '#A0#'=>$permalink, '#A1#'=>'', '#A2#'=>'');
					$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'" target="_blank">@'.$this->user->info->username.'</a>', '#NAME#'=>$this->user->info->fullname, '#A1#'=>'<a href="'.$permalink.'" target="_blank">', '#A2#'=>'</a>', '#A0#'=>'');
					$subject		= $page->lang('emlsubj_ntf_me_if_u_posts_prvmsg', $lng_txt);
					$message_txt	= $page->lang('emltxt_ntf_me_if_u_posts_prvmsg', $lng_txt)."\n\n \"".$this->message.'"';
					$message_htm	= $page->lang('emlhtml_ntf_me_if_u_posts_prvmsg', $lng_htm).'<br /><br /> "'.$this->message.'"';
					$this->network->send_notification_email($uid, 'u_posts_prvmsg', $subject, $message_txt, $message_htm);
				}
			}
			else {
				$n	= intval( $this->network->get_user_notif_rules($this->id)->ntf_them_if_i_comment );
				if( $n == 1 ) {
					global $C, $page;
					$page->load_langfile('email/notifications.php');
					$notify	= array();
					if( $this->post->post_user->id != $this->user->id && $this->post->post_user->id > 0 ) {
						$n	= intval( $this->network->get_user_notif_rules($this->post->post_user->id)->ntf_me_if_u_commments_me );
						if( $n != 0 ) {
							$notify[$this->post->post_user->id]	= $n;
						}
					}
					foreach($this->post->post_comments as $c) {
						if( $c->comment_user->id == $this->user->id ) {
							continue;
						}
						$n	= intval( $this->network->get_user_notif_rules($c->comment_user->id)->ntf_me_if_u_commments_m2 );
						if( isset($notify[$c->comment_user->id]) ) {
							if( $n==1 || ($n==2 && $notify[$c->comment_user->id]==3) || ($n==3 && $notify[$c->comment_user->id]==2) ) {
								$notify[$c->comment_user->id]	= 1;
								continue;
							}
						}
						if( $n != 0 ) {
							$notify[$c->comment_user->id]	= $n;
						}
					}
					foreach($notify as $uid=>$n) {
						$notifkey	= '';
						if( $this->user->id==$this->post->post_user->id ) {
							$notifkey	= count($this->post->post_comments)==0 ? 'u_commments_m2' : 'u_commments_m20';
						}
						elseif( $uid == $this->post->post_user->id ) {
							$notifkey	= count($this->post->post_comments)==0 ? 'u_commments_me' : 'u_commments_me2';
						}
						else {
							$notifkey	= count($this->post->post_comments)==0 ? 'u_commments_m3' : 'u_commments_m32';
						}
						if( $n == 3 || $n == 1 ) {
							if( $this->post->post_user->id == 0 && $this->post->post_group ) {
								$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->user->info->username, '#NAME#'=>$this->user->info->fullname, '#USER2#'=>$this->post->post_group->title, '#NAME2#'=>$this->post->post_group->title, '#A0#'=>$this->post->permalink.'#comments');
								$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'" target="_blank">@'.$this->user->info->username.'</a>', '#NAME#'=>$this->user->info->fullname, '#USER2#'=>'<a href="'.$C->SITE_URL.$this->post->post_group->groupname.'" title="'.htmlspecialchars($this->post->post_group->title).'" target="_blank">@'.$this->post->post_group->title.'</a>', '#NAME2#'=>$this->post->post_group->title, '#A1#'=>'<a href="'.$this->post->permalink.'#comments" target="_blank">', '#A2#'=>'</a>');
							}
							else {
								$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->user->info->username, '#NAME#'=>$this->user->info->fullname, '#USER2#'=>'@'.$this->post->post_user->username, '#NAME2#'=>$this->post->post_user->fullname, '#A0#'=>$this->post->permalink.'#comments');
								$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->user->info->username.'" title="'.htmlspecialchars($this->user->info->fullname).'" target="_blank">@'.$this->user->info->username.'</a>', '#NAME#'=>$this->user->info->fullname, '#USER2#'=>'<a href="'.$C->SITE_URL.$this->post->post_user->username.'" title="'.htmlspecialchars($this->post->post_user->fullname).'" target="_blank">@'.$this->post->post_user->username.'</a>', '#NAME2#'=>$this->post->post_user->fullname, '#A1#'=>'<a href="'.$this->post->permalink.'#comments" target="_blank">', '#A2#'=>'</a>');
							}
							$subject		= $page->lang('emlsubj_ntf_me_if_'.$notifkey, $lng_txt);
							$message_txt	= $page->lang('emltxt_ntf_me_if_'.$notifkey, $lng_txt)."\n\n \"".$this->message.'"';
							$message_htm	= $page->lang('emlhtml_ntf_me_if_'.$notifkey, $lng_htm).'<br /><br /> "'.$this->message.'"';
							$this->network->send_notification_email($uid, $notifkey, $subject, $message_txt, $message_htm);
						}	
					}
				}
			}
			return $id;
		}
	}
	
?>
