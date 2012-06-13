<?php
	
	class postcomment
	{
		private $network;
		private $user;
		private $cache;
		private $db1;
		private $db2;
		public $post;
		public $comment_id;
		public $comment_api_id;
		public $comment_user;
		public $comment_message;
		public $comment_mentioned;
		public $comment_posttags;
		public $comment_date;
		public $error	= FALSE;
		public $tmp;
		
		public function __construct($post_obj, $load_id=FALSE, $load_obj=FALSE)
		{
			global $C;
			$this->tmp	= new stdClass;
			$this->network	= & $GLOBALS['network'];
			$this->user		= & $GLOBALS['user'];
			$this->cache	= & $GLOBALS['cache'];
			$this->db1	= & $GLOBALS['db1'];
			$this->db2	= & $GLOBALS['db2'];
			$this->post	= & $post_obj;
			if( ! $this->network->id ) {
				$this->error	= TRUE;
				return;
			}
			if( ! $this->post instanceof post ) {
				$this->error	= TRUE;
				return;
			}
			if( $this->post->error ) {
				$this->error	= TRUE;
				return;
			}
			if( $load_id ) {
				$id	= intval($load_id);
				$r	= $this->db2->query('SELECT * FROM '.($this->post->post_type=='private'?'posts_pr_comments':'posts_comments').' WHERE id="'.$id.'" LIMIT 1', FALSE);
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
			$u1	= $this->network->get_user_by_id($obj->user_id);
			if( ! $u1 ) {
				$this->error	= TRUE;
				return;
			}
			$this->comment_id		= intval($obj->id);
			$this->comment_api_id	= intval($obj->api_id);
			$this->comment_user	= &$u1;
			$this->comment_message	= stripslashes($obj->message);
			$this->comment_date		= intval($obj->date);
			$this->comment_mentioned	= array();
			$this->comment_posttags	= array();
			if( $obj->mentioned > 0 ) {
				$r	= $this->db2->query('SELECT user_id FROM '.($this->post->post_type=='private'?'posts_pr_comments_mentioned':'posts_comments_mentioned').' WHERE comment_id="'.$obj->id.'" LIMIT '.$obj->mentioned, FALSE);
				while($o = $this->db2->fetch_object($r)) {
					if( $u = $this->network->get_user_by_id($o->user_id) ) {
						$this->comment_mentioned[]	= array($u->username, $u->fullname);
					}
				}
			}
			if( $obj->posttags > 0 ) {
				if( preg_match_all('/\#([א-תÀ-ÿ一-龥а-яa-z0-9\-_]{1,50})/iu', $this->comment_message, $matches, PREG_PATTERN_ORDER) ) {
					foreach($matches[1] as $tg) {
						$this->comment_posttags[]	= trim($tg);
					}
					$this->comment_posttags	= array_unique($this->comment_posttags);
				}
			}
			return TRUE;
		}
		
		public function parse_text()
		{
			global $C;
			if( $this->error ) {
				return FALSE;
			}
			$message	= htmlspecialchars($this->comment_message);
			if( FALSE!==strpos($message,'http://') || FALSE!==strpos($message,'http://') || FALSE!==strpos($message,'ftp://') ) {
				$message	= preg_replace('#(^|\s)((http|https|ftp)://\w+[^\s\[\]]+)#ie', 'post::_postparse_build_link("\\2", "\\1")', $message);
			}
			
			if( count($this->comment_mentioned) > 0 ) {
				$tmp	= array();
				foreach($this->comment_mentioned as $i=>$v) {
					$tmp[$i]	= mb_strlen($v[0]);
				}
				arsort($tmp);
				$tmp2	= array();
				foreach($tmp as $i=>$v) {
					$tmp2[]	= $this->comment_mentioned[$i];
				}
				foreach($tmp2 as $u) {
					$txt	= '<a href="'.$C->SITE_URL.$u[0].'" title="'.htmlspecialchars($u[1]).'"><span class="post_mentioned"><b>@</b>'.$u[0].'</span></a>';
					$message	= preg_replace('/(^|\s)\@'.preg_quote($u[0]).'/ius', '$1'.$txt, $message);
				}
			}
			
			if( count($this->comment_posttags) > 0 ) {
				$tmp	= array();
				foreach($this->comment_posttags as $i=>$v) {
					$tmp[$i]	= mb_strlen($v);
				}
				arsort($tmp);
				$tmp2	= array();
				foreach($tmp as $i=>$v) {
					$tmp2[]	= $this->comment_posttags[$i];
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
		
		public function if_can_delete()
		{
			if( $this->error ) {
				return FALSE;
			}
			if( $this->post->if_can_delete() ) {
				return TRUE;
			}
			if( $this->comment_user->id == $this->user->id ) {
				return TRUE;
			}
			return FALSE;
		}
		
		public function delete_this_comment()
		{
			global $C;
			if( ! $this->if_can_delete() ) {
				return FALSE;
			}
			$this->db2->query('DELETE FROM '.($this->post->post_type=='private'?'posts_pr_comments_mentioned':'posts_comments_mentioned').' WHERE comment_id="'.$this->comment_id.'" ', FALSE);
			$this->db2->query('DELETE FROM '.($this->post->post_type=='private'?'posts_pr_comments':'posts_comments').' WHERE id="'.$this->comment_id.'" LIMIT 1', FALSE);
			$this->db2->query('UPDATE '.($this->post->post_type=='private'?'posts_pr':'posts').' SET comments=comments-1 WHERE id="'.$this->post->post_id.'" LIMIT 1');
			$this->db2->query('UPDATE '.($this->post->post_type=='private'?'posts_pr_comments_watch':'posts_comments_watch').' SET newcomments=newcomments-1 WHERE post_id="'.$this->post->post_id.'" AND newcomments>0');
			$this->error	= TRUE;
			return TRUE;
		}
	}
	
?>