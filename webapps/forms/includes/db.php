<?php

$db_host		= 'localhost';
$db_user		= 'jglen01_admin';
$db_pass		= 'Blue9901';
$db_database	= 'jglen01_webapp'; 


function dbQuery($query, $type = '', $admin = false){
	
	$db_host		= 'localhost';
	$db_user		= 'jglen01_admin';
	$db_pass		= 'Blue9901';
	$db_database	= 'jglen01_webapp'; 
		
	$link = mysql_connect($db_host,$db_user,$db_pass) or die('Unable to establish a DB connection');
		
	mysql_select_db($db_database,$link);

	switch($type){
		case 1: //return all rows as array
			$result = mysql_query($query);
			$i = 0;
			if(mysql_num_rows($result) == 0){return false;}
			while($row = mysql_fetch_assoc($result)){
				foreach($row as $key => $value){
					$return[$i][$key] = $value;
				}
				$i++;
			}
		break;
		
		case 2: //return ONE result
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 0){return false;}
			$row = mysql_fetch_assoc($result);
			foreach($row as $key => $value){
					$return[$key] = $value;
				}
		break;
		case 3: //execute a query with no response
			$result = mysql_query($query);
			return true;
		break;
		case 4: //insert new row and get id inserted
			$result = mysql_query($query);
			return mysql_insert_id();
		break;
		default:
			mysql_query($query);
			$return = mysql_errno();
		break;
	
	}
	
	mysql_close($link);
	
	return $return;

}

?>