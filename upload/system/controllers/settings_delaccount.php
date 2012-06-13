<?php

	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');	
	$this->load_langfile('inside/settings.php');
	
	$D->page_title	= $this->lang('settings_delaccount_pagetitle', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$D->submit	= FALSE;
	$D->error	= FALSE;
	$D->errmsg	= '';
	if( isset($_POST['userpass']) ) 
	{
		$D->submit	= TRUE;
		if( md5($_POST['userpass']) != $this->user->info->password ) {
			$D->error	= TRUE;
			$D->errmsg	= 'st_delaccount_pass_err';
		}
		if( ! $D->error && $this->user->info->is_network_admin ) {
			$db2->query('SELECT id FROM users WHERE id<>"'.$this->user->id.'" AND active=1 AND is_network_admin=1 LIMIT 1');
			if( $db2->num_rows() == 0 ) {
				$D->error	= TRUE;
				$D->errmsg	= 'st_delaccount_admins_err';
			}
		}
		if( ! $D->error ) 
		{
			$db2->query('DELETE FROM groups_admins WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM groups_private_members WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM post_favs WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM post_userbox WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM post_userbox_feeds WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM searches WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM users_dashboard_tabs WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM users_details WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM users_invitations WHERE user_id="'.$this->user->id.'" OR recp_user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM users_notif_rules WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM users_pageviews WHERE user_id="'.$this->user->id.'" LIMIT 1');
			$db2->query('DELETE FROM users WHERE id="'.$this->user->id.'" LIMIT 1');
			
			$db2->query('DELETE FROM users_followed WHERE whom="'.$this->user->id.'" ');
			$res	= $db2->query('SELECT id, whom FROM users_followed WHERE who="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$db2->query('DELETE FROM users_followed WHERE id="'.$tmp->id.'" ');
				$db2->query('UPDATE users SET num_followers=num_followers-1 WHERE id="'.$tmp->whom.'" ');
			}
			$res	= $db2->query('SELECT id, group_id FROM groups_followed WHERE user_id="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$db2->query('DELETE FROM groups_followed WHERE id="'.$tmp->id.'" ');
				$db2->query('UPDATE groups SET num_followers=num_followers-1 WHERE id="'.$tmp->group_id.'" ');
			}
			
			$res	= $db2->query('SELECT * FROM posts WHERE user_id="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$tmpp	= new post('public', FALSE, $tmp);
				$tmpp->delete_this_post();
			}
			$res	= $db2->query('SELECT * FROM posts_pr WHERE user_id="'.$this->user->id.'" OR to_user="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$tmpp	= new post('private', FALSE, $tmp);
				$tmpp->delete_this_post();
			}
			
			$db2->query('DELETE FROM posts_comments_watch WHERE user_id="'.$this->user->id.'" ');
			$res	= $db2->query('SELECT id, post_id FROM posts_comments WHERE user_id="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$db2->query('DELETE FROM posts_comments WHERE id="'.$this->user->id.'" LIMIT 1');
				$db2->query('DELETE FROM posts_comments_mentioned WHERE comment_id="'.$this->user->id.'" ');
				$db2->query('UPDATE posts SET comments=comments-1 WHERE id="'.$tmp->post_id.'" LIMIT 1');
			}
			$res	= $db2->query('SELECT id, post_id FROM posts_mentioned WHERE user_id="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$db2->query('DELETE FROM posts_mentioned WHERE id="'.$tmp->id.'" LIMIT 1');
				$db2->query('UPDATE posts SET mentioned=mentioned-1 WHERE id="'.$tmp->post_id.'" LIMIT 1');
			}
			$res	= $db2->query('SELECT id, post_id FROM posts_pr_mentioned WHERE user_id="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$db2->query('DELETE FROM posts_pr_mentioned WHERE id="'.$tmp->id.'" LIMIT 1');
				$db2->query('UPDATE posts_pr SET mentioned=mentioned-1 WHERE id="'.$tmp->post_id.'" LIMIT 1');
			}
			$res	= $db2->query('SELECT id, comment_id FROM posts_comments_mentioned WHERE user_id="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$db2->query('DELETE FROM posts_comments_mentioned WHERE id="'.$tmp->id.'" LIMIT 1');
				$db2->query('UPDATE posts_comments SET mentioned=mentioned-1 WHERE id="'.$tmp->comment_id.'" LIMIT 1');
			}
			$res	= $db2->query('SELECT id, comment_id FROM posts_pr_comments_mentioned WHERE user_id="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$db2->query('DELETE FROM posts_pr_comments_mentioned WHERE id="'.$tmp->id.'" LIMIT 1');
				$db2->query('UPDATE posts_pr_comments SET mentioned=mentioned-1 WHERE id="'.$tmp->comment_id.'" LIMIT 1');
			}
			
			$res	= $db2->query('SELECT id FROM users_rssfeeds WHERE user_id="'.$this->user->id.'" ');
			while($tmp = $db2->fetch_object($res)) {
				$db2->query('DELETE FROM users_rssfeeds_posts WHERE rssfeed_id="'.$tmp->id.'" ');
			}
			$db2->query('DELETE FROM users_rssfeeds WHERE user_id="'.$this->user->id.'" ');
			
			$this->network->get_user_by_id($this->user->id, TRUE);
			$this->network->get_user_by_username($this->user->info->username, TRUE);
			$this->network->get_user_by_email($this->user->info->email, TRUE);
			
			if( $this->user->info->avatar != $C->DEF_AVATAR_USER ) {
				rm( $C->IMG_DIR.'avatars/'.$this->user->info->avatar );
				rm( $C->IMG_DIR.'avatars/thumbs1/'.$this->user->info->avatar );
				rm( $C->IMG_DIR.'avatars/thumbs2/'.$this->user->info->avatar );
				rm( $C->IMG_DIR.'avatars/thumbs3/'.$this->user->info->avatar );
			}
			
			$this->user->logout();
			$this->redirect( $C->SITE_URL );
		}
	}
	
	$this->load_template('settings_delaccount.php');
	
?>