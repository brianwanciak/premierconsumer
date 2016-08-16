<?php

class pageMeta{

	function __construct($item, $type, $lang){
		$this->lang = $lang;
		$this->type = $type;
		$this->item = $item;
		
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
		}else{
			$this->exists = false;
		}
	}
	
	function getArticleContent(){
		$xmlPath = "content/articles/".$this->item."/".$this->lang."/content.xml";
		if(file_exists($xmlPath)){
			$xml = simplexml_load_file($xmlPath);
			if($xml){
				$alt = "image-alt";
				$this->exists = true;
				$this->title = $xml->pageTitle;
				$this->image = str_replace("../..", "", ($this->lang == "en") ? $xml->image : $xml->$alt);
				$this->description = $xml->shortDesc;
			}else{
				$this->exists = false;
			}
		}else{
			return false;
		}
		
	}


}


//
?>

<div class="row related-items">

<?php if($page->content->relatedQuiz != ""){ 
		$related = new pageMeta($page->content->relatedQuiz, "quiz", $site->getLang());
		if($related->exists){
?>

 <div class="columns large-12 medium-6">
    <div class="shadow box">
      <div class="box-title">
        <h3><?php echo ($site->getLang() == "en") ? "Related Quiz" : "Quiz Relacionado"; ?></h3>
      </div>
      <!--inside_banner_title -->
      <div class="box-content"> 
      	<a href="/quizzes/<?php echo $site->convertToPath($related->title); ?>/<?php echo str_replace("quiz", "", $related->item); ?>">
      	<img src="<?php echo $related->image; ?>">
      	<p style="line-height:18px"><br /><strong><?php echo $related->title; ?></strong></p>
        </a>
      </div>
      <!--box-content -->
    </div>
    <!--box-->
  </div>

<?php }} ?>

<?php if($page->content->relatedPoll != ""){ 
		$related = new pageMeta($page->content->relatedPoll, "poll", $site->getLang());
		if($related->exists){
?>

 <div class="columns large-12 medium-6">
    <div class="shadow box">
      <div class="box-title">
        <h3><?php echo ($site->getLang() == "en") ? "Related Poll" : "Encuesta Relacionada"; ?></h3>
      </div>
      <!--inside_banner_title -->
      <div class="box-content"> 
      	<a href="/polls/<?php echo $site->convertToPath($related->title); ?>/<?php echo str_replace("poll", "", $related->item); ?>">
      	<img src="<?php echo $related->image; ?>">
      	<p style="line-height:18px"><br /><strong><?php echo $related->title; ?></strong></p>
      </div>
      <!--box-content -->
    </div>
    <!--box-->
  </div>

<?php }} ?>


<?php if($page->content->relatedArticle != ""){ 
		$related = new pageMeta($page->content->relatedArticle, "article", $site->getLang());
		if($related->exists){
?>

 <div class="columns large-12 medium-6">
    <div class="shadow box">
      <div class="box-title">
        <h3><?php echo ($site->getLang() == "en") ? "Related Article" : "Artículo Relacionado"; ?></h3>
      </div>
      <!--inside_banner_title -->
      <div class="box-content"> 
      	<a href="/articles/<?php echo $related->item; ?>">
      	<img src="<?php echo $related->image; ?>">
      	<p style="line-height:18px"><br /><strong><?php echo $related->title; ?></strong></p>
        </a>
      </div>
      <!--box-content -->
    </div>
    <!--box-->
  </div>

<?php }} ?>

</div>
