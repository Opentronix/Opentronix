<?php
	
	// Site Address Here:
	// 
		$C->DOMAIN		= '';
		$C->SITE_URL	= '';
	// 
	
	// Random identifier for this installation on this server
	// 
		$C->RNDKEY	= '';
	// 
	
	// MySQL SETTINGS
	// 
		$C->DB_HOST	= '';
		$C->DB_USER	= '';
		$C->DB_PASS	= '';
		$C->DB_NAME	= '';
		$C->DB_MYEXT = ''; // 'mysqli' or 'mysql'
	// 
	
	// CACHE SETTINGS
	// 
		$C->CACHE_MECHANISM	= '';	// 'apc' or 'memcached' or 'mysqlheap' or 'filesystem'
		$C->CACHE_EXPIRE		= '';
		$C->CACHE_KEYS_PREFIX	= '';
		
		// If 'memcached':
		$C->CACHE_MEMCACHE_HOST	= '';
		$C->CACHE_MEMCACHE_PORT	= '';
		
		// If 'filesystem':
		$C->CACHE_FILESYSTEM_PATH	= '';
	// 
	
	// IMAGE MANIPULATION SETTINGS
	// 
		$C->IMAGE_MANIPULATION	= '';	// 'imagemagick_cli' or 'gd'
		
		// if 'imagemagick_cli' - /path/to/convert
		$C->IM_CONVERT	= '';
	// 
	
	// DEFAULT LANGUAGE
	// 
		$C->LANGUAGE	= '';
	// 
	
	// USERS ACCOUNTS SETTINGS
	// 
		// if urls are user.site.com or site.com/user
		// this setting is still beta and it is not working properly
		$C->USERS_ARE_SUBDOMAINS	= '';
	// 
	
	// RPC PING SETTINGS
	// 
		$C->RPC_PINGS_ON		= '';
		$C->RPC_PINGS_SERVERS	= '';
	// 
	
	// TWITTER & FACEBOOK CONNECT SETTINGS
	//
		// To activate Facebook Connect, check out the README.txt file
		$C->FACEBOOK_API_KEY		= '';
		
		// To activate Twitter OAuth login, check out the README.txt file
		$C->TWITTER_CONSUMER_KEY	= '';
		$C->TWITTER_CONSUMER_SECRET	= '';
		
		// Bit.ly Integration - used for sharing posts to twitter
		$C->BITLY_LOGIN			= '';
		$C->BITLY_API_KEY			= '';
		
		// For inviting Yahoo contacts. Check out the README.txt file
		$C->YAHOO_CONSUMER_KEY		= '';
		$C->YAHOO_CONSUMER_SECRET	= '';
	//
	
	// IF YOUR SERVER SUPPORTS CRONJOBS, READ THE FILE ./system/cronjobs/readme.txt 
	// 
		$C->CRONJOB_IS_INSTALLED	= '';
	// 
	
	// DO NOT REMOVE THIS
	// 
		$C->INSTALLED	= '';
		$C->VERSION		= '';
		$C->DEBUG_USERS		= '';
	// 
	
?>