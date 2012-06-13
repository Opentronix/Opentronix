<?php
	
	class EpiOAuthResponse
	{
		private $__resp;
		public function __construct($resp) {
			$this->__resp = $resp;
		}
		public function __get($name)
		{
			if($this->__resp->code < 200 || $this->__resp->code > 299) {
				return false;
			}
			parse_str($this->__resp->data, $result);
			foreach($result as $k => $v) {
				$this->$k = $v;
			}
			return $result[$name];
		}
	}
	
	class EpiOAuth
	{
		public $version = '1.0';
		
		protected $requestTokenUrl;
		protected $accessTokenUrl;
		protected $authorizeUrl;
		protected $consumerKey;
		protected $consumerSecret;
		protected $token;
		protected $tokenSecret;
		protected $signatureMethod;
		
		public function getAccessToken() {
			$resp = $this->httpRequest('GET', $this->accessTokenUrl);
			return new EpiOAuthResponse($resp);
		}
		
		public function getAuthorizationUrl() { 
			$retval = "{$this->authorizeUrl}?";	
			$token = $this->getRequestToken();
			return $this->authorizeUrl . '?oauth_token=' . $token->oauth_token;
		}
		
		public function getRequestToken() {
			$resp = $this->httpRequest('GET', $this->requestTokenUrl);
			return new EpiOAuthResponse($resp);
		}
		
		public function httpRequest($method = null, $url = null, $params = null) {
			if(empty($method) || empty($url)) {
				return false;
			}
			if(empty($params['oauth_signature'])) {
				$params = $this->prepareParameters($method, $url, $params);
			}
			switch($method) {
				case 'GET':
					return $this->httpGet($url, $params);
					break;
				case 'POST':
					return $this->httpPost($url, $params);
					break;
			}
		}
		
		public function setToken($token = null, $secret = null) {
			$params = func_get_args();
			$this->token = $token;
			$this->tokenSecret = $secret;
		} 
		
		public function encode($string) {
			return rawurlencode(utf8_encode($string));
		}
		
		protected function addOAuthHeaders(&$ch, $url, $oauthHeaders)
		{
			$_h = array('Expect:');
			$urlParts = parse_url($url);
			$oauth = 'Authorization: OAuth realm="' . $urlParts['path'] . '",';
			foreach($oauthHeaders as $name => $value) {
				$oauth .= "{$name}=\"{$value}\",";
			}
			$_h[] = substr($oauth, 0, -1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $_h); 
		}
		
		protected function generateNonce()
		{
			if(isset($this->nonce)) { // for unit testing
				return $this->nonce;
			}
			return md5(uniqid(rand(), true));
		}
		
		protected function generateSignature($method = null, $url = null, $params = null)
		{
			if(empty($method) || empty($url)) {
				return false;
			}
			
			// concatenating
			$concatenatedParams = '';
			foreach($params as $k => $v) {
				$v = $this->encode($v);
				$concatenatedParams .= "{$k}={$v}&";
			}
			$concatenatedParams = $this->encode(substr($concatenatedParams, 0, -1));
			
			// normalize url
			$normalizedUrl = $this->encode($this->normalizeUrl($url));
			$method = $this->encode($method); // don't need this but why not?
			
			$signatureBaseString = "{$method}&{$normalizedUrl}&{$concatenatedParams}";
			return $this->signString($signatureBaseString);
		}
			
		protected function httpGet($url, $params = null) {
			if(count($params['request']) > 0) {
				$url .= '?';
				foreach($params['request'] as $k => $v) {
				  $url .= "{$k}={$v}&";
				}
				$url = substr($url, 0, -1);
			}
			$ch = curl_init($url);
			$this->addOAuthHeaders($ch, $url, $params['oauth']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$resp  = $this->curl->addCurl($ch);
			return $resp;
		}
		
		protected function httpPost($url, $params = null) {
			$ch = curl_init($url);
			$this->addOAuthHeaders($ch, $url, $params['oauth']);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params['request']));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$resp  = $this->curl->addCurl($ch);
			return $resp;
		}
		
		protected function normalizeUrl($url = null)
		{
			$urlParts = parse_url($url);
			$scheme = strtolower($urlParts['scheme']);
			$host   = strtolower($urlParts['host']);
			$port = isset($urlParts['port']) ? intval($urlParts['port']) : 80;	
			$retval = "{$scheme}://{$host}";
			if($port > 0 && ($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443)) {
				$retval .= ":{$port}";
			}
			$retval .= $urlParts['path'];
			if(!empty($urlParts['query'])) {
				$retval .= "?{$urlParts['query']}";
			}
			return $retval;
		}
		
		protected function prepareParameters($method = null, $url = null, $params = null)
		{
			if(empty($method) || empty($url)) {
				return false;
			}
			$oauth['oauth_consumer_key'] = $this->consumerKey;
			$oauth['oauth_token'] = $this->token;
			$oauth['oauth_nonce'] = $this->generateNonce();
			$oauth['oauth_timestamp'] = !isset($this->timestamp) ? time() : $this->timestamp; // for unit test
			$oauth['oauth_signature_method'] = $this->signatureMethod;
			$oauth['oauth_version'] = $this->version;
			
			// encoding
			array_walk($oauth, array($this, 'encode'));
			if(is_array($params)) {
				array_walk($params, array($this, 'encode'));
			}
			$encodedParams = array_merge($oauth, (array)$params);
			
			// sorting
			ksort($encodedParams);
			
			// signing
			$oauth['oauth_signature'] = $this->encode($this->generateSignature($method, $url, $encodedParams));
			return array('request' => $params, 'oauth' => $oauth);
		}
			
		protected function signString($string = null) {
			$retval = false;
			switch($this->signatureMethod) {
				case 'HMAC-SHA1':
					$key = $this->encode($this->consumerSecret) . '&' . $this->encode($this->tokenSecret);
					$retval = base64_encode(hash_hmac('sha1', $string, $key, true));
					break;
			}
			return $retval;
		}
		
		public function __construct($consumerKey, $consumerSecret, $signatureMethod='HMAC-SHA1') {
			$this->consumerKey = $consumerKey;
			$this->consumerSecret = $consumerSecret;
			$this->signatureMethod = $signatureMethod;
			$this->curl = EpiCurl::getInstance();
		}
	}
	
	
	class EpiTwitter extends EpiOAuth
	{
		const EPITWITTER_SIGNATURE_METHOD = 'HMAC-SHA1';
		protected $requestTokenUrl = 'http://twitter.com/oauth/request_token';
		protected $accessTokenUrl = 'http://twitter.com/oauth/access_token';
		protected $authorizeUrl = 'http://twitter.com/oauth/authorize';
		protected $apiUrl = 'http://twitter.com';
		
		public function __call($name, $params = null) {
			$parts  = explode('_', $name);
			$method = strtoupper(array_shift($parts));
			$parts  = implode('_', $parts);
			$url    = $this->apiUrl . '/' . preg_replace('/[A-Z]|[0-9]+/e', "'/'.strtolower('\\0')", $parts) . '.json';
			$args	= null;
			if(!empty($params)) {
				$args = array_shift($params);
			}
			return new EpiTwitterJson(call_user_func(array($this, 'httpRequest'), $method, $url, $args));
		}
		
		public function __construct($consumerKey = null, $consumerSecret = null, $oauthToken = null, $oauthTokenSecret = null) {
			parent::__construct($consumerKey, $consumerSecret, self::EPITWITTER_SIGNATURE_METHOD);
			$this->setToken($oauthToken, $oauthTokenSecret);
		}
	}
	
	class EpiTwitterJson
	{
		private $resp;
		public function __construct($resp) {
			$this->resp = $resp;
		}
		public function __get($name) {
			$this->responseText = $this->resp->data;
			$this->response = (array)json_decode($this->responseText, 1);
			foreach($this->response as $k => $v) {
				$this->$k = $v;
			}
			return $this->$name;
		}
	}
	
	
	class EpiCurl
	{
		const timeout = 3;
		static $inst = null;
		static $singleton = 0;
		private $mc;
		private $msgs;
		private $running;
		private $requests = array();
		private $responses = array();
		private $properties = array();
		
		function __construct() {
			if(self::$singleton == 0) {
				throw new Exception('This class cannot be instantiated by the new keyword.  You must instantiate it using: $obj = EpiCurl::getInstance();');
			}
			$this->mc = curl_multi_init();
			$this->properties = array(
				'code'  => CURLINFO_HTTP_CODE,
				'time'  => CURLINFO_TOTAL_TIME,
				'length'=> CURLINFO_CONTENT_LENGTH_DOWNLOAD,
				'type'  => CURLINFO_CONTENT_TYPE
			);
		}
		
		public function addCurl($ch) {
			$key = (string)$ch;
			$this->requests[$key] = $ch;
			$res = curl_multi_add_handle($this->mc, $ch);
			// (1)
			if($res === CURLM_OK || $res === CURLM_CALL_MULTI_PERFORM) {
				do {
					$mrc = curl_multi_exec($this->mc, $active);
				} while ($mrc === CURLM_CALL_MULTI_PERFORM);
				return new EpiCurlManager($key);
			}
			else {
				return $res;
			}
		}
		
		public function getResult($key = null) {
			if($key != null) {
				if(isset($this->responses[$key])) {
					return $this->responses[$key];
				}
				$running = null;
				do {
					$resp = curl_multi_exec($this->mc, $runningCurrent);
					if($running !== null && $runningCurrent != $running) {
						$this->storeResponses($key);
						if(isset($this->responses[$key])) {
							return $this->responses[$key];
						}
					}
					$running = $runningCurrent;
				} while($runningCurrent > 0);
			}
			return false;
		}
		
		private function storeResponses()
		{
			while($done = curl_multi_info_read($this->mc)) {
				$key = (string)$done['handle'];
				$this->responses[$key]['data'] = curl_multi_getcontent($done['handle']);
				foreach($this->properties as $name => $const) {
					$this->responses[$key][$name] = curl_getinfo($done['handle'], $const);
					curl_multi_remove_handle($this->mc, $done['handle']);
				}
			}
		}
		
		static function getInstance()
		{
			if(self::$inst == null) {
				self::$singleton = 1;
				self::$inst = new EpiCurl();
			}
			return self::$inst;
		}
	}
	
	class EpiCurlManager
	{
		private $key;
		private $epiCurl;
		
		function __construct($key)
		{
			$this->key = $key;
			$this->epiCurl = EpiCurl::getInstance();
		}
		
		function __get($name)
		{
			$responses = $this->epiCurl->getResult($this->key);
			return $responses[$name];
		}
	}

?>