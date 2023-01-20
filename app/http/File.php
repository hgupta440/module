<?php
namespace app\http;



class File{
	/*
		Function used to upload file to server 
		Return path and url if file succefully uploaded.

	*/

	public function add(){
		try{
			$request = new \Request();
			$files = $request->file();

			// Validate file uploaded have no error.
			if(is_null($files) || $files['file']['error']!=0){
				\Response::sendResponse(422, array("file"=>"File is required and Maximum File Size allowed is ".MAX_FILE_SIZE." MB"));
			}

			// Calculate file Size in MB
			$fileSize = $files["file"]["size"] / 1048576;
			// Check file size
			if ($fileSize > MAX_FILE_SIZE) {
				\Response::sendResponse(422, array("file"=>"Maximum File Size allowed is ".MAX_FILE_SIZE." MB"));
			}

			$target_dir = getcwd()."/".UPLOAD_PATH;

			// Check and create default folder to upload files.
			if(!is_dir($target_dir)){

				if(!mkdir($target_dir)){
					\Response::sendResponse(500, array("message"=>"Permission denied"));
				}
			}

			// Update file name with timestamp for unique file name.
			$fileName = time()."_".basename($files["file"]["name"]);

			$target_file = $target_dir."/".$fileName;

				
			if (move_uploaded_file($files["file"]["tmp_name"], $target_file)) {
				$data = array(
							"path"=>UPLOAD_PATH.$fileName,
							"url"=>BASE_URL."/".$fileName,
						);
			   \Response::sendResponse(200, $data);
			} else {
			    \Response::sendResponse(500, array("message"=>"Permission denied"));
			}
		}catch(Exception $e){
			\Response::sendResponse(500, array("message"=>$e->getMessage()));
		}

	}
}