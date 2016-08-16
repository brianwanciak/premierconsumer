<?php

$path = $_GET["path"];
$task = $_GET["task"];

$editor = new ContentEditor($path, $task);

// http://coffeerings.posterous.com/php-simplexml-and-cdata
class SimpleXMLExtended extends SimpleXMLElement {
  public function addCData($cdata_text) {
    $node = dom_import_simplexml($this); 
    $no   = $node->ownerDocument; 
    $node->appendChild($no->createCDATASection($cdata_text)); 
  } 
}


class ContentEditor{

	function __construct($path, $task){

		$this->basePath = "../../../content";
		$this->task = $task;
		$this->enSave = $this->esSave = $this->draft = false;
		$this->path = $this->basePath.$path;
		$this->hashpath = $path;
		$this->depth = count(explode("/", $path))-1;
		$this->section = $this->getSection($path);
		$this->category = $this->getCategory($path);
		$this->page = $this->getPage($path);
		$this->hasMoved = false;
		
		//echo "Path: ".$this->path;
		//echo "<br />Section: ".$this->section;
		//echo "<br />Category: ".$this->category;
		//echo "<br />Page: ".$this->page;
		//echo "<br />Depth: ".$this->depth;
		
		if($this->task == 'publish'){
			$this->publish();
		}elseif($this->task == 'unpublish'){
			$this->unpublish();
		}
		
		if(file_exists($this->path."/en/content.draft.xml")){
			$this->draft = true;
		}else{
			$this->draft = false;
		}
		
		if($this->task == 'save'){
			$this->saveContent();
		}
	}
	
	function saveContent(){
		$propertiesXML = $contentXML = $xml = $esPropertiesXML = $esContentXML = $esXml = "";
		$postData = $_POST;
		$category = (isset($_POST["category"])) ? $_POST["category"] : "";
		
		$xml = new SimpleXMLExtended("<content></content>");
		$esXml = new SimpleXMLExtended("<content></content>");
		
		
		foreach($postData as $key => $value){
		
			if(strpos($key, "editor-") !== false){
				$key = str_replace("editor-", "", $key);
				if(strpos($key, "es-") !== false){
					$key = str_replace("es-", "", $key);
					$esXml->$key = NULL;
					$esXml->$key->addCData($value);
				}else{
					$xml->$key = NULL;
					$xml->$key->addCData($value);
				}
			}else{
				if(strpos($key, "es-") !== false){
					$key = str_replace("es-", "", $key);
					$esXml->addChild($key, $value);
				}else{
					$xml->addChild($key, $value);
				}
			}
						
		}
		
		
		///// Handles the moving of artricles
		// If it is a level 3 page it mut have a category so this will handle category changes
		if($this->depth > 2){
			$postCat = explode(" ", strtolower($category));
			$postCat = implode("-", $postCat);
			if($postCat != $this->category){
				$this->hashpath = "/".$this->section."/".$postCat."/".$this->page;
				$this->path = $this->basePath.$this->hashpath;
				$oldPath = $this->basePath."/".$this->section."/".$this->category."/".$this->page;
				$this->hasMoved = true;
			}
		}
		
		if($this->hasMoved){
			mkdir($this->path);
			mkdir($this->path."/en");
			mkdir($this->path."/es");
		}
		
		$xml->asXml($this->path."/en/content.draft.xml");
		$esXml->asXml($this->path."/es/content.draft.xml");
		
		$this->enSave = $this->esSave = true;
		
		$this->draft = true;
		
		if($this->hasMoved){
			$this->recursiveRemoveDirectory($oldPath);
		}
		
	}
	
	function publish(){
		if (!is_dir($this->path."/en/archive")) {
   			mkdir($this->path."/en/archive", 0777, true);
		}
		if (!is_dir($this->path."/es/archive")) {
   			mkdir($this->path."/es/archive", 0777, true);
		}
		
		//archive content.draft.xml
		copy($this->path."/en/content.draft.xml", $this->path."/en/archive/content.".date("m.d.Y").".xml");
		copy($this->path."/es/content.draft.xml", $this->path."/es/archive/content.".date("m.d.Y").".xml");
		
		//copy to content.xml
		copy($this->path."/en/content.draft.xml", $this->path."/en/content.xml");
		copy($this->path."/es/content.draft.xml", $this->path."/es/content.xml");
		
		//delete content.draft.xml
		unlink($this->path."/en/content.draft.xml");
		unlink($this->path."/es/content.draft.xml");
		
	}
	
	function unpublish(){
		
		//archive content.draft.xml
		copy($this->path."/en/content.xml", $this->path."/en/content.draft.xml");
		copy($this->path."/es/content.xml", $this->path."/es/content.draft.xml");
		
		//delete content.draft.xml
		unlink($this->path."/en/content.xml");
		unlink($this->path."/es/content.xml");
		
	}
	
	function recursiveRemoveDirectory($directory)
	{
		foreach(glob("{$directory}/*") as $file)
		{
			if(is_dir($file)) { 
				$this->recursiveRemoveDirectory($file);
			} else {
				unlink($file);
			}
		}
		rmdir($directory);
	}

	
	function processKey($key){
		$key = explode("/", $key);
		return str_replace("_", "", $key[1]);
	}
	
	public function getVar($var){
		return $this->$var;
	}
	
	function getSection($path){
		$parts = explode("/", $path);
		$allowed = array("articles", "calculators", "videos");
		return (in_array($parts[1], $allowed)) ? trim($parts[1]) : false;
	}
	
	function getCategory($path){
		if($this->section && ($this->depth > 1)){
			$parts = explode("/", $path);
			return trim($parts[2]);
		}else{
			return false;
		}
	}
	
	function getPage($path){
		$parts = explode("/", $path);
		return $parts[$this->depth];
	}
	
	public function getCategories(){
		$filename = (file_exists($this->basePath."/".$this->section."/en/categories.draft.xml")) ? "categories.draft.xml" : "categories.xml";
		$content = simplexml_load_file($this->basePath."/".$this->section."/en/".$filename) or die("Error: Cannot create object");
		return $content->children();
	}
	
	public function getContent($lang){
		$filename = (file_exists($this->path."/en/content.draft.xml")) ? "content.draft.xml" : "content.xml";
		$content = simplexml_load_file($this->path."/".$lang."/".$filename) or die("Error: Cannot create object");
		return $content;
	}
	
	public function isDraft(){
		return $this->draft;
	}
	
	public function getKeyLabel($key){
		if($key == "pageTitle"){ return "Page Title"; }
		return ucwords($key);
	}
	
	
}



$en = $editor->getContent("en");
$template = $en->template;
$es = $editor->getContent("es");

//echo "Template: ".$template;

?>

<form id="contentForm">

<?php if($editor->draft){ ?>
	<div class="draftDisclaimer">This is an unpublished draft. Click 'Activate' to make these changes live.</div>
<?php } ?>

<?php include("../edit-templates/".$template.".php"); ?>

</form>

<script type="text/javascript">

	<?php 
		if($editor->getVar('task') == "save"){
		if($editor->getVar('hasMoved')){ ?>
			buildSiteNav();
			new SiteNav();
			window.location.href = "content.php?task=edit#<?php echo str_replace("/", "\\\\", $editor->getVar('hashpath')); ?>";
			
		<?php }
		if($editor->getVar('enSave') && $editor->getVar('esSave')){ ?>
		
		formDialog("Changes have been saved", "success");
		
	<?php }else{ ?>
			
		formDialog("There was a problem saving your changes", "error");
	
	<?php }} ?>
	
	<?php if($editor->draft){ ?>
			$("[data-role='page-publish']").show();
			$("[data-role='page-unpublish']").hide();
	<?php }else{ ?>
			$("[data-role='page-publish']").hide();
			$("[data-role='page-unpublish']").show();
	<?php } ?>
	
	if($("#en-text-editor").length > 0){
		var enContentMain = CKEDITOR.replace( 'en-text-editor', {
			toolbar : 'Basic',
			uiColor : '#9AB8F3'
		} );
	}
	if($("#es-text-editor").length > 0){
		var esContentMain = CKEDITOR.replace( 'es-text-editor', {
			toolbar : 'Basic',
			uiColor : '#9AB8F3'
		} );
	}
	
</script>

