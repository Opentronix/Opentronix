<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	$db2->query('SELECT 1 FROM users WHERE id="'.$this->user->id.'" AND is_network_admin=1 LIMIT 1');
	if( 0 == $db2->num_rows() ) {
		$this->redirect('dashboard');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/admin.php');
	
	$D->page_title	= $this->lang('admpgtitle_statistics', array('#SITE_TITLE#'=>$C->SITE_TITLE));
	
	$firstpost	= $db2->fetch_field('SELECT date FROM posts ORDER BY id ASC LIMIT 1');
	$firstpost	= $firstpost ? intval($firstpost) : time();
	
	$D->dates	= array();
	$D->seldate	= FALSE;
	$D->dates['all']	= (object) array(
		'lnk'		=> 'month:all',
		'txt'		=> $this->lang('admstat_filter_all'),
	);
	$y	= date('Y');
	$m	= date('m');
	for($i=0; $i<12; $i++) {
		$tst		= mktime(0, 0, 1, $m-$i, 1, $y);
		$month	= date('Ym', $tst);
		$D->dates[$month]	= (object) array(
			'lnk'		=> 'month:'.$month,
			'txt'		=> strftime($this->lang('admstat_filter_dateformat_m'), $tst),
		);
		if( $month <= date('Ym',$firstpost) ) {
			break;
		}
	}
	
	$D->year	= date('Y');
	$D->month	= date('m');
	$D->day	= FALSE;
	$D->seldate	= $D->dates[date('Ym')]->txt;
	
	if( $this->param('month') && isset($D->dates[$this->param('month')]) ) {
		if( preg_match('/^[0-9]{6}$/',$this->param('month')) ) {
			$D->year	= substr($this->param('month'), 0, 4);
			$D->month	= substr($this->param('month'), 4, 2);
		}
		else {
			$D->year	= FALSE;
			$D->month	= FALSE;
		}
		$D->seldate	= $D->dates[$this->param('month')]->txt;
	}
	if( $D->month && $this->param('day') ) {
		$tmp	= intval($this->param('day'));
		if( $tmp>=1 && $tmp<=31 ) {
			$D->day	= $tmp;
			$D->seldate	= strftime($this->lang('admstat_filter_dateformat_d'), mktime(0,0,1,$D->month,$D->day,$D->year));
		}
	}
	
	$dt1	= FALSE;
	$dt2	= FALSE;
	$dt1_tst	= FALSE;
	$dt2_tst	= FALSE;
	if( $D->year && $D->month && $D->day ) {
		$dt1_tst	= mktime(0, 0, 1, $D->month, $D->day, $D->year);
		$dt2_tst	= $dt1_tst + 24*60*60 - 1;
		$dt1	= date('Y-m-d', $dt1_tst);
		$dt2	= date('Y-m-d', $dt2_tst);
	}
	elseif( $D->year && $D->month ) {
		$dt1_tst	= mktime(0, 0, 1, $D->month, 1, $D->year);
		$dt2_tst	= mktime(0, 0, 0, $D->month+1, 1, $D->year);
		if( date('Ym')==$D->year.$D->month ) {
			$dt2_tst	= min($dt2_tst, time());
		}
		$dt1	= date('Y-m', $dt1_tst);
		$dt2	= date('Y-m', $dt2_tst);
	}
	else {
		$dt1_tst	= $firstpost;
		$dt2_tst	= time();
		$dt1	= date('Y-m', $dt1_tst);
		$dt2	= date('Y-m', $dt2_tst);
	}
	
	$D->members_total	= $db2->fetch_field('SELECT COUNT(id) FROM users WHERE active=1');
	$D->members_new	= $db2->fetch_field('SELECT COUNT(id) FROM users WHERE active=1 AND reg_date BETWEEN '.$dt1_tst.' AND '.$dt2_tst);
	$D->members_newp	= $D->members_total==0 ? 0 : (100 * $D->members_new / $D->members_total);
	
	$D->posts_total	= $db2->fetch_field('SELECT COUNT(id) FROM posts WHERE user_id<>0 AND api_id<>2');
	$D->posts_new	= $db2->fetch_field('SELECT COUNT(id) FROM posts WHERE user_id<>0 AND api_id<>2 AND date BETWEEN '.$dt1_tst.' AND '.$dt2_tst);
	$D->posts_newp	= $D->posts_total==0 ? 0 : (100 * $D->posts_new / $D->posts_total);
	
	$D->stat_visits_m	= array();
	$D->stat_visits_d	= array();
	$D->stat_visits_h	= array();
	
	$D->stat_posts_m	= array();
	$D->stat_posts_d	= array();
	$D->stat_posts_h	= array();
	
	$D->stat_regs_m	= array();
	$D->stat_regs_d	= array();
	
	if( $D->month || $D->day ) {
		for($i=0; $i<24; $i++) {
			$D->stat_visits_h[$i]	= (object) array( 'cnt' => 0 );
			$D->stat_posts_h[$i]	= (object) array( 'cnt' => 0 );
		}
		$db2->query('SELECT SUBSTRING(date,12,2) AS dt, SUM(pageviews) AS cnt FROM users_pageviews WHERE date LIKE "'.$D->year.'-'.$D->month.'-'.($D->day?($D->day.' '):'').'%" GROUP BY dt ASC');
		while($obj = $db2->fetch_object()) {
			$obj->dt	= intval($obj->dt);
			$obj->cnt	= intval($obj->cnt);
			$D->stat_visits_h[$obj->dt]	= (object) array( 'cnt' => $obj->cnt );
		}
		$db2->query('SELECT FROM_UNIXTIME(date,"%H") AS dt, COUNT(id) AS cnt FROM posts WHERE user_id<>0 AND api_id<>2 AND date>="'.$dt1_tst.'" AND date<="'.$dt2_tst.'" GROUP BY dt ASC');
		while($obj = $db2->fetch_object()) {
			$obj->dt	= intval($obj->dt);
			$obj->cnt	= intval($obj->cnt);
			$D->stat_posts_h[$obj->dt]	= (object) array( 'cnt'	=> $obj->cnt );
		}
	}
	if( $D->month && !$D->day ) {
		$tmpdt2	= max($dt1_tst,$dt2_tst);
		$tmpdt1	= min($dt1_tst,$dt2_tst);
		//for($i=max($tmpdt1,$tmpdt2); $i>=min($tmpdt1,$tmpdt2); $i-=24*60*60) {
		for($i=min($tmpdt1,$tmpdt2); $i<=max($tmpdt1,$tmpdt2); $i+=24*60*60) {
			$tmp	= intval(date('d',$i));
			$D->stat_visits_d[$tmp]	= (object) array( 'cnt' => 0 );
			$D->stat_posts_d[$tmp]	= (object) array( 'cnt' => 0 );
			$D->stat_regs_d[$tmp]	= (object) array( 'cnt' => 0 );
		}
		$db2->query('SELECT SUBSTRING(date,9,2) AS dt, SUM(pageviews) AS cnt FROM users_pageviews WHERE date LIKE "'.$D->year.'-'.$D->month.'-%" GROUP BY dt ASC');
		while($obj = $db2->fetch_object()) {
			$obj->dt	= intval($obj->dt);
			$obj->cnt	= intval($obj->cnt);
			$D->stat_visits_d[$obj->dt]	= (object) array( 'cnt' => $obj->cnt );
		}
		$db2->query('SELECT FROM_UNIXTIME(date,"%d") AS dt, COUNT(id) AS cnt FROM posts WHERE user_id<>0 AND api_id<>2 AND date>="'.$dt1_tst.'" AND date<="'.$dt2_tst.'" GROUP BY dt ASC');
		while($obj = $db2->fetch_object()) {
			$obj->dt	= intval($obj->dt);
			$obj->cnt	= intval($obj->cnt);
			$D->stat_posts_d[$obj->dt]	= (object) array( 'cnt' => $obj->cnt );
		}
		$db2->query('SELECT FROM_UNIXTIME(reg_date,"%d") AS dt, COUNT(id) AS cnt FROM users WHERE reg_date>="'.$dt1_tst.'" AND reg_date<="'.$dt2_tst.'" GROUP BY dt ASC');
		while($obj = $db2->fetch_object()) {
			$obj->dt	= intval($obj->dt);
			$obj->cnt	= intval($obj->cnt);
			$D->stat_regs_d[$obj->dt]	= (object) array( 'cnt' => $obj->cnt );
		}
	}
	if( !$D->month ) {
		$tmpy	= date('Y');
		$tmpm	= date('m');
		for($i=0; $i<12; $i++) {
			$tmpmnt	= mktime(0, 0, 1, $tmpm-$i, 1, $tmpy);
			$tmpdt	= date('Y-m', $tmpmnt);
			$D->stat_visits_m[$tmpdt]	= (object) array( 'cnt' => 0 );
			$D->stat_posts_m[$tmpdt]	= (object) array( 'cnt' => 0 );
			$D->stat_regs_m[$tmpdt]		= (object) array( 'cnt' => 0 );
			if( $firstpost > $tmpmnt ) {
				break;
			}
		}
		$db2->query('SELECT SUBSTRING(date,1,7) AS dt, SUM(pageviews) AS cnt FROM users_pageviews WHERE 1 GROUP BY dt DESC LIMIT 12');
		while($obj = $db2->fetch_object()) {
			$obj->dt	= trim($obj->dt);
			$obj->cnt	= intval($obj->cnt);
			$D->stat_visits_m[$obj->dt]	= (object) array( 'cnt' => $obj->cnt );
		}
		$D->stat_visits_m	= array_reverse($D->stat_visits_m, TRUE);
		$db2->query('SELECT FROM_UNIXTIME(date,"%Y-%m") AS dt, COUNT(id) AS cnt FROM posts WHERE user_id<>0 AND api_id<>2 GROUP BY dt DESC LIMIT 12');
		while($obj = $db2->fetch_object()) {
			$obj->dt	= trim($obj->dt);
			$obj->cnt	= intval($obj->cnt);
			$D->stat_posts_m[$obj->dt]	= (object) array( 'cnt' => $obj->cnt );
		}
		$D->stat_posts_m	= array_reverse($D->stat_posts_m, TRUE);
		$db2->query('SELECT FROM_UNIXTIME(reg_date,"%Y-%m") AS dt, COUNT(id) AS cnt FROM users WHERE 1 GROUP BY dt DESC');
		while($obj = $db2->fetch_object()) {
			$obj->dt	= trim($obj->dt);
			$obj->cnt	= intval($obj->cnt);
			$D->stat_regs_m[$obj->dt]	= (object) array( 'cnt' => $obj->cnt );
		}
	}
	
	if( count($D->stat_visits_m) ) {
		$tmp	= 0;
		foreach($D->stat_visits_m as $dt=>$obj) {
			$tmp	+= $obj->cnt;
			$D->stat_visits_m[$dt]->txt	= $dt;
		}
		foreach($D->stat_visits_m as $dt=>$obj) {
			$D->stat_visits_m[$dt]->cntp	= $tmp==0 ? 0 : (100 * $obj->cnt / $tmp);
		}
		if( $tmp == 0 ) {
			$D->stat_visits_m	= array();
		}
	}
	if( count($D->stat_posts_m) ) {
		$tmp	= 0;
		foreach($D->stat_posts_m as $dt=>$obj) {
			$tmp	+= $obj->cnt;
			$D->stat_posts_m[$dt]->txt	= $dt;
		}
		foreach($D->stat_posts_m as $dt=>$obj) {
			$D->stat_posts_m[$dt]->cntp	= $tmp==0 ? 0 : (100 * $obj->cnt / $tmp);
		}
		if( $tmp == 0 ) {
			$D->stat_posts_m	= array();
		}
	}
	if( count($D->stat_regs_m) ) {
		$tmp	= 0;
		foreach($D->stat_regs_m as $dt=>$obj) {
			$tmp	+= $obj->cnt;
			$D->stat_regs_m[$dt]->txt	= $dt;
		}
		foreach($D->stat_regs_m as $dt=>$obj) {
			$D->stat_regs_m[$dt]->cntp	= $tmp==0 ? 0 : (100 * $obj->cnt / $tmp);
		}
		if( $tmp == 0 ) {
			$D->stat_regs_m	= array();
		}
	}
	if( count($D->stat_visits_d) ) {
		$tmp	= 0;
		foreach($D->stat_visits_d as $dt=>$obj) {
			$tmp	+= $obj->cnt;
			$D->stat_visits_d[$dt]->txt	= $dt;
		}
		foreach($D->stat_visits_d as $dt=>$obj) {
			$D->stat_visits_d[$dt]->cntp	= $tmp==0 ? 0 : (100 * $obj->cnt / $tmp);
		}
		if( $tmp == 0 ) {
			$D->stat_visits_d	= array();
		}
	}
	if( count($D->stat_posts_d) ) {
		$tmp	= 0;
		foreach($D->stat_posts_d as $dt=>$obj) {
			$tmp	+= $obj->cnt;
			$D->stat_posts_d[$dt]->txt	= $dt;
		}
		foreach($D->stat_posts_d as $dt=>$obj) {
			$D->stat_posts_d[$dt]->cntp	= $tmp==0 ? 0 : (100 * $obj->cnt / $tmp);
		}
		if( $tmp == 0 ) {
			$D->stat_posts_d	= array();
		}
	}
	if( count($D->stat_regs_d) ) {
		$tmp	= 0;
		foreach($D->stat_regs_d as $dt=>$obj) {
			$tmp	+= $obj->cnt;
			$D->stat_regs_d[$dt]->txt	= $dt;
		}
		foreach($D->stat_regs_d as $dt=>$obj) {
			$D->stat_regs_d[$dt]->cntp	= $tmp==0 ? 0 : (100 * $obj->cnt / $tmp);
		}
		if( $tmp == 0 ) {
			$D->stat_regs_d	= array();
		}
	}
	if( count($D->stat_visits_h) ) {
		$tmp	= 0;
		foreach($D->stat_visits_h as $dt=>$obj) {
			$tmp	+= $obj->cnt;
			$D->stat_visits_h[$dt]->txt	= str_pad($dt,2,'0',STR_PAD_LEFT);
		}
		foreach($D->stat_visits_h as $dt=>$obj) {
			$D->stat_visits_h[$dt]->cntp	= $tmp==0 ? 0 : (100 * $obj->cnt / $tmp);
		}
		if( $tmp == 0 ) {
			$D->stat_visits_h	= array();
		}
	}
	if( count($D->stat_posts_h) ) {
		$tmp	= 0;
		foreach($D->stat_posts_h as $dt=>$obj) {
			$tmp	+= $obj->cnt;
			$D->stat_posts_h[$dt]->txt	= str_pad($dt,2,'0',STR_PAD_LEFT);
		}
		foreach($D->stat_posts_h as $dt=>$obj) {
			$D->stat_posts_h[$dt]->cntp	= $tmp==0 ? 0 : (100 * $obj->cnt / $tmp);
		}
		if( $tmp == 0 ) {
			$D->stat_posts_h	= array();
		}
	}
	
	$D->charts	= array();
	
	if( count($D->stat_posts_m) ) {
		$tmp	= new stdClass;
		$tmp->bg_colour	= '#ffffff';
		$tmp->x_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->y_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->x_axis->labels	= (object) array( 'labels' => array() );
		foreach($D->stat_posts_m as $obj) { $tmp->x_axis->labels->labels[] = strval($obj->txt); }
		$mxv	= 0;
		foreach($D->stat_posts_m as $obj) { $mxv = max($mxv, $obj->cnt); }
		$mnv	= $mxv;
		foreach($D->stat_posts_m as $obj) { $mnv = min($mnv, $obj->cnt); }
		$d	= $mxv - $mnv;
		$stp	= $d>2000 ? 500 : ($d>1000 ? 200 : ($d>500 ? 100 : ($d>200 ? 50 : ($d>100 ? 20 : ($d>50 ? 10 : ($d>20 ? 5 : 3))))));
		$tmp->y_axis->min	= $mnv;
		$tmp->y_axis->max	= ceil($mxv/$stp)*$stp;
		$tmp->y_axis->steps	= $stp;
		for($i=0; $i<=$tmp->y_axis->max; $i++) { $tmp->y_axis->labels->labels[] = strval($i); }
		$tmp->elements	= array();
		$tmp->elements[0]	= (object) array( 'type' => 'line', 'colour' => '#1975e1', 'width' => 2 );
		$tmp->elements[0]->values	= array();
		foreach($D->stat_posts_m as $obj) { $tmp->elements[0]->values[] = $obj->cnt; }
		$D->charts[]	= (object) array (
			'title'	=> $this->lang('admstat_stat_posts_m', array('#PERIOD#'=>$D->seldate)),
			'json'	=> $tmp,
		);
	}
	if( count($D->stat_posts_d) ) {
		$tmp	= new stdClass;
		$tmp->bg_colour	= '#ffffff';
		$tmp->x_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->y_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->x_axis->labels	= (object) array( 'labels' => array() );
		foreach($D->stat_posts_d as $obj) { $tmp->x_axis->labels->labels[] = strval($obj->txt); }
		$mxv	= 0;
		foreach($D->stat_posts_d as $obj) { $mxv = max($mxv, $obj->cnt); }
		$mnv	= $mxv;
		foreach($D->stat_posts_d as $obj) { $mnv = min($mnv, $obj->cnt); }
		$d	= $mxv - $mnv;
		$stp	= $d>2000 ? 500 : ($d>1000 ? 200 : ($d>500 ? 100 : ($d>200 ? 50 : ($d>100 ? 20 : ($d>50 ? 10 : ($d>20 ? 5 : 3))))));
		$tmp->y_axis->min	= $mnv;
		$tmp->y_axis->max	= ceil($mxv/$stp)*$stp;
		$tmp->y_axis->steps	= $stp;
		for($i=0; $i<=$tmp->y_axis->max; $i++) { $tmp->y_axis->labels->labels[] = strval($i); }
		$tmp->elements	= array();
		$tmp->elements[0]	= (object) array( 'type' => 'line', 'colour' => '#1975e1', 'width' => 2 );
		$tmp->elements[0]->values	= array();
		foreach($D->stat_posts_d as $obj) { $tmp->elements[0]->values[] = $obj->cnt; }
		$D->charts[]	= (object) array (
			'title'	=> $this->lang('admstat_stat_posts_d', array('#PERIOD#'=>$D->seldate)),
			'json'	=> $tmp,
		);
	}
	if( count($D->stat_posts_h) ) {
		$tmp	= new stdClass;
		$tmp->bg_colour	= '#ffffff';
		$tmp->x_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->y_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->x_axis->labels	= (object) array( 'labels' => array() );
		foreach($D->stat_posts_h as $obj) { $tmp->x_axis->labels->labels[] = strval($obj->txt); }
		$mxv	= 0;
		foreach($D->stat_posts_h as $obj) { $mxv = max($mxv, $obj->cnt); }
		$mnv	= $mxv;
		foreach($D->stat_posts_h as $obj) { $mnv = min($mnv, $obj->cnt); }
		$d	= $mxv - $mnv;
		$stp	= $d>2000 ? 500 : ($d>1000 ? 200 : ($d>500 ? 100 : ($d>200 ? 50 : ($d>100 ? 20 : ($d>50 ? 10 : ($d>20 ? 5 : 3))))));
		$tmp->y_axis->min	= $mnv;
		$tmp->y_axis->max	= ceil($mxv/$stp)*$stp;
		$tmp->y_axis->steps	= $stp;
		for($i=0; $i<=$tmp->y_axis->max; $i++) { $tmp->y_axis->labels->labels[] = strval($i); }
		$tmp->elements	= array();
		$tmp->elements[0]	= (object) array( 'type' => 'line', 'colour' => '#1975e1', 'width' => 2 );
		$tmp->elements[0]->values	= array();
		foreach($D->stat_posts_h as $obj) { $tmp->elements[0]->values[] = $obj->cnt; }
		$D->charts[]	= (object) array (
			'title'	=> $this->lang('admstat_stat_posts_h', array('#PERIOD#'=>$D->seldate)),
			'json'	=> $tmp,
		);
	}
	if( count($D->stat_visits_m) ) {
		$tmp	= new stdClass;
		$tmp->bg_colour	= '#ffffff';
		$tmp->x_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->y_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->x_axis->labels	= (object) array( 'labels' => array() );
		foreach($D->stat_visits_m as $obj) { $tmp->x_axis->labels->labels[] = strval($obj->txt); }
		$mxv	= 0;
		foreach($D->stat_visits_m as $obj) { $mxv = max($mxv, $obj->cnt); }
		$mnv	= $mxv;
		foreach($D->stat_visits_m as $obj) { $mnv = min($mnv, $obj->cnt); }
		$d	= $mxv - $mnv;
		$stp	= $d>2000 ? 500 : ($d>1000 ? 200 : ($d>500 ? 100 : ($d>200 ? 50 : ($d>100 ? 20 : ($d>50 ? 10 : ($d>20 ? 5 : 3))))));
		$tmp->y_axis->min	= $mnv;
		$tmp->y_axis->max	= ceil($mxv/$stp)*$stp;
		$tmp->y_axis->steps	= $stp;
		for($i=0; $i<=$tmp->y_axis->max; $i++) { $tmp->y_axis->labels->labels[] = strval($i); }
		$tmp->elements	= array();
		$tmp->elements[0]	= (object) array( 'type' => 'line', 'colour' => '#1975e1', 'width' => 2 );
		$tmp->elements[0]->values	= array();
		foreach($D->stat_visits_m as $obj) { $tmp->elements[0]->values[] = $obj->cnt; }
		$D->charts[]	= (object) array (
			'title'	=> $this->lang('admstat_stat_visits_m', array('#PERIOD#'=>$D->seldate)),
			'json'	=> $tmp,
		);
	}
	if( count($D->stat_visits_d) ) {
		$tmp	= new stdClass;
		$tmp->bg_colour	= '#ffffff';
		$tmp->x_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->y_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->x_axis->labels	= (object) array( 'labels' => array() );
		foreach($D->stat_visits_d as $obj) { $tmp->x_axis->labels->labels[] = strval($obj->txt); }
		$mxv	= 0;
		foreach($D->stat_visits_d as $obj) { $mxv = max($mxv, $obj->cnt); }
		$mnv	= $mxv;
		foreach($D->stat_visits_d as $obj) { $mnv = min($mnv, $obj->cnt); }
		$d	= $mxv - $mnv;
		$stp	= $d>2000 ? 500 : ($d>1000 ? 200 : ($d>500 ? 100 : ($d>200 ? 50 : ($d>100 ? 20 : ($d>50 ? 10 : ($d>20 ? 5 : 3))))));
		$tmp->y_axis->min	= $mnv;
		$tmp->y_axis->max	= ceil($mxv/$stp)*$stp;
		$tmp->y_axis->steps	= $stp;
		for($i=0; $i<=$tmp->y_axis->max; $i++) { $tmp->y_axis->labels->labels[] = strval($i); }
		$tmp->elements	= array();
		$tmp->elements[0]	= (object) array( 'type' => 'line', 'colour' => '#1975e1', 'width' => 2 );
		$tmp->elements[0]->values	= array();
		foreach($D->stat_visits_d as $obj) { $tmp->elements[0]->values[] = $obj->cnt; }
		$D->charts[]	= (object) array (
			'title'	=> $this->lang('admstat_stat_visits_d', array('#PERIOD#'=>$D->seldate)),
			'json'	=> $tmp,
		);
	}
	if( count($D->stat_visits_h) ) {
		$tmp	= new stdClass;
		$tmp->bg_colour	= '#ffffff';
		$tmp->x_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->y_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->x_axis->labels	= (object) array( 'labels' => array() );
		foreach($D->stat_visits_h as $obj) { $tmp->x_axis->labels->labels[] = strval($obj->txt); }
		$mxv	= 0;
		foreach($D->stat_visits_h as $obj) { $mxv = max($mxv, $obj->cnt); }
		$mnv	= $mxv;
		foreach($D->stat_visits_h as $obj) { $mnv = min($mnv, $obj->cnt); }
		$d	= $mxv - $mnv;
		$stp	= $d>2000 ? 500 : ($d>1000 ? 200 : ($d>500 ? 100 : ($d>200 ? 50 : ($d>100 ? 20 : ($d>50 ? 10 : ($d>20 ? 5 : 3))))));
		$tmp->y_axis->min	= $mnv;
		$tmp->y_axis->max	= ceil($mxv/$stp)*$stp;
		$tmp->y_axis->steps	= $stp;
		for($i=0; $i<=$tmp->y_axis->max; $i++) { $tmp->y_axis->labels->labels[] = strval($i); }
		$tmp->elements	= array();
		$tmp->elements[0]	= (object) array( 'type' => 'line', 'colour' => '#1975e1', 'width' => 2 );
		$tmp->elements[0]->values	= array();
		foreach($D->stat_visits_h as $obj) { $tmp->elements[0]->values[] = $obj->cnt; }
		$D->charts[]	= (object) array (
			'title'	=> $this->lang('admstat_stat_visits_h', array('#PERIOD#'=>$D->seldate)),
			'json'	=> $tmp,
		);
	}
	if( count($D->stat_regs_m) ) {
		$tmp	= new stdClass;
		$tmp->bg_colour	= '#ffffff';
		$tmp->x_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->y_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->x_axis->labels	= (object) array( 'labels' => array() );
		foreach($D->stat_regs_m as $obj) { $tmp->x_axis->labels->labels[] = strval($obj->txt); }
		$mxv	= 0;
		foreach($D->stat_regs_m as $obj) { $mxv = max($mxv, $obj->cnt); }
		$mnv	= $mxv;
		foreach($D->stat_regs_m as $obj) { $mnv = min($mnv, $obj->cnt); }
		$d	= $mxv - $mnv;
		$stp	= $d>2000 ? 500 : ($d>1000 ? 200 : ($d>500 ? 100 : ($d>200 ? 50 : ($d>100 ? 20 : ($d>50 ? 10 : ($d>20 ? 5 : 3))))));
		$tmp->y_axis->min	= $mnv;
		$tmp->y_axis->max	= ceil($mxv/$stp)*$stp;
		$tmp->y_axis->steps	= $stp;
		for($i=0; $i<=$tmp->y_axis->max; $i++) { $tmp->y_axis->labels->labels[] = strval($i); }
		$tmp->elements	= array();
		$tmp->elements[0]	= (object) array( 'type' => 'line', 'colour' => '#1975e1', 'width' => 2 );
		$tmp->elements[0]->values	= array();
		foreach($D->stat_regs_m as $obj) { $tmp->elements[0]->values[] = $obj->cnt; }
		$D->charts[]	= (object) array (
			'title'	=> $this->lang('admstat_stat_regs_m', array('#PERIOD#'=>$D->seldate)),
			'json'	=> $tmp,
		);
	}
	if( count($D->stat_regs_d) ) {
		$tmp	= new stdClass;
		$tmp->bg_colour	= '#ffffff';
		$tmp->x_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->y_axis	= (object) array( 'colour' => '#b9b9b9', 'grid-colour' => '#d6d6d6' );
		$tmp->x_axis->labels	= (object) array( 'labels' => array() );
		foreach($D->stat_regs_d as $obj) { $tmp->x_axis->labels->labels[] = strval($obj->txt); }
		$mxv	= 0;
		foreach($D->stat_regs_d as $obj) { $mxv = max($mxv, $obj->cnt); }
		$mnv	= $mxv;
		foreach($D->stat_regs_d as $obj) { $mnv = min($mnv, $obj->cnt); }
		$d	= $mxv - $mnv;
		$stp	= $d>2000 ? 500 : ($d>1000 ? 200 : ($d>500 ? 100 : ($d>200 ? 50 : ($d>100 ? 20 : ($d>50 ? 10 : ($d>20 ? 5 : 3))))));
		$tmp->y_axis->min	= $mnv;
		$tmp->y_axis->max	= ceil($mxv/$stp)*$stp;
		$tmp->y_axis->steps	= $stp;
		for($i=0; $i<=$tmp->y_axis->max; $i++) { $tmp->y_axis->labels->labels[] = strval($i); }
		$tmp->elements	= array();
		$tmp->elements[0]	= (object) array( 'type' => 'line', 'colour' => '#1975e1', 'width' => 2 );
		$tmp->elements[0]->values	= array();
		foreach($D->stat_regs_d as $obj) { $tmp->elements[0]->values[] = $obj->cnt; }
		$D->charts[]	= (object) array (
			'title'	=> $this->lang('admstat_stat_regs_d', array('#PERIOD#'=>$D->seldate)),
			'json'	=> $tmp,
		);
	}
	$this->load_template('admin_statistics.php');
	
?>