<?php
	require_once("../includes/db.php");
	$json = file_get_contents('php://input');
	$obj = json_decode($json);
	
	new PollProcess($obj);
	
	
	
	
class PollProcess{

	function __construct($obj){
		mysql_connect(DB_HOST,DB_USER,DB_PASS);
		$this->obj = $obj;
		
		if($obj[0]->action == "delete"){
			$this->deleteItem();
		}elseif($obj[0]->action == "deletePoll"){
			$this->deletePoll($obj[0]->id);
			return false;
		}elseif($obj[0]->action == "publish"){
			$this->publishPoll($obj[0]->id);
			return false;
		}elseif($obj[0]->action == "unpublish"){
			$this->unPublishPoll($obj[0]->id);
			return false;
		}else{
			$this->processData();
		}
		echo $this->pollId;
		//print_r($obj);
	}

	function deleteItem(){
		$query = "DELETE FROM `".$this->obj[0]->table."` WHERE `id`=".$this->obj[0]->id;
		//echo $query;
		dbQuery($query, 3);
	}

	function processData(){
		$obj = $this->obj;
		$this->pollId = ($obj[0]->id > 0) ? $this->getExistingPoll($obj[0]) : $this->getNewPoll($obj[0]);
		for($i=1; $i<(count($obj)-1); $i++){
			$this->processQuestion($obj[$i]);
		}
	}
	
	function processQuestion($question){
		$questionId = $question->id;
		if($questionId > 1000000){
			$query = "INSERT INTO `poll_questions` (`question`, `lang`, `poll_id`) VALUES ('".mysql_real_escape_string($question->question)."', '".$question->lang."', ".$this->pollId.")";
			$questionId = dbQuery($query, 4);
		}else{
			$query = "UPDATE `poll_questions` SET `question` = '".mysql_real_escape_string($question->question)."' WHERE `id`=".$question->id;
			dbQuery($query, 3);
		}	
		//echo $query;
		//print_r($question->answers);
		for($i=0; $i<count($question->answers); $i++){
			$this->processAnswer($question->answers[$i], $questionId);
		}
	}
	
	function processAnswer($answer, $questionId){
		$answerId = $answer->id;
		if($answerId == 0){
			$query = "INSERT INTO `poll_answers` (`answer`, `question_id`, `language`) VALUES ('".mysql_real_escape_string($answer->answer)."', ". $questionId.", '".$answer->language."')";
			$answerId = dbQuery($query, 4);
		}else{
			$query = "UPDATE `poll_answers` SET `answer` = '".mysql_real_escape_string($answer->answer)."' WHERE `id`=".$answer->id;
			dbQuery($query, 3);
		}	

		//echo $query;
	}
	
	function getExistingPoll($obj){
		$query = "UPDATE `poll` SET `title` = '".mysql_real_escape_string($obj->enTitle)."', `title_es` = '".mysql_real_escape_string($obj->esTitle)."', `description` = '".mysql_real_escape_string($obj->enDesc)."', `description_es` = '".mysql_real_escape_string($obj->esDesc)."', `related_quiz` = '".mysql_real_escape_string($obj->relatedQuiz)."', `related_article` = '".mysql_real_escape_string($obj->relatedArticle)."', `image` = '".mysql_real_escape_string($obj->image)."', `image_es` = '".mysql_real_escape_string($obj->esImage)."', `es_sameImage` = '".mysql_real_escape_string($obj->esSameImage)."', `published` = 0 WHERE `id`=".$obj->id;
		$this->generateManifest();
		dbQuery($query, 3);
		return $obj->id;
	}
	
	function getNewPoll($obj){
		$query = "INSERT INTO `poll` (`title`, `title_es`, `description`, `description_es`, `created`, `published`, `image`, `image_es`, `es_sameImage`, `related_quiz`, `related_article`) VALUES ('".mysql_real_escape_string($obj->enTitle)."', '".mysql_real_escape_string($obj->esTitle)."', '".mysql_real_escape_string($obj->enDesc)."', `description_es` = '".mysql_real_escape_string($obj->esDesc)."', NOW(), 0, '".mysql_real_escape_string($obj->image)."','".mysql_real_escape_string($obj->esImage)."', '".mysql_real_escape_string($obj->esSameImage)."', '".mysql_real_escape_string($obj->relatedQuiz)."', '".mysql_real_escape_string($obj->relatedArticle)."')";
		//echo $query;
		return dbQuery($query, 4);
	}
	
	
	function deletePoll($id){
		$questionIds = dbQuery("SELECT `id` FROM `poll_questions` WHERE `poll_id` = ".$id, 1);
		if($questionIds){
			foreach($questionIds as $qid){
				$qids[] = $qid["id"];
			}
			dbQuery("DELETE FROM `poll_answers` WHERE `question_id` IN (".implode(",", $qids).")", 3);
		}
		dbQuery("DELETE FROM `poll_questions` WHERE `poll_id` = ".$id, 3);
		dbQuery("DELETE FROM `poll` WHERE `id` = ".$id, 3);
	}
	
	function publishPoll($id){
		dbQuery("UPDATE `poll` SET `published` = 1 WHERE `id` = ".$id, 3);
		$this->generateManifest();
	}
	
	function unpublishPoll($id){
		dbQuery("UPDATE `poll` SET `published` = '' WHERE `id` = ".$id, 3);
		$this->generateManifest();
	}
	
	function generateManifest(){
		
		$rows = dbQuery("SELECT *, DATE_FORMAT(`created`, '%m/%d/%Y %l:%i %p') as created_date FROM `poll` ORDER BY `created` DESC", 1);
		$xml = new SimpleXMLElement("<polls/>");
		$esXml = new SimpleXMLElement("<polls/>");
		for($i=0; $i<count($rows); $i++){
			$node = "poll".$rows[$i]["id"];
			$xml->addChild($node);
			$xml->$node->addChild("title", $rows[$i]["title"]);
			$xml->$node->addChild("metaTitle", $rows[$i]["title"]);
			$xml->$node->addChild("pageTitle", $rows[$i]["title"]);
			$xml->$node->addChild("template", "poll");
			$xml->$node->addChild("image", $rows[$i]["image"]);
			$xml->$node->addChild("desc", $rows[$i]["description"]);
			$xml->$node->addChild("id", $rows[$i]["id"]);
			$xml->$node->addChild("published", $rows[$i]["published"]);
			$xml->$node->addChild("relatedArticle", $rows[$i]["related_article"]);
			$xml->$node->addChild("relatedQuiz", $rows[$i]["related_quiz"]);
			$xml->$node->addChild("created", $rows[$i]["created"]);
			
			$esXml->addChild($node);
			$esXml->$node->addChild("title", $rows[$i]["title_es"]);
			$esXml->$node->addChild("metaTitle", $rows[$i]["title_es"]);
			$esXml->$node->addChild("pageTitle", $rows[$i]["title_es"]);
			$esXml->$node->addChild("template", "poll");
			$esXml->$node->addChild("image", ($rows[$i]["es_sameImage"]) ? $rows[$i]["image"] : $rows[$i]["image_es"]);
			$esXml->$node->addChild("desc", $rows[$i]["description_es"]);
			$esXml->$node->addChild("id", $rows[$i]["id"]);
			$esXml->$node->addChild("published", $rows[$i]["published"]);
			$esXml->$node->addChild("relatedArticle", $rows[$i]["related_article"]);
			$esXml->$node->addChild("relatedQuiz", $rows[$i]["related_quiz"]);
			$esXml->$node->addChild("created", $rows[$i]["created"]);
		}
		
		$base = '../../../content/polls';
		$xml->asXML($base."/en/manifest.xml");
		$esXml->asXML($base."/es/manifest.xml");
	}
	

}



function dbQuery($query, $type = '', $admin = false){
	
	$db_host		= DB_HOST;
	$db_user		= DB_USER;
	$db_pass		= DB_PASS;
	$db_database	= DB_NAME;
		
	$link = mysql_connect($db_host,$db_user,$db_pass) or die('Unable to establish a DB connection');
	mysql_set_charset("utf8",$link);
	mysql_select_db($db_database,$link);
	

	switch($type){
		case 1: //return all rows as array
			$result = mysql_query($query);
			$i = 0;
			if(mysql_num_rows($result) == 0){return false;}
			while($row = mysql_fetch_assoc($result)){
				foreach($row as $key => $value){
					$return[$i][$key] = $value;
				}
				$i++;
			}
		break;
		
		case 2: //return ONE result
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 0){return false;}
			$row = mysql_fetch_assoc($result);
			foreach($row as $key => $value){
					$return[$key] = $value;
				}
		break;
		case 3: //execute a query with no response
			$result = mysql_query($query);
			return true;
		break;
		case 4: //insert new row and get id inserted
			$result = mysql_query($query);
			return mysql_insert_id();
		break;
		default:
			mysql_query($query);
			$return = mysql_errno();
		break;
	
	}
	
	mysql_close($link);
	
	return $return;

}
?>