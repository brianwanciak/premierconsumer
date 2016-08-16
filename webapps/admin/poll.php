<?php
define('APP_CHECK',true);

$page = "poll";

require_once("includes/functions.php");
require_once("includes/authentication.php");
require_once("classes/poll.class.php");

$msgSuccess = postVar("msgSuccess");
$msgError = postVar("msgError");
$redirect = false;
$task = getVar("task");

if($task == "edit"){

	$poll = new Poll(getVar("uid"));
	$poll = $poll->poll;
	
}else{

	$polls = new Polls();

}



?>

<!DOCTYPE html>
<html>
  <head>
	<?php 
		$pageTitle = "Poll Management - ";
		require_once("includes/headlibs.php"); 
	?>
    <script type="text/javascript" src="js/poll.js?v=2"></script>
    <link href="css/croppic.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="js/croppic.min.js"></script>
  </head>
  <body>
  
       
	<?php require_once("includes/navbar.php"); ?>
    
	<div class="container">
    
    <?php if($task == "edit"){ ?>
    
    	<div class="row" style="margin-bottom:25px">
            <div class="span6">
                <h3 class="table-title">Title: <?php echo $poll['title']; ?> </h3>
                	
            </div>
            <div class="span6" align="right">
                    	
            	<button type="button" class="btn btn-mini btn-danger" data-role="poll-delete">Delete</button>   
                <button type="button" class="btn btn-small btn-warning publish" data-role="poll-publish">Activate</button>         
                <button type="button" class="btn btn-small btn-danger publish" data-role="poll-unpublish">De-activate</button>                           
                <button type="button" class="btn btn-small" data-role="poll-cancel">Cancel</button>
                <button type="button" class="btn btn-small" data-role="poll-save">Save</button>
                     
			</div>
                        
        </div>
        
        
        <div class="draftDisclaimer" style="display: <?php echo ($poll['published'] != 1) ? "block" : "none"; ?>">This poll is not active on the site, click Activate in order to publish live.</div>

        
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
                      <div class="accordion-inner english">
                       
                       			
                                <table class="table table-bordered pc-table">
                                	<tr>
                                    	<td>Title</td>
                                        <td><input type="text" name="poll-en-title" class="poll-en-title" value="<?php echo $poll['title']; ?>" /></td>
                                    </tr>
                                    <tr class="admin-description-text">
                                            <td>Description:</td>
                                            <td colspan="2"><textarea class="poll-description"><?php echo $poll['description']; ?></textarea></td>
                                        </tr> 
                                    <tr>
                                        <td>Related Quiz</td>
                                        <td>
                                            <select name="relatedQuiz" class="related-quiz dup" data-value="<?php echo $poll['related_quiz']; ?>">
                                                <option value=""></option>
                                            </select>
                                            <input type="hidden" value="<?php echo $poll['related_quiz']; ?>" name="es-relatedQuiz" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Related Article</td>
                                        <td>
                                            <select name="relatedArticle" class="related-article dup" data-value="<?php echo $poll['related_article']; ?>">
                                                <option value=""></option>
                                            </select>
                                            <input type="hidden" value="<?php echo $poll['related_article']; ?>" name="es-relatedArticle" />
                                        </td>
                                    </tr>
                                    <tr class="poll-image">
                                            <td>Image:</td>
                                            <td colspan="2">
                                             <div class="current-image"><?php echo ($poll['image']) ? '<img src="'.$poll['image'].'" />' : ''; ?></div><div id="en-image-holder" class="img-editor"></div><input type="hidden" value="<?php echo $poll['image']; ?>" id="en-image" name="image" />
                                            </td>
                                        </tr>                                    
                                </table>
                                
                                <?php if($poll['id'] != 0){ ?>
                                
									<?php if($poll['en']){ for($i=0; $i<count($poll['en']); $i++){ ?>
                                    <table class="table table-bordered pc-table admin-question" data-lang="english" data-question-id="<?php echo $poll['en'][$i]['id']; ?>">
                                        <tr class="admin-question-text">
                                            <td>Question:</td>
                                            <td colspan="2"><input type="text" name="poll-question" data-id="<?php echo $poll['en'][$i]['id']; ?>" value="<?php echo $poll['en'][$i]['question']; ?>" /></td>
                                        </tr>

                                        <?php if($poll['en'][$i]['answers']){ for($j=0; $j<count($poll['en'][$i]['answers']); $j++){ ?>
                                        <tr class="answer <?php if($correct){ echo "success"; } ?>">
                                            <td class="w-55">A:</td>
                                            <td><input type="text" name="poll-answer" data-id="<?php echo $poll['en'][$i]['answers'][$j]['id']; ?>" value="<?php echo $poll['en'][$i]['answers'][$j]['answer']; ?>" /></td>
                                            <td class="w-35 delete-row"><span class="delete" data-role="delete-answer" data-id="<?php echo $poll['en'][$i]['answers'][$j]['id']; ?>"><span>x</span></span></td>
                                        </tr>
                                        <?php } } ?>
                                        <tr>
                                            <td colspan="3"><button class="btn btn-small" type="button" data-role="add-answer">+ Add Answer</button></td>
                                        </tr>
                                    </table>
                                    <?php } } ?>
                                
                                <?php } ?>
                                                       
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
                      <div class="accordion-inner es">
                       
                       			 <table class="table table-bordered pc-table">
                                	<tr>
                                    	<td>Title</td>
                                        <td><input type="text" name="poll-es-title" class="poll-es-title" value="<?php echo $poll['title_es']; ?>" /></td>
                                    </tr>
                                    <tr class="admin-description-text">
                                            <td>Description:</td>
                                            <td colspan="2"><textarea class="poll-description-es"><?php echo $poll['description_es']; ?></textarea></td>
                                        </tr>  
                                      <tr class="poll-image">
                                            <td>Image:</td>
                                            <td colspan="2">
                                            <div class="current-image">
												<?php  
                                                    if($poll['image_es'] && !$poll['es_sameImage']){
                                                        echo '<img src="'.$poll['image_es'].'" />';
                                                    }else if($poll['es_sameImage']){
                                                        echo '<img src="'.$poll['image'].'" />';
                                                    }
                                                ?>
                                                </div>
                                            <div id="es-image-holder" class="img-editor"></div><input type="hidden" value="<?php echo $poll['image_es']; ?>" id="es-image" name="image_es" />
                                            <label class="checkbox">
                                                <input type="checkbox" id="es-same-image" name="es-sameImage" <?php echo ($poll['es_sameImage']) ? 'checked="checked"' : ""; ?>> Use same image as english
                                            </label>
                                            </td>
                                        </tr> 
                                </table>
                                
                                
                                <?php if($poll['id'] != 0){ ?>
                                
									<?php if($poll['es']){ for($i=0; $i<count($poll['es']); $i++){ ?>
                                    <table class="table table-bordered pc-table admin-question" data-lang="spanish" data-question-id="<?php echo $poll['es'][$i]['id']; ?>">
                                        <tr class="admin-question-text">
                                            <td>Question:</td>
                                            <td colspan="2"><input type="text" name="poll-question" data-id="<?php echo $poll['es'][$i]['id']; ?>" value="<?php echo $poll['es'][$i]['question']; ?>" /></td>
                                        </tr>
                                        
                                        
                                        <?php if($poll['es'][$i]['answers']){ for($j=0; $j<count($poll['es'][$i]['answers']); $j++){ ?>
                                        <tr class="answer <?php if($correct){ echo "success"; } ?>">
                                            <td class="w-55">A:</td>
                                           
                                            <td><input type="text" name="poll-answer" data-id="<?php echo $poll['es'][$i]['answers'][$j]['id']; ?>" value="<?php echo $poll['es'][$i]['answers'][$j]['answer']; ?>" /></td>
                                            <td class="w-35 delete-row"><span class="delete" data-role="delete-answer" data-id="<?php echo $poll['es'][$i]['answers'][$j]['id']; ?>"><span>x</span></span></td>
                                        </tr>
                                        <?php } } ?>
                                        <tr>
                                            <td colspan="3"><button class="btn btn-small" type="button" data-role="add-answer">+ Add Answer</button></td>
                                        </tr>
                                    </table>
                                    <?php } } ?>
                                
                                <?php } ?>
                                
                      </div>
                    </div>
                  </div>
                </div>
                
                
                <input type="hidden" value="<?php echo $poll['id']; ?>" id="poll_id" />
                
                <div id="poll-templates">
                    <table>
                    	<tbody id="answer-template">
                    	<tr class="answer">
                            <td class="w-55">A:</td>
                            <td><input type="text" name="poll-answer" data-id="0" value="" /></td>
                            <td class="w-35 delete-row"><span class="delete" data-role="delete-answer" data-id="0"><span>x</span></span></td>
                        </tr>
                        </tbody>
                    </table>
                    
                    <div id="question-template">
                    <table class="table table-bordered pc-table admin-question new-question">
                        <tr class="admin-question-text">
                            <td>Question:</td>
                            <td colspan="2"><input type="text" name="poll-question" data-id="0" value="" /></td>
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
				new Poll(<?php echo $poll["id"]; ?>);			   
			});
		</script>
    
    
    <?php }else{ ?>
    
    
       <div class="row">
    		<div class="span12">
            	<div class="row" style="margin-bottom:15px">
                	<div class="span4">
                    	<h3 class="table-title">Poll Management</h3>
                    </div>
                    
                    <div class="span8" align="right">
                    	
                        <button onClick="return false;" class="btn btn-primary link-btn" rel="poll.php?task=edit">Add New Poll</button>
                     
                    </div>
                </div>
                
                <table class="table table-bordered table-hover pc-table">
                    	<thead>
                            <tr>
                            	<th>Poll</th>
                                <th>Created</th>
                                <th>Published</th>
                                <th>Action</th>
                            </tr>
                         </thead>
                         
                         
                         <?php
						 foreach($polls->polls as $poll){
						 	echo '<tr>';
							echo '<td><a href="'.$poll["edit_link"].'" data-role="edit-poll">'.$poll["title"].'</a></td>';
							echo '<td>'.$poll["created_date"].'</td>';
							echo '<td>'.$poll["publishedDom"].'</td>';
							echo '<td><a href="'.$poll["edit_link"].'" data-role="edit-poll">Edit</a></td>';
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
	
		<?php if($poll['published'] != 1){ ?>
			$("[data-role='poll-publish']").show();
			$("[data-role='poll-unpublish']").hide();
	<?php }else{ ?>
			$("[data-role='poll-publish']").hide();
			$("[data-role='poll-unpublish']").show();
	<?php } ?>

		$("#es-same-image").click(function(){
			if($(this).is(":checked")){
				$("#es-image-alt").val($("#en-image").val());
			}else{
				$("#es-image-alt").val($("#es-image").val());
			}
		});
		
		populateList("quizList.php", "related-quiz");
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
			enableMousescroll:true,
			loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> '
		}		
		var esImage = new Croppic('es-image-holder', esCropperOptions);
	</script>
    

  </body>
</html>



	
	

	
