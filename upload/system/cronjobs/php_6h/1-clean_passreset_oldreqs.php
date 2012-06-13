<?php
	
	$db2->query('UPDATE users SET pass_reset_key="", pass_reset_valid=0 WHERE pass_reset_key<>"" AND pass_reset_valid<"'.(time()-5*24*60*60).'" ');
	
	$db2->query('DELETE FROM email_change_requests WHERE confirm_valid<"'.(time()-5*24*60*60).'" ');
	
?>