<?php
define('APP_CHECK',true);

$page = "index";

require_once("includes/functions.php");
require_once("includes/authentication.php");

?>

<!DOCTYPE html>
<html>
  <head>
	<?php 
		$pageTitle = "Welcome - ";
		require_once("includes/headlibs.php"); 
	?>
  </head>
  <body>
    

	<?php require_once("includes/navbar.php"); ?>
    
    <div class="container">

	<div style="text-align:center; padding: 25px 0 50px"><img src="images/splash.jpg" /></div>
     

      <?php require_once("includes/footer.php"); ?>

 	</div> <!-- /container -->
  </body>
</html>