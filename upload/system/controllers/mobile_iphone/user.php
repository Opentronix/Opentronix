<?php
	
	if( !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if(  $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
	
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/user.php');
	
	$u	= $this->network->get_user_by_id(intval($this->params->user));
	if( ! $u ) {
		$this->params->user	= $this->user->id;
		$u	= $this->user->info;
	}
	
	$D->page_title	= $u->username.' - '.$C->SITE_TITLE;
	
	$D->usr	= & $u;
	$D->is_my_profile	= $u->id==$this->user->id;
	$D->i_follow_him	= $this->user->if_follow_user($u->id);
	
	$shows	= array('updates', 'info', 'groups', 'friends');
	$D->show	= 'updates';
	
	if( $this->param('show') && in_array($this->param('show'),$shows) ) {
		$D->show	= $this->param('show');
	}
	
	if( !$D->is_my_profile ) {
		if( $this->param('do_follow') && !$D->i_follow_him ) {
			$this->user->follow($u->id, TRUE);
			$D->i_follow_him	= TRUE;
		}
		elseif( $this->param('do_unfollow') && $D->i_follow_him ) {
			$this->user->follow($u->id, FALSE);
			$D->i_follow_him	= FALSE;
		}
	}
	
	$D->usr_avatar	= md5($D->usr->id.'-'.$D->usr->avatar).'.'.pathinfo($D->usr->avatar,PATHINFO_EXTENSION);
	if( ! file_exists($C->TMP_DIR.$D->usr_avatar) ) {
		require_once($C->INCPATH.'helpers/func_images.php');
		copy_attachment_videoimg($C->IMG_DIR.'avatars/'.$D->usr->avatar, $C->TMP_DIR.$D->usr_avatar, 100);
	}
	
	if( $D->show == 'updates' )
	{
		$D->num_results	= 0;
		$D->start_from	= 0;
		$D->posts_html	= '';
		
		$not_in_groups	= array();
		if( ! $this->user->info->is_network_admin ) {
			$r	= $db2->query('SELECT id FROM groups WHERE is_public=0');
			while($obj = $db2->fetch_object($r)) {
				$obj->id	= intval($obj->id);
				$g	= $this->network->get_group_by_id($obj->id);
				if( ! $g ) {
					$not_in_groups[]	= $obj->id;
					continue;
				}
				if( $g->is_public == 1 ) {
					continue;
				}
				$m	= $this->network->get_group_invited_members($g->id);
				if( ! in_array(intval($this->user->id), $m) ) {
					$not_in_groups[]	= $obj->id;
					continue;
				}
			}
		}
		$not_in_groups	= count($not_in_groups)==0 ? '' : ('AND group_id NOT IN('.implode(', ', $not_in_groups).')');
		
		$q1	= 'SELECT COUNT(id) FROM posts WHERE user_id="'.$u->id.'" '.$not_in_groups;
		$q2	= 'SELECT *, "public" AS `type` FROM posts WHERE user_id="'.$u->id.'" '.$not_in_groups.' ORDER BY id DESC ';
		$D->num_results	= $db2->fetch_field($q1);
		$D->posts_number	= 0;
		if( 0 < $D->num_results ) {
			$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_POSTS);
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
	}
	elseif( $D->show == 'info' ) {
		$D->i	= new stdClass;
		$D->i->ims	= array();
		$D->i->prs	= array();
		$db2->query('SELECT * FROM users_details WHERE user_id="'.$u->id.'" LIMIT 1');
		if($obj = $db2->fetch_object()) {
			unset($obj->user_id);
			foreach($obj as $k=>$v) {
				$v	= stripslashes($v);
				if( substr($k,0,5) == 'prof_' ) {
					if( preg_match('/^(.*)\#\#\#(.*)$/iu', $v, $m) ) {
						if( empty($m[1]) ) {
							$m[1] = $u->fullname;
						}
						$D->i->prs[$k]	= array($m[2], $m[1]);
					}
				}
				elseif( substr($k,0,3) == 'im_' ) {
					$D->i->ims[$k]	= $v;
				}
				else {
					$D->i->$k	= $v;
				}
			}
		}
		$D->birthdate	= '';
		$bd_day	= intval(substr($u->birthdate,8,2));
		$bd_month	= intval(substr($u->birthdate,5,2));
		$bd_year	= intval(substr($u->birthdate,0,4));
		if( $bd_day>0 && $bd_month>0 && $bd_year>0 ) {
			$D->birthdate	= mktime(0, 0, 1, $bd_month, $bd_day, $bd_year);
			$D->birthdate	= strftime($this->lang('uinfo_birthdate_dtformat'), $D->birthdate);
		}
		$D->date_register		= strftime($this->lang('uinfo_birthdate_dtformat'), $u->reg_date);
		$D->date_lastlogin	= '';
		$tmp	= intval($db->fetch_field('SELECT lastclick_date FROM users WHERE id="'.$u->id.'" LIMIT 1'));
		if( $tmp > 0 ) {
			$D->date_lastlogin	= strftime($this->lang('uinfo_aboutme_lgndtfrmt'), $tmp);
		}
	}
	elseif( $D->show == 'groups' ) {
		$D->num_results	= 0;
		$D->num_pages	= 0;
		$D->pg		= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->groups_html	= '';
		$tmp	= array_keys($this->network->get_user_follows($D->usr->id)->follow_groups);
		$D->num_results	= count($tmp);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_GROUPS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_GROUPS;
		$tmp	= array_slice($tmp, $from, $C->PAGING_NUM_GROUPS);
		$grps	= array();
		foreach($tmp as $sdf) {
			if($sdf = $this->network->get_group_by_id($sdf)) {
				$grps[]	= $sdf;
			}
		}
		if( count($grps) > 0 ) {
			$i	= 0;
			ob_start();
			foreach($grps as $tmp) {
				$D->g	= $tmp;
				$D->g->list_index	= $i++;
				$this->load_template('mobile_iphone/single_group.php');
			}
			$D->groups_html	= ob_get_contents();
			ob_end_clean();
			unset($tmp, $sdf, $grps, $D->g);
		}
	}
	elseif( $D->show == 'friends' ) {
		$D->filter	= 'followers';
		if( $this->param('filter')=='following' ) {
			$D->filter	= 'following';
		}
		$D->num_results	= 0;
		$D->num_pages	= 0;
		$D->pg		= $this->param('pg') ? intval($this->param('pg')) : 1;
		$D->users_html	= '';
		$tmp	= array_keys( $D->filter=='followers' ? $this->network->get_user_follows($D->usr->id)->followers : $this->network->get_user_follows($D->usr->id)->follow_users );
		$D->num_results	= count($tmp);
		$D->num_pages	= ceil($D->num_results / $C->PAGING_NUM_USERS);
		$D->pg	= min($D->pg, $D->num_pages);
		$D->pg	= max($D->pg, 1);
		$from	= ($D->pg - 1) * $C->PAGING_NUM_USERS;
		$tmp	= array_slice($tmp, $from, $C->PAGING_NUM_USERS);
		$usrs	= array();
		foreach($tmp as $sdf) {
			if($sdf = $this->network->get_user_by_id($sdf)) {
				$usrs[]	= $sdf;
			}
		}
		if( count($usrs) > 0 ) {
			$i	= 0;
			ob_start();
			foreach($usrs as $tmp) {
				$D->u	= $tmp;
				$D->u->list_index	= $i++;
				$this->load_template('mobile_iphone/single_user.php');
			}
			$D->users_html	= ob_get_contents();
			ob_end_clean();
			unset($tmp, $sdf, $usrs, $D->u);
		}
		$D->num_followers	= count($this->network->get_user_follows($D->usr->id)->followers);
		$D->num_following	= count($this->network->get_user_follows($D->usr->id)->follow_users);
	}
	
	$this->load_template('mobile_iphone/user.php');
	
?>