<?php
namespace app\http;



class Login{

	/*
		Function used for authuntication of user 

	*/
	public function login(){
		$request = new \Request();
		$auth = new \Auth();

		$input = $request->getPost();
		$error = array();

		// validation of data post by user
		if(!isset($input['password'])||$input['password']==''){
			$error['password']= "Password is required field";
		}
		if(!isset($input['email'])||$input['email']==''){
			$error['email']= "Email is required field";
		}elseif(!filter_var($input['email'] , FILTER_VALIDATE_EMAIL) ){
			$error['email']="Email field should be valid email";
		}

		// if error found send error message to user
		if(count($error)>0){
			\Response::sendResponse(422, $error);
		}

		// genrate JWT token for login credentials provided.
		$token = $auth->login(array("email"=>$input['email'],"password"=>$input['password']));

		if(!$token){
			\Response::sendResponse(200, array("message"=>"Email Or Password is wrong"));
		}else{
			\Response::sendResponse(200, $token);
		}

	}

	/*
		Function used for Register a new user

	*/
	public function register(){
		try{
			$request = new \Request();
			$db = new \Database();

			$input = $request->getPost();
			$error = array();

			// validation of data post by user
			if(!isset($input['name'])||$input['name']==''){
				$error['name']="Name is required field";
			}
			if(!isset($input['password'])||$input['password']==''){
				$error['password']= "Password is required field";
			}
			if(!isset($input['email'])||$input['email']==''){
				$error['email']= "Email is required field";
			}elseif(!filter_var($input['email'] , FILTER_VALIDATE_EMAIL) ){
				$error['email']="Email field should be valid email";
			}

			// if error found send error message to user
			if(count($error)>0){
				\Response::sendResponse(422, $error);
			}

			// check user with same email already not exist
			$sql = "select * from users where email = :email";
			$result = $db->query($sql,array("email"=>$input['email']));

			if(count($result)==0){

				// Insert new user to database.
				$sql = "insert into users (name,email,password) values(:name,:email,:password)";
				$result = $db->query($sql,array("email"=>$input['email'],'name'=>$input['name'],"password"=>md5($input['password'])));
				if($result){
					\Response::sendResponse(201, array("message"=>"User Created Successfully"));
				}else{
					\Response::sendResponse(500, array("message"=>"Internal Server Error"));
				}
			}else{
				\Response::sendResponse(422, array("message"=>"User already Exist"));
			}
		}catch(Exception $e){
			\Response::sendResponse(500, array("message"=>$e->getMessage()));
		}

	}
}