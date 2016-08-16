
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			
            <?php echo $page->getNode("contentPage"); ?>
            
            <?php
            if($page->getPath() == "/debt-management-program"){
				include("includes/debt-calculator.inc.php");			
			}
			?>
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<?php 
			if($page->getPath() == "/employment-opportunities"){
				include("includes/generalcontact.inc.php");
			}else{
				include("includes/shortcontact.inc.php");  
			}				
		?>

       </div><!--side_col_right -->
       
