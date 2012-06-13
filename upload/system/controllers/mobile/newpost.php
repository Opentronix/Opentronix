<?php
	
	if( !$this->network->id || !$this->user->is_logged ) {
		$this->redirect('home');
	}
	if( $this->network->id && $C->MOBI_DISABLED ) {
		$this->redirect('mobidisabled');
	}
		
	$this->load_langfile('mobile/global.php');
	$this->load_langfile('mobile/newpost.php');
	
	$D->page_title	= $this->lang('newpost_page_title', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$to_user	= FALSE;
	if( $this->param('touser') ) {
		$to_user	= $this->network->get_user_by_username($this->param('touser'));
		if( $to_user && $to_user->id==$this->user->id ) {
			$to_user	= FALSE;
		}
	}
	$to_group	= FALSE;
	if( $this->param('togroup') ) {
		$to_group	= $this->network->get_group_by_name($this->param('togroup'));
		if( $to_group && !isset($this->network->get_user_follows($this->user->id)->follow_groups[$to_group->id]) ) {
			$to_group	= FALSE;
		}
	}
	if( isset($_POST['sharewith']) ) {
		$to_user	= FALSE;
		$to_group	= FALSE;
		if( preg_match('/^user_(.*)$/iu', $_POST['sharewith'], $m) ) {
			$to_user	= $this->network->get_user_by_username($m[1]);
			if( $to_user && $to_user->id==$this->user->id ) {
				$to_user	= FALSE;
			}
		}
		elseif( preg_match('/^group_(.*)$/iu', $_POST['sharewith'], $m) ) {
			$to_group	= $this->network->get_group_by_name($m[1]);
			if( $to_group && !isset($this->network->get_user_follows($this->user->id)->follow_groups[$to_group->id]) ) {
				$to_group	= FALSE;
			}
		}
	}
	
	$D->to_user		= & $to_user;
	$D->to_group	= & $to_group;
	$D->menu_groups	= array();
	foreach($this->user->get_top_groups(10) as $g) {
		$D->menu_groups[$g->id]	= $g;
	}
	if( $to_group && !isset($D->menu_groups[$to_group->id]) ) {
		array_unshift($D->menu_groups, $to_group);
	}
	$D->menu_groups	= array_values($D->menu_groups);
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	$D->message	= '';
	
	if( $this->param('mention') && !isset($_POST['message']) && $tmp = $this->network->get_user_by_username($this->param('mention')) ) {
		$D->message	= '@'.$tmp->username.' ';
	}
	
	if( isset($_POST['message']) )
	{
		$D->submit	= TRUE;
		$D->message	= trim($_POST['message']);
		$D->message	= preg_replace('/\s+/ius', ' ', $D->message);
		$D->message	= trim($D->message);
		if( mb_strlen($D->message) > $C->POST_MAX_SYMBOLS ) {
			$D->message	= mb_substr($D->message, 0, $C->POST_MAX_SYMBOLS);
		}
		$D->message	= trim($D->message);
		
		$p	= new newpost;
		$p->set_api_id($C->API_ID);
		$p->set_message($D->message);
		
		if( !$D->error && $to_user ) {
			if( ! $p->set_to_user($to_user->id) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'newpost_err_system';
			}
		}
		if( !$D->error && $to_group ) {
			if( ! $p->set_group_id($to_group->id) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'newpost_err_system';
			}
		}
		if( !$D->error && empty($D->message) ) {
			$D->error	= TRUE;
			$D->errmsg	= 'newpost_err_emptymsg';
		}
		if( isset($_FILES['attach']) && is_uploaded_file($_FILES['attach']['tmp_name']) && ($C->ATTACH_IMAGE_DISABLED==0 || $C->ATTACH_FILE_DISABLED==0) ) {
			$file	= (object) $_FILES['attach'];
			$is_image	= FALSE;
			$info	= @getimagesize($file->tmp_name);
			if( $info && is_array($info) ) {
				if( $info[2]==IMAGETYPE_GIF || $info[2]==IMAGETYPE_JPEG || $info[2]==IMAGETYPE_PNG ) {
					$is_image	= TRUE;
				}
			}
			$attach_res	= FALSE;
			if( $is_image && $C->ATTACH_IMAGE_DISABLED==0 ) {
				$attach_res	= $p->attach_image($file->tmp_name, $file->name);
			}
			elseif( $C->ATTACH_FILE_DISABLED==0 ) {
				$attach_res	= $p->attach_file($file->tmp_name, $file->name);
			}
			if( ! $attach_res ) {
				$D->error	= TRUE;
				$D->errmsg	= 'newpost_err_attach';
			}
		}
		if( ! $D->error ) {
			if( ! $p->save() ) {
				$D->error	= TRUE;
				$D->errmsg	= 'newpost_err_system';
			}
		}
		if( ! $D->error ) {
			$this->redirect('newpost/okmsg:'.($to_user?'sent':'posted'));
		}	
	}
	$this->load_template('mobile/newpost.php');
	
?>