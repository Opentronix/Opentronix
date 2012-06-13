<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/settings.php');
	
	$D->page_title	= $this->lang('settings_rssfeeds_pagetitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
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
			$this->db2->query('SELECT id FROM users_rssfeeds WHERE is_deleted=0 AND user_id="'.$this->user->id.'" AND feed_url="'.$this->db2->e($D->newfeed_url).'" AND feed_userpwd="'.$usrpwd.'" AND filter_keywords="'.$keywords.'" LIMIT 1');
			if( 0 == $this->db2->num_rows() ) {
				$this->db2->query('INSERT INTO users_rssfeeds SET is_deleted=0, user_id="'.$this->user->id.'", feed_url="'.$this->db2->e($D->newfeed_url).'", feed_title="'.$title.'", feed_userpwd="'.$usrpwd.'", filter_keywords="'.$keywords.'", date_added="'.time().'", date_last_post=0, date_last_crawl="'.time().'", date_last_item="'.$lastdate.'" ');
			}
			$this->redirect($C->SITE_URL.'settings/rssfeeds/msg:added');
		}
		if( !$D->error && $D->newfeed_auth_req && (empty($D->newfeed_username) || empty($D->newfeed_password)) ) {
			$D->newfeed_auth_msg	= TRUE;
		}
	}
	$D->feeds	= array();
	$this->db2->query('SELECT id, feed_url, feed_title, filter_keywords FROM users_rssfeeds WHERE is_deleted=0 AND user_id="'.$this->user->id.'" ORDER BY id ASC');
	while($obj = $this->db2->fetch_object()) {
		$obj->feed_url		= stripslashes($obj->feed_url);
		$obj->feed_title		= stripslashes($obj->feed_title);
		$obj->filter_keywords	= stripslashes($obj->filter_keywords);
		$obj->filter_keywords	= str_replace(',', ', ', $obj->filter_keywords);
		$D->feeds[$obj->id]	= $obj;
	}
	if( $this->param('delfeed') && isset($D->feeds[$this->param('delfeed')]) ) {
		$this->db2->query('UPDATE users_rssfeeds SET is_deleted=1 WHERE id="'.intval($this->param('delfeed')).'" AND is_deleted=0 AND user_id="'.$this->user->id.'" LIMIT 1');
		$this->redirect($C->SITE_URL.'settings/rssfeeds/msg:deleted');
	}
	
	$this->load_template('settings_rssfeeds.php');
	
?>