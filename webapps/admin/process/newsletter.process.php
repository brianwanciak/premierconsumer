<?php 

require_once("../classes/newsletter.class.php");

$month = $_POST["month"];
$year = $_POST["year"];
$image = $_POST["image"];
$desc = $_POST["description"];
$company_news = $_POST["company_news"];
$educational_articles = $_POST["educational_articles"];
$calc_of_the_month = $_POST["calc_of_the_month"];
$company_news_es = $_POST["company_news_es"];
$educational_articles_es = $_POST["educational_articles_es"];
$calc_of_the_month_es = $_POST["calc_of_the_month_es"];
$desc_es = $_POST["description-es"];
$article_path = $_POST["article"];
$article = Newsletter::getArticleContent($article_path, "en");
$article_es = Newsletter::getArticleContent($article_path, "es");
$path = "../../../newsletters/html/";

$article_link_en = "http://www.premierconsumer.org/articles/".$article_path;
$article_link_es = "http://www.librededeudas.com/articles/".$article_path;

$en_path = $path.$month."-".$year.".html";
$es_path = $path.$month."-".$year."-es.html";

$newsletter_link_en = "http://www.premierconsumer.org/".str_replace("../../../", "", $en_path);
$newsletter_link_es = "http://www.librededeudas.com/".str_replace("../../../", "", $es_path);

$en_html = file_get_contents("templates/newsletter.html");
$en_html = str_replace("{title}", $article['title'], $en_html);
$en_html = str_replace("{image}", str_replace("../../", "http://www.premierconsumer.org/", $image), $en_html);
$en_html = str_replace("{month}", ucfirst($month), $en_html);
$en_html = str_replace("{year}", $year, $en_html);
$en_html = str_replace("{description}", $desc, $en_html);
$en_html = str_replace("{newsletter_url_en}", $newsletter_link_en, $en_html);
$en_html = str_replace("{newsletter_url_es}", $newsletter_link_es, $en_html);
$en_html = str_replace("{article_link_en}", $article_link_en, $en_html);
$en_html = str_replace("{company_news}", $company_news, $en_html);
$en_html = str_replace("{educational_articles}", $educational_articles, $en_html);	
$en_html = str_replace("{calc_of_the_month}", $calc_of_the_month, $en_html);		


$es_html = file_get_contents("templates/es-newsletter.html");
$es_html = str_replace("{title}", $article_es['title'], $es_html);
$es_html = str_replace("{image}", str_replace("../../", "http://www.librededeudas.com/", $image), $es_html);
$es_html = str_replace("{month}", Newsletter::getSpanishMonth($month), $es_html);
$es_html = str_replace("{year}", $year, $es_html);
$es_html = str_replace("{description}", $desc_es, $es_html);
$es_html = str_replace("{article_link_es}", $article_link_es, $es_html);
$es_html = str_replace("{company_news_es}", $company_news_es, $es_html);
$es_html = str_replace("{educational_articles_es}", $educational_articles_es, $es_html);	
$es_html = str_replace("{calc_of_the_month_es}", $calc_of_the_month_es, $es_html);		
$es_html = str_replace("{newsletter_url_en}", $newsletter_link_en, $es_html);
$es_html = str_replace("{newsletter_url_es}", $newsletter_link_es, $es_html);


file_put_contents($en_path, $en_html);
file_put_contents($es_path, $es_html);

?>

<div style="margin-bottom:10px">English URL: <a href="<?php echo $newsletter_link_en; ?>" target="_blank"><?php echo $newsletter_link_en; ?></a></div>
<div style="margin-bottom:10px">Spanish URL: <a href="<?php echo $newsletter_link_es; ?>" target="_blank"><?php echo $newsletter_link_es; ?></a></div>
<div>Use this tool to format the HTML before sending: <a href="http://templates.mailchimp.com/resources/inline-css/" target="_blank">CSS Inliner Tool</a></div>
<div style="margin-top:25px">
	<strong>CODE:</strong><br />
	<textarea style="width: 100%; height: 300px"><?php echo htmlentities($es_html); ?></textarea>
</div>