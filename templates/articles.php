
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			
            <?php 
              echo $page->getNode("contentPage"); 
              $lang = $site->getLang();
            ?>
            
            <?php
				$cats = simplexml_load_file("content/articles/".$site->lang."/manifest.xml") or die("Error: Cannot retrieve articles");
				foreach($cats as $cat){ 
			?>
            
            	<div class="cat_box articles">
                    <h2 class="title"><span>+ <?php echo ($lang == "en") ? "view all" : "verlos todos"; ?></span><a href="articles/<?php echo $cat->path; ?>" class="title"><?php echo $cat->title; ?></a></h2>
                    <div class="body">
        
                                <a href="articles/<?php echo $cat->path; ?>" class="thumb-link"><img class="thumb" src="<?php echo $site->processedImage($cat->image); ?>" /></a>
                                
                                <div class="articles-detail">
                                    <h2><a href="articles/<?php echo $cat->path; ?>"><?php echo ($lang == "en") ? "Articles" : "Artículos"; ?> (<?php echo $cat->articles; ?>) </a></h2>
                                    <p><?php echo $cat->desc; ?></p>
                                <!--<p><strong>Recent Articles</strong></p>
                                <ul>
                                    <li><a href="articles/budget-and-goals/tips-to-avoid-tax-trouble.php">
        Tips on How to Avoid Tax Trouble</a></li>
                                    <li><a href="articles/budget-and-goals/15-or-30-year-mortgage.php">Should I do a 15 year or a 30 year Mortgage?</a></li>
                                    <li><a href="articles/budget-and-goals/retire-at-60.php">Retire Abroad at 55 Years Old and $150,000 Savings</a></li>
                                    
         
                                    </ul>-->
                                </div>
                                <div class="clear"></div>
                    </div><!--body -->
                </div><!--cat_box -->
            
            <?php } ?>
            
            	<div class="cat_box articles" style="display:none">
                    <h2 class="title"><span>+ view all</span><a href="/calculators/" class="title">Calculators</a></h2>
                    <div class="body">
        
                                <a href="/calculators/" class="thumb-link"><img class="thumb" src="/assets/images/calculators.jpg" /></a>
                                
                                <div class="articles-detail">
                                    <h2><a href="calculators">Calculators (##) </a></h2>
                                    <p>An excellent way to plan your financial life is by using the following financial calculators.</p>
                                <!--<p><strong>Recent Articles</strong></p>
                                <ul>
                                    <li><a href="articles/budget-and-goals/tips-to-avoid-tax-trouble.php">
        Tips on How to Avoid Tax Trouble</a></li>
                                    <li><a href="articles/budget-and-goals/15-or-30-year-mortgage.php">Should I do a 15 year or a 30 year Mortgage?</a></li>
                                    <li><a href="articles/budget-and-goals/retire-at-60.php">Retire Abroad at 55 Years Old and $150,000 Savings</a></li>
                                    
         
                                    </ul>-->
                                </div>
                                <div class="clear"></div>
                    </div><!--body -->
                </div><!--cat_box -->
            
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<?php include("includes/shortcontact.inc.php"); ?>

       </div><!--side_col_right -->
       
