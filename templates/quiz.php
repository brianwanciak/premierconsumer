
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			            
            <?php 
				$quiz_id = $page->parts[3]; 
			
			?>
            
            	<div id="quizContainer">
                
                </div>
                
                
                <script type="text/javascript">
					$(document).ready(function(){
						var quiz_id = <?php echo $quiz_id; ?>,
							base_url = "http://www.premierconsumer.org/",
							mid_url = "/webapps/quiz.php?quiz=",
							end_url = "&lang=<?php echo ($site->getLang() == "en") ? "english" : "spanish"; ?>";
						$.ajax({
							url: mid_url+quiz_id+end_url,
							type: "GET",
							complete: function(jqXHR, status){
								if(status == "success"){
									$("#quizContainer").html(jqXHR.responseText);
								}
							}
						});
					});
				</script>
           
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<?php 
			
				include("includes/shortcontact.inc.php");  
				include("includes/relatedContent.inc.php");
				
		?>

       </div><!--side_col_right -->
       
