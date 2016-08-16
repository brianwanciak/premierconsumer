
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			            
            <?php 
				$poll_id = $page->parts[3]; 
			?>
            
            	<div id="pollContainer">
                
                </div>
                
                
                <script type="text/javascript">
					$(document).ready(function(){
						var poll_id = <?php echo $poll_id; ?>,
							base_url = "http://www.premierconsumer.org/",
							mid_url = "/webapps/poll.php?poll=",
							end_url = "&lang=<?php echo ($site->getLang() == "en") ? "english" : "spanish"; ?>";
						$.ajax({
							url: mid_url+poll_id+end_url,
							type: "GET",
							complete: function(jqXHR, status){
								if(status == "success"){
									$("#pollContainer").html(jqXHR.responseText);
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
       
        <script src="/js/flot.js"></script>
		<script src="/js/flot.pie.js?v=2"></script>
       
