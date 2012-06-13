<?php
	
	$this->load_langfile('inside/header.php');
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link href="<?= $C->SITE_URL ?>themes/default/css/inside.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript" src="<?= $C->SITE_URL ?>themes/default/js/inside.js"></script>
		<script type="text/javascript" src="<?= $C->SITE_URL ?>themes/default/js/inside_autocomplete.js"></script>
		<script type="text/javascript" src="<?= $C->SITE_URL ?>themes/default/js/inside_postform.js"></script>
		<script type="text/javascript" src="<?= $C->SITE_URL ?>themes/default/js/inside_posts.js"></script>
		<base target="_top" />
		<script type="text/javascript">
			var d = document;
			var w = window;
			var _d = document;
			var _w = window;
			var f_onkeypress	= function(e) {
				if( !e && _w.event ) { e = _w.event; }
				if( !e ) { return; }
				var code = e.charCode ? e.charCode : e.keyCode;
				if( parent && parent.flybox_opened && code==27 ) {
					parent.flybox_close();
				}
			};
			if( d.addEventListener ) {
				d.addEventListener("keypress", f_onkeypress, false);
			}
			else if( d.attachEvent ) {
				d.attachEvent("onkeypress", f_onkeypress);
			}
		</script>
	</head>
	<body style="background-color:white;">