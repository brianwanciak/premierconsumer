<?php

class Newsletters{

	

}




class Newsletter{
	
	function __construct($uid){
		$this->newsletter_id = $uid;
		$this->data = $this->getNewsletter();
		$this->path = "../../../newsletters/html/";
	}
	
	function getNewsletter(){

		if($this->newsletter_id){
		
		}else{
			return Array("id" => "", "title" => "New Newsletter", "month" => strtolower(date("F")), "year" => date("Y"), "published" => 0, "image" => "", "description_es" => "", "images_es" => "", "es-sameImage" => 0);
		}
	}
	
	function getMonths(){
		return Array("January" => "january", 
						"February" => "february", 
						"March" => "march", 
						"April" => "april", 
						"May" => "may", 
						"June" => "june", 
						"July" => "july", 
						"August" => "august", 
						"September" => "september", 
						"October" => "october", 
						"November" => "november", 
						"December" => "december");

	}
	
	function getSpanishMonth($month){
		$months = array("january" => "Enero", 
						"february" => "Febrero", 
						"march" => "Marzo", 
						"april" => "Abril", 
						"may" => "Mayo", 
						"june" => "Junio", 
						"july" => "Julio", 
						"august" => "Agosto", 
						"september" => "Septiembre", 
						"october" => "Octubre", 
						"november" => "Noviembre", 
						"december" => "Diciembre");
						
		return $months[$month];

	}
	
	function getArticleContent($article, $lang){
		if($article != ""){
			$xmlPath = "../../../content/articles/".$article."/".$lang."/content.xml";
			if(file_exists($xmlPath)){
				$xml = simplexml_load_file($xmlPath);
				$data = array();
				if($xml){
					$data["exists"] = true;
					$data["title"] = $xml->pageTitle;
					$data["image"] = str_replace("../..", "", $xml->image);
					$data["description"] = $xml->shortDesc;
				}else{
					$data["exists"] = false;
				}
				return $data;
			}else{
				return false;
			}
		
		}else{
			return false;
		}
		
	}


}


?>