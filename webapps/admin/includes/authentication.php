<?php

require_once("includes/ip-restrict.php");


//create a database connection
$db    = new Database();

// start this baby and give it the database connection
$login = new Login($db);

if(!$login->isUserLoggedIn()){
	header("Location: login.php");
}

define("USER_ID", $_SESSION['user_id']);
define("USER_FIRST_NAME", $_SESSION['user_fname']);
define("USER_LAST_NAME", $_SESSION['user_lname']);
define("USER_EMAIL", $_SESSION['user_email']);
define("USER_GROUP", $_SESSION['user_group']);
define("USER_USERNAME", $_SESSION['user_username']);

?>