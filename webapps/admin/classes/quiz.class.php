<?php

class Quizzes{

	function __construct(){
		$this->quizzes = $this->getQuizzes();
	}
	
	function getQuizzes(){
		$rows = dbQuery("SELECT *, DATE_FORMAT(`created`, '%m/%d/%Y %l:%i %p') as created_date FROM quizes ORDER BY `created` DESC", 1);
		
		if(empty($rows)){
			return false;
		}
		
		for($i=0; $i<count($rows); $i++){
			$rows[$i]['edit_link'] = "quiz.php?task=edit&uid=".$rows[$i]['id'];
			$rows[$i]['publishedDom'] = $this->getPublished($rows[$i]['published']);
		}

		return $rows;
	}
	
	function getPublished($val){
		$label = ($val == 1) ? "Active" : "Inactive";
		$className = ($val == 1) ? "btn-primary" : "btn-warning";
		return '<button class="btn disabled '.$className.'" type="button">'.$label.'</button>';
	}

}




class Quiz{

	function __construct($uid){
		$this->quiz_id = $uid;
		$this->quiz = $this->getQuiz($uid);
		
		//print_r($this->quiz);
	}
	
	function getQuestions($row, $lang){
		$questions = dbQuery("SELECT * FROM `questions` WHERE `quiz_id`=".$this->quiz_id." AND `lang`='".$lang."'", 1);

		if($questions){
			for($i=0; $i<count($questions); $i++){
				$questions[$i]["answers"] = $this->getAnswers($questions[$i]["id"]);
			}
		}
		
		return $questions;
		
	}
	
	function getAnswers($question_id){
		$answers = dbQuery("SELECT * FROM `answers` WHERE `question_id`=".$question_id, 1);
		return $answers;
	}
	
	function getQuiz(){
		
		if($this->quiz_id){
		
			$row = dbQuery("SELECT *, DATE_FORMAT(`created`, '%m/%d/%Y %l:%i %p') as created_date FROM quizes WHERE id=".$this->quiz_id, 2);
		
			if(empty($row)){
				return false;
			}
			
			$row['edit_link'] = "quiz.php?task=edit&uid=".$row['id'];
			$row['en'] = $this->getQuestions($row, 'english');
			$row['es'] = $this->getQuestions($row, 'spanish');
		
		}else{
			$row = array("id" => 0, "title" => "New Quiz", "en" => array("title" => ""));
		}

		return $row;
	}

}


?>