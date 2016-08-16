<?php


generateArticlesManifest("en");
generateArticlesManifest("es");



function generateArticlesManifest($lang){

	$cats = getCategories("articles");
	
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
			$xml->$cat->addChild("articles", getArticles($cat, $lang));
			// Should have a function here to get the 
			//
			//

			
		}
	}
	
	$base = '../../../content/articles';
	$xml->asXML($base."/".$lang."/manifest.xml");
	//echo "<textarea>".$xml->asXml()."</textarea>";

}

function generateCategoryManifest($cat, $articles, $lang){
	$count = 0;
	$xml = new SimpleXMLElement("<articles></articles>");
	foreach($articles as $article){
		$props = getArticleProperties($article, $cat, $lang);
		//print_r($props);
		if($props){
		
			if($props->sameImage == "on"){
				$label = "image-alt";
				$props->image = $props->$label;
			}	
			$node = "article".$count;
			$xml->addChild($node);
			$xml->$node->addChild("title", $props->metaTitle);
			$xml->$node->addChild("image", $props->image);
			$xml->$node->addChild("path", $article);
			$xml->$node->addChild("desc", $props->shortDesc);
			$xml->$node->addChild("published", $props->published);
			$xml->$node->addChild("articleDate", $props->dateModified);
			$count++;
		}
	}
	
	$base = '../../../content/articles';
	$xml->asXML($base."/".$cat."/".$lang."/manifest.xml");
	//echo $xml->asXml();
	return $count;
}



function getArticles($cat, $lang){
	$base = '../../../content/articles';
	$list = glob($base . '/' . $cat . '/*' , GLOB_ONLYDIR);
	foreach($list as $l){
		$articles[] = str_replace($base. '/' . $cat . "/", "", $l);
	}
	$articles = array_delete($articles, "en");
	$articles = array_delete($articles, "es");
	return generateCategoryManifest($cat, $articles, $lang);
	//print_r($articles);
}

function getCategoryProperties($cat, $lang){
	$base = '../../../content/articles';
	$filename = "content.xml";
	$content = simplexml_load_file($base."/".$cat."/".$lang."/".$filename) or die("Error: Cannot create object");
	return $content->children();
}

function getArticleProperties($article, $cat, $lang){
	$base = '../../../content/articles';
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