<?php
	
	if( !isset($_GET['url'],$_GET['status']) ) {
		$this->redirect('home');
	}
	
	$url		= urldecode($_GET['url']);
	$status	= urldecode($_GET['status']);
	
	if( empty($url) || empty($status) ) {
		$this->redirect('home');
	}
	
	$bitly	= 'http://api.bit.ly/shorten?version=2.0.1&longUrl='.$url.'&login='.$C->BITLY_LOGIN.'&apiKey='.$C->BITLY_API_KEY;
	$result	= @file_get_contents($bitly);
	if( !$result || empty($result) ) {
		$this->redirect('http://twitter.com/home?status='.urlencode($status));
		exit;
	}
	$result	= @json_decode($result, TRUE);
	if( !$result || !isset($result['statusCode']) || $result['statusCode']!='OK' || !isset($result['results']) ) {
		$this->redirect('http://twitter.com/home?status='.urlencode($status));
		exit;
	}
	$result	= reset($result['results']);
	if( !$result || !isset($result['shortUrl']) || empty($result['shortUrl']) ) {
		$this->redirect('http://twitter.com/home?status='.urlencode($status));
		exit;
	}
	$result	= $result['shortUrl'];
	$result	= ': '.$result;
	$reslen	= mb_strlen($result);
	$pmxlen	= 140 - $reslen;
	
	if( mb_strlen($status) > $pmxlen ) {
		$status	= str_cut($status, $pmxlen-2);
	}
	$status	.= $result;
	
	$this->redirect('http://twitter.com/home?status='.urlencode($status));
	exit;
	
?>