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
              
                       
                       <table class="table table-bordered pc-table editor-content">
                        	
                            <tr>
                                <td>Short Description</td>
                                <td><textarea name="shortDesc"><?php echo $en->shortDesc; ?></textarea></td>
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

                       
                       <table class="table table-bordered pc-table editor-content">
                        	
                            <tr>
                                <td>Short Description</td>
                                <td><textarea name="es-shortDesc"><?php echo $es->shortDesc; ?></textarea></td>
                            </tr>
                           
                        </table>
                        
                        
                        
                       
                        
              </div>
            </div>
          </div>
        </div>

    </span>
</div>

<input type="hidden" name="template" value="calculator-category" />
<input type="hidden" name="es-template" value="calculator-category" />
<input type="hidden" name="dateModified" value="<?php echo date("m/d/Y"); ?>" />

