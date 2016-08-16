<?php

$name = $_POST["name"];
$cat = $_POST["category"];


createArticle($name, $cat);

function createArticle($name, $cat){

	$basePath = "../../../content";
	$pathName = convertToPath($name); 
	$pathCat = convertToPath($cat);  
	$articlePath = "/articles/".$pathCat."/".$pathName;
	
	$path = $basePath.$articlePath;
	
	if (file_exists($path)) {
		echo "exists";
		exit();
	}
	
	$xml = "<content><template>article</template><category>".$cat."</category><metaTitle>".$name."</metaTitle><metaDescription></metaDescription><metaKeywords></metaKeywords><pageTitle></pageTitle><shortDesc></shortDesc><contentPage></contentPage></content>";	
	
	mkdir($path);
	mkdir($path."/en");
	mkdir($path."/es");
	
	$enSave = (file_put_contents($path."/en/content.draft.xml",$xml)) ? true : false;
	$esSave = (file_put_contents($path."/es/content.draft.xml",$xml)) ? true : false;
	
	if($enSave && $esSave){
		echo str_replace("/", "\\", $articlePath);
	}else{
		echo "Fail";
	}

}

function convertToPath($str){
	$tmp = explode(" ", strtolower(trim($str)));
	$tmp = preg_replace("/[^a-z0-9]/", '', $tmp);
	$tmp = implode("-", $tmp);
	return $tmp;
}

?>