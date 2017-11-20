<?php
/**
**
**/
class Service{
	private $result;
	
	public function __construct(){
		session_start();
		if(empty($_SESSION["BDD"])){
			$_SESSION["BDD"] = array("server","client","soap","Zend","REST");
		}
	}

	private function setResult($response, $apiError=false, $apiErrorMsg=""){
		$this->result = new stdClass();		
		$this->result->response = $response;
		$this->result->apiError = $apiError;
		$this->result->apiErrorMsg = $apiErrorMsg;
		return $this->result;
	}

	/**
	* getWord
	*
	* @param integer $limit
	* @param  string $order
	* @return  array $result
	**/
	public function getWord($data){
		
		$limit = (empty($data["limit"]))?0:(int) $data["limit"];
		$order = (empty($data["order"]))?'ASC': $data["order"];
		$result = $_SESSION["BDD"];

		(strtoupper($order === "ASC")) ? sort($result, SORT_NATURAL | SORT_FLAG_CASE) : rsort($result, SORT_NATURAL | SORT_FLAG_CASE);

		if ($limit>0){
			$result = array_splice($result, 0, $limit);
		}//END if $limit

		return $this->setResult($result);
	}//END getWord()

	/**
	* addWord
	*
	* @param string $word
	* @return  boolean
	**/

	public function postWord($word){
		if(empty($data["word"]) || in_array($data["word"], $_SESSION["BDD"])){
			return $this->setResult(false, true, "word empty or aleready exist");
		}//END if $word
		array_push($_SESSION["BDD"], $data["word"]);
		return $this->setResult(true);
	}//END addWord()


	/**
	* updateWord
	*
	* @param string $oldWord
	* @param string $newWord
	* @return  boolean
	**/

	public function putWord($data){
		if(empty($data["oldWord"]) || empty($data["newWord"])){
			return $this->setResult(false,true, "oldWord or newWord empty");
		}//END if $old/newWord

		$key = array_search($data["oldWord"], $_SESSION["BDD"]);

		if($key === false || in_array($data["newWord"], $_SESSION["BDD"])){
			return $this->setResult(false, true, "oldWord not found or newWord already exist");
		}//END if $key

		$_SESSION["BDD"][$key] = $data["newWord"];
		return $this->setResult(true);
	}//END update()

	/**
	* deleteWord
	*
	* @param string $word
	* @return  boolean
	**/
	public function deleteWord($data){

		if(empty($data["word"])){
			
			return $this->setResult(false, true, "Word empty");
		}//END if 

		$key = array_search($data["word"], $_SESSION["BDD"]);

		if($key === false){
			return $this->setResult(false, true, "Word not found or newWord already exist");
		}//END if $key


		unset($_SESSION["BDD"][$key]);
		return $this->setResult(true);


	}//END deleteWord

	


}//END CLASS