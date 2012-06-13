<?php
	
	class network
	{
		public $id;
		public $info;
		public $is_private;
		public $is_public;
		
		public function __construct()
		{
			$this->id	= FALSE;
			$this->C	= new stdClass;
			$this->info	= new stdClass;
			$this->cache	= & $GLOBALS['cache'];
			$this->db1		= & $GLOBALS['db1'];
			$this->db2		= & $GLOBALS['db2'];
		}
		
		public function LOAD()
		{
			if( $this->id ) {
				return FALSE;
			}
			$this->load_network_settings();
			$this->info	= (object) array(
				'id'	=> 1,
			);
			$this->is_private	= FALSE;
			$this->is_public	= TRUE;
			$this->id	= $this->info->id;
			return $this->id;
		}
		
		public function load_network_settings()
		{
			$db	= &$this->db1;
			$r	= $db->query('SELECT * FROM settings', FALSE);
			while($obj = $db->fetch_object($r)) {
				$this->C->{$obj->word}	= stripslashes($obj->value);
			}
			
			global $C;
			foreach($this->C as $k=>$v) {
				$C->$k	= & $this->C->$k;
			}
			if( ! isset($C->ATTACH_LINK_DISABLED) ) { $C->ATTACH_LINK_DISABLED = 0; }
			if( ! isset($C->ATTACH_FILE_DISABLED) ) { $C->ATTACH_FILE_DISABLED = 0; }
			if( ! isset($C->ATTACH_IMAGE_DISABLED) ) { $C->ATTACH_IMAGE_DISABLED = 0; }
			if( ! isset($C->ATTACH_VIDEO_DISABLED) ) { $C->ATTACH_VIDEO_DISABLED = 0; }
			if( ! isset($C->HDR_SHOW_COMPANY) ) { $C->HDR_SHOW_COMPANY = 1; }
			if( ! isset($C->HDR_SHOW_LOGO) ) { $C->HDR_SHOW_LOGO = 1; }
			if( ! isset($C->HDR_CUSTOM_LOGO) ) { $C->HDR_CUSTOM_LOGO = ''; }
			if( ! isset($C->HDR_SHOW_FAVICON) ) { $C->HDR_SHOW_FAVICON = 1; }
			if( ! isset($C->HDR_CUSTOM_FAVICON) ) { $C->HDR_CUSTOM_FAVICON = ''; }
			if( ! isset($C->MOBI_DISABLED) ) { $C->MOBI_DISABLED = 0; }
			
			$current_language	= new stdClass;
			include($C->INCPATH.'languages/'.$C->LANGUAGE.'/language.php');
			setlocale(LC_ALL, $current_language->php_locale);
			
			if( ! isset($C->DEF_TIMEZONE) ) {
				$C->DEF_TIMEZONE	= $current_language->php_timezone;
			}
			date_default_timezone_set($C->DEF_TIMEZONE);
			
			if( !isset($C->SITE_TITLE) || empty($C->SITE_TITLE) ) {
				$C->SITE_TITLE	= 'Opentronix';
			}
			$C->OUTSIDE_SITE_TITLE	= $C->SITE_TITLE;
		}
		
		public function get_user_by_username($uname, $force_refresh=FALSE, $return_id=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			if( empty($uname) ) {
				return FALSE;
			}
			$cachekey	= 'n:'.$this->id.',username:'.strtolower($uname);
			$uid	= $this->cache->get($cachekey);
			if( FALSE!==$uid && TRUE!=$force_refresh ) {
				return $return_id ? $uid : $this->get_user_by_id($uid);
			}
			$uid	= FALSE;
			$r	= $this->db2->query('SELECT id FROM users WHERE username="'.$this->db2->escape($uname).'" AND active=1 LIMIT 1', FALSE);
			if( $o = $this->db2->fetch_object($r) ) {
				$uid	= intval($o->id);
				$this->cache->set($cachekey, $uid, $GLOBALS['C']->CACHE_EXPIRE);
				return $return_id ? $uid : $this->get_user_by_id($uid);
			}
			$this->cache->del($cachekey);
			return FALSE;
		}
		
		public function get_user_by_email($email, $force_refresh=FALSE, $return_id=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			if( ! is_valid_email($email) ) {
				return FALSE;
			}
			$cachekey	= 'n:'.$this->id.',usermail:'.strtolower($email);
			$uid	= $this->cache->get($cachekey);
			if( FALSE!==$uid && TRUE!=$force_refresh ) {
				return $return_id ? $uid : $this->get_user_by_id($uid);
			}
			$uid	= FALSE;
			$r	= $this->db2->query('SELECT id FROM users WHERE email="'.$this->db2->escape($email).'" AND active=1 LIMIT 1', FALSE);
			if( $o = $this->db2->fetch_object($r) ) {
				$uid	= intval($o->id);
				$this->cache->set($cachekey, $uid, $GLOBALS['C']->CACHE_EXPIRE);
				return $return_id ? $uid : $this->get_user_by_id($uid);
			}
			$this->cache->del($cachekey);
			return FALSE;
		}
		
		public function get_user_by_id($uid, $force_refresh=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			$uid	= intval($uid);
			if( 0 == $uid ) {
				return FALSE;
			}
			static $loaded = array();
			$cachekey	= 'n:'.$this->id.',userid:'.$uid;
			if( isset($loaded[$cachekey]) && TRUE!=$force_refresh ) {
				return $loaded[$cachekey];
			}
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				$loaded[$cachekey] = $data;
				return $data;
			}
			$r	= $this->db2->query('SELECT * FROM users WHERE id="'.$uid.'" LIMIT 1', FALSE);
			if($o = $this->db2->fetch_object($r)) {
				$o->active		= intval($o->active);
				$o->fullname	= stripslashes($o->fullname);
				$o->about_me	= stripslashes($o->about_me);
				$o->tags		= trim(stripslashes($o->tags));
				$o->tags		= empty($o->tags) ? array() : explode(', ', $o->tags);
				if( empty($o->avatar) ) {
					$o->avatar	= $GLOBALS['C']->DEF_AVATAR_USER;
				}
				$o->age	= '';
				$bd_day	= intval( substr($o->birthdate, 8, 2) );
				$bd_month	= intval( substr($o->birthdate, 5, 2) );
				$bd_year	= intval( substr($o->birthdate, 0, 4) );
				if( $bd_day>0 && $bd_month>0 && $bd_year>0 ) {
					if( date('Y') > $bd_year ) {
						$o->age	= date('Y') - $bd_year;
						if( $bd_month>date('m') || ($bd_month==date('m') && $bd_day>date('d')) ) {
							$o->age	--;
						}
					}
				}
				$o->position	= stripslashes($o->position);
				$o->location	= stripslashes($o->location);
				$o->network_id	= $this->id;
				$this->cache->set($cachekey, $o, $GLOBALS['C']->CACHE_EXPIRE);
				$loaded[$cachekey] = $o;
				return $o;
			}
			$this->cache->del($cachekey);
			return FALSE;
		}
		
		public function get_user_follows($uid, $force_refresh=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			$uid	= intval($uid);
			if( 0 == $uid ) {
				return FALSE;
			}
			static $loaded = array();
			$cachekey	= 'n:'.$this->id.',userfollows:'.$uid;
			if( isset($loaded[$cachekey]) && TRUE!=$force_refresh ) {
				return $loaded[$cachekey];
			}
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				$loaded[$cachekey] = $data;
				return $data;
			}
			$data	= new stdClass;
			$data->followers		= array();
			$data->follow_users	= array();
			$data->follow_groups	= array();
			$r	= $this->db2->query('SELECT who, whom_from_postid FROM users_followed WHERE whom="'.$uid.'" ORDER BY id DESC', FALSE);
			while($o = $this->db2->fetch_object($r)) {
				$data->followers[$o->who]	= $o->whom_from_postid;
			}
			$r	= $this->db2->query('SELECT whom, whom_from_postid FROM users_followed WHERE who="'.$uid.'" ORDER BY id DESC', FALSE);
			while($o = $this->db2->fetch_object($r)) {
				$data->follow_users[$o->whom]	= $o->whom_from_postid;
			}
			$r	= $this->db2->query('SELECT group_id, group_from_postid FROM groups_followed WHERE user_id="'.$uid.'" ORDER BY id DESC', FALSE);
			while($o = $this->db2->fetch_object($r)) {
				$data->follow_groups[$o->group_id]	= $o->group_from_postid;
			}
			$this->cache->set($cachekey, $data, $GLOBALS['C']->CACHE_EXPIRE);
			$loaded[$cachekey] = $data;
			return $data;
		}
		
		public function get_mostactive_users($force_refresh=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			$cachekey	= 'n:'.$this->id.',mostactive_userz';
			$data	= $cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				return $data;
			}
			$data	= array();
			$days	= 5;
			$num	= 20;
			$this->db2->query('SELECT user_id, COUNT(id) AS c FROM posts WHERE date>"'.(time()-$days*24*60*60).'" AND api_id<>2 GROUP BY user_id ORDER BY c DESC LIMIT '.$num);
			while($obj = $this->db2->fetch_object()) {
				$data[]	= intval($obj->user_id);
			}
			$this->cache->set($cachekey, $data, $GLOBALS['C']->CACHE_EXPIRE);
			return $data;
		}
		
		public function get_latest_users($force_refresh=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			$cachekey	= 'n:'.$this->id.',latest_userz';
			$data	= $cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				return $data;
			}
			$data	= array();
			$num	= 20;
			$this->db2->query('SELECT id FROM users WHERE active=1 ORDER BY id DESC LIMIT '.$num);
			while($obj = $this->db2->fetch_object()) {
				$data[]	= intval($obj->id);
			}
			$this->cache->set($cachekey, $data, $GLOBALS['C']->CACHE_EXPIRE);
			return $data;
		}
		
		public function get_online_users($force_refresh=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			$cachekey	= 'n:'.$this->id.',online_userz';
			$data	= $cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				return $data;
			}
			$data	= array();
			$num	= 20;
			$this->db2->query('SELECT id, lastclick_date FROM users WHERE active=1 ORDER BY lastclick_date DESC LIMIT '.$num);
			while($obj = $this->db2->fetch_object()) {
				if( $obj->lastclick_date < time()-30*60 ) {
					break;
				}
				$data[]	= intval($obj->id);
			}
			$this->cache->set($cachekey, $data, $GLOBALS['C']->CACHE_EXPIRE);
			return $data;
		}
		
		public function get_group_by_name($gname, $force_refresh=FALSE, $return_id=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			if( empty($gname) ) {
				return FALSE;
			}
			$cachekey	= 'n:'.$this->id.',groupname:'.strtolower($gname);
			$gid	= $this->cache->get($cachekey);
			if( FALSE!==$gid && TRUE!=$gid ) {
				return $return_id ? $gid : $this->get_group_by_id($gid);
			}
			$gid	= FALSE;
			$r	= $this->db2->query('SELECT id FROM groups WHERE groupname="'.$this->db2->escape($gname).'" OR title="'.$this->db2->escape($gname).'" LIMIT 1', FALSE);
			if( $o = $this->db2->fetch_object($r) ) {
				$gid	= intval($o->id);
				$this->cache->set($cachekey, $gid, $GLOBALS['C']->CACHE_EXPIRE);
				return $return_id ? $gid : $this->get_group_by_id($gid);
			}
			$this->cache->del($cachekey);
			return FALSE;
		}
		
		public function get_group_by_id($gid, $force_refresh=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			$gid	= intval($gid);
			if( 0 == $gid ) {
				return FALSE;
			}
			static $loaded = array();
			$cachekey	= 'n:'.$this->id.',groupid:'.$gid;
			if( isset($loaded[$cachekey]) && TRUE!=$force_refresh ) {
				return $loaded[$cachekey];
			}
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				$loaded[$cachekey] = $data;
				return $data;
			}	
			$r	= $this->db2->query('SELECT * FROM groups WHERE id="'.$gid.'" LIMIT 1', FALSE);
			if($o = $this->db2->fetch_object($r)) {
				$o->title		= stripslashes($o->title);
				$o->is_public	= $o->is_public==1;
				$o->is_private	= !$o->is_public;
				$o->is_deleted	= FALSE;
				$o->about_me	= stripslashes($o->about_me);
				if( empty($o->avatar) ) {
					$o->avatar	= $GLOBALS['C']->DEF_AVATAR_GROUP;
				}
				$this->cache->set($cachekey, $o, $GLOBALS['C']->CACHE_EXPIRE);
				$loaded[$cachekey] = $o;
				return $o;
			}
			$this->cache->del($cachekey);
			return FALSE;
		}
		
		public function get_deleted_group_by_id($gid, $force_refresh=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			$gid	= intval($gid);
			if( 0 == $gid ) {
				return FALSE;
			}
			static $loaded = array();
			$cachekey	= 'n:'.$this->id.',deletedgroupid:'.$gid;
			if( isset($loaded[$cachekey]) && TRUE!=$force_refresh ) {
				return $loaded[$cachekey];
			}
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				$loaded[$cachekey] = $data;
				return $data;
			}
			$r	= $this->db2->query('SELECT * FROM groups_deleted WHERE id="'.$gid.'" LIMIT 1', FALSE);
			if($o = $this->db2->fetch_object($r)) {
				$o->title		= stripslashes($o->title);
				$o->is_public	= $o->is_public==1;
				$o->is_private	= !$o->is_public;
				$o->is_deleted	= TRUE;
				$this->cache->set($cachekey, $o, $GLOBALS['C']->CACHE_EXPIRE);
				$loaded[$cachekey] = $o;
				return $o;
			}
			$this->cache->del($cachekey);
			return FALSE;
		}
		
		public function get_group_invited_members($gid, $force_refresh=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			if( ! $g = $this->get_group_by_id($gid, $force_refresh) ) {
				return FALSE;
			}
			static $loaded = array();
			$cachekey	= 'n:'.$this->id.',group_invited_members:'.$gid;
			if( isset($loaded[$cachekey]) && TRUE!=$force_refresh ) {
				return $loaded[$cachekey];
			}
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				$loaded[$cachekey] = $data;
				return $data;
			}
			$data	= array();
			$r	= $this->db2->query('SELECT user_id FROM groups_private_members WHERE group_id="'.$g->id.'" ORDER BY id ASC', FALSE);
			while($obj = $this->db2->fetch_object($r)) {
				$data[]	= intval($obj->user_id);
			}
			$this->cache->set($cachekey, $data, $GLOBALS['C']->CACHE_EXPIRE);
			$loaded[$cachekey] = $data;
			return $data;
		}
		
		public function get_group_members($gid, $force_refresh=FALSE)
		{
			if( ! $this->id ) {
				return FALSE;
			}
			if( ! $g = $this->get_group_by_id($gid, $force_refresh) ) {
				return FALSE;
			}
			$cachekey	= 'n:'.$this->id.',group_members:'.$gid;
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				return $data;
			}
			$data	= array();
			if($g->is_public == 0) {
				$u_in	= $this->get_group_invited_members($gid, $force_refresh);
				$r	= $this->db2->query('SELECT id FROM users WHERE active=1 AND is_network_admin=1', FALSE);
				while($sdf = $this->db2->fetch_object($r)) {
					$u_in[]	= intval($sdf->id);
				}
				$u_in	= array_unique($u_in);
				$u_in	= count($u_in)==0 ? '-1' : implode(', ', $u_in);
				$r	= $this->db2->query('SELECT user_id, group_from_postid FROM groups_followed WHERE group_id="'.$g->id.'" AND user_id IN('.$u_in.') ORDER BY id ASC', FALSE);
			}
			else {
				$r	= $this->db2->query('SELECT user_id, group_from_postid FROM groups_followed WHERE group_id="'.$g->id.'" ORDER BY id ASC', FALSE);
			}
			while($o = $this->db2->fetch_object($r)) {
				$data[intval($o->user_id)]	= intval($o->group_from_postid);
			}
			$this->cache->set($cachekey, $data, $GLOBALS['C']->CACHE_EXPIRE);
			return $data;
		}
		
		public function get_last_post_id()
		{
			if( ! $this->id ) {
				return 0;
			}
			return intval($this->db2->fetch_field('SELECT MAX(id) FROM posts'));
		}
		
		public function get_recent_posttags($in_sql, $count=20, $force_refresh=FALSE)
		{
			$cachekey	= 'n:'.$this->id.',active_tags:'.md5($in_sql);
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				return array_slice($data, 0, $count);
			}
			$this->cache->set($cachekey, array(), $GLOBALS['C']->CACHE_EXPIRE); // this is to avoid running the query below multiple times at once 
			$data	= array();
			$this->db2->query('SELECT message, date FROM posts WHERE api_id<>2 AND posttags<>0 '.$in_sql.' ORDER BY id DESC LIMIT 1000');
			while($tmp = $this->db2->fetch_object()) {
				if( ! preg_match_all('/\#([א-תÀ-ÿ一-龥а-яa-z0-9\-_]{1,50})/iu', stripslashes($tmp->message), $matches, PREG_PATTERN_ORDER) ) {
					continue;
				}
				$thisposttags	= array();
				foreach($matches[1] as $tg) {
					$thisposttags[]	= mb_strtolower(trim($tg));
				}
				$thisposttags	= array_unique($thisposttags);
				$weight	= 1;
				if( $tmp->date > time()-24*3600 ) {
					$weight	= 100;
				}
				elseif( $tmp->date > time()-7*24*3600 ) {
					$weigth	= 20;
				}
				elseif( $tmp->date > time()-30*24*3600 ) {
					$weight	= 5;
				}
				foreach($thisposttags as $tg) {
					if( ! isset($data[$tg]) ) {
						$data[$tg]	= 0;
					}
					$data[$tg]	+= $weight;
				}
			}
			arsort($data);
			$data	= array_keys($data);
			$data	= array_slice($data, 0, 50);
			$this->cache->set($cachekey, $data, $GLOBALS['C']->CACHE_EXPIRE);
			return array_slice($data, 0, $count);
		}
		
		public function get_user_notif_rules($user_id, $force_refresh=FALSE)
		{
			$cachekey	= 'n:'.$this->id.',usr_ntf_rulz:'.$user_id;
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				return $data;
			}
			$this->db2->query('SELECT * FROM users_notif_rules WHERE user_id="'.$user_id.'" LIMIT 1');
			if( ! $obj = $this->db2->fetch_object() ) {
				require_once( $GLOBALS['C']->INCPATH.'helpers/func_signup.php' );
				set_user_default_notification_rules($user_id);
			}
			$this->db2->query('SELECT * FROM users_notif_rules WHERE user_id="'.$user_id.'" LIMIT 1');
			if( ! $obj = $this->db2->fetch_object() ) {
				return FALSE;
			}
			unset($obj->user_id);
			$this->cache->set($cachekey, $obj, $GLOBALS['C']->CACHE_EXPIRE);
			return $obj;
		}
		
		public function get_posts_api($id, $force_refresh=FALSE)
		{
			$id	= intval($id);
			static $loaded = array();
			$cachekey	= 'n:'.$this->id.',post_app:'.$id;
			if( isset($loaded[$cachekey]) && TRUE!=$force_refresh ) {
				return $loaded[$cachekey];
			}
			$data	= $this->cache->get($cachekey);
			if( FALSE!==$data && TRUE!=$force_refresh ) {
				$loaded[$cachekey]	= $data;
				return $data;
			}
			$r	= $this->db2->query('SELECT id, name FROM applications WHERE id="'.$id.'" LIMIT 1', FALSE);
			if( $data = $this->db2->fetch_object($r) ) {
				$data->name	= stripslashes($data->name);
				$this->cache->set($cachekey, $data, $GLOBALS['C']->CACHE_EXPIRE);
				$loaded[$cachekey]	= $data;
				return $data;
			}
			return FALSE;
		}
		
		public function send_notification_post($to_user_id, $in_group_id, $lang_key, $lang_params, $if_exists_action='ignore')
		{
			global $C;
			$to_user_id		= intval($to_user_id);
			$in_group_id	= intval($in_group_id);
			if( $C->API_ID == 1 ) {
				if( preg_match('/^(http(s)?\:\/\/)m\.(.*)$/iu', $C->SITE_URL, $m) ) {
					$siteurl	= $m[1].$m[3];
					foreach($lang_params as &$p) {
						$p	= str_replace($C->SITE_URL, $siteurl, $p);
					}
				}
				elseif( preg_match('/\/m(\/|$)/iu', $C->SITE_URL, $m) ) {
					$siteurl	= preg_replace('/\/m(\/|$)/', '', $C->SITE_URL);
					$siteurl	= rtrim($siteurl,'/').'/';
					foreach($lang_params as &$p) {
						$p	= str_replace($C->SITE_URL, $siteurl, $p);
					}
				}
			}
			$data	= (object) array (
				'type'		=> 'notif',
				'to_user_id'	=> $to_user_id,
				'in_group_id'	=> $in_group_id,
				'lang_key'		=> $lang_key,
				'lang_params'	=> $lang_params,
				'from_user_id'	=> $GLOBALS['user']->id,
			);
			$data	= $this->db2->e(serialize($data));
			if( $to_user_id > 0 ) {
				if( $if_exists_action != 'ignore' ) {
					$r	= $this->db2->query('SELECT id FROM posts WHERE user_id="0" AND group_id="0" AND message="'.$data.'" LIMIT 1', FALSE);
					if($obj = $this->db2->fetch_object($r)) {
						if( $if_exists_action == 'quit' ) {
							return;
						}
						if( $if_exists_action == 'replace' ) {
							$this->db2->query('DELETE FROM posts WHERE id="'.$obj->id.'" LIMIT 1', FALSE);
							$this->db2->query('DELETE FROM post_userbox WHERE post_id="'.$obj->id.'" ', FALSE);
						}
					}
				}
				$this->db2->query('INSERT INTO posts SET user_id="0", message="'.$data.'", date="'.time().'", ip_addr="'.ip2long($_SERVER['REMOTE_ADDR']).'" ', FALSE);
				$this->db2->query('INSERT DELAYED INTO post_userbox SET user_id="'.$to_user_id.'", post_id="'.intval($this->db2->insert_id()).'" ');
			}
			elseif( $in_group_id > 0 ) {
				if( $if_exists_action != 'ignore' ) {
					$r	= $this->db2->query('SELECT id FROM posts WHERE user_id="0" AND group_id="'.$in_group_id.'" AND message="'.$data.'" LIMIT 1', FALSE);
					if($obj = $this->db2->fetch_object($r)) {
						if( $if_exists_action == 'quit' ) {
							return;
						}
						if( $if_exists_action == 'replace' ) {
							$this->db2->query('DELETE FROM posts WHERE id="'.$obj->id.'" LIMIT 1', FALSE);
						}
					}
				}
				$this->db2->query('INSERT DELAYED INTO posts SET group_id="'.$in_group_id.'", message="'.$data.'", date="'.time().'", ip_addr="'.ip2long($_SERVER['REMOTE_ADDR']).'" ', FALSE);
			}
		}
		public function send_notification_email($to_user_id, $notif_type, $subject, $message_txt, $message_html, $inD=FALSE)
		{
			global $C, $D, $page;
			if( $inD ) {
				foreach($inD as $k=>$v) {
					$D->$k	= $v;
				}
			}
			$to_user	= $this->get_user_by_id($to_user_id);
			if( !$to_user || empty($subject) || empty($message_txt) || empty($message_html) ) {
				return;
			}
			$D->page	= & $page;
			$D->user	= $to_user;
			$D->subject		= $subject;
			$D->message_txt	= $message_txt;
			$D->message_html	= $message_html;
			$msgtxt	= $page->load_template('email/notifications_txt.php', FALSE);
			$msghtml	= $page->load_template('email/notifications_html.php', FALSE);
			if( empty($msgtxt) || empty($msghtml) ) {
				return;
			}
			if( $C->SITE_URL != $C->DEF_SITE_URL ) {
				$msgtxt	= str_replace($C->SITE_URL, $C->DEF_SITE_URL, $msgtxt);
				$msghtml	= str_replace($C->SITE_URL, $C->DEF_SITE_URL, $msghtml);
			}
			if( preg_match('/^(http(s)?\:\/\/)m\.(.*)$/iu', $C->DEF_SITE_URL, $m) ) {
				$siteurl	= $m[1].$m[3];
				$msgtxt	= str_replace($C->DEF_SITE_URL, $siteurl, $msgtxt);
				$msghtml	= str_replace($C->DEF_SITE_URL, $siteurl, $msghtml);
			}
			do_send_mail_html($to_user->email, $subject, $msgtxt, $msghtml);
		}
		
		public function get_dashboard_tabstate($user_id, $tabs)
		{
			$user_id	= intval($user_id);
			if( is_array($tabs) ) {
				$result	= array();
				$tmp	= array();
				foreach($tabs as $tab) {
					$result[$tab]	= 0;
					$tmp[]	= '"'.$this->db2->e($tab).'"';
				}
				$tmp	= implode(', ', $tmp);
				$r	= $this->db2->query('SELECT tab, state, newposts FROM users_dashboard_tabs WHERE user_id="'.$user_id.'" AND tab IN('.$tmp.') LIMIT '.count($tabs), FALSE);
				while( $obj = $this->db2->fetch_object($r) ) {
					$result[$obj->tab]	= $obj->state==0 ? 0 : intval($obj->newposts);
					if( $result[$obj->tab] > 99 ) {
						$result[$obj->tab]	= '99';
					}
				}
				return $result;
			}
			else {
				$r	= $this->db2->query('SELECT tab, state, newposts FROM users_dashboard_tabs WHERE user_id="'.$user_id.'" AND tab="'.$this->db2->e($tabs).'" LIMIT 1', FALSE);
				if( ! $obj = $this->db2->fetch_object($r) ) {
					return 0;
				}
				$result	= $obj->state==0 ? 0 : intval($obj->newposts);
				if( $result > 99 ) {
					$result	= '99';
				}
				return $result;
			}
		}
		public function set_dashboard_tabstate($user_id, $tab, $withnum=0)
		{
			$user_id	= intval($user_id);
			$withnum	= intval($withnum);
			$currnum	= $this->get_dashboard_tabstate($user_id, $tab);
			if( $currnum==0 && $withnum<=0 ) {
				return TRUE;
			}
			if( $currnum==0 && $withnum>0 ) {
				$this->db2->query('REPLACE INTO users_dashboard_tabs SET user_id="'.$user_id.'", tab="'.$this->db2->e($tab).'", state="1", newposts="'.$withnum.'" ', FALSE);
				return TRUE;
			}
			if( $currnum>0 && $withnum==0 ) {
				$this->reset_dashboard_tabstate($user_id, $tab);
				return TRUE;
			}
			if( $currnum>0 && $withnum>0 ) {
				$withnum	+= $currnum;
				$this->db2->query('REPLACE INTO users_dashboard_tabs SET user_id="'.$user_id.'", tab="'.$this->db2->e($tab).'", state="1", newposts="'.$withnum.'" ', FALSE);
				return TRUE;
			}
		}
		public function reset_dashboard_tabstate($user_id, $tab)
		{
			$this->db2->query('DELETE FROM users_dashboard_tabs WHERE user_id="'.$user_id.'" AND tab="'.$this->db2->e($tab).'" ', FALSE);
			return TRUE;
		}
	}
	
?>
