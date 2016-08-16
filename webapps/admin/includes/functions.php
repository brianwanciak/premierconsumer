<?php

if(!isset($_POST)){
	if(!APP_CHECK){die("Error, you can not access this file directly");}
}
date_default_timezone_set('America/New_York');

app_class_loader();

function app_class_loader(){
	require_once("includes/db.php");
	require_once("classes/login.class.php");
	require_once("classes/db.class.php");
}


function getVar($var){
	if(isset($_GET[$var])){
		return $_GET[$var];
	}else{
		return false;
	}
	
}

function postVar($var){
	if(isset($_POST[$var])){
		return $_POST[$var];
	}else{
		return false;
	}
	
}

function renderStatus($id, $type){
	$states = dbQuery("SELECT * FROM status", 1);
	$status = dbQuery("SELECT * FROM status WHERE id=".$id, 2);
	
	if($type == "edit"){
		$html = '<div class="btn-group status-select"><button onclick="javascript:void(0); return false;" class="btn status-label '.$status['color'].'">'.$status['title'].'</button><button class="btn dropdown-toggle '.$status['color'].'" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu">';
		foreach($states as $state){
			$html .= '<li><a href="javascript:void(0);" rel="'.$state['color'].';'.$state['id'].';'.$state['title'].'">'.$state['title'].'</a></li>';
		}
		$html .= '</ul></div><input type="hidden" value="'.$id.'" name="data[status_id]" id="status_id" />';
	}else{
		$html = '<div class="btn-group"><button onclick="javascript:void(0); return false;" class="btn '.$status['color'].' disabled">'.$status['title'].'</button></div>';
	}
	return $html;
}


function renderOwner($id, $type){

	if($id == 0){
		$user["name"] = "Not Assigned";
	}else{
		$user = dbQuery("SELECT * FROM user WHERE id=".$id, 2);
		$user['name'] = $user['fname']." ".$user['lname'];
	}

	$users = dbQuery("SELECT * FROM user", 1);	
	
	if($type == "edit"){
		$html = '<div class="btn-group owner-select"><button onclick="javascript:void(0); return false;" class="btn owner-label">'.$user['name'].'</button><button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu">';
			$html .= '<li><a href="javascript:void(0);" rel="0;Not Assigned">Not Assigned</a></li>';
		foreach($users as $u){
			$html .= '<li><a href="javascript:void(0);" rel="'.$u['id'].';'.$u['fname'].' '.$u['lname'].'">'.$u['fname'].' '.$u['lname'].'</a></li>';
		}
		$html .= '</ul></div><input type="hidden" value="'.$id.'" name="data[user_id]" id="owner_id" />';
	}else{
		$html = $user['name'];
	}
	return $html;
}


function userFilter($id){

	if($id == 0){
		$user["name"] = "Filter By User";
	}else{
		$user = dbQuery("SELECT * FROM user WHERE id=".$id, 2);
		$user['name'] = $user['fname']." ".$user['lname'];
	}

	$users = dbQuery("SELECT * FROM user", 1);	
	
	$html = '<div class="btn-group user-filter-select"><button onclick="javascript:void(0); return false;" class="btn user-filter-label">'.$user['name'].'</button><button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu">';
	foreach($users as $u){
		$html .= '<li><a href="javascript:void(0);" rel="'.$u['id'].';'.$u['fname'].' '.$u['lname'].'">'.$u['fname'].' '.$u['lname'].'</a></li>';
	}
	$html .= '</ul></div>';
	
	return $html;
}

function statusFilter($id){

	if($id == 0){
		$status["title"] = "Filter By Status";
	}else{
		$status = dbQuery("SELECT * FROM status WHERE id=".$id, 2);
		$status['title'] = $status['title'];
	}

	$statusList = dbQuery("SELECT * FROM status", 1);	
	
	$html = '<div class="btn-group status-filter-select"><button onclick="javascript:void(0); return false;" class="btn status-filter-label">'.$status['title'].'</button><button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu">';
	foreach($statusList as $s){
		$html .= '<li><a href="javascript:void(0);" rel="'.$s['id'].';'.$s['title'].'">'.$s['title'].'</a></li>';
	}
	$html .= '</ul></div>';
	
	return $html;
}

function getUser($id){
	$query = "SELECT * FROM `user` WHERE id=".$id;
	
	$u = dbQuery($query, 2);
	return $u["fname"]." ".$u["lname"];
}

function getGroups(){
	$query = "SELECT * FROM `group`";
	$rows = dbQuery($query, 1);
	return $rows;
}

function getVal($table, $lookup, $field, $value){
	$query = "SELECT `".$lookup."` FROM `".$table."` WHERE `".$field."` = '".$value."'";
	//echo $query ;
	$row = dbQuery($query, 2);
	return $row[$lookup];
	//return $row;
}

function dbQuery($query, $type = '', $admin = false){
	
	$db_host		=  DB_HOST;
	$db_user		=  DB_USER;
	$db_pass		=  DB_PASS;
	$db_database	=  DB_NAME; 
		
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