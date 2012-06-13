<?php
	
	if( !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
		
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/newpost.php');
	
	$D->page_title	= $this->lang('newpost_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->post_temp_id	= md5(time().rand());
	$D->pf_message	= '';
	$D->pf_sharewith	= 'all';
	$D->pf_sharewithx	= '';
	$to_user	= FALSE;
	$to_group	= FALSE;
	if( $this->param('private') ) {
		$tmp	= $this->network->get_user_by_username($this->param('private'));
		if( $tmp ) {
			$to_user	= $tmp;
			$D->pf_sharewith	= 'user';
			$D->pf_sharewithx	= $tmp->username;
		}
	}
	elseif( $this->param('group') ) {
		$tmp	= $this->network->get_group_by_name($this->param('group'));
		if( $tmp && isset($this->network->get_user_follows($this->user->id)->follow_groups[$tmp->id]) ) {
			$to_group	= $tmp;
			$D->pf_sharewith	= 'group:'.$tmp->title;
			$D->pf_sharewithx	= $tmp->title;
		}
	}
	if( $this->param('mention') ) {
		$tmp	= $this->network->get_user_by_username($this->param('mention'));
		if( $tmp ) {
			$D->pf_message	= '@'.$tmp->username.' ';
		}
	}
	
	$D->menu_groups	= array();
	foreach($this->user->get_top_groups(5) as $g) {
		$D->menu_groups[$g->id]	= $g;
	}
	if( $to_group && !isset($D->menu_groups[$to_group->id]) ) {
		array_unshift($D->menu_groups, $to_group);
	}
	$D->menu_groups	= array_values($D->menu_groups);
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	
	if( isset($_POST['post_temp_id'], $_POST['message']) ) {
		$post_temp_id	= trim($_POST['post_temp_id']);
		$message		= trim($_POST['message']);
		$sharewith		= trim($_POST['sharewith']);
		$sharewithx		= trim($_POST['sharewith_inp']);
		if( empty($message) || empty($post_temp_id) || empty($sharewith) ) {
			$D->error	= TRUE;
		}
		if( ! $D->error ) {
			$attached_link		= intval($_POST['attached_link']);
			$attached_image		= intval($_POST['attached_image']);
			$attached_videoembed	= intval($_POST['attached_videoembed']);
			$attached_file		= intval($_POST['attached_file']);
			$s	= & $this->user->sess;
			if( !isset($s['POSTFORM_TEMP_POSTS'][$post_temp_id]) && ($attached_link || $attached_image || $attached_videoembed || $attached_file) ) {
				$D->error	= TRUE;
			}
		}
		$redirect	= $C->SITE_URL.'dashboard';
		if( ! $D->error ) {
			$a	= array();
			if( isset($s['POSTFORM_TEMP_POSTS'][$post_temp_id]) ) {
				$a	= $s['POSTFORM_TEMP_POSTS'][$post_temp_id]->get_attached();
			}
			if( ! $attached_link ) { unset($a['link']); }
			if( ! $attached_image ) { unset($a['image']); }
			if( ! $attached_videoembed ) { unset($a['videoembed']); }
			if( ! $attached_file ) { unset($a['file']); }
			$p	= new newpost();
			$p->set_attached($a);
			$p->set_api_id($C->API_ID);
			$p->set_message($message);
			if( preg_match('/^group\:(.*)$/iu', $sharewith, $m) ) {
				if( $g = $this->network->get_group_by_name($m[1]) ) {
					if( ! $p->set_group_id($g->id) ) {
						$D->error	= TRUE;
					}
					$redirect	= $C->SITE_URL.$g->groupname;
				}
				else {
					$D->error	= TRUE;
				}
			}
			elseif( $sharewith == 'user' ) {
				if( $u = $this->network->get_user_by_username($sharewithx) ) {
					if( ! $p->set_to_user($u->id) ) {
						$D->error	= TRUE;
					}
					$redirect	= $C->SITE_URL.'dashboard/show:private';
				}
				else {
					$D->error	= TRUE;
				}
			}
			elseif( $sharewith != 'all' ) {
				$D->error	= TRUE;
			}
		}
		if( ! $D->error ) {
			if( ! $p->save() ) {
				$D->error	= TRUE;
			}
			else {
				$this->redirect($redirect);
			}
		}
	}
	
	$this->load_template('mobile_iphone/post.php');
	
?>