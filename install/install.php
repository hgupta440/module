<?php

// intial instalation of Database.
	$db = new Database();

	$result = $db->query("SHOW TABLES LIKE 'modules' ");
	
	if(is_array($result) && count($result)>0){
		die("Please rename or remove install folder");
	}else{
		$sqlFile = glob(dirname(__FILE__).'/*.{sql}', GLOB_BRACE);
		foreach ($sqlFile as $key => $file) {
			$sql = file_get_contents($file);
			$db->query($sql);
		}
	}
	die("Please rename or remove install folder");
	

