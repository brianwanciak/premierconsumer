
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			
            <?php echo $page->getNode("contentPage"); ?>
            
            <?php
				$articles = simplexml_load_file("content/".$page->path."/".$site->lang."/manifest.xml") or die("Error: Cannot retrieve articles");
				foreach($articles as $article){ 
			?>
            
            	<div class="article clearfix">
        
                        <a href="<?php echo $page->path; ?>/<?php echo $article->path; ?>"><img class="thumb" src="<?php echo $site->processedImage($article->image) ;?>" /></a>
                        <h2><a href="<?php echo $page->path; ?>/<?php echo $article->path; ?>"><?php echo $article->title ; ?></a></h2>
                        <p><?php echo $article->desc; ?></p>
                        
                </div>
            
            <?php } ?>

            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<?php include("includes/shortcontact.inc.php"); ?>

       </div><!--side_col_right -->
       
