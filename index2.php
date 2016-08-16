<?php require("core/config.php"); ?>

<?php include("includes/header.inc.php"); ?>

<?php

if($page->getPath() == "/index"){
	include("templates/homepage.php");
}else{
	include("templates/".$page->getNode("template").".php");
}

?>
        
<?php include("includes/footer.inc.php"); ?>""