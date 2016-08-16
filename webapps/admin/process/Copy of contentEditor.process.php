<?php
	function saveContent(){
		$propertiesXML = $contentXML = $xml = $esPropertiesXML = $esContentXML = $esXml = "";
		$postData = $_POST;
		$category = (isset($_POST["config/category"])) ? $_POST["config/category"] : "";
		foreach($postData as $key => $value){
			if(strpos($key, "pageProperties") === 0){
				$key = $this->processKey($key);
				$propertiesXML .= "<".$key.">".$value."</".$key.">";
			}elseif(strpos($key, "pageContent") === 0){
				$key = $this->processKey($key);
				$contentXML .= '<'.$key.'><![CDATA['.$value.']]></'.$key.'>';
			}elseif(strpos($key, "es-pageProperties") === 0){
				$key = $this->processKey($key);
				$esPropertiesXML .= "<".$key.">".$value."</".$key.">";
			}elseif(strpos($key, "es-pageContent") === 0){
				$key = $this->processKey($key);
				$esContentXML .= '<'.$key.'><![CDATA['.$value.']]></'.$key.'>';
			}
		}
		$xml = "<content><config><category>".$category."</category></config><pageProperties>".$propertiesXML."</pageProperties><pageContent><sections>".$contentXML."</sections></pageContent></content>";
		$esXml = "<content><config><category>".$category."</category></config><pageProperties>".$esPropertiesXML."</pageProperties><pageContent><sections>".$esContentXML."</sections></pageContent></content>";
		
		
		///// Handles the moving of artricles
		if($this->depth > 2){
			$postCat = explode(" ", strtolower($category));
			$postCat = implode("-", $postCat);
			if($postCat != $this->category){
				$this->hashpath = "/".$this->section."/".$postCat."/".$this->page;
				$this->path = $this->basePath.$this->hashpath;
				$oldPath = $this->basePath."/".$this->section."/".$this->category."/".$this->page;
				$this->hasMoved = true;
			}
		}
		
		if($this->hasMoved){
			mkdir($this->path);
			mkdir($this->path."/en");
			mkdir($this->path."/es");
		}
		
		$this->enSave = (file_put_contents($this->path."/en/content.draft.xml",$xml)) ? true : false;
		$this->esSave = (file_put_contents($this->path."/es/content.draft.xml",$esXml)) ? true : false;
		$this->draft = true;
		
		if($this->hasMoved){
			$this->recursiveRemoveDirectory($oldPath);
		}
		
	}
?>

