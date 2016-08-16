

<?php

require_once("admin/includes/db.php");



//$con = mysql_connect("localhost", "jglen01_admin", "Blue9901");
$con = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
  mysql_set_charset('utf8',$con);
  mysql_select_db(DB_NAME);
  $results = false;
	if(isset($_POST['results'])){
		$results = true;
		$quiz = $_POST['quiz'];
		$lang = $_POST['lang'];
		$sql = "SELECT * FROM `questions` WHERE quiz_id = ".$quiz." AND lang = '".$lang."'";
  		$result = mysql_query($sql);
  		
  		while($row = mysql_fetch_array($result)){
			$questions[] = $row;
		}
		
		echo '<div id="summary"></div>';
		echo '<h2>'.getTranslation("Results").'</h2>';
		$i = 1;  
		foreach($questions as $q){
			$answer = $_POST['answer'.$q['id']];
			$query = "SELECT * FROM answers WHERE id=".$q['answer_id'];
			$result = mysql_query($query);
			$c_answer = mysql_fetch_array($result);
			
			echo '<div class="q-block">';
			echo '<div class="question">'.$i.'. '.$q['question'].'</div>';
			if($answer == $q['answer_id']){
				echo '<div class="result"><span class="right">'.getTranslation("CORRECT").'!</span><br />'.$c_answer['answer'].'</div>';
				$correct_answers[] = $q['answer_id'];
				$query = "UPDATE `questions` SET `right` = ".($q['right']+1)." WHERE id = ".$q['id']." LIMIT 1";
				mysql_query($query);
			}else{
				echo '<div class="result"><span class="wrong">'.getTranslation("WRONG").'!</span> '.getTranslation("The correct answer is").':<br />'.$c_answer['answer'].'</div>';
				$query = "UPDATE `questions` SET `wrong` = ".($q['wrong']+1)." WHERE id = ".$q['id']." LIMIT 1";
				mysql_query($query);
			}
			
			$desc = $q['description'];
			if($lang == "spanish"){
				$list = get_html_translation_table(HTML_ENTITIES);
				unset($list['"']);
				unset($list['<']);
				unset($list['>']);
				unset($list['&']);
				
				//$desc = strtr($desc, $list);
			}

			echo '<div class="explanation"><strong>'.getTranslation("Explanation").'</strong><br />'.$desc.'</div>';
			
			echo '</div>';
			$i++;
		}
		
		echo '<div id="summary2">';
		echo '<h2>'.getTranslation("Summary").'</h2>';
		echo '<div class="summary">';
		echo '<p>'.getTranslation("You answered").' <strong>'.count($correct_answers).'</strong> '.getTranslation("of").' <strong>'.($i-1).'</strong> '.getTranslation("questions correctly").'.</p>';
		echo '<p><strong>'.getTranslation("History").'</strong></p>';
		$j = 1;
		echo '<p>';
		foreach($questions as $q){
			echo getTranslation("Question").' '.$j.': '.getTranslation("Answered correctly by").' <strong>'.$q['right'].'</strong> '.getTranslation("people").'<br />';
			$j++;
		}
		echo '</p>';
		echo '</div>';
		echo '</div>';
		
	
	}
	
	
	
	//START QUIZ OUTPUT
	if(!$results){

		$quiz_id = $_GET['quiz'];
		$lang = $_GET['lang'];

		if($quiz_id == "" || !isset($_GET)){
			die();
		}
  ?>
  <div id="quizContent">
 	<div class="loading-icon"><img src="/assets/images/ajax-loader-wheel.gif" /></div>
    <div class="loading-overlay"></div>
  <form action="quiz.php" method="post" name="quiz_form" id="quizForm">
  <?php
  $sql = "SELECT * FROM `questions` WHERE quiz_id = ".$quiz_id." AND lang = '".$lang."'";
  $result = mysql_query($sql);
  
  $i = 1;  
  while($row = mysql_fetch_array($result)){
  	echo '<div class="q-block"><div class="question">'.$i.'. '.$row['question'].'</div>';
	
	$query = "SELECT * FROM `answers` WHERE `question_id` = ".$row['id']." AND language = '".$lang."'";
	$result2 = mysql_query($query);
	$j = 1;
	while($row2 = mysql_fetch_array($result2)){
		echo '<div class="answer"><input type="radio" name="answer'.$row['id'].'" value="'.$row2['id'].'" /><p> '.$row2['answer'].'</p></div>';
		$j++;
	}
  	echo '</div>';
	$i++;
  }
  
 
	
	
?> 
	<input type="hidden" value="<?php echo $quiz_id ?>" name="quiz" />
    <input type="hidden" value="<?php echo $lang ?>" name="lang" />
    <input type="hidden" value="1" name="results" />
	<input type="submit" value="<?php echo getTranslation("Submit");?>" name="submit" />

</form>
</div>
	<script type="text/javascript">
		$("#quizForm").submit(function(e){
			e.preventDefault();
			submitForm();
		});
		
		function submitForm(){
			$("body").addClass("working");	
			var data = $("#quizForm").serialize();
			$.ajax({
				url: "/webapps/quiz.php",
				type: "POST",
				data: data,
				complete: function(jqXHR, status){
					if(status == "success"){
						$("#quizContent").html(jqXHR.responseText);
					}
					$("body").removeClass("working");
				}
			});
		}
		
	</script>
<?php
 }
 mysql_close();
 
 
 function getTranslation($term){
 	$lang = (isset($_POST["lang"])) ? $_POST['lang'] : $_GET["lang"];
	if($lang == "spanish"){
		switch($term){
			case "Summary";
				return "Sumario";
				break;
			case "Question";
				return "Pregunta";
				break;
			case "History";
				return "Historia";
				break;
			case "Answered correctly by";
				return "Respondida correctamente por";
				break;
			case 'people':
				return "personas";
				break;
			case "Results";
				return "Resultados";
				break;
			case "Explanation":
				return "Explicación";
				break;
			case "WRONG":
				return "Incorrecto";
				break;
			case "CORRECT":
				return "Correcto";
				break;
			case "The correct answer is";
				return "La respuesta correcta es";
				break;
			case "Submit":
				return "Enviar Quiz";
				break;
			case "You answered":
				return "Usted respondió ";
				break;
			case "questions correctly":
				return "preguntas correctamente";
				break;
			case "of":
				return "de";
				break;
			default;
				return $term;
				break;
		}
	}else{
		return $term;
	}
	
 }

?>