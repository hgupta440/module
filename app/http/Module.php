<?php
namespace app\http;



class Module{
	/*
		function used to add new module with notes.
		
	*/
	public function add(){
		$request = new \Request();

		$db = new \Database();

		$error = array();

		$input = $request->getPost();
		
		// validation of data post by user
		if(!isset($input['subject'])||$input['subject']==''){
			$error['subject']="Name is required field";
		}
		if(!isset($input['description'])||$input['description']==''){
			$error['description']= "Password is required field";
		}
		if(!isset($input['start_date'])||$input['start_date']==''){
			$error['start_date']= "Email is required field";
		}
		if(!isset($input['due_date'])||$input['due_date']==''){
			$error['due_date']= "Email is required field";
		}
		if(!isset($input['status'])||$input['status']==''){
			$error['status']= "Email is required field";
		}
		if(!isset($input['priority'])||$input['priority']==''){
			$error['priority']= "Email is required field";
		}

		// if error found send error message to user
		if(count($error)>0){
			\Response::sendResponse(422, $error);
		}

		$user = \Auth::user();
		
		// ad login user for database
		$input['created_by'] = $user['id'];

		// format date  string as required.
		$input['start_date'] = date("Y-m-d",strtotime($input['start_date']));
		$input['due_date'] = date("Y-m-d",strtotime($input['due_date']));

		$sql = "call insert_module(:data);";
		$result = $db->query($sql,array("data"=>json_encode($input)));
		if($result){
			\Response::sendResponse(201, array("message"=>"Module Created Successfully"));
		}else{
			\Response::sendResponse(500, array("message"=>"Internal Server Error"));
		}
	}
	/*
		function used to list all modules with there notes.
		
	*/
	public function list(){
		$request = new \Request();

		$db = new \Database();

		$input = $request->getPost();
		
		$filter = $input['filter'];

		$filterCondition = array();


		// Check list of filter user provided.
		if(isset($filter['status']) && $filter['status']!=''){
			$status = implode("','", json_decode($filter['status']));
			$filterCondition[] = "status IN ('".$status."')";
		}
		if(isset($filter['priority']) && $filter['priority']!=''){
			$priority = implode("','", json_decode($filter['priority']));
			$filterCondition[] = "priority IN ('".$priority."')";
		}
		if(isset($filter['due_date']) && $filter['due_date']!=''){

			$due_date = date("Y-m-d",strtotime($filter['due_date']));

			$filterCondition[] = "due_date = '".$due_date."'";
		}
		if(isset($filter['notes']) && $filter['notes']!=''){
			if($filter['notes']==1){
				$filterCondition[] = "noteCount > 0";
			}else{
				$filterCondition[] = "noteCount = 0";
			}
		}

		// check array of filter and  create query condition
		if(count($filterCondition)>0){
			$filter = implode(" and ", $filterCondition);

			$filter = " where ".$filter;
		}
		
		$sql = "SELECT *, if(modules.priority='High',3,if(modules.priority='Medium',2,if(modules.priority='Low',1,0))) as priorityIndex,(select count(*) from notes where notes.moduleId = modules.id) as noteCount, (select JSON_ARRAYAGG(json_object('id', id,'subject', subject,'note', note,'attachment',(select JSON_ARRAYAGG(json_object('id', id,'attachment', attachment)) from attachment where notes.id = attachment.noteId ))) from notes where notes.moduleId = modules.id) notes FROM `modules` ".$filter." order by priorityIndex DESC, noteCount DESC";

		$result = $db->query($sql);	

		\Response::sendResponse(200, array("data"=>$result));
	}
}