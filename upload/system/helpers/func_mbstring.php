<?php
	
	//
	// In case "mbstring" extension is not available, we define some important mb functions...
	//
	
	if( ! defined('MB_CASE_UPPER') ) {
		define( 'MB_CASE_UPPER', 0 );
	}
	if( ! defined('MB_CASE_LOWER') ) {
		define( 'MB_CASE_LOWER', 1 );
	}
	if( ! defined('MB_CASE_TITLE') ) {
		define( 'MB_CASE_TITLE', 2 );
	}
	
	if( ! function_exists('mb_convert_case') )
	{
		function mb_convert_case($str, $mode, $enc=FALSE)
		{
			if( $mode == 0 ) {
				return mb_strtoupper($str, $enc);
			}
			if( $mode == 1 ) {
				return mb_strtolower($str, $enc);
			}
			if( $mode == 2 ) {
				return ucwords($str);
			}
			return $str;
		}
	}
	
	if( ! function_exists('mb_convert_encoding') )
	{
		function mb_convert_encoding($str, $to_enc, $from_enc=FALSE)
		{
			if( function_exists('iconv') ) {
				if($from_enc) {
					return iconv($from_enc, $to_enc, $str);
				}
				else {
					return iconv(iconv_get_encoding('internal_encoding'), $to_enc, $str);
				}
			}
			return $str;
		}
	}
	
	if( ! function_exists('mb_strtolower') )
	{
		function mb_strtolower($str, $enc=FALSE)
		{
			return strtolower($str);
		}
	}
	
	if( ! function_exists('mb_strtoupper') )
	{
		function mb_strtoupper($str, $enc=FALSE)
		{
			return strtoupper($str);
		}
	}
	
	if( ! function_exists('mb_internal_encoding') )
	{
		function mb_internal_encoding($enc=FALSE)
		{
			if( function_exists('iconv_set_encoding') ) {
				if($enc) {
					iconv_set_encoding('internal_encoding', $enc);
				}
				return iconv_get_encoding('internal_encoding');
			}
			return '';
		}
	}
	
	if( ! function_exists('mb_strlen') )
	{
		function mb_strlen($str, $enc=FALSE)
		{
			if( function_exists('iconv_strlen') ) {
				return $enc ? iconv_strlen($str, $enc) : iconv_strlen($str);
			}
			return strlen($str);
		}
	}
	
	if( ! function_exists('mb_substr') )
	{
		function mb_substr($str, $start, $len=FALSE, $enc=FALSE)
		{
			if( function_exists('iconv_substr') ) {
				if( $enc ) {
					return $len ? iconv_substr($str, $start, $len, $enc) : iconv_substr($str, $start, $enc);
				}
				else {
					return $len ? iconv_substr($str, $start, $len) : iconv_substr($str, $start);
				}
			}
			return $len ? substr($str, $start, $len) : substr($str, $start);
		}
	}
	
	
	if( ! function_exists('mb_strpos') )
	{
		function mb_strpos($haystack, $needle, $offset=FALSE, $enc=FALSE)
		{
			if( function_exists('iconv_strpos') ) {
				if( $enc ) {
					return $offset ? iconv_strpos($haystack, $needle, $offset, $enc) : iconv_strpos($haystack, $needle, $enc);
				}
				else {
					return $offset ? iconv_strpos($haystack, $needle, $offset) : iconv_strpos($haystack, $needle);
				}
			}
			return $offset ? strpos($haystack, $needle, $offset) : strpos($haystack, $needle);
		}
	}
	
	if( ! function_exists('mb_list_encodings') )
	{
		function mb_list_encodings()
		{
			return array('UTF-8');
		}
	}

?>