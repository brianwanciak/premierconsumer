<?php 
	$lang = $site->getLang();
	$english = $lang == "en" ? true : false;
?>

      
       <div id="side_col_left" style="width:100%; min-height: 400px">
       
       
       <div class="thank-you">
       			<a href="/articles"><img src="/assets/images/<?php echo ($english) ? "thankyou_banner" : "thankyou_banner_es" ; ?>.jpg" /></a>
               
       </div>
       

<?php if($site->isLive){ ?>

	<?php if($site->lang == "en"){ ?>
       <script type="text/javascript"> if (!window.mstag) mstag = {loadTag : function(){},time : (new Date()).getTime()};</script> <script id="mstag_tops" type="text/javascript" src="//flex.atdmt.com/mstag/site/61ac3632-2d60-43cb-bbe0-f65e546fba67/mstag.js"></script> <script type="text/javascript"> mstag.loadTag("conversion", {cp:"5050",dedup:"1"})</script> <noscript> <iframe src="//flex.atdmt.com/mstag/tag/61ac3632-2d60-43cb-bbe0-f65e546fba67/conversion.html?cp=5050&dedup=1" frameborder="0" scrolling="no" width="1" height="1" style="visibility:hidden;display:none"> </iframe> </noscript>
		<!-- Google Code for Contact Form Conversion Page -->
		<script language="JavaScript" type="text/javascript">
        <!--
        var google_conversion_id = 1063813910;
        var google_conversion_language = "en_US";
        var google_conversion_format = "1";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "c0gtCPqKnQEQloai-wM";
        //-->
        </script>
        <script language="JavaScript" src="https://www.googleadservices.com/pagead/conversion.js">
        </script>
        <noscript>
        <img height="1" width="1" border="0" src="https://www.googleadservices.com/pagead/conversion/1063813910/?label=c0gtCPqKnQEQloai-wM&amp;guid=ON&amp;script=0"/>
        </noscript>

       
    <?php }else if($site->lang == "es"){ ?>
        <script type="text/javascript"> if (!window.mstag) mstag = {loadTag : function(){},time : (new Date()).getTime()};</script> <script id="mstag_tops" type="text/javascript" src="//flex.atdmt.com/mstag/site/61ac3632-2d60-43cb-bbe0-f65e546fba67/mstag.js"></script> <script type="text/javascript"> mstag.loadTag("conversion", {cp:"5050",dedup:"1"})</script> <noscript> <iframe src="//flex.atdmt.com/mstag/tag/61ac3632-2d60-43cb-bbe0-f65e546fba67/conversion.html?cp=5050&dedup=1" frameborder="0" scrolling="no" width="1" height="1" style="visibility:hidden;display:none"> </iframe> </noscript>

        <!-- Google Code for Contact Form Conversion Page -->
		<script language="JavaScript" type="text/javascript">
        <!--
        var google_conversion_id = 1063813910;
        var google_conversion_language = "en_US";
        var google_conversion_format = "1";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "c0gtCPqKnQEQloai-wM";
        //-->
        </script>
        <script language="JavaScript" src="https://www.googleadservices.com/pagead/conversion.js">
        </script>
        <noscript>
        <img height="1" width="1" border="0" src="https://www.googleadservices.com/pagead/conversion/1063813910/?label=c0gtCPqKnQEQloai-wM&amp;guid=ON&amp;script=0"/>
        </noscript>
    <?php } ?>
    
<?php } ?>