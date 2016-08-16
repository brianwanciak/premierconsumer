<?php

function insertLead($data){
	
	require_once("db.php");
	
	$link = mysql_connect($db_host,$db_user,$db_pass) or die('Unable to establish a DB connection');
		
	mysql_select_db($db_database,$link);
	
	foreach($data as $field => $value){
		$arr[] = "`".$field."` = '".mysql_real_escape_string($value)."'";
	}
	$query = "INSERT INTO leads SET ".implode(", ", $arr);
		
	mysql_query($query);
	$result = mysql_errno();
		
	mysql_close($link);

}

?>