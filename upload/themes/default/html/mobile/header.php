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
		<link href="<?= $C->OUTSIDE_SITE_URL ?>themes/default/css/mobile.css" media="handheld" rel="stylesheet" type="text/css" />
		<link href="<?= $C->OUTSIDE_SITE_URL ?>themes/default/css/mobile.css" media="screen" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?= $C->OUTSIDE_SITE_URL ?>themes/default/js/mobile.js"></script>
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<?php if( $this->lang('global_html_direction') == 'rtl' ) { ?>
		<style type="text/css"> body { direction:rtl; } </style>
		<?php } ?>
	</head>
	<body>
		<h1 id="hdr" style="text-align:left; background-image:none;"><?= $C->SITE_TITLE ?></div></h1>
		<hr />
		
		<?php if( $this->user->is_logged ) { ?>
		<div id="nav">
			<?php if( $this->request[0] == 'dashboard' ) { ?>
			<a href="<?= $C->SITE_URL ?>dashboard" accesskey="0" class="on"><b><?= $this->lang('header_nav_home') ?></b></a>
			<?php } else { ?>
			<a href="<?= $C->SITE_URL ?>dashboard" accesskey="0"><?= $this->lang('header_nav_home') ?></a>
			<?php }  ?>
			<span>|</span>
			<?php if( $this->request[0] == 'newpost' ) { ?>
			<a href="<?= $C->SITE_URL ?>newpost" accesskey="1" class="on"><b><?= $this->lang('header_nav_newpost') ?></b></a>
			<?php } else { ?>
			<a href="<?= $C->SITE_URL ?>newpost" accesskey="1"><?= $this->lang('header_nav_newpost') ?></a>
			<?php }  ?>
			<span>|</span>
			<?php if( $this->request[0] == 'search' ) { ?>
			<a href="<?= $C->SITE_URL ?>search" accesskey="2" class="on"><b><?= $this->lang('header_nav_search') ?></b></a>
			<?php } else { ?>
			<a href="<?= $C->SITE_URL ?>search" accesskey="2"><?= $this->lang('header_nav_search') ?></a>
			<?php }  ?>
			<div class="klear"></div>
		</div>
		<hr />
		<?php } ?>
