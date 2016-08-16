<?php

	$cats = simplexml_load_file("../../../content/articles/en/manifest.xml") or die("Error: Cannot retrieve articles");
	foreach($cats as $cat){ 
		echo '<optgroup label="'.$cat->title.'">';
		$articles = simplexml_load_file("../../../content/articles/".$cat->path."/en/manifest.xml") or die("Error: Cannot retrieve articles");
		foreach($articles as $article){
			echo '<option value="'.$cat->path.'/'.$article->path.'">'.$article->title.'</option>';
		}
		echo '</optgroup>';
	}

?>