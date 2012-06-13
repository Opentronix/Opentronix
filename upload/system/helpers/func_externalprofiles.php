<?php
	
	function validate_facebook_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/facebook\.com\/home\.php(.*)\#/iuU', 'facebook.com/', $str);
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?facebook\.com\//i', '', $str);
		$str	= trim($str,'/');
		if( preg_match('/(profile\.php\?id\=([0-9]+))/', $str, $m) ) {
			$result_url		= 'http://www.facebook.com/'.$m[1];
			$result_name	= '';
			return array($result_url, $result_name);
		}
		else {
			$str	= preg_replace('/(\?|\&).*$/', '', $str);
			$str	= trim($str,'/');
			$str	= trim($str);
			if( preg_match('/^[a-z0-9\.]{5,50}$/i', $str) ) {
				$result_url		= 'http://www.facebook.com/'.$str;
				$result_name	= $str;
				if( preg_match('/^(home|profile|friends|inbox|editaccount|logout|ext|photo|posted|ajax|social_graph|ads|facebook|careers|terms|privacy|mobile|help|friends)(\.php)?$/', $result_name) ) {
					return FALSE;
				}
				return array($result_url, $result_name);
			}
		}
		return FALSE;
	}
	
	function validate_twitter_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?twitter\.com\//i', '', $str);
		$str	= trim($str,'/');
		$str	= preg_replace('/\/.*$/', '', $str);
		$str	= trim($str,'/');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z0-9_]{1,15}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://twitter.com/'.$str;
		$result_name	= $str;
		if( preg_match('/^(home|invitations|account|following|followers|replies|inbox|favorites|search|statuses|devices|public_timeline)(\.php)?$/', $result_name) ) {
			return FALSE;
		}
		return array($result_url, $result_name);
	}
	
	function validate_flickr_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?flickr\.com\/photos\//i', '', $str);
		$str	= trim($str,'/');
		$str	= preg_replace('/\/.*$/', '', $str);
		$str	= trim($str,'/');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z][a-z0-9_\.\@]{3,31}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://www.flickr.com/photos/'.$str;
		$result_name	= $str;
		return array($result_url, $result_name);
	}
	
	function validate_friendfeed_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?friendfeed\.com\//i', '', $str);
		$str	= trim($str,'/');
		$str	= preg_replace('/\/.*$/', '', $str);
		$str	= trim($str,'/');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z0-9]{3,25}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://friendfeed.com/'.$str;
		$result_name	= $str;
		return array($result_url, $result_name);
	}
	
	function validate_delicious_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?(delicious\.com|del\.icio\.us)\//i', '', $str);
		$str	= trim($str,'/');
		$str	= preg_replace('/\/.*$/', '', $str);
		$str	= trim($str,'/');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z0-9\_\.]{1,100}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://delicious.com/'.$str;
		$result_name	= $str;
		if( preg_match('/^(home|bookmarks|popular|recent|url|network|user|tags|subscriptions|tag|help|inbox|settings|logout|save|search)(\.php)?$/', $result_name) ) {
			return FALSE;
		}
		return array($result_url, $result_name);
	}

	function validate_digg_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?digg\.com\/users\//i', '', $str);
		$str	= trim($str,'/');
		$str	= preg_replace('/\/.*$/', '', $str);
		$str	= trim($str,'/');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z0-9]{4,15}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://digg.com/users/'.$str;
		$result_name	= $str;
		return array($result_url, $result_name);
	}
	
	function validate_linkedin_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?linkedin\.com\/in\//i', '', $str);
		$str	= trim($str,'/');
		$str	= preg_replace('/\/.*$/', '', $str);
		$str	= trim($str,'/');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z0-9]{5,30}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://www.linkedin.com/in/'.$str;
		$result_name	= $str;
		return array($result_url, $result_name);
	}
	
	function validate_myspace_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?myspace\.com\//i', '', $str);
		$str	= trim($str,'/');
		$str	= preg_replace('/\/.*$/', '', $str);
		$str	= trim($str,'/');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z0-9-_\.]{1,50}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://www.myspace.com/'.$str;
		$result_name	= $str;
		if( preg_match('/^(index|modules)(\.php|\.cfm)?$/', $result_name) ) {
			return FALSE;
		}
		return array($result_url, $result_name);
	}
	
	function validate_orcut_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/uid\=([0-9]+)/', $str, $m) ) {
			return FALSE;
		}
		$result_url		= 'http://www.orkut.com/Main#Profile?uid='.$m[1];
		$result_name	= '';
		return array($result_url, $result_name);
	}
	
	function validate_mixx_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?mixx\.com\/users\//i', '', $str);
		$str	= trim($str,'/');
		$str	= preg_replace('/\/.*$/', '', $str);
		$str	= trim($str,'/');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z][a-z0-9\.\_]{0,15}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://www.mixx.com/users/'.$str;
		$result_name	= $str;
		return array($result_url, $result_name);
	}
	
	function validate_youtube_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/^(http(s)?\:\/\/(www\.)?)?youtube\.com\/user\//i', '', $str);
		$str	= trim($str,'/');
		$str	= preg_replace('/\/.*$/', '', $str);
		$str	= trim($str,'/');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z0-9]{2,20}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://www.youtube.com/user/'.$str;
		$result_name	= $str;
		return array($result_url, $result_name);
	}
	
	function validate_favit_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		if( preg_match('/^(http(s)?\:\/\/(www\.)?)?([a-z0-9]{4,32})\.favit\.me$/i', $str, $m) ) {
			$str	= $m[4];
		}
		else {
			$str	= preg_replace('/^http(s)?\:\/\/(www\.)?favit\.com\//i', '', $str);
			$str	= trim($str,'/');
			$str	= preg_replace('/\/.*$/', '', $str);
			$str	= trim($str,'/');
			$str	= trim($str);
			if( empty($str) ) {
				return FALSE;
			}
			if( ! preg_match('/^[a-z0-9]{4,32}$/i', $str) ) {
				return FALSE;
			}
		}
		$result_url		= 'http://'.$str.'.favit.me';
		$result_name	= $str;
		if( preg_match('/^(reader|logout|discover|popular|about|home|reader|saves|tools|login|register|search)$/', $result_name) ) {
			return FALSE;
		}
		return array($result_url, $result_name);
	}
	
	function validate_edno23_profile_url($str)
	{
		if( empty($str) ) {
			return FALSE;
		}
		$str	= preg_replace('/edno23\.com/i', '', $str);
		$str	= preg_replace('/http(s)?\:\/\/(www\.)?/i', '', $str);
		$str	= trim($str, '/');
		$str	= trim($str, '.');
		$str	= trim($str);
		if( empty($str) ) {
			return FALSE;
		}
		if( ! preg_match('/^[a-z0-9\-_]{4,20}$/i', $str) ) {
			return FALSE;
		}
		$result_url		= 'http://'.$str.'.edno23.com';
		$result_name	= $str;
		if( preg_match('/^(all|contacts|error|faq|firefox|getjs|home|invite|login|mobileversion|post|profile|rss|register|search|tour|view|watch|watched|widgets)$/', $result_name) ) {
			return FALSE;
		}
		return array($result_url, $result_name);
	}
	
?>