<!DOCTYPE html>
<!--[if IE 8]> 				 <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" > <!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $page->getTitle(); ?> | <?php echo $site->getCompanyName(); ?> - <?php echo $site->getLabel("site-tagline"); ?></title>
<meta name="keywords" content="<?php echo $page->content->metaKeywords; ?>" />
<meta name="description" http-equiv="description" content="<?php echo $page->content->metaDescription; ?>">
<meta name="google-site-verification" content="c8fYhVTodLhXBD13ftPGC3hwrVwe5N1UG4kHpvI4oEY" />
<meta name="google-site-verification" content="fmv_dsKm_qeUUkjTlLqwVTJyO40I5f8OKqMDchSINGE" />
<link rel="icon" type="image/ico" href="/favicon.ico">
<link rel="stylesheet" href="/foundation/css/normalize.css?v=10">
<link rel="stylesheet" href="/foundation/css/foundation.css?v=10">
<link href="/css/layout.css?v=18" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/fancybox/jquery.fancybox-1.3.1.css" media="screen" />

<?php if($page->getNode("template") == "calculator"){ ?>
<link type="text/css" rel="StyleSheet" href="/css/calculators/KJE.css" />
<link type="text/css" rel="StyleSheet" href="/css/calculators/KJESiteSpecific.css" />
<?php } ?>
<!--[if IE]>
<style>
div.fix{
height:330px}
</style>
<![EndIf]-->

<!--[if IE 6]> <style> div#wrapper{width:915px}</style> <![endif]-->

<script src="/foundation/js/vendor/jquery.js"></script>
<script language="JavaScript" src="/js/country.js"></script>
  
<script src="/js/jquery.autotab.js" type="text/javascript"></script>
<script src="/js/scripts.js?v=2" type="text/javascript" charset="iso-8859-1"></script>


</head>

<body class="<?php echo $site->getLang(); ?>">
<?php if($site->isLive){ ?>

	<?php if($site->lang == "en"){ ?>
        <!-- Google Tag Manager -->
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-W2BH67"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-W2BH67');</script>
        <!-- End Google Tag Manager -->
    <?php }else if($site->lang == "es"){ ?>
        <!-- Google Tag Manager -->
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-KC9BHW"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-KC9BHW');</script>
        <!-- End Google Tag Manager -->
    <?php } ?>
    
<?php } ?>

<div id="top_bar">
	<div class="hide-for-medium-up top-phone"><a href="tel:<?php echo $site->getConfig("tollfree"); ?>"><?php echo $site->getLabel("tap-call"); ?></a></div>
</div>

<div id="container" class="clearfix">

	<div class="row">
    <div class="columns large-12">

	<div id="wrapper" class="clearfix">
    
		<div id="header">
        	
            <div class="row">
            	<div class="columns large-5 medium-6">
                	<a href="/"><img src="/assets/images/<?php echo $site->getLang(); ?>/logo.jpg?v=4" id="logo" /></a>                </div>
          <div class="columns large-7 medium-6">
                	<div id="slogan">
                    	<img src="/assets/images/<?php echo $site->getLang(); ?>/slogan.png" class="slogan-medium" />
                        <img src="/assets/images/<?php echo $site->getLang(); ?>/slogan.png" class="slogan-large">
                        <div class="phone-number phone-styled hide-for-small"><span><?php echo $site->getConfig("tollfree"); ?></span></div>
                    </div>  
                	<div id="language_links">
                        <a href="<?php echo $page->urlEN;?>" class="english btn orange"><span>English</span></a>
                        <a href="<?php echo $page->urlES;?>" class="spanish btn orange"><span>Espa&ntilde;ol</span></a>                    </div><!--language_links -->    
                </div>
          </div>
        	
           
        	
           
            
        
        </div><!--header -->
        
        <div>
        <!--<div div class="contain-to-grid sticky">-->
        <nav class="top-bar shadow ctx-sticky" style="">
          <ul class="title-area">
            <!-- Title Area -->
            <li class="name">            </li>
            <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
            <li class="toggle-topbar menu-icon"><a href="javascript:void(0);"><span>Menu</span></a></li>
          </ul>

          
        <section class="top-bar-section">
            <ul class="left">
              <li class=""><a href="/"><?php echo $site->getLabel("nav-home"); ?></a></li>
              <li class="divider"></li>
              <li class="has-dropdown not-click"><a href="/about-us"><?php echo $site->getLabel("nav-about-us"); ?></a>
                <ul class="dropdown">
                  <li class=""><a href="/about-us"><?php echo $site->getLabel("nav-about-us"); ?></a></li>
                  <li class=""><a href="/message-from-directors"><?php echo $site->getLabel("nav-message-directors"); ?></a></li>
                  <li class=""><a href="/employment-opportunities"><?php echo $site->getLabel("nav-employment"); ?></a></li>
                </ul>
              </li>
              <li class="divider"></li>
              <li class="has-dropdown not-click"><a href="/how-we-can-help-you"><?php echo $site->getLabel("nav-how-we-can-help"); ?></a>
                <ul class="dropdown">
                  <li class=""><a href="/how-we-can-help-you"><?php echo $site->getLabel("nav-how-we-can-help"); ?></a></li>
                  <li class=""><a href="/debt-management-program"><?php echo $site->getLabel("nav-debt-management"); ?></a></li>
                  <li class=""><a href="/seminars"><?php echo $site->getLabel("nav-free-seminars"); ?></a></li>
                  <li class=""><a href="/free-analysis"><?php echo $site->getLabel("nav-free-analysis"); ?></a></li>
                </ul>
              </li>
              <li class="divider"></li>
              <li class="has-dropdown not-click"><a href="/articles"><?php echo $site->getLabel("nav-learning-center"); ?></a>
                <ul class="dropdown">
                  <li><a href="/articles"><?php echo $site->getLabel("nav-articles"); ?></a></li>
                  <li><a href="/calculators/"><?php echo $site->getLabel("nav-calculators"); ?></a></li>
                  <li><a href="/quizzes"><?php echo $site->getLabel("nav-quizzes"); ?></a></li>
                  <li><a href="/polls"><?php echo $site->getLabel("nav-polls"); ?></a></li>
                  <li><a href="/videos"><?php echo $site->getLabel("nav-videos"); ?></a></li>
                  <li><a href="/outside-resources"><?php echo $site->getLabel("nav-resources"); ?></a></li>
                  <li><a href="/debt-management-program"><?php echo $site->getLabel("nav-debt-management"); ?></a></li>
                  <li><a href="/frequently-asked-questions"><?php echo $site->getLabel("nav-faq"); ?></a></li>
                </ul>
              </li>
              <li class="divider"></li>
              <li><a href="/free-analysis"><?php echo $site->getLabel("nav-free-analysis"); ?></a></li>
              <li class="divider"></li>
              <li><a href="/contact-us"><?php echo $site->getLabel("nav-contact-us"); ?></a></li>
              <li class="divider"></li>
            </ul>
            <!-- Right Nav Section -->
            <ul class="right">
              <!--<li class="divider hide-for-small"></li>-->
              <li class="has-dropdown not-click"><a href="http://secure.pdsservers.com/PremierConsumer/clients/"><?php echo $site->getLabel("nav-clients"); ?></a>
                <ul class="dropdown">
                  <li><a href="http://secure.pdsservers.com/PremierConsumer/clients/"><?php echo $site->getLabel("nav-login"); ?></a></li>
                  <li><a href="https://www.eservicepayments.com/cgi-bin/specialwebapp.vps?appid=programtorun=PremierConsumer.LoginPage__languagepk=1"><?php echo $site->getLabel("nav-payment"); ?></a></li>
                  <li><a href="/satisfaction-survey"><?php echo $site->getLabel("nav-survey"); ?></a></li> 
                </ul>
              </li>
            </ul>
          </section>
</nav>
        
        </div>
			

        <!--<div style="padding: 0px 5px"><div style="margin-bottom:10px; background-color:#FFCCFF; border:1px solid #FF99FF; padding:10px"><strong>Important!</strong> Our phone systems are currently down at this time. Please note that we are working hard to restore service and they should be back up within a few hours. Should you need assistance, please use our contact form. Thank you for your understanding.</div></div> -->
        
        <div class="row body-content">