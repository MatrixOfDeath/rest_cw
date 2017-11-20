<?php 

class RestClient{

	private $userAgent;
	private $requestUrl;
	private $requestBody;
	private $responseBody;
	private $responseHeader;
	

	public function __construct($url){
		if(isset($_SESSION)){
			session_start();
		}//END session
		if(!filter_var($url, FILTER_VALIDATE_URL)){			
			throw new Exception("Url not valid");			
		}//END filter
		
		$this->userAgent = "RestClient_V1";
		$this->requestUrl = $url;
	}//END construct

	public function setUserAgent($value){
		if(!empty($value)){
			$this->userAgent;
		}
	}//END setUserAgent	

	public function query($httpMethod="GET", $requestParam){
		if(empty($requestParam)){
			throw new Exception("requestParam is not valid");				
		}

		$this->requestBody = $requestParam;
		$ch = curl_init($this->requestUrl);
		switch (strtoupper($httpMethod)) {
			case 'GET'		: $this->getMethod($ch);		break;
			case 'POST'		: $this->postMethod($ch);		break;
			case 'PUT'		: $this->putMethod($ch);		break;
			case 'DELETE'	: $this->deleteMethod($ch);		break;
			default: throw new Exception("HTTP method is not valid");
			break;
		}//END switch
		return json_decode($this->responseBody);
	}//END query

	private function getMethod(&$ch){
		curl_setopt($ch, CURLOPT_URL, $this->requestUrl."?".$this->requestBody);
		$this->doExecute($ch);
	}
	private function postMethod(&$ch){
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
		curl_setopt($ch, CURLOPT_POST, true);
		$this->doExecute($ch);
	}
	private function putMethod(&$ch){
		$f = fopen("php://temp", "rw");
		fwrite($f, $this->requestBody);
		rewind($f); 
		curl_setopt($ch, CURLOPT_INFILE, $f);
		curl_setopt($ch, CURLOPT_INFILESIZE, strlen($this->requestBody));
		curl_setopt($ch, CURLOPT_PUT, true);
		$this->doExecute($ch);
		fclose($f);
	}
	private function deleteMethod(&$ch){
		curl_setopt($ch, CURLOPT_URL, $this->requestUrl."?".$this->requestBody);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		$this->doExecute($ch);
	}
	private function doExecute(&$ch){
		$strCookie = session_name()."=".session_id();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: aplication/json"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $strCookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $strCookie);
		curl_setopt($ch, CURLOPT_COOKIESESSION, false);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);

		//execution du curl
		$this->responseBody		= 	curl_exec($ch);
		$this->responseHeader 	=	curl_getinfo($ch);
		curl_close($ch);

	}
	
}//END class