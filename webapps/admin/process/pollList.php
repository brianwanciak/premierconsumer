<?php

	$polls = simplexml_load_file("../../../content/polls/en/manifest.xml") or die("Error: Cannot retrieve articles");
	foreach($polls as $poll){ 
		echo '<option value="poll'.$poll->id.'">'.$poll->title.'</option>';
	}

?>