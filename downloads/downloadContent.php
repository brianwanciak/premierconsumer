<?php

    // get the HTML
	$lang = ( strpos($_SERVER["HTTP_HOST"], "premierconsumer")) ? "en" : "es";
    $path = str_replace("/downloads", "", $_SERVER['REQUEST_URI']);
	function printArticleContent($path, $lang){
		$xmlPath = "../content".$path."/".$lang."/content.xml";
		if(file_exists($xmlPath)){
			$xml = simplexml_load_file($xmlPath);
			if($xml){
				$html = "<h2>".$xml->pageTitle."</h2>";
				$html .= $xml->contentPage;
				return $html;
			}else{
				return false;
			}
		}else{
			return false;
		}
		
	}
	
	$content = printArticleContent($path, $lang);

 ?>
 
 <?php if($content){ ?>
 
<style>
	h2{
		font-size: 18px
		}
	p, li, ol{
		font-size: 14px;
		line-height: 18px;
		}
	li, ol{
		padding-bottom: 10px
		}
</style>
 
<div class="article-body">

<img src="http://www.premierconsumer.org/assets/images/logo.png" style="width:300px; margin-bottom: 15px; margin-left:-5px" />

<?php echo $content; ?>

</div>

<page_footer> 
      <strong>Copyright Premier Consumer Credit Counseling</strong><br />
		Miami, Florida USA. All Rights Reserved 
</page_footer> 
 
 
 <?php } ?>
 