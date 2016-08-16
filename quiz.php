<?php
$page = "quiz";
$page_title = "Quiz | Premier Consumer Credit Counseling - The Road to Your Financial Freedom";
?>

<?php include("includes/header.inc.php"); ?>
        
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1>Who We Are</h1></div>
       <div class="box-content">
			<h1>Quiz</h1>
            
            <iframe src="<?php echo $quiz_url; ?>?quiz=2&lang=english" frameborder="none" style="border:0px solid #fff; overflow-x: hidden; overflow-y: auto; width:622px; height:800px "></iframe>
            </div><!--box-content-->
       </div><!--box shadow-->
       </div><!--side_col_left -->
       
       <div id="side_col_right">
   
   		<?php include("includes/shortcontact.inc.php"); ?>
   		 
        
        
       </div><!--side_col_right -->
       
<?php include("includes/footer.inc.php"); ?>