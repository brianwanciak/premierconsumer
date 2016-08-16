

       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			            
            <?php echo $page->getNode("contentPage"); ?>
            
            
            <div id="KJEAllContent" class=#KJEAllContent></div>
    
   		 <!--[if lt IE 9]>
        <script language="JavaScript" SRC="excanvas.js"></script>
        <![endif]-->
        <?php if ($site->getLang() == "es"){ ?>
            <script language="JavaScript" type="text/javascript" SRC="/js/calculators/KJESpanish.js"></script>
        <?php }else{ ?>
            <script language="JavaScript" type="text/javascript" SRC="/js/calculators/KJE.js"></script>
        <?php } ?>
        
        <script language="JavaScript" type="text/javascript" SRC="/js/calculators/KJESiteSpecific.js"></script>
        
		<script language="JavaScript" type="text/javascript" SRC="/js/calculators/<?php echo $page->getNode("jsFile"); ?>.js"></script>
        
        <script language="JavaScript" type="text/javascript" SRC="/js/calculators/<?php echo $page->getNode("jsFile"); ?>Params.js"></script>

           
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<?php 
			
				include("includes/shortcontact.inc.php");  
				
		?>

       </div><!--side_col_right -->
       
