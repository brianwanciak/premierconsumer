<?php

	$quizzes = simplexml_load_file("../../../content/quizzes/en/manifest.xml") or die("Error: Cannot retrieve articles");
	foreach($quizzes as $quiz){ 
		echo '<option value="quiz'.$quiz->id.'">'.$quiz->title.'</option>';
	}

?>