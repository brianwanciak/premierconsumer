
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			
            <div class="b-hlite">
            <?php echo $page->getNode("contentPage"); ?>
            </div>
            <?php
				$cats = simplexml_load_file("content/calculators/".$site->lang."/manifest.xml") or die("Error: Cannot retrieve articles");
				$i=0;
				foreach($cats as $cat){ 
			?>
            
            	<div class="cat_box expandable <?php echo ($i==0) ? "" : "closed"; ?>">
                    <h2 class="title"><span>+view <?php echo ($i==0) ? "less" : "more"; ?></span><a href="javascript:void(0);" class="title"><?php echo $cat->title; ?></a></h2>
                    <div class="body">
                    
                    		<?php
								$calcs = $cat->calculators->children();
								$j=0;
								foreach($calcs as $calc){
								//if($calc->published == "1"){
							?>
                        	
                            <div class="article">
                            <table>
                                <tbody><tr valign="top">
                                    <td style="padding-right: 10px; width: 540px">
                                    <a href="<?php echo $cat->path; ?>/<?php echo $calc->path; ?>"><img src="<?php echo $site->processedImage($calc->image); ?>" class="thumb"></a>
                                    <h2><a href="<?php echo $cat->path; ?>/<?php echo $calc->path; ?>"><?php echo $calc->title; ?></a></h2>
                                    <p><?php echo $calc->desc; ?></p>
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
       
