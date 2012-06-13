<?php
	
	if( !$this->network->id ) {
		echo 'ERROR';
		return;
	}
	if( !$this->user->is_logged ) {
		echo 'ERROR';
		return;
	}
	
	if( isset($_POST['checktabs']) )
	{
		$checktabs	= explode(',', $_POST['checktabs']);
		foreach($checktabs as $k=>$v) {
			if( $v!='all' && $v!='@me' && $v!='private' && $v!='commented' && $v!='feeds' ) {
				unset($checktabs[$k]);
			}
		}
		$result	= $this->network->get_dashboard_tabstate($this->user->id, $checktabs);
		$withnew	= array();
		foreach($result as $k=>$v) {
			$withnew[]	= $k.':'.$v;
		}
		echo "OK:\n";
		echo implode("\n", $withnew);
		return;
	}
	
	echo 'ERROR';
	return;
	
?>