<?php
// function used to scan and include files from provided directory.
function includeClass($dir){
	$list = scandir($dir);
	foreach ($list as $file) {
		if($file=="."||$file==".."){
			continue;
		}
		if(is_dir($dir."/".$file)){
			includeClass($dir."/".$file);
		}else{
			include_once $dir."/".$file;
		}
	}
}

// include all driver class to application.
includeClass(getcwd()."/app/driver");

// check defult database installation.
if(is_dir(getcwd()."/install")){

	include_once getcwd()."/install/install.php";
}


// include defined routes in system.
$route = require_once(getcwd()."/routes/api.php");

// if now route found in url default URL nmention.
if(!isset($_GET['route'])){
	$_GET['route']="/";
}

//check Route fuond exist or regiser with system.
if(isset($route[$_SERVER['REQUEST_METHOD']][$_GET['route']])){
	try{
		// Authuntication Required for route.
		if(in_array($_GET['route'], $route['AUTH'][$_SERVER['REQUEST_METHOD']]) && ! \Auth::valid()){
			\Response::sendResponse(401, array("message"=>"Unauthorized"));
		}

		// extraction controller and function from route
		$function = explode("@",$route[$_SERVER['REQUEST_METHOD']][$_GET['route']]);

		list($controller,$function) = $function;

		$controllerFile = str_replace("\\", "/", $controller);

		// Check controller file exist.
		if(!file_exists(getcwd()."/".$controllerFile.".php")){
			die("controller file not Exist");
		}

		include_once getcwd()."/".$controllerFile.".php";

		// Check controller class and funtion exist.
		if(!class_exists($controller) || !method_exists($controller, $function)){
			die("controller or function not Exist");
		}
		
		$class = new $controller();
		echo $class->$function();
		die;

	}catch(Exception $e){
			\Response::sendResponse(500, array("message"=>$e->getMessage()));
	}
}else{
	\Response::sendResponse(404, array("message"=>"Not Found"));
}