<?php
	
	$this->load_langfile('inside/global.php');
	
	echo '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>';
	
	if( !$this->network->id ) {
		echo '<result></result>';
		exit;
	}
	if( !$this->user->is_logged ) {
		echo '<result></result>';
		exit;
	}
	
	$post_temp_id	= isset($_POST['post_temp_id']) ? trim($_POST['post_temp_id']) : FALSE;
	$post_message	= isset($_POST['message']) ? trim($_POST['message']) : '';
	if( ! $post_temp_id ) {
		echo '<result></result>';
		exit;
	}
	if( empty($post_message) ) {
		echo '<result><status>ERROR</status><message>'.$this->lang('pf_msgerr_text').'</message></result>';
		exit;
	}
	
	$s	= & $this->user->sess;
	if( ! isset($s['POSTFORM_TEMP_POSTS']) ) {
		$s['POSTFORM_TEMP_POSTS']	= array();
	}
	if( ! isset($s['POSTFORM_TEMP_POSTS'][$post_temp_id]) ) {
		$s['POSTFORM_TEMP_POSTS'][$post_temp_id]	= new newpost();
	}
	$post_temp	= & $s['POSTFORM_TEMP_POSTS'][$post_temp_id];
	
	$post_edit	= isset($_POST['editpost']) ? trim($_POST['editpost']) : FALSE;
	if($post_edit)
	{
		if( ! preg_match('/^(public|private)_([0-9]+)$/', $post_edit, $m) ) {
			echo '<result></result>';
			exit;
		}
		$p	= new newpost();
		if( ! $p->load_post($m[1], $m[2]) ) {
			echo '<result><status>ERROR</status><message>'.$this->lang('pf_msgerr_sys').'</message></result>';
			exit;
		}
		$p->set_message($post_message);
		$a	= $p->get_attached();
		$all	= array('link', 'image', 'file', 'videoembed');
		foreach($all as $type) {
			if( isset($_POST['at_'.$type]) && $_POST['at_'.$type]=="-1" ) {
				$tmp	= $post_temp->get_attached();
				$a[$type]	= $tmp[$type];
			}
			elseif( isset($_POST['at_'.$type], $a[$type]) && $_POST['at_'.$type]==$a[$type]->attachment_id ) {
			}
			else {
				if(isset($a[$type])) { unset($a[$type]); }
			}
		}
		$p->set_attached($a);
		$res	= $p->save();
		if( ! $res ) {
			echo '<result><status>ERROR</status><message>'.$this->lang('pf_msgerr_sys').'</message></result>';
			exit;
		}
		echo '<result><status>OK</status><message>'.$this->lang('pf_msgok_edited').'</message></result>';
		exit;
	}
	else
	{
		$p	= new newpost();
		$p->set_api_id(0);
		$p->set_message($post_message);
		if( isset($_POST['username']) ) {
			$uid	= $this->network->get_user_by_username($_POST['username'], FALSE, TRUE);
			$r	= $p->set_to_user($uid);
			if( ! $r ) {
				echo '<result><status>ERROR</status><message>'.$this->lang('pf_msgerr_user').'</message></result>';
				exit;
			}
		}
		elseif( isset($_POST['groupname']) ) {
			$gid	= $this->network->get_group_by_name($_POST['groupname'], FALSE, TRUE);
			$r	= $p->set_group_id($gid);
			if( ! $r ) {
				echo '<result><status>ERROR</status><message>'.$this->lang('pf_msgerr_group').'</message></result>';
				exit;
			}
		}
		if(isset($_POST['at_link']) && $_POST['at_link']=="-1") {
			$a	= $p->get_attached();
			$tmp	= $post_temp->get_attached();
			$a['link']	= $tmp['link'];
			$p->set_attached($a);
		}
		if(isset($_POST['at_image']) && $_POST['at_image']=="-1") {
			$a	= $p->get_attached();
			$tmp	= $post_temp->get_attached();
			$a['image']	= $tmp['image'];
			$p->set_attached($a);
		}
		if(isset($_POST['at_videoembed']) && $_POST['at_videoembed']=="-1") {
			$a	= $p->get_attached();
			$tmp	= $post_temp->get_attached();
			$a['videoembed']	= $tmp['videoembed'];
			$p->set_attached($a);
		}
		if(isset($_POST['at_file']) && $_POST['at_file']=="-1") {
			$a	= $p->get_attached();
			$tmp	= $post_temp->get_attached();
			$a['file']	= $tmp['file'];
			$p->set_attached($a);
		}
		$res	= $p->save();
		if( ! $res ) {
			echo '<result><status>ERROR</status><message>'.$this->lang('pf_msgerr_sys').'</message></result>';
			exit;
		}
		$okmsg	= isset($_POST['username'])&&!empty($_POST['username']) ? 'pf_msgok_sent' : 'pf_msgok_posted';
		echo '<result><status>OK</status><message>'.$this->lang($okmsg).'</message></result>';
		exit;
	}
	
?>