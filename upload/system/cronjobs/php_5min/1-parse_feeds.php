<?php
	
	$new_rss_posts	= 0;
	
	$r	= $db2->query('SELECT * FROM groups_rssfeeds WHERE is_deleted=0');
	while($obj = $db2->fetch_object($r)) {
		$db2->query('UPDATE groups_rssfeeds SET date_last_crawl="'.time().'" WHERE id="'.$obj->id.'" LIMIT 1');
		$obj->feed_url		= stripslashes($obj->feed_url);
		$obj->feed_userpwd	= stripslashes($obj->feed_userpwd);
		$obj->feed_title		= stripslashes($obj->feed_title);
		$obj->filter_keywords	= stripslashes($obj->filter_keywords);
		$f	= null;
		$f	= new rssfeed($obj->feed_url, $obj->feed_userpwd);
		$f->fetch();
		if( $f->error ) {
			continue;
		}
		if( $f->title!=$obj->feed_title && !empty($f->title) ) {
			$db2->query('UPDATE groups_rssfeeds SET feed_title="'.$db2->e($f->title).'" WHERE id="'.$obj->id.'" LIMIT 1');
		}
		$dt	= $f->get_lastitem_date();
		if( ! $dt ) {
			continue;
		}
		$db2->query('UPDATE groups_rssfeeds SET date_last_item="'.$dt.'" WHERE id="'.$obj->id.'" LIMIT 1');
		$items	= $f->get_ordered_items($obj->date_last_item, $obj->filter_keywords);
		if( count($items) > 0 ) {
			$posts	= 0;
			foreach($items as $item) {
				$message	= $item->source_title;
				if( empty($message) && !empty($item->source_description) ) {
					$message	= $item->source_description;
				}
				if( empty($message) ) {
					continue;
				}
				$p	= null;
				$p	= new newpost();
				$p->set_api_id(2);
				$p->set_user_advanced( $network, ((object)array('id'=>0,'is_logged'=>TRUE)) );
				$p->set_group_id($obj->group_id);
				$p->set_message($message);
				if( ! empty($item->source_url) ) {
					$p->attach_link($item->source_url);
				}
				if( ! empty($item->source_image) ) {
					$p->attach_image($item->source_image);
				}
				if( ! empty($item->source_video) ) {
					$p->attach_videoembed($item->source_video);
				}
				if( ! empty($item->source_description) && $item->source_description!=$message ) {
					$p->attach_richtext($item->source_description);
				}
				if( $pid = $p->save() ) {
					$pid	= intval(str_replace(array('_private','_public'),'',$pid));
					$db2->query('INSERT INTO groups_rssfeeds_posts SET rssfeed_id="'.$obj->id.'", post_id="'.$pid.'" ');
					$new_rss_posts	++;
					$posts	++;
				}
			}
			if( $posts > 0 ) {
				$db2->query('UPDATE groups_rssfeeds SET date_last_post="'.time().'" WHERE id="'.$obj->id.'" LIMIT 1');
			}
		}
	}
	$r	= $db2->query('SELECT * FROM users_rssfeeds WHERE is_deleted=0');
	while($obj = $db2->fetch_object($r)) {
		if( $obj->user_id == 0 ) {
			continue;
		}
		$db2->query('UPDATE users_rssfeeds SET date_last_crawl="'.time().'" WHERE id="'.$obj->id.'" LIMIT 1');
		$obj->feed_url		= stripslashes($obj->feed_url);
		$obj->feed_userpwd	= stripslashes($obj->feed_userpwd);
		$obj->feed_title		= stripslashes($obj->feed_title);
		$obj->filter_keywords	= stripslashes($obj->filter_keywords);
		$f	= null;
		$f	= new rssfeed($obj->feed_url, $obj->feed_userpwd);
		$f->fetch();
		if( $f->error ) {
			continue;
		}
		if( $f->title!=$obj->feed_title && !empty($f->title) ) {
			$db2->query('UPDATE users_rssfeeds SET feed_title="'.$db2->e($f->title).'" WHERE id="'.$obj->id.'" LIMIT 1');
		}
		$dt	= $f->get_lastitem_date();
		if( ! $dt ) {
			continue;
		}
		$db2->query('UPDATE users_rssfeeds SET date_last_item="'.$dt.'" WHERE id="'.$obj->id.'" LIMIT 1');
		$items	= $f->get_ordered_items($obj->date_last_item, $obj->filter_keywords);
		if( count($items) > 0 ) {
			$posts	= 0;
			foreach($items as $item) {
				$message	= $item->source_title;
				if( empty($message) && !empty($item->source_description) ) {
					$message	= $item->source_description;
				}
				if( empty($message) ) {
					continue;
				}
				$p	= null;
				$p	= new newpost();
				$p->set_api_id(2);
				$tmpu	= $network->get_user_by_id($obj->user_id);
				$tmpu->info	= $tmpu;
				$tmpu->is_logged	= TRUE;
				$p->set_user_advanced( $network,  $tmpu );
				$p->set_message($message);
				if( ! empty($item->source_url) ) {
					$p->attach_link($item->source_url);
				}
				if( ! empty($item->source_image) ) {
					$p->attach_image($item->source_image);
				}
				if( ! empty($item->source_video) ) {
					$p->attach_videoembed($item->source_video);
				}
				if( ! empty($item->source_description) && $item->source_description!=$message ) {
					$p->attach_richtext($item->source_description);
				}
				if( $pid = $p->save() ) {
					$pid	= intval(str_replace(array('_private','_public'),'',$pid));
					$db2->query('INSERT INTO users_rssfeeds_posts SET rssfeed_id="'.$obj->id.'", post_id="'.$pid.'" ');
					$new_rss_posts	++;
					$posts	++;
				}
			}
			if( $posts > 0 ) {
				$db2->query('UPDATE users_rssfeeds SET date_last_post="'.time().'" WHERE id="'.$obj->id.'" LIMIT 1');
			}
		}
	}
	
	if( $new_rss_posts > 0 ) {
		echo 'New RSS posts: '.$new_rss_posts."\n";
	}
	
?>