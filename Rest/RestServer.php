<?php
/*

	GET 	http://xxxx/word/1/asc
	POST 	http://xxxx/			 	body:word,titi
	PUT 	http://xxxx/			 	body:word,titi,tata	
	DELETE 	http://xxxx/			 	body:word,tata

	/ZEND 
	GET 	http://xxxx/server.php?method=word&limit=1&order=asc
	POST 	http://xxxx/server.php			 	body:method=word&word=titi
	PUT 	http://xxxx/server.php			 	body:method=word&oldWord=titi&newWord=tata	
	DELETE 	http://xxxx/server.php			 	body:method=word&word=tata
	
	//ZEND 
	GET 	http://xxxx/word/$id
	POST 	http://xxxx/word			 	
	PUT 	http://xxxx/word/$oldWord		 	body:word=titinewWord=tata	
	DELETE 	http://xxxx/word/$id			 	body:newWord=tata	
*/

class RestServer{
	private $service;
	private $httpMethod;
	private $classMethod;
	private $resource;
	private $requestParam;
	private $userAgent;
	private $json;

	public function __construct($service){

		header("Content-type", "application/json");

		$this->json = new stdClass();
		$this->json->response = "";
		$this->json->apiError = false;
		$this->json->apiErrorMsg = "";
		$this->json->serverError = false;
		$this->json->serverErrorMsg = "";
		
		$this->httpMethod = $_SERVER["REQUEST_METHOD"];
		$this->userAgent = $_SERVER["HTTP_USER_AGENT"];

		if(class_exists($service)){
			$this->service = new $service();
		}else{
			throw new Exception("Error (1) : '$service' not found");
		}

		$D = array();
		switch($this->httpMethod){
			case'GET':
			case 'DELETE':
			$D = $_GET;
			break;

			case'POST':
			case 'PUT':
		parse_str(file_get_contents("php://input"),$D);
			break;

			default:
			throw new Exception("Method HTTP:'{$this->httpMethod}, invalid method !");
		}
		if(!empty($D["resource"])){
			$this->resource = $D["resource"];
			unset($D["resource"]);
			$this->requestParam = $D; 
		}else{
			throw new Exception("Error resource not found");		
		}

		$this->classMethod = strtolower($this->httpMethod).ucFirst(strtolower($this->resource));
		if(!method_exists($this->service,$this->classMethod)){
			throw new Exception("Resource: '{$this->resource}', invalid");
			
		}

		//var_dump($D);
	}//END construct

	public function handle(){
		$result = call_user_func(array($this->service, $this->classMethod), $this->requestParam);
		
		$this->json->response 		= $result->response;
		$this->json->apiError 		= $result->apiError;
		$this->json->apiErrorMsg 	= $result->apiErrorMsg;
		exit;
	}//END handle

	public function showError($serverErrorMsg){
		$this->json->serverError 	= true;
		$this->json->serverErrorMsg = $serverErrorMsg;
		exit;
	}//END showError()

	public function __destruct(){
		echo json_encode($this->json, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
	}//END destruct()

}