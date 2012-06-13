<?php
	
	define( 'SITE_TITLE',	'Opentronix' );
	define( 'VERSION',	'1.5.0' );
	
	define( 'INCPATH',	dirname(__FILE__).'/include/' );
	chdir( INCPATH );
	
	ini_set( 'error_reporting',			0	);
	ini_set( 'display_errors',			0	);
	ini_set( 'magic_quotes_runtime',		0	);
	ini_set( 'max_execution_time',		20*60	);
	
	if( function_exists('mb_internal_encoding') ) {
		mb_internal_encoding('UTF-8');
	}
	setlocale( LC_TIME,	'en_US.UTF-8' );
	if( function_exists('date_default_timezone_set') ) {
		date_default_timezone_set('America/New_York');
	}
	
	if( 1 ) {
		ini_set( 'error_reporting', E_ALL | E_STRICT	);
		ini_set( 'display_errors',			1	);
	}
	
	ignore_user_abort(TRUE);
	
	session_start();
	
	require_once( INCPATH.'functions.php' );
	
	if( ! isset($_SESSION['INSTALL_STEP']) ) {
		$_SESSION['INSTALL_STEP']	= 0;
	}
	if( ! isset($_SESSION['INSTALL_DATA']) ) {
		$_SESSION['INSTALL_DATA']	= array();
	}
	if( isset($_GET['reset']) ) {
		session_unset();
		$_SESSION['INSTALL_STEP']	= 0;
		$_SESSION['INSTALL_DATA']	= array();
	}
	
	$step	= $_SESSION['INSTALL_STEP'];
	
	if( isset($_GET['next']) ) {
		$step	++;
		if( ! file_exists(INCPATH.'step_'.$step.'.php') ) {
			$step	--;
		}
	}
	elseif( isset($_GET['prev']) ) {
		$step	--;
	}
	
	$step	= max($step, 1);
	$step	= min($step, 8);
	
	$PAGE_TITLE	= '';
	
	$header	= '';
	$footer	= '';
	$html	= '';
	
	$OLDC	= load_old_config();
	
	require_once( INCPATH.'step_'.$step.'.php' );
	require_once( INCPATH.'html_header.php' );
	require_once( INCPATH.'html_footer.php' );
	
	echo $header;
	echo $html;
	echo $footer;
	
?>
