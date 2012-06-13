<?php
	
	if( $this->user->is_logged ) {
		$this->user->write_pageview();
	}
	
	$this->load_langfile('mobile/header.php');
	
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?= htmlspecialchars($D->page_title) ?></title>
		<link href="<?= $C->OUTSIDE_SITE_URL ?>themes/default/css/mobile_iphone.css" media="handheld" rel="stylesheet" type="text/css" />
		<link href="<?= $C->OUTSIDE_SITE_URL ?>themes/default/css/mobile_iphone.css" media="screen" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?= $C->OUTSIDE_SITE_URL ?>themes/default/js/mobile_iphone.js"></script>
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
		<?php if( isset($D->page_favicon) ) { ?>
		<link href="<?= $D->page_favicon ?>" type="image/x-icon" rel="shortcut icon" />
		<?php } elseif( isset($C->HDR_SHOW_FAVICON) && $C->HDR_SHOW_FAVICON == 1 ) { ?>
		<link href="<?= $C->SITE_URL ?>themes/blue-sky/imgs/favicon.ico" type="image/x-icon" rel="shortcut icon" />
		<?php } elseif( isset($C->HDR_SHOW_FAVICON) && $C->HDR_SHOW_FAVICON == 2 ) { ?>
		<link href="<?= $C->IMG_URL.'attachments/1/'.$C->HDR_CUSTOM_FAVICON ?>" type="image/x-icon" rel="shortcut icon" />
		<?php } ?>
		<script type="text/javascript"> var siteurl = "<?= $C->SITE_URL ?>"; </script>
		<?php if( $this->lang('global_html_direction') == 'rtl' ) { ?>
		<style type="text/css"> body { direction:rtl; } </style>
		<?php } ?>
	</head>
	<body>
		<div id="hdr">
			<h1><a href="<?= $C->SITE_URL ?>"><?= htmlspecialchars($C->SITE_TITLE) ?></a></h1>
			<?php if( $this->user->is_logged ) { ?>
			<a href="javascript:;" onclick="toggle_menu();" id="menubtn"></a>
			<?php } ?>
		</div>