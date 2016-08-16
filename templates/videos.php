
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			
            <div class="b-hlite">
            <?php echo $page->getNode("contentPage"); ?>
            </div>
            <?php
				$cats = simplexml_load_file("content/videos/".$site->lang."/manifest.xml") or die("Error: Cannot retrieve articles");
				$i=0;
				foreach($cats as $cat){ 
			?>
            
            	<div class="cat_box expandable">
                    <h2 class="title"><span>+view less</span><a href="javascript:void(0);" class="title"><?php echo $cat->title; ?></a></h2>
                    <div class="body">
                    
                    		<?php
								$vids = $cat->videos->children();
								$j=0;
								foreach($vids as $vid){
								//if($vid->published == "1"){
							?>
                        	
                            <div class="article">
                            <table>
                                <tbody><tr valign="top">
                                    <td style="padding-right: 10px; width: 540px">
                                    <a href="videos/<?php echo $cat->path; ?>/<?php echo $vid->path; ?>"><img src="<?php echo $site->processedImage($vid->image); ?>" class="thumb"></a>
                                    <h2><a href="videos/<?php echo $cat->path; ?>/<?php echo $vid->path; ?>"><?php echo $vid->title; ?></a></h2>
                                    <p><?php echo $vid->desc; ?></p>
                                    </td>
                                    <td>
        
                                    </td>
                                </tr>
                            </tbody></table>
                            </div>
                            
                            <?php
								$j++;
								}
								//}
							?>
        
                         
        
                    </div><!--body -->
                </div>
            
            
            <?php $i++; } ?>
            
            
            
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<?php include("includes/shortcontact.inc.php"); ?>

       </div><!--side_col_right -->
       
