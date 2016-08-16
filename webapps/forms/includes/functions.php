<?php

date_default_timezone_set('America/New_York');

function spanishEncode($string){
	$convert = array("á"=>"&aacute;", "é"=>"&eacute;", "í"=>"&iacute;", "ó"=>"&oacute;", "ú"=>"&uacute;", "ñ"=>"&ntilde;", "Á"=>"&Aacute;", "É"=>"&Eacute;", "Í"=>"&Iacute;", "Ó"=>"&Oacute;", "Ú"=>"&Uacute;", "Ñ"=>"&Ntilde;");
	foreach($convert as $key => $value){
		if(strpos($string, $key)){
			$string = str_replace($key, $value, $string);
		}
	}
	return $string;
}


function checkEmail( $email ){
    $check = filter_var( $email, FILTER_VALIDATE_EMAIL );
	if($check){
		return true;
	}else{
		return false;
	}
}

function checkPhones($p1, $p2, $p3){

	return true;
	if((strlen($p1) == 12) || (strlen($p2) == 12) || (strlen($p3) == 12)){
		return true;
	}else{
		return false;
	}

}

$base_url = "http://www.premierconsumer.org/";
$base_url_libre = "http://www.librededeudas.com/";



?>