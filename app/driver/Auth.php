<?php
/*
	Class used for Authentication of user

*/
use ReallySimpleJWT\Token;
class Auth
{
	private static $key = JWT_KEY;
	/*
		Function used to check login credintials of user  
		Return JWT token with expire time.

	*/
	public function login($credentials)
	{				
		$db = new \Database();
		$sql = "select * from users where email = :email AND password = :password";
		$user = $db->query($sql,array("email"=>$credentials['email'],"password"=>md5($credentials['password'])));

		if(count($user)==0){
			return false;
		}else{
			$payload = [
			    'iat' => time(),
			    'uid' => $user[0]['id'],
			    'exp' => time() + 3600
			];

			$secret = \Auth::$key;
			$token = Token::customPayload($payload, $secret);

			$data = array(
				"token"=>$token,
				"expireAt"=>$payload['exp']
			);
			return $data;
		}
	}
	/*
		Function used to check user is authurized or not.  
		Return Boolen.

	*/
	public static function valid()
	{
		$token = \Auth::headerToken();
		$secret = \Auth::$key;
		return Token::validate($token, $secret);
	}
	/*
		Function used to get details of login user.  
		Return user array.

	*/
	public static function user()
	{
		$token = \Auth::headerToken();
		
		$result = Token::getPayload($token);
		$db = new \Database();
		$sql = "select * from users where id = :id";
		$users = $db->query($sql,array("id"=>$result['uid']));
		return $users[0];

	}
	/*
		Function used to get details token from header.  
		Return header token.

	*/
	public static function headerToken(){
		$headers = apache_request_headers();
		$token = trim(str_replace("Bearer","", @$headers['Authorization']));
		return $token;
	}
}