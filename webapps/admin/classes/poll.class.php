<?php

class Polls{

	function __construct(){
		$this->polls = $this->getPolls();
	}
	
	function getPolls(){
		$rows = dbQuery("SELECT *, DATE_FORMAT(`created`, '%m/%d/%Y %l:%i %p') as created_date FROM poll ORDER BY `created` DESC", 1);
		
		if(empty($rows)){
			return false;
		}
		
		for($i=0; $i<count($rows); $i++){
			$rows[$i]['edit_link'] = "poll.php?task=edit&uid=".$rows[$i]['id'];
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




class Poll{

	function __construct($uid){
		$this->poll_id = $uid;
		$this->poll = $this->getPoll($uid);
		
		//print_r($this->quiz);
	}
	
	function getQuestions($row, $lang){
		$questions = dbQuery("SELECT * FROM `poll_questions` WHERE `poll_id`=".$this->poll_id." AND `lang`='".$lang."'", 1);

		if($questions){
			for($i=0; $i<count($questions); $i++){
				$questions[$i]["answers"] = $this->getAnswers($questions[$i]["id"]);
			}
		}
		
		return $questions;
		
	}
	
	function getAnswers($question_id){
		$answers = dbQuery("SELECT * FROM `poll_answers` WHERE `question_id`=".$question_id, 1);
		return $answers;
	}
	
	function getPoll(){
		
		if($this->poll_id){
		
			$row = dbQuery("SELECT *, DATE_FORMAT(`created`, '%m/%d/%Y %l:%i %p') as created_date FROM poll WHERE id=".$this->poll_id, 2);
		
			if(empty($row)){
				return false;
			}
			
			$row['edit_link'] = "poll.php?task=edit&uid=".$row['id'];
			$row['en'] = $this->getQuestions($row, 'english');
			$row['es'] = $this->getQuestions($row, 'spanish');
		
		}else{
			$row = array("id" => 0, "title" => "New Poll", "title_es" => "", "description" => "", "description_es" => "");
		}

		return $row;
	}

}


?>