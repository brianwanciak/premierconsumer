<?php


generateArticlesManifest("en");
generateArticlesManifest("es");



function generateArticlesManifest($lang){

	$cats = getCategories("calculators");
	
	$xml = new SimpleXMLElement("<categories></categories>");
	for($i=0;$i<count($cats);$i++){
		if($cats[$i]->published == 1){
			$cat = $cats[$i]->label;
			$xml->addChild($cat);
			
			$catProps = getCategoryProperties($cat, $lang);
			
			if($catProps->sameImage == "on"){
				$label = "image-alt";
				$catProps->image = $catProps->$label;
			}		
			$xml->$cat->addChild("title", $cats[$i]->$lang);
			$xml->$cat->addChild("path", $cats[$i]->label);
			$xml->$cat->addChild("image", $catProps->image);
			$xml->$cat->addChild("desc", $catProps->shortDesc);
			$xml->$cat->addChild("calculators");
			getArticles($cat, $lang, $xml);
			// Should have a function here to get the 

			
		}
	}
	
	$base = '../../../content/calculators';
	$xml->asXML($base."/".$lang."/manifest.xml");
	//echo "<textarea>".$xml->asXml()."</textarea>";

}

function generateCategoryManifest($cat, $articles, $lang, $xml){
	$count = 0;
	foreach($articles as $article){
		$props = getArticleProperties($article, $cat, $lang);
		//print_r($props);
		if($props){
		
			if($props->sameImage == "on"){
				$label = "image-alt";
				$props->image = $props->$label;
			}	
			$node = "calculator".$count;
			$xml->$cat->calculators->addChild($node);
			$xml->$cat->calculators->$node->addChild("title", $props->metaTitle);
			$xml->$cat->calculators->$node->addChild("image", $props->image);
			$xml->$cat->calculators->$node->addChild("path", $article);
			$xml->$cat->calculators->$node->addChild("desc", $props->shortDesc);
			$xml->$cat->calculators->$node->addChild("published", $props->published);
			$count++;
		}
	}
	
	//$base = '../../../content/calculators';
	//$xml->asXML($base."/".$cat."/".$lang."/manifest.xml");
	//echo $xml->asXml();
	//return $xml->asXml();
}



function getArticles($cat, $lang, $xml){
	$base = '../../../content/calculators';
	$list = glob($base . '/' . $cat . '/*' , GLOB_ONLYDIR);
	foreach($list as $l){
		$articles[] = str_replace($base. '/' . $cat . "/", "", $l);
	}
	$articles = array_delete($articles, "en");
	$articles = array_delete($articles, "es");
	generateCategoryManifest($cat, $articles, $lang, $xml);
	//print_r($articles);
}

function getCategoryProperties($cat, $lang){
	$base = '../../../content/calculators';
	$filename = "content.xml";
	$content = simplexml_load_file($base."/".$cat."/".$lang."/".$filename) or die("Error: Cannot create object");
	return $content->children();
}

function getArticleProperties($article, $cat, $lang){
	$base = '../../../content/calculators';
	$filename = "content.xml";
	if(file_exists($base."/".$cat."/".$article."/".$lang."/".$filename)){
		$content = simplexml_load_file($base."/".$cat."/".$article."/".$lang."/".$filename) or die("Error: Cannot create object");
		return $content->children();
	}else{
		return false;
	}
}


function dirToArray($dir) { 
   
   $result = array(); 

   $cdir = scandir($dir); 
   foreach ($cdir as $key => $value) 
   { 
      if (!in_array($value,array(".",".."))) 
      { 
         if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
         { 
            $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value); 
         } 
         else 
         { 
            $result[] = array("path" => $dir . DIRECTORY_SEPARATOR, "value" => $value); 
         } 
      } 
   } 
   
   return $result; 
} 


function array_delete($array, $element){
    return array_diff($array, array($element));
}


function getCategories($section){
	$basePath = "../../../content";
	$filename = (file_exists($basePath."/".$section."/en/categories.draft.xml")) ? "categories.draft.xml" : "categories.xml";
	$content = simplexml_load_file($basePath."/".$section."/en/".$filename) or die("Error: Cannot create object");
	return $content->children();
}

?>