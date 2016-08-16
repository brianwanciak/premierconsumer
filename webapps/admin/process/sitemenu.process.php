<?php


$GLOBALS['dir'] = '../../../content';
$files = dirToArray($GLOBALS['dir']);

echo '<div id="sitemenu">';
arrayToTree($files, "");
echo '</div>';


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


function arrayToTree($array) {
	//print_r($array); 
	$hiddenDirectories = array("global", "_notes", "en", "es", "under-construction", "thank-you", "learning-center");
	echo '<ul>';
	
	foreach($array as $key => $val){
	
		if(is_array($val)){
			if(!in_array($key, $hiddenDirectories)){
				echo '<li>'.formatName($key, $val);
				arrayToTree($val);
				echo '</li>';
			}
		}
		
	}
	
	echo '</ul>';
	
}


function formatName($val, $path){
	return '<a href="#'.formatPath($path['en'][0]['path']).'">'.ucwords(str_replace("-", " ", $val)).'</a>';
}

function formatPath($path){
	return str_replace("/en/", "", str_replace($GLOBALS['dir'], "", $path));
}



?>
