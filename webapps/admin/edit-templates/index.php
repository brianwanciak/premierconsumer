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
                               
								<?php for($i=1; $i<=6; $i++){ 
									$cType = "en-cType-".$i;
									$cItem = "en-cItem-".$i;
								?>
                                    <tr class="c-item-wrap">
                                        <td>Carousel Position <?php echo $i; ?></td>
                                        <td>
                                            <div class="c-type-wrap">
                                                <select name="en-cType-<?php echo $i; ?>" class="c-type en-c-type dup" data-value="<?php echo $en->$cType; ?>">
                                                	<option value="">--Select Type--</option>
                                                    <option value="article">Article</option>
                                                    <option value="quiz">Quiz</option>
                                                    <option value="poll">Poll</option>
                                                </select>
                                            </div>
                                            <div class="c-item-wrap">
                                                <select name="en-cItem-<?php echo $i; ?>" class="c-item en-c-item dup" data-value="<?php echo $en->$cItem; ?>">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                            <input type="hidden" value="<?php echo $en->$cItem; ?>" name="es-cItem-<?php echo $i; ?>" class="es-c-item" />
                                             <input type="hidden" value="<?php echo $en->$cType; ?>" name="es-cType-<?php echo $i; ?>" class="en-c-type" />
                                        </td>
                                        <td class="c-move">
                                            <span class="c-move-up">Up</span>
                                            <span class="c-move-down">Down</span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                    
                                
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
                            
                            
                      
                        </table>
                        
                        <h4>Alerts</h4>
                        <table class="table table-bordered pc-table editor-content">
                        	<tr>
                                <td>Type</td>
                                <td>
                                	<select name="alertType" class="alert-type">
                                    	<option value="">Do Not Show</option>
                                        <option value="critical" <?php if($en->alertType == "critical"){ ?>selected<?php } ?>>Critical</option>
                                        <option value="alert" <?php if($en->alertType == "alert"){ ?>selected<?php } ?>>Alert</option>
                                        <option value="informational" <?php if($en->alertType == "informational"){ ?>selected<?php } ?>>Informational</option>
                                    </select>
                                    <input type="hidden" class="es-alert-type" name="es-alertType" value="<?php echo $en->alertType; ?>" />
                                </td>
                            </tr>
                        	<tr>
                                <td colspan="2"><textarea id="en-text-editor"><?php echo $en->contentPage; ?></textarea></td>
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
                        </table>
                        
                        <h4>Alerts</h4>
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

<input type="hidden" name="template" value="index" />
<input type="hidden" name="es-template" value="index" />
<input type="hidden" name="dateModified" value="<?php echo date("m/d/Y"); ?>" />


<script type="text/javascript">

    $(".alert-type").change(function(){
        $(".es-alert-type").val($(this).val());
    });

	$(".dup").change(function(){
		$("[name='es-"+$(this).attr("name")+"']").val($(this).val());
	});
	
	
	$("select.c-type").each(function(){
		var type = $(this).attr("data-value");
		var wrap = $(this).parents("tr");
		if(type != ""){
			$(this).val(type);
			populateList(type+"List.php", $("select.c-item", wrap));
		}
	});

	$(".c-type").change(function(){
		var type = $(this).val();
		var wrap = $(this).parents("tr");
		if(type != ""){
			
			populateList(type+"List.php", $("select.c-item", wrap));
			$("option:first-child", $(this)).remove();
			
		}else{
						
		}
	});

	
	function populateList(contentFile, selectItem){
		$.ajax({
			url: "process/"+contentFile,
			type: "GET",
			complete: function(jqXHR, status){
				if(status == "success"){
					selectItem.html("<option value=''></option>"+jqXHR.responseText);
					$("option[value='"+selectItem.attr("data-value")+"']", selectItem).attr("selected", "selected");
				}
			}
		});
	}
	
	
	$(".c-move-up").click(function(){
		var tr = $(this).parents("tr");
		tr.prev().before(tr);
		reOrderCarousel();
	});
	
	$(".c-move-down").click(function(){
		var tr = $(this).parents("tr");
		tr.next().after(tr);
		reOrderCarousel();
	});
	
	
	function reOrderCarousel(){
		var i = 1;
		$("tr.c-item-wrap").each(function(){
			$("td:first-child", $(this)).html("Carousel Position "+i.toString());
			$(".en-c-type", $(this)).attr("name", "en-cType-"+i);
			$(".en-c-item", $(this)).attr("name", "en-cItem-"+i);
			$(".es-c-type", $(this)).attr("name", "es-cType-"+i);
			$(".es-c-item", $(this)).attr("name", "es-cItem-"+i);
			i++;
		});
	}
	
</script>