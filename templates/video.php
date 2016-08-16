
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			
            
            <div class="yt-video flex-video">
            	<iframe width="560" height="315" src="<?php echo $page->getNode("youTubeLink"); ?>" frameborder="0" allowfullscreen></iframe>
            </div>
            
            <?php echo $page->getNode("contentPage"); ?>
            
            <p class="univision-desc">Premier Consumer Credit Counseling, Inc. in partnership with Univision and Univision Tarjeta is proud to present to you our series of financial and educational webinar presentations. We encourage you to view the presentations and take careful notes. Should you have any questions please do not hesitate to contact our credit counselors at 1.800.296.4950 or please contact us.</p>
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<?php 
			
				include("includes/shortcontact.inc.php");  
		?>

       </div><!--side_col_right -->
       
