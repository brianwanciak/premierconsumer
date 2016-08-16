
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
		<div class="article-image">
                  <img src="<?php echo $site->processedImage($page->getNode("image")); ?>" />
            </div>
            <?php echo $page->getNode("contentPage"); ?>
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">
       
       <?php include("includes/articleDownload.php"); ?>
	   <?php include("includes/shortcontact.inc.php"); ?>
	   <?php include("includes/relatedContent.inc.php"); ?>
       


       </div><!--side_col_right -->
      