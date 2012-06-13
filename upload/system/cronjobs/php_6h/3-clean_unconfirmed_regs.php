<?php
	
	$db1->query('DELETE FROM unconfirmed_registrations WHERE date<"'.(time()-14*24*60*60).'" ');
	
?>