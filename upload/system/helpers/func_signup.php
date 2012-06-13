<?php
	
	function set_user_default_notification_rules($user_id)
	{
		global $db2, $network;
		$rules	= array (
			// 0 - off, 1 - on
			'ntf_them_if_i_follow_usr'	=> 1,
			'ntf_them_if_i_comment'		=> 1,
			'ntf_them_if_i_edt_profl'	=> 1,
			'ntf_them_if_i_edt_pictr'	=> 1,
			'ntf_them_if_i_create_grp'	=> 1,
			'ntf_them_if_i_join_grp'	=> 1,
			
			// 0 - off, 2 - post, 3 - email, 1 - both
			'ntf_me_if_u_follows_me'	=> 3,
			'ntf_me_if_u_follows_u2'	=> 2,
			'ntf_me_if_u_commments_me'	=> 3,
			'ntf_me_if_u_commments_m2'	=> 3,
			'ntf_me_if_u_edt_profl'		=> 2,
			'ntf_me_if_u_edt_pictr'		=> 2,
			'ntf_me_if_u_creates_grp'	=> 2,
			'ntf_me_if_u_joins_grp'		=> 2,
			'ntf_me_if_u_invit_me_grp'	=> 1,
			'ntf_me_if_u_posts_qme'		=> 3,
			'ntf_me_if_u_posts_prvmsg'	=> 3,
			'ntf_me_if_u_registers'		=> 0,
		);
		$in_sql	= array();
		$in_sql[]	= '`user_id`="'.$user_id.'"';
		foreach($rules as $k=>$v) {
			$in_sql[]	= '`'.$k.'`="'.$v.'"';
		}
		$in_sql	= implode(', ', $in_sql);
		$db2->query('REPLACE INTO users_notif_rules SET '.$in_sql);
	}
	
?>