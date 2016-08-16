<?php

class pageMeta{

	function __construct($item, $type, $lang, $site){
		$this->lang = $lang;
		$this->type = $type;
		$this->item = $item;
		$this->site = $site;

		switch($type){
			case "quiz":
				$this->path = "quizzes";
				$this->getContent();
				break;
			case "poll":
				$this->path = "polls";
				$this->getContent();
				break;
			case "article":
				$this->getArticleContent();
				break;
			default:
				break;
				
		}
		
		
		
	}
	
	function getContent(){
		$xml = simplexml_load_file("content/".$this->path."/".$this->lang."/manifest.xml");
		$result = $xml->xpath($this->item);
		if($result && $result[0]->published == "1"){
			$this->exists = true;
			$this->title = $result[0]->title;
			$this->image = str_replace("../..", "", $result[0]->image);
			$this->description = $result[0]->desc;
			
			if($this->type == "poll"){
				$this->url = "/polls/".$this->site->convertToPath($this->title)."/".str_replace("poll", "", $this->item);
			}else{
				$this->url = "/quizzes/".$this->site->convertToPath($this->title)."/".str_replace("quiz", "", $this->item);
			}
		}else{
			$this->exists = false;
		}
	}
	
	function getArticleContent(){
	
		$xmlPath = "content/articles/".$this->item."/".$this->lang."/content.xml";
		if(file_exists($xmlPath)){
			$xml = simplexml_load_file($xmlPath);
			if($xml){
				$this->exists = true;
				$this->title = $xml->pageTitle;
				$imgLabel = ($xml->image == "") ? "image-alt" : "image";
				$this->image = str_replace("../..", "", $xml->$imgLabel);
				$this->description = $xml->shortDesc;
				$this->url = "/articles/".$this->item;
			}else{
				$this->exists = false;
			}
		}else{
			return false;
		}
		
	}


}


function getCat($cat, $lang){
	if($lang == "es"){
		if($cat == "article"){
			$cat = "Artículo";
		}else if($cat == "poll"){
			$cat = "Encuesta";
		}else if($cat == "quiz"){
			$cat = "Quiz";
		}
		return $cat;
	}else{
		return ucwords($cat);
	}
}


//print_r($page->content);
//
?>




<div class="homepage-hlites" style="margin-top:10px">
<div class="hp-title" style="margin-bottom:20px"><?php echo ($site->getLang() == "en") ? "Recent Articles, Quizzes and Polls" : "Art&iacute;culos Recientes, Quizes y Encuestas"; ?></div>


<?php 
	$lang = ($site->getLang() == "en") ? "en-" : "";
	for($i=1; $i<=6; $i++){
	$isFirst = ($i == 1 || $i == 4) ? true : false;
	$isLast = ($i == 3 || $i == 6) ? true : false;
	$cType = $lang."cType-".$i;
	$cItem = $lang."cItem-".$i;
	$item = new pageMeta($page->content->$cItem, $page->content->$cType, $site->getLang(), $site);
		
?>

	<?php if($isFirst){ ?>
    	
  		<ul class="large-block-grid-3 medium-block-grid-3">
    <?php } ?>
    
    		<?php if($page->content->$cItem != ""){ ?>
            <li>
                <div class="hp-hlite">
                    <div class="hp-hlite-img">
                        <a href="javascript:void(0);" class="hp-hlite-cat-link blue <?php echo $page->content->$cType;?>"><?php echo getCat($page->content->$cType, $site->getLang());?></a>
                        <a href="<?php echo $item->url; ?>"><img src="<?php echo $item->image; ?>" /></a>
                    </div>
                    <div class="hp-hlite-link">
                        <a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a>
                    </div>
                </div>       
             </li>
             <?php } ?>
              
	<?php if($isLast){ ?>
    	</ul>
    <?php } ?>

<?php } ?>

	



</div>
              
              
              
              
        