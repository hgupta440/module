<?php
/*
	Class used for Database connection

*/
class Database
{
	private $link;
	
	/*
		Function used to Connection with database  
		
	*/
	private function connect()
	{
		try
		{			
			// Create connection
			$this->link = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		}
		catch(Exception $e)
		{
			\Response::sendResponse(500, array("message"=>$e->getMessage()));
		}
		return true;
	}

	
	/*
		Function used to execute query in database.
		$query: SQL statement with variable fr execution
		$arg: array of values of variable used in SQL statement.  
		
	*/
	
	public function query($query,$arg=array())
	{
		
		try
		{
			$rows = array();
			if($this->connect()){
				$stmt = $this->link->prepare($query);
				$result = $stmt->execute($arg);
				if($this->link->lastInsertId()>0){
					$rows = array("LastId"=>$this->link->lastInsertId());
				}elseif($stmt->rowCount()>0){
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				}
				if(count($rows)>0){
					return $rows;
				}else{
					return true;
				}
			}else{
				return false;
			}
		}
		catch(Exception $e)
		{
			\Response::sendResponse(500, array("message"=>$e->getMessage()));
		}

		return $rows;
	}

	
}