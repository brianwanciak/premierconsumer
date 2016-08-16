<?php 
	$domain = str_replace("www.", "", $_SERVER["HTTP_HOST"]);
	$lang = ( $domain == "librededeudas.com" ) ? "es" : "en";
	$english = $lang == "en" ? true : false;
?>
<div class="home-page">
    	

            <div class="row">
            	<div class="columns large-8">

                <?php if($page->content->alertType != ""){ ?>
                    <div class="hp-alert rounded <?php echo $page->content->alertType; ?>">
                        <?php echo $page->content->contentPage; ?>
                    </div>
                <?php } ?>

                <div class="hero-banner rounded" style="overflow: hidden;">
                        <div class="hero-img">
                            <a href="/free-analysis"><img src="assets/images/<?php echo $lang; ?>/hero-banner.jpg" /></a>
                        </div>
                        <div class="hero-text">
                        <span>
                            <?php echo ($english) ? "Become Debt Free, Stay Debt Free, And Become Financially Educated." : "Lib&eacute;rese De Sus Deudas, Mant&eacute;ngase Libre De Deudas y Educado Financieramente."; ?>
                        </span>
                        </div>
                  </div>
                  
                  <div class="hp-lower-hlite-wrap">
                  		
                        <div class="hp-lower-hlite border clearfix">
                        	<a class="hp-lower-hlite-img" href="articles"><img src="/assets/images/homepage/education-center.jpg" class="rounded" /></a>
                            <div class="hp-lower-hlite-text">
                            	<h3><?php echo ($english) ? "Credit Education Zone" : "Centro de Aprendizaje" ; ?></h3>
                                <p><?php echo ($english) ? "As a community based nationwide reach Non-Profit organization, we are committed to getting you back on track." : htmlentities("Somos una organización sin fines de lucro de carácter comunitario y con alcance nacional que puede ayudarle a ponerse al día en su vida financiera.") ; ?></p>
                                
                            </div>
                        </div>
                        <div class="hp-lower-hlite border clearfix">
                        	<a class="hp-lower-hlite-img" href="/debt-management-program"><img src="/assets/images/homepage/debt-management.jpg" class="rounded" /></a>
                            <div class="hp-lower-hlite-text">
                            	<h3><?php echo ($english) ? "Debt Management Program" : htmlentities("Programa de Consolidación de Deudas") ; ?></h3>
                                <p><?php echo ($english) ? "Our certified credit counselors will <strong>completely review your financial situation</strong> including your debts, obligations, income, and needs." : htmlentities("Nuestros consejeros de crédito certificados podrán aconsejarle sobre cómo ser parte de nuestro programa de consolidación de deudas.") ; ?></p>
                               
                            </div>
                        </div>
                        
                        <div class="hp-lower-hlite border clearfix">
                        	<a class="hp-lower-hlite-img" href="/videos"><img src="/assets/images/homepage/univision.jpg" /></a>
                            <div class="hp-lower-hlite-text">
                            	<h3><?php echo ($english) ? "Univision Seminars" : htmlentities("Seminarios de Univisión") ; ?></h3>
                                <p><?php echo ($english) ? "Premier Consumer Credit Counseling, Inc. in partnership with Univision and Univision Tarjeta is proud to..." : htmlentities("Premier Consumer Credit Counseling, Inc. en asociación con Univisión y Univisión Tarjeta está orgulloso..."); ?></p>
                               
                            </div>
                        </div>
                        <div class="hp-lower-hlite border clearfix">
                        	<a class="hp-lower-hlite-img" href="/videos/miscellaneous/radio-caracol-fair-2013"><img src="/assets/images/homepage/radio-caracol.jpg" class="rounded" /></a>
                            <div class="hp-lower-hlite-text">
                            	<h3><?php echo ($english) ? "Radio Caracol Fair 2013" : htmlentities("Feria de Caracol Radio") ; ?></h3>
                                <p><?php echo ($english) ? "Premier Consumer Credit Counseling presents the Radio Caracol Fair 2013." : htmlentities("Premier Consumer Credit Counseling presente en las ferias de Caracol!") ; ?></p>
                                
                            </div>
                        </div>
                  </div>
                </div>
                <div class="columns large-4">
                	<div class="shadow box main-form" style="background-color:#E3E9F5">
                    <div class="box-title">
                        <h3><?php echo ($english) ? "Become DEBT FREE!" : "Podemos ayudarle" ; ?></h3>
                    </div><!--inside_banner_title -->
                	<div class="box-content">
                	<div class="b-hlite" style="margin-bottom:15px; background-color:#fff"><p style="font-size:0.7em; padding-bottom:8px"><?php echo ($english) ? "Find out how much you can save in minutes. Get a free savings estimate right now." : htmlentities("Descubra todo lo que puede ahorrarse en sólo minutos. Llene este formulario rápidamente y comience el camino a su libertad financiera."); ?></p><p style="font-size:0.7em"> <?php echo ($english) ? "No commitment. Fill out the form to find out more." :  htmlentities("No tiene ningun costo, obligación ni compromiso.") ; ?></p></div>
                     <form action="<?php echo $site->getFormUrl(); ?>main-contact.php" style="margin-bottom:0" method="post" class="custom" name="form1">
                    	
                                	<label><?php echo ($english) ? "First Name*" : "Primer nombre*" ; ?></label>
                                    <input type="text" name="FirstName" id="FirstName"/>
                                    <label><?php echo ($english) ? "Last Name*" : "Apellido*" ; ?></label>
                                    <input type="text" name="LastName" id="LastName"/>
                                    <label><?php echo ($english) ? "Email*" : "Email*" ; ?></label>
                                    <input type="text" name="Email" id="Email"/>
                                    
                                
                                <label><?php echo ($english) ? "Home Phone" : htmlentities("Teléfono de la Casa"); ?></label>
                                <div class="phone-verif-wrap">
                                    <span class="valid-icon"></span>
                                    <input type="tel" id="Home1" name="Home1" class="phone-verif" maxlength="14"  />
                                </div>
                                    
                                <label><?php echo ($english) ? "Work Phone" : htmlentities("Teléfono del trabajo") ; ?></label>
                                 
                                <div class="phone-verif-wrap">
                                    <span class="valid-icon"></span>
                                    <input type="tel" id="Work1" name="Work1" class="phone-verif" maxlength="14"  />
                                </div>   
                             
                                   
                                <label><?php echo ($english) ? "Cell Phone" : htmlentities("Teléfono celular") ; ?></label>
                                
                                   <div class="phone-verif-wrap">
                                    <span class="valid-icon"></span>
                                    <input type="tel" id="Cell1" name="Cell1" class="phone-verif" maxlength="14"  />
                                </div>
                                
                                <label><?php echo ($english) ? "Best Time to Contact" : htmlentities("Mejor hora para contactarle") ; ?></label>
                                    <select id="Availability" name="Availability" style="width:205px">
                                        <option value="Best Time: Morning"><?php echo ($english) ? "Morning" : htmlentities("Mañana") ; ?></option>
                                        <option value="Best Time: Afternoon"><?php echo ($english) ? "Afternoon" : "Tarde" ; ?></option>
                                        <option selected="" value="Best Time: Evening"><?php echo ($english) ? "Evening" : "Noche" ; ?></option>
                                    </select>
                                    <label><?php echo ($english) ? "Total Debt" : "Total de la Deuda" ; ?></label>
                                    <select name="TotalDebt" style="width:205px">
                                        <option selected="" value="$5,000 - $9,999">$5,000 - $9,999</option>
                                        <option value="$10,000 - $14,999">$10,000 - $14,999</option>
                                        <option value="$15,000 - $19,999">$15,000 - $19,999</option>
                                        <option value="$20,000 - $24,999">$20,000 - $24,999</option>
                                        <option value="$25,000 - $29,999">$25,000 - $29,999</option>
                                        <option value="$30,000 - $34,999">$30,000 - $34,999</option>
                                        <option value="$35,000 - $39,999">$35,000 - $39,999</option>
                                        <option value="$40,000 - $44,999">$40,000 - $44,999</option>
                                        <option value="$45,000 - $49,999">$45,000 - $49,999</option>
                                        <option value="$50,000+">$50,000+</option>
                                    </select>
                                    
                                	<div>
                                    	
                                        <?php if($visitor->isUSA){ ?>
                                            <a onclick="fnSubmit2();return false;" class="btn blue full" href="javascript:void(0);"><?php echo $site->getLabel("get-started"); ?></a>
                                        <?php }else{ ?>
                                            <div class="disclaimer" style="padding:5px 0px 0px 52px"><?php echo $site->getFormDisclaimer(); ?></div>
                                        <?php } ?>
                                    	
                                        
                                    </div>
                                
                        <script type="text/javascript">
							var randomnumber=Math.floor(Math.random()*100);
							document.write('<input type="hidden" name="key" value="'+randomnumber+'" />');
						</script>
                        <input type="hidden" name="ref" value="<?php echo $_SERVER['HTTP_REFERER'] ?>" />
                    </form>
                </div><!--box-content -->
                </div><!--box-->
                
                    <div class="related-items">
                    <div class="shadow box">
                        <div class="box-title">
                            <h3><?php echo ($english) ? "Financial Freedom" : "Libre de Deudas" ; ?></h3>
                        </div><!--inside_banner_title -->
                        <div class="box-content">
                             <img src="/assets/images/homepage/financial-freedom.jpg" />
                      <p><a href="/free-analysis"><?php echo ($english) ? "Get a FREE Analysis" : htmlentities("Análisis Gratuito") ; ?></a></p>
                        </div><!--box-content -->
                    </div><!--box-->
                    </div>
                </div>
            </div>
            
            
            <?php include("includes/homepageCarousel.inc.php"); ?>
        
   
        </div> <!--home-page-->
        
<?php if($site->isLive){ ?>

	<?php if($site->lang == "es"){ ?>
        <!-- Google Code for LibeDeudas.com Homepage visitors Remarketing List -->
		<script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 1063813910;
        var google_conversion_language = "en";
        var google_conversion_format = "3";
        var google_conversion_color = "666666";
        var google_conversion_label = "1LPoCILl1wEQloai-wM";
        var google_conversion_value = 0;
        /* ]]> */
        </script>
        <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
        </script>
        <noscript>
        <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1063813910/?label=1LPoCILl1wEQloai-wM&amp;guid=ON&amp;script=0"/>
        </div>
        </noscript>
    <?php } ?>
    
<?php } ?>