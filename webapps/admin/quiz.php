<?php
define('APP_CHECK',true);

$page = "quiz";

require_once("includes/functions.php");
require_once("includes/authentication.php");
require_once("classes/quiz.class.php");

$msgSuccess = postVar("msgSuccess");
$msgError = postVar("msgError");
$redirect = false;
$task = getVar("task");

if($task == "edit"){

	$quiz = new Quiz(getVar("uid"));
	$quiz = $quiz->quiz;

}else{

	$quizzes = new Quizzes();

}



?>

<!DOCTYPE html>
<html>
  <head>
	<?php 
		$pageTitle = "Quiz Management - ";
		require_once("includes/headlibs.php"); 
	?>
    <script type="text/javascript" src="js/quiz.js?v=2"></script>
    <link href="css/croppic.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="js/croppic.min.js"></script>
  </head>
  <body>
  
       
	<?php require_once("includes/navbar.php"); ?>
    
	<div class="container">
    
    <?php if($task == "edit"){ ?>
    
    	<div class="row" style="margin-bottom:25px">
            <div class="span6">
                <h3 class="table-title">Title: <?php echo $quiz['title']; ?> </h3>
                	
            </div>
            <div class="span6" align="right">
                    	
            	<button type="button" class="btn btn-mini btn-danger" data-role="quiz-delete">Delete</button>  
                <button type="button" class="btn btn-small btn-warning publish" data-role="quiz-publish">Activate</button>         
                <button type="button" class="btn btn-small btn-danger publish" data-role="quiz-unpublish">De-activate</button>                              
                <button type="button" class="btn btn-small" data-role="quiz-cancel">Cancel</button>
                <button type="button" class="btn btn-small" data-role="quiz-save">Save</button>
                     
			</div>
                        
        </div>
        
  
		<div class="draftDisclaimer" style="display: <?php echo ($quiz['published'] != 1) ? "block" : "none"; ?>">This quiz is not active on the site, click Activate in order to publish live.</div>
		
        
        <div class="row">
        	<span class="span12">
            
            	<div class="accordion" id="accordion2">
                  <div class="accordion-group">
                    <div class="accordion-heading">
                      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                        English
                      </a>
                    </div>
                    <div id="collapseOne" class="accordion-body collapse in">
                      <div class="accordion-inner">
                       
                       			
                                <table class="table table-bordered pc-table">
                                	<tr>
                                    	<td>Title</td>
                                        <td><input type="text" name="quiz-en-title" class="quiz-en-title" value="<?php echo $quiz['title']; ?>" /></td>
                                    </tr>
                                    <tr class="admin-description-text">
                                            <td>Description:</td>
                                            <td colspan="2"><textarea class="quiz-description"><?php echo $quiz['description']; ?></textarea></td>
                                        </tr> 
                                    <tr>
                                        <td>Related Poll</td>
                                        <td>
                                            <select name="relatedPoll" class="related-poll dup" data-value="<?php echo $quiz['related_poll']; ?>">
                                                <option value=""></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Related Article</td>
                                        <td>
                                            <select name="relatedArticle" class="related-article dup" data-value="<?php echo $quiz['related_article']; ?>">
                                                <option value=""></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="quiz-image">
                                            <td>Image:</td>
                                            <td colspan="2">
                                            <div class="current-image"><?php echo ($quiz['image']) ? '<img src="'.$quiz['image'].'" />' : ''; ?></div><div id="en-image-holder" class="img-editor"></div><input type="hidden" value="<?php echo $quiz['image']; ?>" id="en-image" name="image" />
                                            </td>
                                    </tr> 
                                
                                </table>
                                
                                <?php if($quiz['id'] != 0){ ?>
                                
									<?php if($quiz['en']){ for($i=0; $i<count($quiz['en']); $i++){ ?>
                                    <table class="table table-bordered pc-table admin-question" data-lang="english" data-question-id="<?php echo $quiz['en'][$i]['id']; ?>">
                                        <tr class="admin-question-text">
                                            <td colspan="2">Question:</td>
                                            <td><input type="text" name="quiz-question" data-id="<?php echo $quiz['en'][$i]['id']; ?>" value="<?php echo $quiz['en'][$i]['question']; ?>" /></td>
                                            <td class="w-35 delete-row"><span class="delete" data-role="delete-question" data-id="<?php echo $quiz['en'][$i]['id']; ?>"><span>x</span></span></td>
                                        </tr>
                                        <tr class="admin-description-text">
                                            <td colspan="2">Description:</td>
                                            <td><textarea class="quiz-question-description"><?php echo $quiz['en'][$i]['description']; ?></textarea></td>
                                            <td class="w-35 delete-row"></td>
                                        </tr>
                                        <?php if($quiz['en'][$i]['answers']){ for($j=0; $j<count($quiz['en'][$i]['answers']); $j++){ 
											$correct = ($quiz['en'][$i]['answers'][$j]['id'] == $quiz['en'][$i]['answer_id']) ? true : false;
										?>
                                        <tr class="answer <?php if($correct){ echo "success"; } ?>">
                                            <td class="w-55">A:</td>
                                            <td><input type="radio" data-role="quiz-answer-correct" name="answer-<?php echo $quiz['en'][$i]['id']; ?>" <?php if($correct){ echo "checked"; } ?> value="<?php echo $quiz['en'][$i]['answers'][$j]['id']; ?>" /></td>
                                            <td><input type="text" name="quiz-answer" data-id="<?php echo $quiz['en'][$i]['answers'][$j]['id']; ?>" value="<?php echo $quiz['en'][$i]['answers'][$j]['answer']; ?>" /></td>
                                            <td class="w-35 delete-row"><span class="delete" data-role="delete-answer" data-id="<?php echo $quiz['en'][$i]['answers'][$j]['id']; ?>"><span>x</span></span></td>
                                        </tr>
                                        <?php } } ?>
                                        <tr>
                                            <td colspan="4"><button class="btn btn-small" type="button" data-role="add-answer">+ Add Answer</button></td>
                                        </tr>
                                    </table>
                                    <?php } } ?>
                                
                                <?php } ?>
                                
                                <button class="btn btn-small btn-success" type="button" data-role="add-question" data-lang="english" data-quiz-id="<?php echo $quiz['id']; ?>">+ Add Question</button>
                       
                      </div>
                    </div>
                  </div>
                  <div class="accordion-group">
                    <div class="accordion-heading">
                      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                        Spanish
                      </a>
                    </div>
                    <div id="collapseTwo" class="accordion-body collapse">
                      <div class="accordion-inner">
                       
                       			 <table class="table table-bordered pc-table">
                                	<tr>
                                    	<td>Title</td>
                                        <td><input type="text" name="quiz-es-title" class="quiz-es-title" value="<?php echo $quiz['title_es']; ?>" /></td>
                                    </tr>
                                     <tr class="admin-description-text">
                                            <td>Description:</td>
                                            <td colspan="2"><textarea class="quiz-description-es"><?php echo $quiz['description_es']; ?></textarea></td>
                                        </tr>  
                                     
                                    <tr class="quiz-image">
                                            <td>Image:</td>
                                            <td colspan="2">
                                            <div class="current-image">
												<?php  
                                                    if($quiz['image_es'] && !$quiz['es_sameImage']){
                                                        echo '<img src="'.$quiz['image_es'].'" />';
                                                    }else if($quiz['es_sameImage']){
                                                        echo '<img src="'.$quiz['image'].'" />';
                                                    }
                                                ?>
                                                </div>
                                            <div id="es-image-holder" class="img-editor"></div><input type="hidden" value="<?php echo $quiz['image_es']; ?>" id="es-image" name="image_es" />
                                            <label class="checkbox">
                                                <input type="checkbox" id="es-same-image" name="es-sameImage" <?php echo ($quiz['es_sameImage']) ? 'checked="checked"' : ""; ?>> Use same image as english
                                            </label>
                                            
                                            </td>
                                        </tr> 
                                </table>
                                
                                
                                <?php if($quiz['id'] != 0){ ?>
                                
									<?php if($quiz['es']){ for($i=0; $i<count($quiz['es']); $i++){ ?>
                                    <table class="table table-bordered pc-table admin-question" data-lang="spanish" data-question-id="<?php echo $quiz['es'][$i]['id']; ?>">
                                        <tr class="admin-question-text">
                                            <td colspan="2">Question:</td>
                                            <td><input type="text" name="quiz-question" data-id="<?php echo $quiz['es'][$i]['id']; ?>" value="<?php echo $quiz['es'][$i]['question']; ?>" /></td>
                                            <td class="w-35 delete-row"><span class="delete" data-role="delete-question" data-id="<?php echo $quiz['es'][$i]['id']; ?>"><span>x</span></span></td>
                                        </tr>
                                        <tr class="admin-description-text">
                                            <td colspan="2">Description:</td>
                                            <td><textarea class="quiz-question-description"><?php echo $quiz['es'][$i]['description']; ?></textarea></td>
                                            <td class="w-35 delete-row"></td>
                                        </tr>
                                        <?php if($quiz['es'][$i]['answers']){ for($j=0; $j<count($quiz['es'][$i]['answers']); $j++){ 
											$correct = ($quiz['es'][$i]['answers'][$j]['id'] == $quiz['es'][$i]['answer_id']) ? true : false;
										?>
                                        <tr class="answer <?php if($correct){ echo "success"; } ?>">
                                            <td class="w-55">A:</td>
                                            <td><input type="radio" data-role="quiz-answer-correct" name="answer-<?php echo $quiz['es'][$i]['id']; ?>" <?php if($correct){ echo "checked"; } ?> value="<?php echo $quiz['es'][$i]['answers'][$j]['id']; ?>" /></td>
                                            <td><input type="text" name="quiz-answer" data-id="<?php echo $quiz['es'][$i]['answers'][$j]['id']; ?>" value="<?php echo $quiz['es'][$i]['answers'][$j]['answer']; ?>" /></td>
                                            <td class="w-35 delete-row"><span class="delete" data-role="delete-answer" data-id="<?php echo $quiz['es'][$i]['answers'][$j]['id']; ?>"><span>x</span></span></td>
                                        </tr>
                                        <?php } } ?>
                                        <tr>
                                            <td colspan="4"><button class="btn btn-small" type="button" data-role="add-answer">+ Add Answer</button></td>
                                        </tr>
                                    </table>
                                    <?php } } ?>
                                
                                <?php } ?>
                                
                                <button class="btn btn-small btn-success" type="button" data-role="add-question" data-lang="spanish" data-quiz-id="<?php echo $quiz['id']; ?>">+ Add Question</button>
                       
                       
                      </div>
                    </div>
                  </div>
                </div>
                
                
                <input type="hidden" value="<?php echo $quiz['id']; ?>" id="quiz_id" />
                
                <div id="quiz-templates">
                    <table>
                    	<tbody id="answer-template">
                    	<tr class="answer">
                            <td class="w-55">A:</td>
                            <td><input type="radio" data-role="quiz-answer-correct" name="answer-0" value="" /></td>
                            <td><input type="text" name="quiz-answer" data-id="0" value="" /></td>
                            <td class="w-35 delete-row"><span class="delete" data-role="delete-answer" data-id="0"><span>x</span></span></td>
                        </tr>
                        </tbody>
                    </table>
                    
                    <div id="question-template">
                    <table class="table table-bordered pc-table admin-question new-question">
                        <tr class="admin-question-text">
                            <td colspan="2">Question:</td>
                            <td><input type="text" name="quiz-question" data-id="0" value="" /></td>
                            <td class="w-35 delete-row"><span class="delete" data-role="delete-question" data-id="0"><span>x</span></span></td>
                        </tr>
                        <tr class="admin-description-text">
                            <td colspan="2">Description:</td>
                            <td><textarea class="quiz-question-description"></textarea></td>
                            <td class="w-35 delete-row"></td>
                        </tr>
                        <tr>
                            <td colspan="4"><button class="btn btn-small" type="button" data-role="add-answer" data-question-id="">+ Add Answer</button></td>
                        </tr>
                    </table>
                    </div>
                    
                </div>
            	
            
            </span>
        </div>
        
        
        <script type="text/javascript">
			$(document).ready(function(){
				new Quiz(<?php echo $quiz["id"]; ?>);			   
			});
		</script>
    
    
    <?php }else{ ?>
    
    
       <div class="row">
    		<div class="span12">
            	<div class="row" style="margin-bottom:15px">
                	<div class="span4">
                    	<h3 class="table-title">Quiz Management</h3>
                    </div>
                    
                    <div class="span8" align="right">
                    	
                        <button onClick="return false;" class="btn btn-primary link-btn" rel="quiz.php?task=edit">Add New Quiz</button>
                     
                    </div>
                </div>
                
                <table class="table table-bordered table-hover pc-table">
                    	<thead>
                            <tr>
                            	<th>Quiz</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                         </thead>
                         
                         
                         <?php
						 foreach($quizzes->quizzes as $quiz){
						 	echo '<tr>';
							echo '<td><a href="'.$quiz["edit_link"].'" data-role="edit-quiz">'.$quiz["title"].'</a></td>';
							echo '<td>'.$quiz["created_date"].'</td>';
							echo '<td>'.$quiz["publishedDom"].'</td>';
							echo '<td><a href="'.$quiz["edit_link"].'" data-role="edit-quiz">Edit</a></td>';
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
	
	<?php if($quiz['published'] != 1){ ?>
			$("[data-role='quiz-publish']").show();
			$("[data-role='quiz-unpublish']").hide();
	<?php }else{ ?>
			$("[data-role='quiz-publish']").hide();
			$("[data-role='quiz-unpublish']").show();
	<?php } ?>

		$("#es-same-image").click(function(){
			if($(this).is(":checked")){
				$("#es-image-alt").val($("#en-image").val());
			}else{
				$("#es-image-alt").val($("#es-image").val());
			}
		});
		
		populateList("pollList.php", "related-poll");
		populateList("articleList.php", "related-article");
		
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
			uploadUrl:'imgUpload.process.php',
			cropUrl:'imgCropSave.process.php',
			outputUrlId:'en-image',
			modal:false,
			imgEyecandy:false,	
			//loadPicture:'<?php echo $quiz["image"]; ?>',
			enableMousescroll:true,
			loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> '
		}		
		var enImage = new Croppic('en-image-holder', enCropperOptions);
		
		var esCropperOptions = {
			uploadUrl:'imgUpload.process.php',
			cropUrl:'imgCropSave.process.php',
			outputUrlId:'es-image',
			modal:false,
			imgEyecandy:false,	
			//loadPicture:'<?php echo $quiz["image_es"]; ?>',
			enableMousescroll:true,
			loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> '
		}		
		var esImage = new Croppic('es-image-holder', esCropperOptions);
	</script>
    

  </body>
</html>



	
	

	
