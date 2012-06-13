<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/settings.php');
	
	$D->page_title	= $this->lang('settings_notif_pagetitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->i	= $this->network->get_user_notif_rules($this->user->id);
	
	$p	= & $_POST;
	
	$D->submit	= FALSE;
	if( isset($_POST['sbm']) ) {
		$D->submit	= TRUE;
		$i	= array();
		$i['ntf_them_if_i_follow_usr']	= isset($p['ntf_them_if_i_follow_usr'])		? 1 : 0;
		$i['ntf_them_if_i_comment']		= isset($p['ntf_them_if_i_comment'])		? 1 : 0;
		$i['ntf_them_if_i_edt_profl']		= isset($p['ntf_them_if_i_edt_profl'])		? 1 : 0;
		$i['ntf_them_if_i_edt_pictr']		= isset($p['ntf_them_if_i_edt_pictr'])		? 1 : 0;
		$i['ntf_them_if_i_create_grp']	= isset($p['ntf_them_if_i_create_grp'])		? 1 : 0;
		$i['ntf_them_if_i_join_grp']		= isset($p['ntf_them_if_i_join_grp'])		? 1 : 0;
		$i['ntf_me_if_u_follows_me']		= isset($p['ntf_me_if_u_follows_me'][3])		? 1 : 2;
		$i['ntf_me_if_u_follows_u2']		= isset($p['ntf_me_if_u_follows_u2'][2],$p['ntf_me_if_u_follows_u2'][3])	? 1 : ( isset($p['ntf_me_if_u_follows_u2'][2])		? 2 : ( isset($p['ntf_me_if_u_follows_u2'][3])		? 3 : 0 ) );
		$i['ntf_me_if_u_commments_me']	= isset($p['ntf_me_if_u_commments_me'][3])	? 3 : 0;
		$i['ntf_me_if_u_commments_m2']	= isset($p['ntf_me_if_u_commments_m2'][3])	? 3 : 0;
		$i['ntf_me_if_u_edt_profl']		= isset($p['ntf_me_if_u_edt_profl'][2],$p['ntf_me_if_u_edt_profl'][3])		? 1 : ( isset($p['ntf_me_if_u_edt_profl'][2])		? 2 : ( isset($p['ntf_me_if_u_edt_profl'][3])		? 3 : 0 ) );
		$i['ntf_me_if_u_edt_pictr']		= isset($p['ntf_me_if_u_edt_pictr'][2],$p['ntf_me_if_u_edt_pictr'][3])		? 1 : ( isset($p['ntf_me_if_u_edt_pictr'][2])		? 2 : ( isset($p['ntf_me_if_u_edt_pictr'][3])		? 3 : 0 ) );
		$i['ntf_me_if_u_creates_grp']		= isset($p['ntf_me_if_u_creates_grp'][2],$p['ntf_me_if_u_creates_grp'][3])	? 1 : ( isset($p['ntf_me_if_u_creates_grp'][2])		? 2 : ( isset($p['ntf_me_if_u_creates_grp'][3])		? 3 : 0 ) );
		$i['ntf_me_if_u_joins_grp']		= isset($p['ntf_me_if_u_joins_grp'][2],$p['ntf_me_if_u_joins_grp'][3])		? 1 : ( isset($p['ntf_me_if_u_joins_grp'][2])		? 2 : ( isset($p['ntf_me_if_u_joins_grp'][3])		? 3 : 0 ) );
		$i['ntf_me_if_u_invit_me_grp']	= isset($p['ntf_me_if_u_invit_me_grp'][3])	? 1 : 2;
		$i['ntf_me_if_u_posts_qme']		= isset($p['ntf_me_if_u_posts_qme'][3])		? 3 : 0;
		$i['ntf_me_if_u_posts_prvmsg']	= isset($p['ntf_me_if_u_posts_prvmsg'][3])	? 3 : 0;
		$i['ntf_me_if_u_registers']		= isset($p['ntf_me_if_u_registers'][2],$p['ntf_me_if_u_registers'][3])		? 1 : ( isset($p['ntf_me_if_u_registers'][2])		? 2 : ( isset($p['ntf_me_if_u_registers'][3])		? 3 : 0 ) );
		$in_sql	= array();
		$in_sql[]	= '`user_id`="'.$this->user->id.'"';
		foreach($i as $k=>$v) {
			$in_sql[]	= '`'.$k.'`="'.$v.'"';
		}
		$in_sql	= implode(', ', $in_sql);
		$db2->query('REPLACE INTO users_notif_rules SET '.$in_sql);
		$D->i	= $this->network->get_user_notif_rules($this->user->id, TRUE);
	}
	
	$this->load_template('settings_notifications.php');
	
?>