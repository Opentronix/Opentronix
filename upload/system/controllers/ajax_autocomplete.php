<?php
	
	$this->load_langfile('inside/global.php');
	
	echo '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>';
	
	if( !$this->network->id ) {
		echo '<result></result>';
		return;
	}
	if( !$this->user->is_logged ) {
		echo '<result></result>';
		return;
	}
	
	$datatype	= isset($_POST['datatype']) ? trim($_POST['datatype']) : '';
	if( $datatype!='username' && $datatype!='groupname' ) {
		echo '<result></result>';
		return;
	}
	$word		= isset($_POST['word']) ? trim($_POST['word']) : '';
	if( mb_strlen($word) < 2 ) {
		echo '<result></result>';
		return;
	}
	
	echo '<result>';
	
	if( $datatype == 'username' )
	{
		$w	= $this->db2->escape($word);
		$r	= $this->db2->query('SELECT id FROM users WHERE id<>"'.$this->user->id.'" AND active=1 AND (username LIKE "'.$w.'%" OR fullname LIKE "'.$w.'%" OR fullname LIKE "% '.$w.'%") ORDER BY num_followers DESC, fullname ASC LIMIT 5');
		$i	= 0;
		$n	= $this->db2->num_rows();
		while($obj = $this->db2->fetch_object($r)) {
			$i	++;
			if( ! $u = $this->network->get_user_by_id($obj->id) ) {
				continue;
			}
			$fullname	= htmlspecialchars($u->fullname);
			$username	= htmlspecialchars($u->username);
			$pos	= mb_stripos($fullname, $word);
			if(FALSE !== $pos) {
				$tmp1	= mb_substr($fullname,0,$pos);
				$tmp2	= mb_substr($fullname,$pos,mb_strlen($word));
				$tmp3	= mb_substr($fullname,$pos+mb_strlen($word));
				$fullname	= $tmp1.'<span>'.$tmp2.'</span>'.$tmp3;
			}
			$pos	= mb_stripos($username, $word);
			if(FALSE !== $pos) {
				$tmp1	= mb_substr($username,0,$pos);
				$tmp2	= mb_substr($username,$pos,mb_strlen($word));
				$tmp3	= mb_substr($username,$pos+mb_strlen($word));
				$username	= $tmp1.'<span>'.$tmp2.'</span>'.$tmp3;
			}
			$html	= '<a href="javascript:;" style="'.($i==$n?'border-bottom:0px;':'').'">';
			$html	.= '<img src="'.$C->IMG_URL.'avatars/thumbs3/'.$u->avatar.'" alt="" />';
			$html	.= '<div><b>'.$fullname.'</b>@'.$username.'</div>';
			$html	.= '</a>';
			echo '<row word="'.htmlspecialchars($u->username).'" url="'.htmlspecialchars($C->SITE_URL.$u->username).'" html="'.htmlspecialchars($html).'" />';
		}
	}
	elseif( $datatype == 'groupname' )
	{
		$not_in_groups	= array();
		if( !$this->user->is_logged || !$this->user->info->is_network_admin ) {
			$r	= $db2->query('SELECT id FROM groups WHERE is_public=0');
			while($obj = $db2->fetch_object($r)) {
				$g	= $this->network->get_group_by_id($obj->id);
				if( ! $g ) {
					$not_in_groups[]	= $obj->id;
					continue;
				}
				if( ! $this->user->is_logged ) {
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
		$not_in_groups	= count($not_in_groups)>0 ? ('AND id NOT IN('.implode(', ', $not_in_groups).')') : '';
		$w	= $this->db2->escape($word);
		$r	= $this->db2->query('SELECT id FROM groups WHERE (groupname LIKE "%'.$w.'%" OR title LIKE "%'.$w.'%") '.$not_in_groups.' ORDER BY num_followers DESC, title ASC LIMIT 5');
		$i	= 0;
		$n	= $this->db2->num_rows();
		while($obj = $this->db2->fetch_object($r)) {
			$i	++;
			if( ! $g = $this->network->get_group_by_id($obj->id) ) {
				continue;
			}
			$title	= str_cut($g->title, 14);
			$title	= htmlspecialchars($title);
			$pos	= mb_stripos($title, $word);
			if(FALSE !== $pos) {
				$tmp1	= mb_substr($title,0,$pos);
				$tmp2	= mb_substr($title,$pos,mb_strlen($word));
				$tmp3	= mb_substr($title,$pos+mb_strlen($word));
				$title	= $tmp1.'<span>'.$tmp2.'</span>'.$tmp3;
			}
			$html	= '<a href="javascript:;" style="'.($i==$n?'border-bottom:0px;':'').'">';
			$html	.= '<img src="'.$C->IMG_URL.'avatars/thumbs3/'.$g->avatar.'" alt="" />';
			$html	.= '<div><b>'.$title.'</b>'.htmlspecialchars(str_cut($g->about_me,17)).'</div>';
			$html	.= '</a>';
			echo '<row word="'.htmlspecialchars($g->title).'" url="'.htmlspecialchars($C->SITE_URL.$g->groupname).'" html="'.htmlspecialchars($html).'" />';
		}
	}
	
	echo '</result>';
	
?>