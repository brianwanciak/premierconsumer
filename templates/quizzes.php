
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content article-cat">
			
            <?php echo $page->getNode("contentPage"); ?>
            
            
            <?php
				$quizzes = simplexml_load_file("content/quizzes/".$site->lang."/manifest.xml") or die("Error: Cannot retrieve articles");

        $arr = array();
        foreach($quizzes as $quiz){
            if($quiz->published == 1){
              $quiz->quizDate = date_format(date_create($quiz->created), "m-d-Y");
              $arr[]=$quiz;
            }
        }

        function sortQuizzes($a, $b){
          return strtotime($b->created)-strtotime($a->created);
        }

        usort($arr, sortQuizzes);
        ?>

        <div class="recent-title"><?php echo ($site->getLang() == "en") ? "Recently Added" : "Recientemente Añadido"; ?></div>
          <ul class="large-block-grid-2 medium-block-grid-2 recent">
          <?php for($i=0; $i<=1; $i++){ ?>
            <li>
                <div class="hp-hlite">
                    <div class="hp-hlite-img">
                        <a href="quizzes/<?php echo $site->convertToPath($arr[$i]->title); ?>/<?php echo $arr[$i]->id; ?>"><img src="<?php echo $site->processedImage($arr[$i]->image) ;?>"></a>
                    </div>
                    <div class="hp-hlite-link">
                        <a href="quizzes/<?php echo $site->convertToPath($arr[$i]->title); ?>/<?php echo $arr[$i]->id; ?>"><?php echo $arr[$i]->title; ?></a>
                        <span class="article-date"><?php echo $arr[$i]->quizDate; ?></span>
                    </div>
                </div>       
             </li>
          <?php } ?>
          </ul>

          <div class="recent-title sub"><?php echo ($site->getLang() == "en") ? "Latest Quizzes" : "Quizzes Más Recientes"; ?></div>
          <?php for($i=2; $i<=7; $i++){ ?>
                <div class="article clearfix">
          
                          <a href="quizzes/<?php echo $site->convertToPath($arr[$i]->title); ?>/<?php echo $arr[$i]->id; ?>"><img class="thumb" src="<?php echo $site->processedImage($arr[$i]->image) ;?>" /></a>

                          <h2><a href="quizzes/<?php echo $site->convertToPath($arr[$i]->title); ?>/<?php echo $arr[$i]->id; ?>"><?php echo $arr[$i]->title ; ?></a> <span class="article-date"><?php echo $arr[$i]->quizDate; ?></span></h2>
                          <p><?php echo substr($arr[$i]->desc, 0, 125); ?>...</p>
                          
                  </div>
              
        <?php } ?>

        <div class="recent-title sub"><?php echo ($site->getLang() == "en") ? "Quiz Archive" : "Archivo De Quizzes"; ?></div>
        <?php for($i=8; $i<=count($arr); $i++){ ?>
                <div class="article clearfix min">
          
                          <a href="quizzes/<?php echo $site->convertToPath($arr[$i]->title); ?>/<?php echo $arr[$i]->id; ?>"><img class="thumb" src="<?php echo $site->processedImage($arr[$i]->image) ;?>" /></a>

                          <h2><a href="quizzes/<?php echo $site->convertToPath($arr[$i]->title); ?>/<?php echo $arr[$i]->id; ?>"><?php echo $arr[$i]->title ; ?></a> <span class="article-date"><?php echo $arr[$i]->quizDate; ?></span></h2>
                          <p><?php echo $arr[$i]->desc; ?></p>
                          
                  </div>
              
        <?php } ?>
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<?php 
			
				include("includes/shortcontact.inc.php");  
				
		?>

       </div><!--side_col_right -->
       
