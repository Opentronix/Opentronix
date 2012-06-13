<?php
	
	class user
	{
		public $id;
		public $network;
		public $is_logged;
		public $info;
		public $sess;
		
		public function __construct()
		{
			$this->id	= FALSE;
			$this->network	= & $GLOBALS['network'];
			$this->cache	= & $GLOBALS['cache'];
			$this->db1		= & $GLOBALS['db1'];
			$this->db2		= & $GLOBALS['db2'];
			$this->info		= new stdClass;
			$this->is_logged	= FALSE;
			$this->sess		= array();
		}
		
		public function LOAD()
		{
			if( ! $this->network->id ) {
				return FALSE;
			}
			global $C;
			$this->_session_start();
			if( isset($this->sess['IS_LOGGED'], $this->sess['LOGGED_USER']) && $this->sess['IS_LOGGED'] && $this->sess['LOGGED_USER'] ) {
				$u	= & $this->sess['LOGGED_USER'];
				$u	= $this->network->get_user_by_id($u->id);
				if( ! $u ) {
					return FALSE;
				}
				if( $this->network->id && $this->network->id == $u->network_id ) {
					$this->is_logged	= TRUE;
					$this->info	= & $u;
					$this->id	= $this->info->id;
					$this->db2->query('UPDATE users SET lastclick_date="'.time().'" WHERE id="'.$this->id.'" LIMIT 1');
					$deflang	= $C->LANGUAGE;
					if( ! empty($this->info->language) ) {
						$C->LANGUAGE	= $this->info->language;
					}
					if( $C->LANGUAGE != $deflang ) {
						$current_language	= new stdClass;
						include($C->INCPATH.'languages/'.$C->LANGUAGE.'/language.php');
						date_default_timezone_set($current_language->php_timezone);
						setlocale(LC_ALL, $current_language->php_locale);
					}
					if( ! empty($this->info->timezone) ) {
						date_default_timezone_set($this->info->timezone);
					}
					if( $this->info->active == 0 ) {
						$this->logout();
						return FALSE;
					}
					return $this->id;
				}
			}
			if( $this->try_autologin() ) {
				$this->LOAD();
			}
			return FALSE;
		}
		
		private function _session_start()
		{
			if( ! $this->network->id ) {
				return FALSE;
			}
			if( ! isset($_SESSION['NETWORKS_USR_DATA']) ) {
				$_SESSION['NETWORKS_USR_DATA']	= array();
			}
			if( ! isset($_SESSION['NETWORKS_USR_DATA'][$this->network->id]) ) {
				$_SESSION['NETWORKS_USR_DATA'][$this->network->id]	= array();
			}
			$this->sess	= & $_SESSION['NETWORKS_USR_DATA'][$this->network->id];
		}
		
		public function login($login, $pass, $rememberme=FALSE)
		{
			global $C;
			if( ! $this->network->id ) {
				return FALSE;
			}
			if( $this->is_logged ) {
				return FALSE;
			}
			if( empty($login) ) {
				return FALSE;
			}
			$login	= $this->db2->escape($login);
			$pass		= $this->db2->escape($pass);
			$this->db2->query('SELECT id FROM users WHERE (email="'.$login.'" OR username="'.$login.'") AND password="'.$pass.'" AND active=1 LIMIT 1');
			if( ! $obj = $this->db2->fetch_object() ) {
				return FALSE;
			}
			$this->info	= $this->network->get_user_by_id($obj->id, TRUE);
			if( ! $this->info ) {
				return FALSE;
			}
			$this->is_logged		= TRUE;
			$this->sess['IS_LOGGED']	= TRUE;
			$this->sess['LOGGED_USER']	= & $this->info;
			$this->id	= $this->info->id;
			
			$ip	= $this->db2->escape( ip2long($_SERVER['REMOTE_ADDR']) );
			$this->db2->query('UPDATE users SET lastlogin_date="'.time().'", lastlogin_ip="'.$ip.'", lastclick_date="'.time().'" WHERE id="'.$this->id.'" LIMIT 1');
			if( TRUE == $rememberme ) {
				$tmp	= $this->id.'_'.md5($this->info->username.'~~'.$this->info->password.'~~'.$_SERVER['HTTP_USER_AGENT']);
				setcookie('rememberme', $tmp, time()+60*24*60*60, '/', cookie_domain());
			}
			
			$this->sess['total_pageviews']	= 0;
			$this->sess['cdetails']	= $this->db2->fetch('SELECT * FROM users_details WHERE user_id="'.$this->id.'" LIMIT 1');
			return TRUE;
		}
		
		public function try_autologin()
		{
			if( ! $this->network->id ) {
				return FALSE;
			}
			if( $this->is_logged ) {
				return FALSE;
			}
			if( ! isset($_COOKIE['rememberme']) ) {
				return FALSE;
			}
			$tmp	= explode('_', $_COOKIE['rememberme']);
			$this->db2->query('SELECT username, password, email FROM users WHERE id="'.intval($tmp[0]).'" AND active=1 LIMIT 1');
			if( ! $obj = $this->db2->fetch_object() ) {
				return FALSE;
			}
			$obj->username	= stripslashes($obj->username);
			$obj->password	= stripslashes($obj->password);
			if( $tmp[1] == md5($obj->username.'~~'.$obj->password.'~~'.$_SERVER['HTTP_USER_AGENT']) ) {
				return $this->login($obj->username, $obj->password, TRUE);
			}
			setcookie('rememberme', NULL, time()+30*24*60*60, '/', cookie_domain());
			$_COOKIE['rememberme']	= NULL;
			return FALSE;
		}
		
		public function logout()
		{
			if( ! $this->is_logged ) {
				return FALSE;
			}
			setcookie('rememberme', NULL, time()+60*24*60*60, '/', cookie_domain());
			$_COOKIE['rememberme']	= NULL;
			$this->sess['IS_LOGGED']	= FALSE;
			$this->sess['LOGGED_USER']	= NULL;
			unset($this->sess['IS_LOGGED']);
			unset($this->sess['LOGGED_USER']);
			$this->id	= FALSE;
			$this->info	= new stdClass;
			$this->is_logged	= FALSE;
			$_SESSION['TWITTER_CONNECTED']	= FALSE;
		}
		
		public function follow($whom_id, $how=TRUE)
		{
			if( ! $this->is_logged ) {
				return FALSE;
			}
			$whom	= $this->network->get_user_by_id($whom_id);
			if( ! $whom ) {
				return FALSE;
			}
			$f	= $this->network->get_user_follows($this->id)->follow_users;
			if( isset($f[$whom_id]) && $how==TRUE ) {
				return TRUE;
			}
			if( !isset($f[$whom_id]) && $how==FALSE ) {
				return TRUE;
			}
			if( $how == TRUE ) {
				$this->db2->query('INSERT INTO users_followed SET who="'.$this->id.'", whom="'.$whom_id.'", date="'.time().'", whom_from_postid="'.$this->network->get_last_post_id().'" ');
				$this->db2->query('UPDATE users SET num_followers=num_followers+1 WHERE id="'.$whom_id.'" LIMIT 1');
				
				$n	= intval( $this->network->get_user_notif_rules($this->id)->ntf_them_if_i_follow_usr );
				if( $n == 1 ) {
					global $C, $page;
					$page->load_langfile('inside/notifications.php');
					$page->load_langfile('email/notifications.php');
					$send_post	= TRUE;
					$send_mail	= FALSE;
					$n	= intval( $this->network->get_user_notif_rules($whom_id)->ntf_me_if_u_follows_me );
					if( $n == 2 ) { $send_post = TRUE; } elseif( $n == 3 ) { $send_mail = TRUE; } elseif( $n == 1 ) { $send_post = TRUE; $send_mail = TRUE; }
					if( $send_post ) {
						$lng	= array('#USER#'=>'<a href="'.$C->SITE_URL.$this->info->username.'" title="'.htmlspecialchars($this->info->fullname).'"><span class="mpost_mentioned">@</span>'.$this->info->username.'</a>');
						$this->network->send_notification_post($whom_id, 0, 'msg_ntf_me_if_u_follows_me', $lng, 'replace');
					}
					if( $send_mail ) {
						$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->info->username, '#NAME#'=>$this->info->fullname, '#A0#'=>$C->SITE_URL.$this->info->username);
						$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->info->username.'" title="'.htmlspecialchars($this->info->fullname).'" target="_blank">@'.$this->info->username.'</a>', '#NAME#'=>$this->info->fullname);
						$subject		= $page->lang('emlsubj_ntf_me_if_u_follows_me', $lng_txt);
						$message_txt	= $page->lang('emltxt_ntf_me_if_u_follows_me', $lng_txt);
						$message_htm	= $page->lang('emlhtml_ntf_me_if_u_follows_me', $lng_htm);
						$this->network->send_notification_email($whom_id, 'u_follows_me', $subject, $message_txt, $message_htm);
					}
					$followers	= array_keys($this->network->get_user_follows($this->id)->followers);
					foreach($followers as $uid) {
						if( $uid == $whom_id ) { continue; }
						$send_post	= FALSE;
						$send_mail	= FALSE;
						$n	= intval( $this->network->get_user_notif_rules($uid)->ntf_me_if_u_follows_u2 );
						if( $n == 2 ) { $send_post = TRUE; } elseif( $n == 3 ) { $send_mail = TRUE; } elseif( $n == 1 ) { $send_post = TRUE; $send_mail = TRUE; }
						if( $send_post ) {
							$lng	= array('#USER#'=>'<a href="'.$C->SITE_URL.$this->info->username.'" title="'.htmlspecialchars($this->info->fullname).'"><span class="mpost_mentioned">@</span>'.$this->info->username.'</a>', '#USER2#'=>'<a href="'.$C->SITE_URL.$whom->username.'" title="'.htmlspecialchars($whom->fullname).'"><span class="mpost_mentioned">@</span>'.$whom->username.'</a>');
							$this->network->send_notification_post($uid, 0, 'msg_ntf_me_if_u_follows_u2', $lng, 'replace');
						}
						if( $send_mail ) {
							$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->info->username, '#NAME#'=>$this->info->fullname, '#A0#'=>$C->SITE_URL.$this->info->username, '#USER2#'=>$whom->username, '#NAME2#'=>$whom->fullname);
							$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->info->username.'" title="'.htmlspecialchars($this->info->fullname).'" target="_blank">@'.$this->info->username.'</a>', '#NAME#'=>$this->info->fullname, '#USER2#'=>'<a href="'.$C->SITE_URL.$whom->username.'" title="'.htmlspecialchars($whom->fullname).'" target="_blank">@'.$whom->username.'</a>', '#NAME2#'=>$whom->fullname);
							$subject		= $page->lang('emlsubj_ntf_me_if_u_follows_u2', $lng_txt);
							$message_txt	= $page->lang('emltxt_ntf_me_if_u_follows_u2', $lng_txt);
							$message_htm	= $page->lang('emlhtml_ntf_me_if_u_follows_u2', $lng_htm);
							$this->network->send_notification_email($uid, 'u_follows_u2', $subject, $message_txt, $message_htm);
						}
					}
				}
			}
			else {
				$this->db2->query('DELETE FROM users_followed WHERE who="'.$this->id.'" AND whom="'.$whom_id.'" ');
				$this->db2->query('UPDATE users SET num_followers=num_followers-1 WHERE id="'.$whom_id.'" LIMIT 1');
				$this->db2->query('DELETE FROM post_userbox WHERE user_id="'.$this->id.'" AND post_id IN(SELECT id FROM posts WHERE user_id="'.$whom_id.'")');
				$this->db2->query('DELETE FROM post_userbox_feeds WHERE user_id="'.$this->id.'" AND post_id IN(SELECT id FROM posts WHERE user_id="'.$whom_id.'")');
			}
			$this->network->get_user_by_id($whom_id, TRUE);
			$this->network->get_user_follows($whom_id, TRUE);
			$this->network->get_user_follows($this->id, TRUE);
			return TRUE;
		}
		
		public function follow_group($group_id, $how=TRUE)
		{
			if( ! $this->is_logged ) {
				return FALSE;
			}
			$group	= $this->network->get_group_by_id($group_id);
			if( ! $group ) {
				return FALSE;
			}
			$priv_members	= array();
			if( $group->is_private && !$this->info->is_network_admin ) {
				$priv_members	= $this->network->get_group_invited_members($group_id);
				if( ! $priv_members ) {
					return FALSE;
				}
				if( ! in_array(intval($this->id), $priv_members) ) {
					return FALSE;
				}
			}
			$f	= $this->network->get_user_follows($this->id)->follow_groups;
			if( isset($f[$group_id]) && $how==TRUE ) {
				return TRUE;
			}
			if( !isset($f[$group_id]) && $how==FALSE ) {
				return TRUE;
			}
			if( $how == TRUE ) {
				$this->db2->query('INSERT INTO groups_followed SET user_id="'.$this->id.'", group_id="'.$group_id.'", date="'.time().'", group_from_postid="'.$this->network->get_last_post_id().'" ');
				$this->db2->query('UPDATE groups SET num_followers=num_followers+1 WHERE id="'.$group_id.'" LIMIT 1');
				$n	= intval( $this->network->get_user_notif_rules($this->id)->ntf_them_if_i_join_grp );
				if( $n == 1 ) {
					global $C, $page;
					$page->load_langfile('inside/notifications.php');
					$page->load_langfile('email/notifications.php');
					$followers	= array_keys($this->network->get_user_follows($this->id)->followers);
					foreach($followers as $uid) {
						$uid	= intval($uid);
						if( $group->is_private && !in_array($uid, $priv_members) ) {
							continue;
						}
						$send_post	= FALSE;
						$send_mail	= FALSE;
						$n	= intval( $this->network->get_user_notif_rules($uid)->ntf_me_if_u_joins_grp );
						if( $n == 2 ) { $send_post = TRUE; } elseif( $n == 3 ) { $send_mail = TRUE; } elseif( $n == 1 ) { $send_post = TRUE; $send_mail = TRUE; }
						if( $send_post ) {
							$lng	= array('#USER#'=>'<a href="'.$C->SITE_URL.$this->info->username.'" title="'.htmlspecialchars($this->info->fullname).'"><span class="mpost_mentioned">@</span>'.$this->info->username.'</a>', '#GROUP#'=>'<a href="'.$C->SITE_URL.$group->groupname.'" title="'.$group->title.'">'.$group->title.'</a>');
							$this->network->send_notification_post($uid, 0, 'msg_ntf_me_if_u_joins_grp', $lng, 'replace');
						}
						if( $send_mail ) {
							$lng_txt	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'@'.$this->info->username, '#NAME#'=>$this->info->fullname, '#GROUP#'=>$group->title, '#A0#'=>$C->SITE_URL.$group->groupname);
							$lng_htm	= array('#SITE_TITLE#'=>$C->SITE_TITLE, '#USER#'=>'<a href="'.$C->SITE_URL.$this->info->username.'" title="'.htmlspecialchars($this->info->fullname).'" target="_blank">@'.$this->info->username.'</a>', '#NAME#'=>$this->info->fullname, '#GROUP#'=>'<a href="'.$C->SITE_URL.$group->groupname.'" title="'.$group->title.'" target="_blank">'.$group->title.'</a>');
							$subject		= $page->lang('emlsubj_ntf_me_if_u_joins_grp', $lng_txt);
							$message_txt	= $page->lang('emltxt_ntf_me_if_u_joins_grp', $lng_txt);
							$message_htm	= $page->lang('emlhtml_ntf_me_if_u_joins_grp', $lng_htm);
							$this->network->send_notification_email($uid, 'u_joins_grp', $subject, $message_txt, $message_htm);
						}
					}
					$lng	= array('#USER#'=>'<a href="'.$C->SITE_URL.$this->info->username.'" title="'.htmlspecialchars($this->info->fullname).'"><span class="mpost_mentioned">@</span>'.$this->info->username.'</a>', '#GROUP#'=>'<a href="'.$C->SITE_URL.$group->groupname.'" title="'.$group->title.'">'.$group->title.'</a>');
					$this->network->send_notification_post(0, $group_id, 'msg_ntf_grp_if_u_joins', $lng, 'replace');
				}
			}
			else {
				if( ! $this->if_can_leave_group($group_id) ) {
					return FALSE;
				}
				$this->db2->query('DELETE FROM groups_admins WHERE user_id="'.$this->id.'" AND group_id="'.$group_id.'" ');
				$this->db2->query('DELETE FROM groups_followed WHERE user_id="'.$this->id.'" AND group_id="'.$group_id.'" ');
				$this->db2->query('UPDATE groups SET num_followers=num_followers-1 WHERE id="'.$group_id.'" LIMIT 1');
				$not_in_users	= array_keys($this->network->get_user_follows($this->id)->follow_users);
				$not_in_users	= count($not_in_users)==0 ? '' : 'AND user_id NOT IN('.implode(', ', $not_in_users).')';
				$this->db2->query('DELETE FROM post_userbox WHERE user_id="'.$this->id.'" AND post_id IN(SELECT id FROM posts WHERE group_id="'.$group_id.'" AND user_id<>"'.$this->id.'" '.$not_in_users.' )');
				$this->db2->query('DELETE FROM post_userbox_feeds WHERE user_id="'.$this->id.'" AND post_id IN(SELECT id FROM posts WHERE group_id="'.$group_id.'" AND user_id<>"'.$this->id.'" '.$not_in_users.' )');
			}
			$this->network->get_group_by_id($group_id, TRUE);
			$this->network->get_group_members($group_id, TRUE);
			$this->network->get_user_follows($this->id, TRUE);
			$this->get_top_groups(1, TRUE);
			return TRUE;
		}
		
		public function if_follow_user($user_id)
		{
			if( ! $this->is_logged ) {
				return FALSE;
			}
			return isset($this->network->get_user_follows($this->id)->follow_users[$user_id]);
		}
		
		public function if_follow_group($group_id)
		{
			if( ! $this->is_logged ) {
				return FALSE;
			}
			return isset($this->network->get_user_follows($this->id)->follow_groups[$group_id]);
		}
		
		public function if_can_leave_group($group_id)
		{
			if( ! $this->is_logged ) {
				return FALSE;
			}
			static $loaded=array();
			if( ! isset($loaded[$group_id]) ) {
				$r	= $this->db2->query('SELECT user_id FROM groups_admins WHERE group_id="'.intval($group_id).'" AND user_id<>"'.$this->id.'" LIMIT 1', FALSE);
				$loaded[$group_id]	= $this->db2->num_rows($r)>0;	// демек ако има други админи освен мен, мога да куитна
			}
			return $loaded[$group_id];
		}
		
		public function get_top_groups($num, $force_refresh=FALSE)
		{
			static $loaded=FALSE;
			if( !$loaded || $force_refresh ) {
				$loaded	= array();
				$tmp	= $this->network->get_user_follows($this->id)->follow_groups;
				foreach($tmp as $gid=>$sdf) {
					$g	= $this->network->get_group_by_id($gid, $force_refresh);
					if( ! $g ) {
						continue;
					}
					$loaded[]	= $g;
				}
				$loaded	= array_reverse($loaded);
			}
			return array_slice($loaded, 0, $num);
		}
		
		public function write_pageview()
		{
			if( ! $this->is_logged ) {
				return FALSE;
			}
			$this->sess['total_pageviews']	++;
			$dt	= date('Y-m-d H');
			$this->db2->query('UPDATE users_pageviews SET pageviews=pageviews+1 WHERE user_id="'.$this->id.'" AND date="'.$dt.'" LIMIT 1');
			if( $this->db2->affected_rows() == 0 ) {
				$this->db2->query('INSERT INTO users_pageviews SET pageviews=1, user_id="'.$this->id.'", date="'.$dt.'" ');
			}
		}
	}
	
?>