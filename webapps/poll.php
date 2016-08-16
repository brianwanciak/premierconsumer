



<?php

require_once("admin/includes/db.php");
$con = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
  mysql_set_charset('utf8',$con);
  mysql_select_db(DB_NAME);
  
  if(isset($_POST['results'])){
  
  	$poll = $_POST['poll'];
	$lang = $_POST['lang'];
	$question = $_POST['question'];
	$sql = "SELECT * FROM `poll_questions` WHERE poll_id = ".$poll." AND lang = '".$lang."'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$questionText = $row['question'];
	
	$a = $_POST['answer'];
	$query = "UPDATE `poll_answers` SET count = count+1 WHERE id = ".$a;
	mysql_query($query);
	
	$query = "SELECT * FROM `poll_answers` WHERE `question_id` = ".$question;
	$result = mysql_query($query);
	$total = 0;
	while($row = mysql_fetch_array($result)){
			$data[] = '{ label: "'.$row["answer"].'",  data: '.$row["count"].'}';
			$total += $row["count"];
	}
	
	?>
    
   
    
    
    <?php
	
	echo '<h2>'.getTranslation("Results").'</h2>';
	echo '<div class="q-block"><div class="question">'.$questionText.'</div>'; 
	
	?>
    <div id="pie" class="graph" style="width:100%; height: 500px; margin: 50px 0"></div>
     <script type="text/javascript">
		$(function () {
	
			var data = [
				<?php echo implode(",", $data); ?>
			];
		
			$.plot($("#pie"), data,
			{
					series: {
						pie: {
							show: true,
							radius: 1,
							label: {
								show: true,
								radius: 1,
								formatter: function(label, series){
									return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
								},
								background: { opacity: 0.8 }
							}
						}
					},
					legend: {
						show: false
					}
			});
		
		});
		
	</script>
    
    <?php
    
  }else{
  ?>
  <div id="pollContent">
  	<form action="poll.php" method="post" name="pollForm" id="pollForm" onsubmit="validatePoll(); return false;">
    
    <?php

    	$poll_id = $_GET['poll'];
		$lang = $_GET['lang'];

		if($poll_id == "" || !isset($_GET)){
			die();
		}

		$sql = "SELECT * FROM `poll_questions` WHERE poll_id = ".$poll_id." AND lang = '".$lang."'";
  		$result = mysql_query($sql);
		
		$row = mysql_fetch_array($result);
		
		$question = $row['id'];
		echo '<div class="q-block"><div class="question">'.$row['question'].'</div>';
		
		$query = "SELECT * FROM `poll_answers` WHERE `question_id` = ".$row['id']." AND language = '".$lang."'";
		$result2 = mysql_query($query);
		$j = 1;
		while($row2 = mysql_fetch_array($result2)){
			echo '<div class="answer"><input type="radio" name="answer" value="'.$row2['id'].'" /> <p>'.$row2['answer'].'</p></div>';
			$j++;
		}
		echo '</div>';
	
	?>
    	<div class="alert">*<?php echo getTranslation("Please make a selection"); ?></div>
    
    	<input type="hidden" value="<?php echo $poll_id ?>" name="poll" />
    	<input type="hidden" value="<?php echo $lang ?>" name="lang" />
        <input type="hidden" value="<?php echo $question; ?>" name="question" />
        <input type="hidden" value="1" name="results" />
		<input type="submit" value="<?php echo getTranslation("Submit");?>" name="submit" />
    
    </form>
  
  	<div class="disclaimer"><?php echo getTranslation("This online poll only reflects the opinions of those Premier Consumer users who have voluntarily chosen to participate."); ?></div>
  	
    </div>
    
    <script type="text/javascript">
		
		
		function submitForm(){
			$("body").addClass("working");	
			var data = $("#pollForm").serialize();
			$.ajax({
				url: "/webapps/poll.php",
				type: "POST",
				data: data,
				complete: function(jqXHR, status){
					if(status == "success"){
						$("#pollContent").html(jqXHR.responseText);
					}
					$("body").removeClass("working");
				}
			});
		}
		
		
		function validatePoll(){
			var a = jQuery("input[name=answer]:checked");
			if(typeof a.val() != "undefined"){
				
				submitForm();
				
			}else{
				jQuery(".alert").show();
			}
		}
		
	</script>
  
  <?php
  }


function getTranslation($term){
 	$lang = (isset($_POST["lang"])) ? $_POST["lang"] : $_GET['lang'];
	if($lang == "spanish"){
		switch($term){
			case "Results":
				return "Resultados";
				break;
			case "This online poll only reflects the opinions of those Premier Consumer users who have voluntarily chosen to participate.":
				return "Esta encuesta refleja solamente las opiniones de aquellos usuario que han escogido participar voluntariamente.";
				break;
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
				return "Enviar";
				break;
			case "You answered":
				return "Usted respondió ";
				break;
			case "questions correctly":
				return "preguntas correctamente";
				break;
			case "Please make a selection":
				return "Por favor escoja una respuesta.";
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

