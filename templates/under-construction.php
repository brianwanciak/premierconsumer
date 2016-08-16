<?php 
	$lang = $site->getLang();
	$english = $lang == "en" ? true : false;
?>

      
       <div id="side_col_left" style="width:100%; min-height: 500px">
       
       
       <div class="maintenance">
       			<div class="maint-title"><?php echo ($english) ? "Website Under Maintenance" : "Website Under Maintenance" ; ?></div>
       			<img src="/assets/images/maintenance.png" />
                <div class="maint-desc">
                	 <?php echo ($english) ? "This section of our website is undergoing maintenance and will be available as soon as possible, if you have any question please call us at 1.800.296.4950" : htmlentities("Esta sección de nuestra página se encuentra en mantenimiento y estará disponible a la mayor brevedad posible. Si tiene alguna pregunta por favor llámenos al 1.800.296.4950") ; ?>
                </div>
       </div>
       
  <script type="text/javascript">
  
  	$(document).ready(function(){
		$(".footer-nav, .top-bar-section, .seals").remove();
	});
  	
  </script>     
