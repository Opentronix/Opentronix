<?php
	
	if( empty($C->TWITTER_CONSUMER_KEY) || empty($C->TWITTER_CONSUMER_SECRET) ) {
		$this->redirect('home');
	}
	
	require_once($C->INCPATH.'classes/class_EpiTwitter.php');
	
	if( ! isset($_SESSION['TWITTER_CONNECTED']) ) {
		$_SESSION['TWITTER_CONNECTED']	= FALSE;
	}
	if( $_SESSION['TWITTER_CONNECTED'] && !$_SESSION['TWITTER_CONNECTED']->id ) {
		$_SESSION['TWITTER_CONNECTED']	= FALSE;
	}
	
	if( isset($_GET['oauth_token'],$_SESSION['twitter_tmp_token'],$_SESSION['twitter_tmp_redirectto']) && $_GET['oauth_token']==$_SESSION['twitter_tmp_token'] )
	{
		$_SESSION['TWITTER_CONNECTED']	= FALSE;
		$twitterObj = new EpiTwitter($C->TWITTER_CONSUMER_KEY, $C->TWITTER_CONSUMER_SECRET);
		$twitterObj->setToken($_GET['oauth_token']);
		$token = $twitterObj->getAccessToken();
		$twitterObj->setToken($token->oauth_token, $token->oauth_token_secret);
		$twitterInfo = $twitterObj->get_accountVerify_credentials();
		$userinfo	= (object) $twitterInfo->response;
		if( $userinfo && $userinfo->id ) {
			$_SESSION['TWITTER_CONNECTED']	= $userinfo;
		}
		$redir	= $_SESSION['twitter_tmp_redirectto'];
		unset($_SESSION['twitter_tmp_token']);
		unset($_SESSION['twitter_tmp_redirectto']);
		$this->redirect($redir);
		exit;
	}
	
	
	$twitterObj = new EpiTwitter($C->TWITTER_CONSUMER_KEY, $C->TWITTER_CONSUMER_SECRET);
	$redir	= $twitterObj->getAuthorizationUrl();
	$token	= preg_replace('/^(.*)\?oauth\_token\=(.*)$/iuU', '$2', $redir);
	
	$_SESSION['twitter_tmp_token']	= $token;
	$_SESSION['twitter_tmp_redirectto']	= isset($_GET['backto']) ? trim($_GET['backto']) : '/home';
	
	$this->redirect($redir);
	
?>