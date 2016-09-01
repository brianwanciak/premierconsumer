	</div><!--row-->
    
    </div><!--wrapper -->
    
    </div> <!--columns-->
    </div> <!--row-->
    
</div><!--container -->

<div id="footer_wrapper">

	<div id="footer">

        <div id="subfooter">
        	<div class="row">
                	<div class="columns large-12">
                        <div class="footer-links footer-nav">
                            <a href="/index"><?php echo $site->getLabel("nav-home"); ?></a> | 
                            <a href="/about-us"><?php echo $site->getLabel("nav-about-us"); ?></a> | 
                            <a href="/how-we-can-help-you"><?php echo $site->getLabel("nav-how-we-can-help"); ?></a> | 
                            <a href="/learning-center"><?php echo $site->getLabel("nav-learning-center"); ?></a> | 
                            <a href="/free-analysis"><?php echo $site->getLabel("nav-free-analysis"); ?></a> | 
                            <a href="/contact-us"><?php echo $site->getLabel("nav-contact-us"); ?></a> | 
                            <a href="/privacy-policy"><?php echo $site->getLabel("privacy-policy"); ?></a> | 
                            <a href="/terms-and-conditions"><?php echo $site->getLabel("terms-conditions"); ?></a>
                        </div>
                        <div class="footer-links">
                            <p>Copyright &copy; <?php echo date("Y"); ?>. <?php echo $site->getConfig("company"); ?>. All Rights Reserved.</p>
                            <p><?php echo $site->getConfig("address"); ?>, <?php echo $site->getConfig("address2"); ?>, Tel: <?php echo $site->getConfig("tollfree"); ?> Local: <?php echo $site->getConfig("localphone"); ?> Fax: <?php echo $site->getConfig("fax"); ?></p>
                        </div>
                        <div class="seals">
                        	<div class="seals-first">
                            	<div class="seal">
                                    <img src="/assets/images/miami-herald.jpg" />
                                    <div class="clear"></div>
                                </div>
                                <div class="seal">
                                    <img src="/assets/images/greater-miami.jpg" />
                                    <div class="clear"></div>
                                </div>
                                <div class="seal">
                                    <script type="text/javascript" src="https://seal.websecurity.norton.com/getseal?host_name=www.<?php echo $site->domain; ?>&amp;size=S&amp;use_flash=NO&amp;use_transparent=NO&amp;lang=en"></script>
                                       
                                    <div class="clear"></div>
                                </div>
                               
                                <div class="clear"></div>
                            </div>
                           
                        <div class="clear"></div>
                        </div>
        
        		</div>
            </div>
        
        </div><!--subfooter -->

    </div><!--footer -->
    
</div><!--footer_wrapper -->


<?php if($site->isLive){ ?>

	<?php if($site->lang == "en"){ ?>
        
    <?php }else if($site->lang == "es"){ ?>
        
    <?php } ?>
    
<?php } ?>


<script src="/foundation/js/foundation.min.js"></script>
<script src="/foundation/js/foundation/foundation.topbar.js"></script>
<script src="/foundation/js/foundation/foundation.forms.js"></script>
<script src="/foundation/js/foundation/foundation.reveal.js"></script>
<script src="/foundation/js/foundation/foundation.orbit.js"></script>
<script src="/foundation/js/vendor/custom.modernizr.js"></script>
<script src="/js/plugins.js"></script>
<script src="/js/stickynav.js"></script>

<script>
    $(document).foundation();
</script>

<?php if($site->chatEnabled){ ?>
  <span style="color: #0000FF; text-decoration: underline; cursor: pointer;" id="phplive_btn_1470770952" onclick="phplive_launch_chat_0(0)"></span>
  <script type="text/javascript">

  (function() {
  var phplive_e_1470770952 = document.createElement("script") ;
  phplive_e_1470770952.type = "text/javascript" ;
  phplive_e_1470770952.async = true ;
  phplive_e_1470770952.src = "//www.premierconsumer.org/livesupport/js/phplive_v2.js.php?v=0|1470770952|0|" ;
  document.getElementById("phplive_btn_1470770952").appendChild( phplive_e_1470770952 ) ;
  })() ;

  </script>
<?php } ?>

<span style="color: #0000FF; text-decoration: underline; cursor: pointer;" id="phplive_btn_1472695226" onclick="phplive_launch_chat_0(0)"></span>



</body>
</html>
