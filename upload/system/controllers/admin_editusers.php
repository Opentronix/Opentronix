<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	$db2->query('SELECT 1 FROM users WHERE id="'.$this->user->id.'" AND is_network_admin=1 LIMIT 1');
	if( 0 == $db2->num_rows() ) {
		$this->redirect('dashboard');
	}
	
	require_once( $C->INCPATH.'helpers/func_images.php' );
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/admin.php');
	
	$D->page_title	= $this->lang('admpgtitle_editusers', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	if( isset($_POST['editusername']) ) {
		$tmp	= trim($_POST['editusername']);
		if( $this->network->get_user_by_username($tmp) ) {
			$this->redirect('admin/editusers/user:'.$tmp);
		}
	}
	
	$D->user	= FALSE;
	if( $this->param('user') ) {
		$tmp	= trim($this->param('user'));
		if( $tmp = $this->network->get_user_by_username($tmp) ) {
			$D->user	= $tmp;
		}
	}
	
	if( $D->user )
	{
		$this->load_langfile('inside/settings.php'); // load settings texts
		$this->load_langfile('inside/admin.php'); // overwrite some of them
		
		$tabs	= array('profile', 'picture', 'rssfeeds');
		$D->tab	= 'profile';
		if( $this->param('tab') && in_array($this->param('tab'),$tabs) ) {
			$D->tab	= $this->param('tab');
		}
		$D->submit	= FALSE;
		$D->error	= FALSE;
		$D->errmsg	= '';
		
		if( $D->tab == 'profile' ) {
			$D->menu_bdate_d	= array();
			$D->menu_bdate_m	= array();
			$D->menu_bdate_y	= array();
			if( $D->user->birthdate == '0000-00-00' ) {
				$D->menu_bdate_d[0]	= '';
				$D->menu_bdate_m[0]	= '';
				$D->menu_bdate_y[0]	= '';
			}
			for($i=1; $i<=31; $i++) {
				$D->menu_bdate_d[$i]	= $i;
			}
			for($i=1; $i<=12; $i++) {
				$D->menu_bdate_m[$i]	= strftime('%B', mktime(0,0,1,$i,1,2009));
			}
			for($i=intval(date('Y')); $i>=1900; $i--) {
				$D->menu_bdate_y[$i]	= $i;
			}
			$D->name		= $D->user->fullname;
			$D->position	= $D->user->position;
			$D->location	= $D->user->location;
			$D->gender		= $D->user->gender;
			$D->aboutme		= $D->user->about_me;
			$D->tags		= implode(', ', $D->user->tags);
			$D->bdate_d		= 0;
			$D->bdate_m		= 0;
			$D->bdate_y		= 0;
			if( $D->user->birthdate != '0000-00-00' ) {
				$D->bdate_d		= intval(substr($D->user->birthdate,8,2));
				$D->bdate_m		= intval(substr($D->user->birthdate,5,2));
				$D->bdate_y		= intval(substr($D->user->birthdate,0,4));
			}
			if( isset($_POST['sbm']) ) {
				$D->submit	= TRUE;
				$D->name		= trim($_POST['name']);
				$D->name		= strip_tags($D->name);
				$D->position	= trim($_POST['position']);
				$D->location	= trim($_POST['location']);
				$D->gender		= isset($_POST['gender']) ? trim($_POST['gender']) : '';
				$D->aboutme		= trim($_POST['aboutme']);
				$D->tags		= trim($_POST['tags']);
				$D->bdate_d		= intval($_POST['bdate_d']);
				$D->bdate_m		= intval($_POST['bdate_m']);
				$D->bdate_y		= intval($_POST['bdate_y']);
				if( $D->gender!='m' && $D->gender!='f' ) {
					$D->gender	= '';
				}
				if( !isset($D->menu_bdate_m[$D->bdate_m]) || !isset($D->menu_bdate_d[$D->bdate_d]) || !isset($D->menu_bdate_y[$D->bdate_y]) ) {
					$D->bdate_m	= 0;
					$D->bdate_d	= 0;
					$D->bdate_y	= 0;
				}
				if( $D->bdate_d==0 || $D->bdate_m==0 || $D->bdate_y==0 ) {
					$D->bdate_m	= 0;
					$D->bdate_d	= 0;
					$D->bdate_y	= 0;
					$birthdate	= '0000-00-00';
				}
				else {
					$birthdate	= $D->bdate_y.'-'.str_pad($D->bdate_m,2,0,STR_PAD_LEFT).'-'.str_pad($D->bdate_d,2,0,STR_PAD_LEFT);
				}
				$D->tags	= str_replace(array("\n","\r"), ',', $D->tags);
				$D->tags	= preg_replace('/\,+/ius', ',', $D->tags);
				$D->tags	= explode(',', $D->tags);
				foreach($D->tags as $k=>$v) {
					$v	= trim($v);
					if( FALSE == preg_match('/^[ا-یא-תÀ-ÿ一-龥а-яa-z0-9\-\_\.\s\+]{2,}$/iu', $v) ) {
						unset($D->tags[$k]);
						continue;
					}
					$D->tags[$k]	= $v;
				}
				$D->tags	= implode(', ', $D->tags);
				
				$db2->query('UPDATE users SET fullname="'.$db2->e($D->name).'", about_me="'.$db2->e($D->aboutme).'", tags="'.$db2->e($D->tags).'", gender="'.$db2->e($D->gender).'", birthdate="'.$db2->e($birthdate).'", position="'.$db2->e($D->position).'", location="'.$db2->e($D->location).'" WHERE id="'.$D->user->id.'" LIMIT 1');
				$D->user	= $this->network->get_user_by_id($D->user->id, TRUE);
			}
		}
		elseif( $D->tab == 'picture' ) {
			if( isset($_FILES['avatar']) && is_uploaded_file($_FILES['avatar']['tmp_name']) ) {
				$D->submit	= TRUE;
				$f	= (object) $_FILES['avatar'];
				list($w, $h, $tp) = getimagesize($f->tmp_name);
				if( $w==0 || $h==0 ) {
					$D->error	= TRUE;
					$D->errmsg	= 'st_avatar_err_invalidfile';
				}
				elseif( $tp!=IMAGETYPE_GIF && $tp!=IMAGETYPE_JPEG && $tp!=IMAGETYPE_PNG && $tp!=IMAGETYPE_BMP ) {
					$D->error	= TRUE;
					$D->errmsg	= 'st_avatar_err_invalidformat';
				}
				elseif( $w<$C->AVATAR_SIZE || $h<$C->AVATAR_SIZE ) {
					$D->error	= TRUE;
					$D->errmsg	= 'st_avatar_err_toosmall';
				}
				else {
					$fn	= time().rand(100000,999999).'.png';
					$res	= copy_avatar($f->tmp_name, $fn);
					if( ! $res ) {
						$D->error	= TRUE;
						$D->errmsg	= 'st_avatar_err_cantcopy';
					}
				}
				if( ! $D->error ) {
					$old	= $D->user->avatar;
					if( $old != $C->DEF_AVATAR_USER ) {
						rm( $C->IMG_DIR.'avatars/'.$old );
						rm( $C->IMG_DIR.'avatars/thumbs1/'.$old );
						rm( $C->IMG_DIR.'avatars/thumbs2/'.$old );
						rm( $C->IMG_DIR.'avatars/thumbs3/'.$old );
					}
					$db2->query('UPDATE users SET avatar="'.$db2->escape($fn).'" WHERE id="'.$D->user->id.'" LIMIT 1');
					$D->user	= $this->network->get_user_by_id($D->user->id, TRUE);
				}
			}
			elseif( $this->param('del') == 'current' ) {
				$old	= $D->user->avatar;
				if( $old != $C->DEF_AVATAR_USER ) {
					rm( $C->IMG_DIR.'avatars/'.$old );
					rm( $C->IMG_DIR.'avatars/thumbs1/'.$old );
					rm( $C->IMG_DIR.'avatars/thumbs2/'.$old );
					rm( $C->IMG_DIR.'avatars/thumbs3/'.$old );
					$db2->query('UPDATE users SET avatar="" WHERE id="'.$D->user->id.'" LIMIT 1');
					$D->user	= $this->network->get_user_by_id($D->user->id, TRUE);
					$D->msg	= 'deleted';
				}
			}
			list($D->currw, $D->currh) = getimagesize($C->IMG_DIR.'avatars/'.$D->user->avatar);
		}
		if( $D->tab == 'rssfeeds' ) {
			$D->newfeed_url		= '';
			$D->newfeed_filter	= '';
			$D->newfeed_auth_req	= FALSE;
			$D->newfeed_auth_msg	= FALSE;
			$D->newfeed_username	= '';
			$D->newfeed_password	= '';
			if( isset($_POST['sbm']) ) {
				$D->submit	= TRUE;
				$D->newfeed_url		= trim($_POST['newfeed_url']);
				$D->newfeed_filter	= trim( mb_strtolower($_POST['newfeed_filter']) );
				$D->newfeed_filter	= preg_replace('/[^\,ا-یא-תÀ-ÿ一-龥а-яa-z0-9-\_\.\#\s]/iu', '', $D->newfeed_filter);
				$D->newfeed_filter	= preg_replace('/\s+/ius', ' ', $D->newfeed_filter);
				$D->newfeed_filter	= preg_replace('/(\s)*(\,)+(\s)*/iu', ',', $D->newfeed_filter);
				$D->newfeed_filter	= trim( trim($D->newfeed_filter, ',') );
				$D->newfeed_filter	= str_replace(',', ', ', $D->newfeed_filter);
				$D->newfeed_username	= isset($_POST['newfeed_username']) ? trim($_POST['newfeed_username']) : '';
				$D->newfeed_password	= isset($_POST['newfeed_password']) ? trim($_POST['newfeed_password']) : '';
				if( empty($D->newfeed_url) ) {
					$D->error	= TRUE;
					$D->errmsg	= 'st_rssfeeds_err_feed';
				}
				$f	= '';
				if( !$D->error ) {
					$f	= new rssfeed($D->newfeed_url);
					$auth	= $f->check_if_requires_auth();
					if( $f->error ) {
						$D->error	= TRUE;
						$D->errmsg	= 'st_rssfeeds_err_feed';
					}
					elseif( $auth ) {
						$D->newfeed_auth_req	= TRUE;
					}
					else {
						$f->read();
						if( $f->error ) {
							$D->error	= TRUE;
							$D->errmsg	= 'st_rssfeeds_err_feed';
						}
					}
				}
				if( !$D->error && $D->newfeed_auth_req && !empty($D->newfeed_username) && !empty($D->newfeed_password) ) {
					$f->set_userpwd($D->newfeed_username.':'.$D->newfeed_password);
					$auth	= $f->check_if_requires_auth();
					if( $f->error || $auth ) {
						$D->error	= TRUE;
						$D->errmsg	= 'st_rssfeeds_err_auth';
					}
					else {
						$f->read();
						if( $f->error ) {
							$D->error	= TRUE;
							$D->errmsg	= 'st_rssfeeds_err_feed';
						}
					}
				}
				if( !$D->error && $f->is_read ) {
					$f->fetch();
					$lastdate	= $f->get_lastitem_date();
					if( ! $lastdate ) {
						$lastdate	= time();
					}
					$title	= $f->title;
					if( empty($title) ) {
						$title	= preg_replace('/^(http|https|ftp)\:\/\//iu', '', $D->newfeed_url);
					}
					$title	= $this->db2->e($title);
					$usrpwd	= $D->newfeed_auth_req ? ($D->newfeed_username.':'.$D->newfeed_password) : '';
					$usrpwd	= $this->db2->e($usrpwd);
					$keywords	= str_replace(', ', ',', $D->newfeed_filter);
					$keywords	= $this->db2->e($keywords);
					$this->db2->query('SELECT id FROM users_rssfeeds WHERE is_deleted=0 AND user_id="'.$D->user->id.'" AND feed_url="'.$this->db2->e($D->newfeed_url).'" AND feed_userpwd="'.$usrpwd.'" AND filter_keywords="'.$keywords.'" LIMIT 1');
					if( 0 == $this->db2->num_rows() ) {
						$this->db2->query('INSERT INTO users_rssfeeds SET is_deleted=0, user_id="'.$D->user->id.'", feed_url="'.$this->db2->e($D->newfeed_url).'", feed_title="'.$title.'", feed_userpwd="'.$usrpwd.'", filter_keywords="'.$keywords.'", date_added="'.time().'", date_last_post=0, date_last_crawl="'.time().'", date_last_item="'.$lastdate.'" ');
					}
					$this->redirect('admin/editusers/user:'.$D->user->username.'/tab:rssfeeds/msg:added');
				}
				if( !$D->error && $D->newfeed_auth_req && (empty($D->newfeed_username) || empty($D->newfeed_password)) ) {
					$D->newfeed_auth_msg	= TRUE;
				}
			}
			$D->feeds	= array();
			$this->db2->query('SELECT id, feed_url, feed_title, filter_keywords FROM users_rssfeeds WHERE is_deleted=0 AND user_id="'.$D->user->id.'" ORDER BY id ASC');
			while($obj = $this->db2->fetch_object()) {
				$obj->feed_url		= stripslashes($obj->feed_url);
				$obj->feed_title		= stripslashes($obj->feed_title);
				$obj->filter_keywords	= stripslashes($obj->filter_keywords);
				$obj->filter_keywords	= str_replace(',', ', ', $obj->filter_keywords);
				$D->feeds[$obj->id]	= $obj;
			}
			if( $this->param('delfeed') && isset($D->feeds[$this->param('delfeed')]) ) {
				$this->db2->query('UPDATE users_rssfeeds SET is_deleted=1 WHERE id="'.intval($this->param('delfeed')).'" AND is_deleted=0 AND user_id="'.$D->user->id.'" LIMIT 1');
				$this->redirect('admin/editusers/user:'.$D->user->username.'/tab:rssfeeds/msg:deleted');
			}
		}
	}
	
	$this->load_template('admin_editusers.php');
	
?>