<?php
	
	$public_groups	= array(0);
	$db2->query('SELECT id FROM groups WHERE is_public=1');
	while($obj = $db2->fetch_object()) {
		$public_groups[]	= $obj->id;
	}
	$public_groups	= implode(', ', $public_groups);
	
	$res	= $db2->query('SELECT id FROM users');
	while($tmp = $db2->fetch_object($res)) {
		$nm	= $db2->fetch_field('SELECT COUNT(id) FROM posts WHERE user_id="'.$tmp->id.'" AND api_id!=2 AND group_id IN('.$public_groups.') ');
		$db2->query('UPDATE users SET num_posts="'.intval($nm).'" WHERE id="'.$tmp->id.'" LIMIT 1');
	}
	
	$res	= $db2->query('SELECT id FROM groups');
	while($tmp = $db2->fetch_object($res)) {
		$nm	= $db2->fetch_field('SELECT COUNT(id) FROM posts WHERE group_id="'.$tmp->id.'" AND (user_id!=0 OR api_id=2) ');
		$db2->query('UPDATE groups SET num_posts="'.intval($nm).'" WHERE id="'.$tmp->id.'" LIMIT 1');
	}
	
	
?>