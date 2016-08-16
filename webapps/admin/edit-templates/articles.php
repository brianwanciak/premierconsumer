<div class="row-fluid">
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

<input type="hidden" name="template" value="articles" />
<input type="hidden" name="es-template" value="articles" />
<input type="hidden" name="dateModified" value="<?php echo date("m/d/Y"); ?>" />