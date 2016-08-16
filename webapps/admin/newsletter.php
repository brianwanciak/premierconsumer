<?php
define('APP_CHECK',true);

$page = "newsletters";

require_once("includes/functions.php");
require_once("includes/authentication.php");
require_once("classes/newsletter.class.php");

$msgSuccess = postVar("msgSuccess");
$msgError = postVar("msgError");
$redirect = false;
$task = getVar("task");

if($task == "edit"){

	$newsletter = new Newsletter(getVar("uid")); 
	$newsletter = $newsletter->data;
	
}else{

	$newsletters = new Newsletters();

}



?>

<!DOCTYPE html>
<html>
  <head>
	<?php 
		$pageTitle = "Newsletter Management - ";
		require_once("includes/headlibs.php"); 
	?>
    <script type="text/javascript" src="js/newsletter.js"></script>
    <link href="css/croppic.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="js/croppic.min.js"></script>
	<script src="ckeditor/ckeditor.js"></script>
	<style type="text/css" rel="stylesheet">
		.img-editor{
			width: 413px;
			height: 248px;
		}
	</style>
  </head>
  <body>
  
       
	<?php require_once("includes/navbar.php"); ?>
    
	<div class="container">
    
    <?php if($task == "edit"){ ?>
	
		<form class="newsletter-form">
    
    	<div class="row" style="margin-bottom:25px">
            <div class="span6">
                <h3 class="table-title">Newsletter Generator</h3>
                	
            </div>
            <div class="span6" align="right">
                                                
                <button type="button" class="btn btn-small" data-role="newsletter-cancel">Cancel</button>
                <button type="button" class="btn btn-small btn-success" data-role="newsletter-save">Generate Newsletter</button>
                     
			</div>
                        
        </div>		
		
		<div class="row newsletter-results" style="margin-bottom:25px; display: none">
            <div class="span12">
                
				
                	
            </div>
              
        </div>		
        
        <div class="row">
        	<span class="span12">
            
            	<div class="accordion" id="accordion2">
                  <div class="accordion-group">
                    <div class="accordion-heading">
                      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                        Configuration
                      </a>
                    </div>
                    <div id="collapseOne" class="accordion-body collapse in">
                      <div class="accordion-inner">
                       
                       			
                                <table class="table table-bordered pc-table">
                                	<tr>
                                    	<td>Month</td>
                                        <td>
                                        	<select name="month">
                                        	<?php
												$months = Newsletter::getMonths();
												foreach($months as $month => $value){
													$selected = $newsletter["month"] == $value ? "selected" : "";
													echo '<option value="'.$value.'" '.$selected.'>'.$month.'</option>';
												}
											
											?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td>Year</td>
                                        <td>
											<select name="year">
                                        	<?php
												$year = date("Y")-1;
												for($i=$year;$i<=$year+2;$i++){
													$selected = date("Y") == $i ? 'selected' : "";
													echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
												}
											?>
											</select>
                                        </td>
                                    </tr>
									<tr>
                                        <td>Article of the Month</td>
                                        <td>
                                            <select name="article" class="article" data-value="<?php echo $newsletter['article']; ?>">
                                                <option value=""></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="admin-description-text">
                                            <td>Description:</td>
                                            <td colspan="2"><textarea name="description" rows="6"><?php echo $newsletter['description_es']; ?></textarea></td>
                                        </tr>  
                                    
									<tr class="admin-description-text">
										<td>Spanish Description:</td>
										<td colspan="2"><textarea name="description-es" rows="6"><?php echo $newsletter['description_es']; ?></textarea></td>
                                    </tr>  
									
                                    <tr class="newsletter-image">
                                            <td>Image:</td>
                                            <td colspan="2">
                                            <div class="current-image"><?php echo ($newsletter['image']) ? '<img src="'.$newsletter['image'].'" />' : ''; ?></div><div id="en-image-holder" class="img-editor"></div><input type="hidden" value="<?php echo $newsletter['image']; ?>" id="en-image" name="image" />
                                            </td>
                                    </tr> 
                                    
                                    <tr class="admin-description-text">
                                            <td colspan="2">
                                            	<div style="padding: 10px; margin: -5px; font-size: 18px; color:#fff; background-color:#666">SideBar Content</div>
                                            </td>
                                        </tr> 
                                    
									<tr class="admin-description-text">
										<td>Company News</td>
										<td><textarea id="company-news" name="company_news">
										<p>Each month we will feature a different educational article specifically written to help you stay on the road to <strong>financial freedom</strong>.</p><p>Remember you can set up your account for automatic payments by calling us, also please visit our website for you most current account activity and to send us statements or documents securely. <a href="http://premierconsumer.org/">Visit us now</a></p>
										</textarea></td>
                                    </tr> 
									
									<tr class="admin-description-text">
										<td>Educational Articles</td>
										<td><textarea id="educational_articles" name="educational_articles">
											<p><a href="http:/www.premierconsumer.org/articles/credit/credit-ratings-and-their-factors.php">Credit Ratings and Their Factors</a><br />The exact formula to calculate a consumer credit score is a company trade secret...</p>
                                                            
											<p><a href="http://www.premierconsumer.org/articles/budget-and-goals/saving-early-retirement.php">Saving Early Equals a More Flexible Retirement Plan</a><br />Retirement may be years away, but you can start planning and saving for it even...</p>
											
											<p><a href="http://www.premierconsumer.org/articles/life/is-it-time-to-reinvent-yourself.php">Is It Time to Re-invent Yourself?</a><br />Stuck with a negative self-image, a boring job you hate, or a comfort eating...</p>
										</textarea></td>
                                    </tr> 
									
									<tr class="admin-description-text">
										<td>Calculator of the Month</td>
										<td><textarea id="calc_of_the_month" name="calc_of_the_month">
										<p><a href="http:/www./premierconsumer.org/calculators/how-much-do-you-owe.php">How much do you owe?</a><br />Use this calculator to find out how much you owe. This can be used as a good starting point for your debt management plan...<br /><a href="http://www.premierconsumer.org/calculators/how-much-do-you-owe.php">Check it out</a></p>
										</textarea></td>
                                    </tr> 
									
									
									<tr class="admin-description-text">
                                            <td colspan="2">
                                            	<div style="padding: 10px; margin: -5px; font-size: 18px; color:#fff; background-color:#666">SideBar Content [Spanish]</div>
                                            </td>
                                        </tr> 
                                    
									<tr class="admin-description-text">
										<td>Company News</td>
										<td><textarea id="company-news-es" name="company_news_es">
											<p>Cada mes presentaremos un art&iacute;culo educativo diferente escrito espec&iacute;ficamente para ayudarle a mantenerse en el camino hacia la <strong>libertad financiera</strong>.</p>
											<p>Recuerde que usted puede configurar su cuenta de pagos autom&aacute;ticos llam&aacute;ndonos, tambi&eacute;n puede visitar nuestro sitio web para verificar la actividad mas reciente de su cuenta y enviarnos las declaraciones o documentos de forma segura. <a href="http://www.librededeudas.com/">Vis&iacute;tenos ahora.</a></p>
											</textarea>
										</td>
									</tr> 
									
									<tr class="admin-description-text">
										<td>Educational Articles</td>
										<td><textarea id="educational_articles_es" name="educational_articles_es">
										<p><a href="http://www.librededeudas.com/articles/credit/credit-ratings-and-their-factors.php">Calificaciones de cr&eacute;dito y sus factores</a><br />La f&oacute;rmula exacta para calcular el puntaje de cr&eacute;dito de un consumidor...</p>
                                                            
										<p><a href="http://www.librededeudas.com/articles/budget-and-goals/saving-early-retirement.php">Planee su retiro con anticipaci&oacute;n</a><br />El retiro puede demorarse en llegar muchos a&ntilde;os, pero usted puede...</p>
										
										<p><a href="http://www.librededeudas.com/articles/life/is-it-time-to-reinvent-yourself.php">&iquest;Es tiempo de re-inventarse?</a><br />&iquest;Estancado en una autoimagen negativa, en un trabajo aburrido que no le gusta...?</p>
										</textarea></td>
                                    </tr> 
									
									<tr class="admin-description-text">
										<td>Calculator of the Month</td>
										<td><textarea id="calc_of_the_month_es" name="calc_of_the_month_es">
										<p><a href="http://www.librededeudas.com/calculators/how-much-do-you-owe.php">&iquest;Cu&aacute;nto debe?</a><br />Use esta aplicaci&oacute;n para calcular cuanto debe. Este puede ser usado como un buen punto de partida para su plan de administraci&oacute;n de deudas.<br /><a href="http://www.librededeudas.com/calculators/how-much-do-you-owe.php">Check it out</a></p>
										</textarea></td>
                                    </tr> 
                                    
                                
                                </table>
                              
                      </div>
                    </div>
                  </div>
                  
                </div>
                
                
                <input type="hidden" value="<?php echo $newsletter['id']; ?>" id="newsletter_id" />
                
                
            	
            
            </span>
        </div>
        
        
        <script type="text/javascript">
			$(document).ready(function(){
				new Newsletter(<?php echo $newsletter["id"]; ?>);			   
			});
			
			var companyNews = CKEDITOR.replace( 'company-news', {
				toolbar : 'Basic',
				uiColor : '#9AB8F3'
			} );
			var calcOfTheMonth = CKEDITOR.replace( 'calc_of_the_month', {
				toolbar : 'Basic',
				uiColor : '#9AB8F3'
			} );
			var educationalArticles = CKEDITOR.replace( 'educational_articles', {
				toolbar : 'Basic',
				uiColor : '#9AB8F3'
			} );
			
			var companyNewsES = CKEDITOR.replace( 'company-news-es', {
				toolbar : 'Basic',
				uiColor : '#9AB8F3'
			} );
			var calcOfTheMonthES = CKEDITOR.replace( 'calc_of_the_month_es', {
				toolbar : 'Basic',
				uiColor : '#9AB8F3'
			} );
			var educationalArticlesES = CKEDITOR.replace( 'educational_articles_es', {
				toolbar : 'Basic',
				uiColor : '#9AB8F3'
			} );
		</script>
    
		</form>
    
    <?php }else{ ?>
    
    
       <div class="row">
    		<div class="span12">
            	<div class="row" style="margin-bottom:15px">
                	<div class="span4">
                    	<h3 class="table-title">Newsletter Management</h3>
                    </div>
                    
                    <div class="span8" align="right">
                    	
                        <button onClick="return false;" class="btn btn-primary link-btn" rel="newsletter.php?task=edit">Add New Newsletter</button>
                     
                    </div>
                </div>
                
                <table class="table table-bordered table-hover pc-table">
                    	<thead>
                            <tr>
                            	<th>Newsletter</th>
                                <th>Link</th>
                                <th>Get Code</th>
                            </tr>
                         </thead>
                         
                         
                         <?php
						 foreach($newsletters as $newsletter){
						 	echo '<tr>';
							echo '<td>'.$newsletter["name"].'</td>';
							echo '<td>'.$newsletter["link"].'</td>';
							echo '<td><a href="'.$newsletter["code-link"].'" data-role="newsletter-code">Get Code</a></td>';
							echo '</tr>';
						 }
						 
						 ?>
                
                		</table>
                
             </div>
      </div>
	
      
      
      
      <?php } ?>
    	
      <?php require_once("includes/footer.php"); ?>
    
    </div>
    
    <script type="text/javascript">
	
	<?php if($newsletter["published"] != 1){ ?>
			$("[data-role='newsletter-publish']").show();
			$("[data-role='newsletter-unpublish']").hide();
	<?php }else{ ?>
			$("[data-role='newsletter-publish']").hide();
			$("[data-role='newsletter-unpublish']").show();
	<?php } ?>

		
		populateList("articleList.php", "article");
		
		function populateList(contentFile, selectClass){
			$.ajax({
				url: "process/"+contentFile,
				type: "GET",
				complete: function(jqXHR, status){
					if(status == "success"){
						$("."+selectClass).append(jqXHR.responseText);
						$("."+selectClass+" option[value='"+$("."+selectClass).attr("data-value")+"']").attr("selected", "selected");
					}
				}
			});
		}
	
		var enCropperOptions = {
			uploadUrl:'imgUpload.process.php?type=newsletter',
			cropUrl:'imgCropSave.process.php?type=newsletter',
			outputUrlId:'en-image',
			modal:false,
			imgEyecandy:false,	
			//loadPicture:'<?php echo $quiz["image"]; ?>',
			enableMousescroll:true,
			loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> '
		}		
		var enImage = new Croppic('en-image-holder', enCropperOptions);
		
	
	</script>
    

  </body>
</html>



	
	

	
