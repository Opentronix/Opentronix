<?php
	
	$date		= time() - 14*24*60*60;
	$faved	= array();
	$r	= $db2->query('SELECT DISTINCT post_id FROM post_favs WHERE post_type="public" ');
	while($tmp = $db2->fetch_object($r)) {
		$faved[intval($tmp->post_id)]	= 1;
	}
	$posts	= array();
	$r	= $db2->query('SELECT id FROM posts WHERE api_id=2 AND date<"'.$date.'" AND comments=0 ');
	while($tmp = $db2->fetch_object($r)) {
		$tmp->id	= intval($tmp->id);
		if( isset($faved[$tmp->id]) ) {
			continue;
		}
		$posts[]	= $tmp->id;
	}
	
	$user	= (object) array (
		'is_logged'	=> TRUE,
		'id'		=> 0,
		'info'	=> (object) array('is_network_admin' => 1),
	);
	foreach($posts as $tmp) {
		$p	= new post('public', $tmp);
		$p->delete_this_post();
	}
	
?>