<?php

error_reporting(0);
@ini_set('display_errors', 0);


$site = new Site;
$visitor = new Visitor;
$page = new Page($site);
class Site{
	function __construct(){
		$this->checkRedirect();
		$this->domain = str_replace("www.", "", $_SERVER["HTTP_HOST"]);
		$dev = (strpos($this->domain, "2prevue.com")) ? true : false;
		$this->isLive = $dev ? false : true;
		$this->premierUrl = $dev ? "premierconsumer.2prevue.com" : "premierconsumer.org";
		$this->libreUrl = $dev ? "librededeudas.2prevue.com" : "librededeudas.com";
		$this->lang = ( $this->domain == $this->premierUrl ) ? "en" : "es";
		$this->download_path = "/downloads/";
		$this->form_url = ($this->lang == "en") ? "/webapps/forms/" : "/webapps/forms/es-";
		$this->config = $this->getConfiguration();
		$this->labels = $this->getTranslations();
		$this->chatEnabled = true;
	}

	function checkRedirect(){
		
			$host = $_SERVER['HTTP_HOST'];
			$path = $_SERVER['REQUEST_URI'];
			$updated = false;
			if(strpos($host, "www.") !== false){
				
			}else{
				$redirect = "https://www.".$host.$path;
				$updated = true;
			}

			if(!$this->is_https() && !$updated ){
				$redirect = "https://".$host.$path;
				$updated = true;
			}

			if($updated){

				header('HTTP/1.1 301 Moved Permanently');
				header('Location: ' . $redirect);

			}

	}

	function is_https(){ 
	    if ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) 
	        return true; 
	    else 
	        return false; 
	} 

	function getConfiguration(){
		$xml = file_get_contents("content/global/".$this->lang."/config.xml");
		$this->content = simplexml_load_string($xml);
		return $this->content;
	}
	
	function getTranslations(){
		$xml = file_get_contents("content/global/translations.xml");
		$this->content = simplexml_load_string($xml);
		return $this->content;
	}
	
	function getConfig($key){
		return $this->config->$key;
	}
	
	function getLabel($key){
		$lang = $this->lang;
		return $this->labels->$key->$lang;
	}
	
	function getCompanyName(){
		return $this->config->company;
	}
	
	function getFormUrl(){
		return $this->form_url;
	}
	
	function getFormDisclaimer(){
		return $this->config->formDisclaimer;
	}
	
	function getDownLoadPath(){
		return $this->download_path;
	}
	
	public function getDomain()
  	{
      return $this->domain;
  	}
	
	public function getLang()
  	{
      return $this->lang;
  	}
	
	public function processedImage($path){
		if($path != ""){
			return str_replace("../..", "", $path);
		}else{
			return "/assets/images/default.jpg";
		}
	}
	
	public function convertToPath($str){
		$tmp = explode(" ", strtolower(trim($str)));
		$tmp = preg_replace("/[^a-z0-9]/", '', $tmp);
		$tmp = implode("-", $tmp);
		return $tmp;
	}

	
}


class Page{

	function __construct($site){
		$this->site = $site;
		$page = basename($_SERVER['PHP_SELF']);
		$this->path = $this->getPage();//substr($page, 0, strlen($page)-4);	
		$this->parts = explode("/", $this->path);
		$this->checkVanity($this->parts[1]);
		$this->content = $this->getContent($this->parts);
		$this->urlEN = str_replace("index", "", "https://www.".$site->premierUrl.$this->path);
		$this->urlES = str_replace("index", "", "https://www.".$site->libreUrl.$this->path);
		
	}

	function checkVanity($key){
		$vanity = array(
			"console" => "/webapps/admin/index.php",
			"livesupport" => "/livesupport/index.php",
			"ejemplodepresupuesto" => "/downloads/ejemplo-de-presupuesto.pdf",
			"files" => "https://premierconsumer.sharefile.com/r/r1e1876e9f894ac3a",
			"bienvenida" => "/downloads/pccc_welcome_spanish.pdf",
			"pollretiro" => "/polls",
			"gratis" => "/free-analysis",
			"cease" => "/downloads/cease_and_desist_notification.doc",
			"citrix" => "/downloads/CitrixReceiver3.4.exe",
			"free" => "/free-analysis",
			"paybytext" => "https://www.eservicepayments.com/cgi-bin/Vanco_ver3.vps?appver3=tYgT1GfNxRUldiimjHMvOYXMXFwduDtmuyOuwNPunYNskSODEa-Up5lt373GHnco2evTpo0mld6BrVzd2nG0p65zANdpyPSt43btEwV42-c=&ver=3",
			"satisfaction" => "/satisfaction-survey",
			"satisfaccion" => "/satisfaction-survey",
			"upload" => "https://premierconsumer.sharefile.com/r/r1e1876e9f894ac3a",
			"archivos" => "https://premierconsumer.sharefile.com/r/r1e1876e9f894ac3a"
		);

		if($vanity[$key]){
			header("Location: ".$vanity[$key]);
			exit();
		}
		
	}
	
	function getPage(){
		$path = $_SERVER['REQUEST_URI'];
		if(strpos($path, "?")){
			$parts = explode("?", $path);
			$path = $parts[0];
		}
		return ($path == "/") ? "/index" : $path;
	}
	
	function getPath(){
		return $this->path;
	}
	
	function getContent($parts){
		if(strpos($this->path, "polls/")){
			$xml = simplexml_load_file("content/polls/".$this->site->getLang()."/manifest.xml");
			$node = "poll".$parts[3];
			$this->content = $xml->$node->children();
		}else if(strpos($this->path, "quizzes/")){
			$xml = simplexml_load_file("content/quizzes/".$this->site->getLang()."/manifest.xml");
			$node = "quiz".$parts[3];
			$this->content = $xml->$node->children();
		}else{
			$file = "content".$this->path."/".$this->site->getLang()."/content.xml";
			if(!file_exists($file)){
				if($this->site->getLang() == "es"){
					header("Location: /404-es.html");
				}else{
					header("Location: /404.html");
				}
			}
			$this->content = simplexml_load_file($file);
		}
		
		return $this->content;
	}
	
	function getTitle(){
		return $this->content->metaTitle;
	}
	function getPageTitle(){
		return $this->content->pageTitle;
	}
	function getPageDescription(){
		return $this->content->metaDescription;
	}
	function getPageKeywords(){
		return $this->content->metaKeywords;
	}
	function getNode($nodeName){
		return $this->content->$nodeName;
	}
	
}


class Articles{
	
}

class Visitor{
	function __construct(){
		$enabled  = false;
		if($enabled){
			$this->ip = $_SERVER['REMOTE_ADDR'];
			$api_key = "daac05061b6c5ed24973b5eb6a2d91329877203423ff3396a96d4a0fde9ed566";
			$key_check = "http://api.ipinfodb.com/v3/ip-country/?key=".$api_key."&ip=".$this->ip;
			$result = file_get_contents($key_check);
			$this->isUSA = $this->isUSA($result);
		}else{
			$this->isUSA = true;
		}
		
		//echo $xml->geoplugin_countryCode;		
	}
	
	function isUSA($result){
		return (strpos($result, "United States") !== false) ? true : false; 
	}
}

?>