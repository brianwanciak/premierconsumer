<?php

$host = $_SERVER['HTTP_HOST'];
$path = $_SERVER['REQUEST_URI'];
$updated = false;
if(strpos($host, "www.") !== false){
	
}else{
	$redirect = "https://www.".$host.$path;
	$updated = true;
}

if(!is_https() && !$updated ){
	$redirect = "https://".$host.$path;
	$updated = true;
}

if($updated){

	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . $redirect);

}

function is_https(){ 
    if ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) 
        return true; 
    else 
        return false; 
} 
//if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
//	$domain = (strpos($_SERVER['HTTP_HOST'], "www")) ? $_SERVER['HTTP_HOST'] : "www.".$_SERVER['HTTP_HOST'];
 //   $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  //  
//}

?>

