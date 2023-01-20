<?php
/*
	Class used for Fetch and Sanitized Request from user

*/
class Request
{
	private $request = array();
	private $file;
	
	public function __construct()
	{
		if($_SERVER['REQUEST_METHOD']=='GET'){
			// unset route parameter from request
			unset($_GET['route']);
			$this->request = $_GET;	
		}
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$this->request = $_POST;	
		}
		// find and merge body parameter with request parameter.
		$body = json_decode(file_get_contents('php://input'),true);
		if(!is_null($body) && is_array($body)){
			$this->request = array_merge($this->request,$body);
		}

		$this->request = $this->sanitize($this->request);

		// add file uploaded data from request.
		if(!empty($_FILES)){
			$this->file = $_FILES;
		}
		
	}
	/*
		Function return all request data from user

	*/
	public function getPost(){
		return $this->request;
	}

	/*
		Function return Files posted by user

	*/
	public function file(){
		return $this->file;
	}

	/*
		Function used  to sanitize request.
	*/
	private function sanitize($data){
		foreach ($data as $key => $value) {
			if(!is_array($value)){
				$value = preg_replace("/<script.*?>(.*)?<\/script>/im","$1",$value);
				$data[$key] = $value;
			}else{
				$value = $this->sanitize($value);
			}
		}
		return $data;
	}
	
}