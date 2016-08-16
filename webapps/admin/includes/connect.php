<?php

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

error_reporting(0);

/* Database config */

//$db_host		= 'localhost';
//$db_user		= 'crowdapp';
//$db_pass		= 'c09rowdap_p';
//$db_database	= 'webteam'; 

	$db_host		= 'localhost';
	$db_user		= 'jglen01_admin';
	$db_pass		= 'Blue9901';
	$db_database	= 'jglen01_webapp'; 

/* End config */



$link = mysql_connect($db_host,$db_user,$db_pass) or die('Unable to establish a DB connection');

mysql_select_db($db_database,$link);


//mysql_query("SET names UTF8");

?>