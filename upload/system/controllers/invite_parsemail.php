<?php
	
	if( !$this->network->id ) {
		$this->redirect('home');
	}
	if( !$this->user->is_logged ) {
		$this->redirect('signin');
	}
	
	$this->load_langfile('inside/global.php');
	$this->load_langfile('inside/invite.php');
	
	$D->page_title	= $this->lang('os_invite_ttl_parsemail', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#OUTSIDE_SITE_TITLE#'=>$C->OUTSIDE_SITE_TITLE));
	
	$tabs	= array('gmail', 'facebook', 'twitter', 'yahoo');
	$D->tab	= 'gmail';
	if( $this->param('tab') && in_array($this->param('tab'), $tabs) ) {
		$D->tab	= $this->param('tab');
	}
	
	if( $this->param('get')=='loaded' && isset($this->user->sess['INVITE_EMAILS_LOADED']) && isset($_POST['emails']) )
	{	
		$emails	= array();
		foreach($_POST['emails'] as $e) {
			if( isset( $this->user->sess['INVITE_EMAILS_LOADED'][$e] ) ) {
				$emails[$e]	= $this->user->sess['INVITE_EMAILS_LOADED'][$e];
			}
		}
		if( 0 == count($emails) ) {
			$this->redirect('invite/parsemail/tab:'.$this->param('tab'));
		}
		foreach($emails as $k=>$v) {
			if( empty($v) ) {
				$emails[$k]	= $k;
			}
		}
		$_POST['name']	= array();
		$_POST['email']	= array();
		foreach($emails as $k=>$v) {
			$_POST['name'][]	= $v;
			$_POST['email'][]	= $k;
		}
		unset($_POST['emails']);
		unset($this->user->sess['INVITE_EMAILS_LOADED']);
		require_once( $this->controllers.'invite.php' );
		return;
	}
	elseif( $D->tab == 'gmail' )
	{
		$D->submit	= FALSE;
		$D->error	= FALSE;
		$D->errmsg	= '';
		$D->email	= '';
		if( isset($_POST['email'], $_POST['pass']) ) {
			$D->submit	= TRUE;
			$D->email	= trim($_POST['email']);
			$D->email	= mb_strtolower($D->email);
			$D->email	= preg_replace('/\@gmail\.com$/iu', '', $D->email);
			$D->email	= trim($D->email);
			$D->pass	= trim($_POST['pass']);
			$D->parsed_mails	= array();
			if( ! is_valid_email($D->email.'@gmail.com') ) {
				$D->error	= TRUE;
				$D->errmsg	= 'inv_prsml_gmail_err_eml';
			}
			elseif( empty($D->pass) ) {
				$D->error	= TRUE;
				$D->errmsg	= 'inv_prsml_gmail_err_lgn';
			}
			else {
				$ch	= curl_init();
				curl_setopt_array($ch, array(
					CURLOPT_AUTOREFERER	=> TRUE,
					CURLOPT_RETURNTRANSFER	=> TRUE,
					CURLOPT_HEADER	=> FALSE,
					CURLOPT_NOBODY	=> FALSE,
					CURLOPT_CONNECTTIMEOUT	=> 10,
					CURLOPT_TIMEOUT	=> 15,
					CURLOPT_MAXREDIRS	=> 5,
					CURLOPT_URL		=> 'https://www.google.com/accounts/ClientLogin',
					CURLOPT_POST	=> TRUE,
					CURLOPT_POSTFIELDS	=> 'accountType=GOOGLE&Email='.urlencode($D->email.'@gmail.com').'&Passwd='.$D->pass.'&service=cp',
				));
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
				$res	= curl_exec($ch);
				curl_close($ch);
				if( ! $res ) {
					$D->error	= TRUE;
					$D->errmsg	= 'inv_prsml_gmail_err_sys';
				}
				if( ! $D->error ) {
					$res	= trim($res);
					if( empty($res) ) {
						$D->error	= TRUE;
						$D->errmsg	= 'inv_prsml_gmail_err_sys';
					}
				}
				if( ! $D->error ) {
					if( ! preg_match('/Auth\=([a-z0-9\-\_]+)/ius', $res, $matches) ) {
						$D->error	= TRUE;
						$D->errmsg	= 'inv_prsml_gmail_err_lgn';
					}
				}
				if( ! $D->error ) {
					$auth	= trim($matches[1]);
					if( empty($auth) ) {
						$D->error	= TRUE;
						$D->errmsg	= 'inv_prsml_gmail_err_lgn';
					}
				}
				if( ! $D->error ) {
					$ch	= curl_init();
					curl_setopt_array($ch, array(
						CURLOPT_AUTOREFERER	=> TRUE,
						CURLOPT_RETURNTRANSFER	=> TRUE,
						CURLOPT_HEADER	=> FALSE,
						CURLOPT_NOBODY	=> FALSE,
						CURLOPT_CONNECTTIMEOUT	=> 10,
						CURLOPT_TIMEOUT	=> 15,
						CURLOPT_MAXREDIRS	=> 5,
						CURLOPT_URL		=> 'http://www.google.com/m8/feeds/contacts/'.urlencode($D->email.'@gmail.com').'/full?max-results=1000',
						CURLOPT_HTTPHEADER	=> array('Authorization: GoogleLogin auth='.$auth),
					));
					@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
					$res	= curl_exec($ch);
					curl_close($ch);
					if( ! $res ) {
						$D->error	= TRUE;
						$D->errmsg	= 'inv_prsml_gmail_err_sys';
					}
				}
				if( ! $D->error ) {
					preg_match_all('/\<entry[^\>]*>(.*)\<\/entry\>/iusU', $res, $matches, PREG_PATTERN_ORDER);
					foreach($matches[1] as $entry) {
						if( ! preg_match('/\<title.*>(.*)\<\/title\>/iu', $entry, $m) ) {
							continue;
						}
						$title	= trim($m[1]);
						if( ! preg_match('/\<gd\:email.*address\=(\"|\\\')(.*)(\"|\\\').*\>/iuU', $entry, $m) ) {
							continue;
						}
						$email	= trim($m[2]);
						$D->parsed_mails[$email]	= $title;
					}
					if( 0 == count($D->parsed_mails) ) {
						$D->error	= TRUE;
						$D->errmsg	= 'inv_prsml_gmail_err_nobody';
					}
				}
			}
			if( ! $D->error ) {
				$this->user->sess['INVITE_EMAILS_LOADED']	= $D->parsed_mails;
				$this->load_template('invite_choose_mails.php');
				return;
			}
		}
	}
	elseif( $D->tab == 'facebook' )
	{
		$D->use_fb_connect	= isset($C->FACEBOOK_API_KEY) && !empty($C->FACEBOOK_API_KEY);
		
		$code	= '';
		$db1->query('SELECT code FROM invitation_codes WHERE user_id="'.$this->user->id.'" LIMIT 1');
		if( $tmp = $db1->fetch_object() ) {
			$code	= $tmp->code;
		}
		else {
			$code	= md5(time().rand(0,9999999));
			$db1->query('INSERT INTO invitation_codes SET code="'.$code.'", user_id="'.$this->user->id.'" ');
		}
		$D->invite_link	= $C->SITE_URL.'signup/invited:'.$code;
		
		if( ! $D->use_fb_connect ) {
			$D->facebook_link	= 'http://www.facebook.com/sharer.php?u='.urlencode($D->invite_link).'&t='.urlencode($this->lang('inv_prsml_fb_nofbconnect_post', array('#SITE_TITLE#'=>$C->SITE_TITLE,)));
		}
	}
	elseif($D->tab == 'yahoo')
	{
		$D->error	= FALSE;
		$D->errmsg	= '';
		
		$oauth_consumer_key	= $C->YAHOO_CONSUMER_KEY;
		$oauth_consumer_secret	= $C->YAHOO_CONSUMER_SECRET;
		$callback = $C->SITE_URL.'invite/parsemail/tab:yahoo';
		$oauth_signature = $oauth_consumer_secret.'%26'.'&';
		
		if(isset($_GET['start']))
		{		
			$request_url = 'https://api.login.yahoo.com/oauth/v2/get_request_token';	
	
			$nonce = md5(rand().time().rand());
			
			$request_body = $request_url.'?oauth_nonce='.$nonce.'&oauth_timestamp='.time().'&oauth_consumer_key='.$oauth_consumer_key;
			$request_body .= '&oauth_signature_method=plaintext&oauth_signature='.$oauth_signature;
			$request_body .= '&oauth_version=1.0&xoauth_lang_pref="en-us"&oauth_callback='.$callback;
			
			$my_request = curl_init();
			curl_setopt($my_request, CURLOPT_URL, $request_body);
			curl_setopt($my_request, CURLOPT_RETURNTRANSFER, TRUE);
			$request_result = curl_exec($my_request);
			curl_close($my_request);
			
			parse_str($request_result, $request_result);
			
			if(isset($request_result['oauth_token_secret'],  $request_result['oauth_token']))
			{
				$_SESSION['nonce'] =  $nonce;
				$_SESSION['oauth_token_secret'] = $request_result['oauth_token_secret'];
				$_SESSION['oauth_token'] = $request_result['oauth_token'];
				header('Location:'. $request_result['xoauth_request_auth_url']);
			}else
			{
				$D->error = TRUE;
				$D->errmsg = $this->lang('inv_prsml_yahoo_err');
			}
		}
		elseif(isset($_GET['oauth_verifier']))
		{
			$request_url = 'https://api.login.yahoo.com/oauth/v2/get_token';
			
			$oauth_signature = $oauth_consumer_secret.'%26';
			
			$request_body = $request_url.'?oauth_consumer_key='.$oauth_consumer_key.'&oauth_signature_method=plaintext';
			$request_body .= '&oauth_version=1.0&oauth_verifier='.$_GET['oauth_verifier'].'&oauth_token='.$_GET['oauth_token'];
			$request_body .= '&oauth_timestamp='.time();
			$request_body .= '&oauth_nonce='.$_SESSION['nonce'].'&oauth_signature='.$oauth_signature.$_SESSION['oauth_token_secret'];
			 
			$my_request = curl_init();
			curl_setopt($my_request, CURLOPT_URL, $request_body);
			curl_setopt($my_request, CURLOPT_RETURNTRANSFER, TRUE);
			$access_request_result = curl_exec($my_request);
			curl_close($my_request);
			
			parse_str($access_request_result, $access_request_result);
			
			if(isset($access_request_result['oauth_token'], $access_request_result['oauth_token_secret']) && !$D->error )
			{
				$request_url = 'http://social.yahooapis.com/v1/user/'.$access_request_result['xoauth_yahoo_guid'].'/contacts;out=name,email;count=max';
				
				$oauth_timestamp = time();
						
				$parameters = 'oauth_consumer_key='.urlencode(utf8_encode($oauth_consumer_key));
				$parameters .= '&oauth_nonce='.urlencode(utf8_encode($_SESSION['nonce']));
				$parameters .= '&oauth_signature_method='.urlencode(utf8_encode("HMAC-SHA1"));
				$parameters .= '&oauth_timestamp='.urlencode(utf8_encode($oauth_timestamp));
				$parameters .= '&oauth_token='.urlencode(utf8_encode($access_request_result['oauth_token']));
				$parameters .= '&oauth_version='.urlencode(utf8_encode('1.0'));
				
				$resource_string = 'GET&'.urlencode(utf8_encode($request_url)).'&'.urlencode(utf8_encode($parameters));
				$oauth_signature = $oauth_consumer_secret;
				
				$sig =  base64_encode(hash_hmac('sha1', $resource_string, $oauth_signature.'&'.$access_request_result['oauth_token_secret'], true));
		
				$headers = array(
					"Authorization: OAuth 
					realm=\"yahooapis.com\",
					oauth_consumer_key=\"".$oauth_consumer_key."\",
					oauth_nonce=\"".$_SESSION['nonce']."\",
					oauth_signature_method=\"HMAC-SHA1\",
					oauth_timestamp=\"".$oauth_timestamp."\",
					oauth_token=\"".urlencode(utf8_encode($access_request_result['oauth_token']))."\",
					oauth_version=\"1.0\",
					oauth_signature=\"".urlencode(utf8_encode($sig))."\""
					);		
				
				$my_request = curl_init();
				curl_setopt($my_request, CURLOPT_URL, $request_url);
				curl_setopt($my_request, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($my_request, CURLOPT_RETURNTRANSFER, TRUE);
				$contacts_request_result = curl_exec($my_request);
				curl_close($my_request);
				
				$D->parsed_mails	= array();
	
				if( preg_match_all('/\<contact\s(.*)\<\/contact\>/iusU', $contacts_request_result, $m, PREG_PATTERN_ORDER) ) {
					foreach($m[1] as $result) {
						$email	= '';
						$name		= '';
						if( preg_match_all('/\<fields\s(.*)\<\/fields\>/iusU', $result, $mm, PREG_PATTERN_ORDER) ) {
							foreach($mm[1] as $tmp) {
								if( preg_match('/\<type\>(.*)\<\/type\>.*\<value\>(.*)\<\/value\>/ius', $tmp, $mmm) ) {
									$type		= trim($mmm[1]);
									$value	= trim($mmm[2]);
									if( $type == 'email' ) {
										$email	= $value;
									}
									elseif( $type=='name' && preg_match('/\<givenName\>(.*)\<\/givenName\>/ius', $value, $mmmm) ) {
										$name		= trim($mmmm[1]);
									}
								}
							}
						}
						if( is_valid_email($email) ) {
							$D->parsed_mails[$email]	= $name;
						}
					}
				}
				
				if(count($D->parsed_mails) > 0 ) 
				{
					$this->user->sess['INVITE_EMAILS_LOADED']	= $D->parsed_mails;
					$_SERVER['REQUEST_URI'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
					$this->load_template('invite_choose_mails.php');
					return;
				}else
				{
					$D->error = TRUE;
					$D->errmsg = $this->lang('inv_prsml_yahoo_err');
				}
			}else
			{
				$D->error = TRUE;
				$D->errmsg = $this->lang('inv_prsml_yahoo_err');
			}		
		}
	}
	elseif( $D->tab == 'twitter' )
	{
		$code	= '';
		$db1->query('SELECT code FROM invitation_codes WHERE user_id="'.$this->user->id.'" LIMIT 1');
		if( $tmp = $db1->fetch_object() ) {
			$code	= $tmp->code;
		}
		else {
			$code	= md5(time().rand(0,9999999));
			$db1->query('INSERT INTO invitation_codes SET code="'.$code.'", user_id="'.$this->user->id.'" ');
		}
		$D->shorten_link	= $C->SITE_URL.'signup/invited:'.$code;
		if( isset($C->BITLY_LOGIN, $C->BITLY_API_KEY) && !empty($C->BITLY_LOGIN) && !empty($C->BITLY_API_KEY) ) {
			$bitly	= 'http://api.bit.ly/shorten?version=2.0.1&longUrl='.$D->shorten_link.'&login='.$C->BITLY_LOGIN.'&apiKey='.$C->BITLY_API_KEY;
			$result	= @file_get_contents($bitly);
			if( $result ) {
				$result	= @json_decode($result, TRUE);
				if( $result && isset($result['statusCode'], $result['results']) ) {
					$result	= reset($result['results']);
					if( isset($result['shortUrl']) && !empty($result['shortUrl']) ) {
						$D->shorten_link	= $result['shortUrl'];
					}
				}
			}
		}
		$D->twitter_status	= $this->lang('inv_prsml_twitterpost', array('#SITE_TITLE#'=>$C->SITE_TITLE, '#LINK#'=>$D->shorten_link));
		$D->twitter_link		= 'http://twitter.com/home?status='.urlencode($D->twitter_status);
	}
	
	$this->load_template('invite_parsemail.php');
	
?>