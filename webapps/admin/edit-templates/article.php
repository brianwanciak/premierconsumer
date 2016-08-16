<div class="row-fluid">
    <span class="span12">
    
        <div class="accordion" id="accordion2">
        
                <div class="accordion-group">
                <div class="accordion-heading">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseZero">
                    General Settings
                  </a>
                </div>
                <div id="collapseZero" class="accordion-body collapse in">
                  <div class="accordion-inner english">
                   
                           <table class="table table-bordered pc-table editor-content">
                                <tr>
                                    <td>Category</td>
                                    <td><select name="category">
									<?php 
										$cats = $editor->getCategories(); 
										for($i=0;$i<count($cats);$i++){
											$selected = ($cats[$i]->en == trim($en->category)) ? "selected" : "";
											echo '<option value="'.$cats[$i]->en.'" '.$selected.'>'.$cats[$i]->en.'</option>';;
										}	
									?>
                                    </select>
                                    
                                    </td>
                                </tr>
                                <tr>
                                    <td>Related Quiz</td>
                                    <td>
                                    	<select name="relatedQuiz" class="related-quiz dup" data-value="<?php echo $en->relatedQuiz; ?>">
                                        	<option value=""></option>
                                        </select>
                                        <input type="hidden" value="<?php echo $en->relatedQuiz; ?>" name="es-relatedQuiz" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Related Poll</td>
                                    <td>
                                    	<select name="relatedPoll" class="related-poll dup" data-value="<?php echo $en->relatedPoll; ?>">
                                        	<option value=""></option>
                                        </select>
                                        <input type="hidden" value="<?php echo $en->relatedPoll; ?>" name="es-relatedPoll" />
                                    </td>
                                </tr>
                           </table>
                           
                                                   
                  </div>
                </div>
              </div>

          <div class="accordion-group">
            <div class="accordion-heading">
              <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                English
              </a>
            </div>
            <div id="collapseOne" class="accordion-body collapse">
              <div class="accordion-inner english">
              
                       
                       <h4>Page Properties</h4>
                       <table class="table table-bordered pc-table editor-content">
                        	<tr>
                                <td>Meta Title</td>
                                <td><input type="text" name="metaTitle" value="<?php echo $en->metaTitle; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Meta Description</td>
                                <td><input type="text" name="metaDescription" value="<?php echo $en->metaDescription; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Meta Keywords</td>
                                <td><input type="text" name="metaKeywords" value="<?php echo $en->metaKeywords; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Page Title</td>
                                <td><input type="text" name="pageTitle" value="<?php echo $en->pageTitle; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Short Description</td>
                                <td><textarea name="shortDesc"><?php echo $en->shortDesc; ?></textarea></td>
                            </tr>
                            <tr>
                                <td>Article Image</td>
                                <td><div class="current-image"><?php echo ($en->image) ? '<img src="'.$en->image.'" />' : ''; ?></div><div id="en-image-holder" class="img-editor"></div><input type="hidden" value="<?php echo $en->image; ?>" id="en-image" name="image" />
                                
                                </td>
                            </tr>
                        </table>
                        
                        
                        
                        <h4>Page Content</h4>
                        <table class="table table-bordered pc-table editor-content">
                        	<tr>
                                <td><textarea id="en-text-editor"><?php echo $en->contentPage; ?></textarea></td>
                            </tr>
                        </table>
                        
                       
                                               
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

                       
                       <h4>Page Properties</h4>
                       <table class="table table-bordered pc-table editor-content">
                        	<tr>
                                <td>Meta Title</td>
                                <td><input type="text" name="es-metaTitle" value="<?php echo $es->metaTitle; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Meta Description</td>
                                <td><input type="text" name="es-metaDescription" value="<?php echo $es->metaDescription; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Meta Keywords</td>
                                <td><input type="text" name="es-metaKeywords" value="<?php echo $es->metaKeywords; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Page Title</td>
                                <td><input type="text" name="es-pageTitle" value="<?php echo $es->pageTitle; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Short Description</td>
                                <td><textarea name="es-shortDesc"><?php echo $es->shortDesc; ?></textarea></td>
                            </tr>
                            <tr>
                                <td>Article Image</td>
                                <td><div class="current-image">
									<?php  
										if($es->image && !$es->sameImage){
											echo '<img src="'.$es->image.'" />';
										}else if($es->sameImage){
											$label = "image-alt";
											echo '<img src="'.$es->$label.'" />';
										}
									?>
                                    </div>
                                <div id="es-image-holder" class="img-editor"></div><input type="hidden" value="<?php echo $es->image; ?>" id="es-image" name="es-image" />
                                <input type="hidden" value="<?php echo $en->image; ?>" name="es-image-alt" id="es-image-alt" />
                                <label class="checkbox">
   									<input type="checkbox" id="es-same-image" name="es-sameImage" <?php echo ($es->sameImage) ? 'checked="checked"' : ""; ?>> Use same image as english
  								</label></td>
                            </tr>
                        </table>
                        
                        
                        
                        <h4>Page Content</h4>
                        <table class="table table-bordered pc-table editor-content">
                        	<tr>
                                <td><textarea id="es-text-editor"><?php echo $es->contentPage; ?></textarea></td>
                            </tr>
                        </table>
                        
              </div>
            </div>
          </div>
        </div>

    </span>
</div>

<input type="hidden" name="template" value="article" />
<input type="hidden" name="es-template" value="article" />
<input type="hidden" name="dateModified" value="<?php echo date("m/d/Y"); ?>" />
<input type="hidden" name="es-dateModified" value="<?php echo date("m/d/Y"); ?>" />


<script type="text/javascript">

	$("#es-same-image").click(function(){
		if($(this).is(":checked")){
			$("#es-image-alt").val($("#en-image").val());
		}else{
			$("#es-image-alt").val($("#es-image").val());
		}
	});

	var enCropperOptions = {
		uploadUrl:'imgUpload.process.php',
		cropUrl:'imgCropSave.process.php',
		outputUrlId:'en-image',
		modal:false,
		imgEyecandy:false,	
		//loadPicture:'<?php echo $en->image; ?>',
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
		//loadPicture:'<?php echo $es->image; ?>',
		enableMousescroll:true,
		loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> '
	}		
	var esImage = new Croppic('es-image-holder', esCropperOptions);
	
	$(".dup").change(function(){
		$("[name='es-"+$(this).attr("name")+"']").val($(this).val());
	});
	
	populateList("pollList.php", "related-poll");
	populateList("quizList.php", "related-quiz");
	
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
</script>