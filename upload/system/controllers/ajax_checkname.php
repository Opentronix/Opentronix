<?php
	
	$this->load_langfile('inside/global.php');
	
	echo '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>';
	
	if( !$this->network->id ) {
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
	
	
	if( $datatype == 'username' )
	{
		$u	= $this->network->get_user_by_username($word);
		if( $u ) {
			echo '<result>'.htmlspecialchars($u->username).'</result>';
			return;
		}
	}
	elseif( $datatype == 'groupname' )
	{
		$g	= $this->network->get_group_by_name($word);
		if( $g ) {
			echo '<result>'.htmlspecialchars($g->title).'</result>';
			return;
		}
	}
	
	echo '<result></result>';
	return;
	
?>