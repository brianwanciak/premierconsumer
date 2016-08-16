
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			
            <?php echo $page->getNode("contentPage"); ?>
            
            <div class="cat_box">
        	<h2 class="title"><a class="title">Contact Form</a></h2>
            <div class="body">
            	 <form action="<?php echo $site->getFormUrl(); ?>contact.php" method="post" class="custom full-width" name="frmContactUs" id="contactUsForm">
							
							<label><?php echo $site->getLabel("form-department"); ?>:</label>
                            <select name="Department" style="max-width:325px">
                                <option value="1"><?php echo $site->getLabel("form-corporate-relations"); ?></option>
                                <option value="2"><?php echo $site->getLabel("form-member-services"); ?></option>
                            </select>
                            <label><?php echo $site->getLabel("form-name"); ?>*:</label>
                            <input type="text" style="max-width: 325px;" name="Name"/>
                            <label><?php echo $site->getLabel("form-email"); ?>*:</label>
                            <input type="text" style="max-width: 325px;" name="Email"/>
                            <label><?php echo $site->getLabel("form-phone"); ?>:</label>
                            <div class="phone-verif-wrap" style="max-width: 325px;">
                                <span class="valid-icon"></span>
                                <input type="tel" id="Phone" name="Phone" class="phone-verif" maxlength="14"  />
                            </div>  
                            <label><?php echo $site->getLabel("form-subject"); ?>:</label>
                            <input type="text" name="Subject"/>
                            <label><?php echo $site->getLabel("form-message"); ?>:</label>
                            <textarea style="height: 100px;" name="Message"></textarea>
							
                            <?php if($visitor->isUSA){ ?>
                            	<a href="javascript:void(0);" class="btn blue full" onclick="validateForm(); return false;"><?php echo $site->getLabel("form-contact-us"); ?></a>
                            <?php }else{ ?>
                            	<div class="disclaimer" style="padding:5px 0px 0px 52px"><?php echo $site->getFormDisclaimer(); ?></div>
                            <?php } ?>
                                    
                            <input type="hidden" name="ref" value="<?php echo $_SERVER['HTTP_REFERER'] ?>" />
                            <script type="text/javascript">
							var randomnumber=Math.floor(Math.random()*100);
							document.write('<input type="hidden" name="key" value="'+randomnumber+'" />');
						</script>
							</form>
            </div><!--body -->
        </div>
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<div class="shadow box dl-and-share">
            <div class="box-title">
                <h3><?php echo $site->getLabel("company-information"); ?></h3>
            </div><!--inside_banner_title -->
            <div class="box-content">
                 <p style="margin-bottom:8px"><strong><?php echo $site->getConfig("company"); ?></strong><br />
                        <?php echo $site->getConfig("address"); ?><br />
                        <?php echo $site->getConfig("address2"); ?></p>
                    <p style="margin-bottom:8px"><strong><?php echo $site->getLabel("form-toll-free-number"); ?></strong><br /><?php echo $site->getConfig("tollfree"); ?></p>
                    
                    <p style="margin-bottom:8px"><strong><?php echo $site->getLabel("form-local-number"); ?></strong><br /><?php echo $site->getConfig("localphone"); ?></p>
                    
                    <p style="margin-bottom:8px"><strong><?php echo $site->getLabel("form-facsimile"); ?></strong><br /><?php echo $site->getConfig("fax"); ?></p>
                    
                    
                    <p style="text-align:left"><strong><?php echo $site->getLabel("web-addresses"); ?></strong>
                    <a href="http://www.premierconsumer.org">www.premierconsumer.org</a><br />
                    <a href="http://www.premierconsumer.com">www.premierconsumer.com</a><br />
                    <a href="http://www.librededeudas.com">www.librededeudas.com</a><br />
                    </p>
            </div><!--box-content -->
        </div><!--box-->

       </div><!--side_col_right -->

       <script type="text/javascript">

            function validateForm(){
                
                if(document.frmContactUs.Name.value == ''){
                    alert("Please Enter Your Name");
                }else if(document.frmContactUs.Email.value == ''){
                    alert("Please Enter Your Email");
                }else if($("#contactUsForm").find(".invalid").length > 0){
                    alert("Please correct invalid phone numbers");
                }else{
                
                    document.frmContactUs.submit();
                }
            
            }

        </script>
       
