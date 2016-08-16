<?php

$task = $_GET["task"];

$editor = new ArticleCategories($task);

class ArticleCategories{

	function __construct($task){
		$this->task = $task;
		$this->save = false;
		$this->path = "../../../content/articles/en/categories.xml";
		
		if($this->task == 'save'){
			$this->saveContent();
		}
	}
	
	function saveContent(){
		$tagList = array("en", "es", "label", "published");
		$xml = "<categories>";
		
		for($j=0; $j<count($_POST['en']); $j++){
			$xml .= "<category>";
			foreach($tagList as $key => $value){
				$xml .= "<".$value.">".$_POST[$value][$j]."</".$value.">";
			}
			$xml .= "</category>";
		}

		
		$xml .= "</categories>";
		$this->save = (file_put_contents($this->path,$xml)) ? true : false;		
	}
	

	public function getContent(){
		$content = simplexml_load_file($this->path) or die("Error: Cannot create object");
		return $content;
	}
	
	public function getVar($var){
		return $this->$var;
	}
	
	function processKey($key){
		$key = explode("-", $key);
		return str_replace("_", "", $key[1]);
	}

	
}

?>

<form id="contentForm">

<div class="row">
    <span class="span12">
    
        <div class="accordion" id="accordion2">
          <div class="accordion-group">
            <div class="accordion-heading">
              <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                English and Spanish
              </a>
            </div>
            <div id="collapseOne" class="accordion-body collapse in">
              <div class="accordion-inner english">
               
                       <?php
					   
					   	$content = $editor->getContent();
					   ?>
                       
                       <?php $i=0; foreach($content->children() as $key => $value){	 ?>
                       	<table class="table table-bordered pc-table editor-content">
                            <tr>
                                <td>English Title</td>
                                <td><input type="text" name="en[]" value="<?php echo $value->en; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Spanish Title</td>
                                <td><input type="text" name="es[]" value="<?php echo $value->es; ?>" /></td>
                            </tr>
                            <tr style="display:none">
                                <td>Label</td>
                                <td><input type="text" name="label[]" value="<?php echo $value->label; ?>" /></td>
                            </tr>
                            <tr style="display:none">
                                <td>Status</td>
                                <td><input type="text" name="published[]" value="<?php echo $value->published; ?>" /></td>
                            </tr>
                        </table>
                        <? $i++; } ?>
                        
                        <button data-role="add-category" type="button" class="btn btn-small btn-success" onclick="addCategory()" style="display:none">+ Add Category</button>
                                
                </div>
            </div>
          </div>
         
        </div>

    </span>
</div>

</form>

<div id="template" style="display:none">
<table class="table table-bordered pc-table editor-content">
    <tr>
        <td>English Title</td>
        <td><input type="text" name="en[]" value="" /></td>
    </tr>
    <tr>
        <td>Spanish Title</td>
        <td><input type="text" name="es[]" value="" /></td>
    </tr>
    <tr>
        <td>Label</td>
        <td><input type="text" name="label[]" value="" /></td>
    </tr>
    <tr>
        <td>Status</td>
        <td><input type="text" name="published[]" value="1" /></td>
    </tr>
</table>
</div>

<script type="text/javascript">

	function addCategory(){
		var html = $("#template").html();
		$('[data-role="add-category"]').before(html);
	}

	<?php 
		if($editor->getVar('task') == "save"){
		if($editor->getVar('save')){ ?>
		
		formDialog("Changes have been saved", "success");
		
	<?php }else{ ?>
			
		formDialog("There was a problem saving your changes", "error");
	
	<?php }} ?>

	
</script>

