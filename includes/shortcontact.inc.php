<div class="phone-number-wrap no-margin" style="display:none">
    <div class="pc-hide-for-medium-up"><div class="phone-number"><a href="tel:<?php echo $site->getConfig("tollfree"); ?>"><span><?php echo $site->getLabel("tap-call"); ?></span></a></div></div>
    <div class="pc-hide-for-small"><div class="phone-number"><a href="tel:<?php echo $site->getConfig("tollfree"); ?>"><span><?php echo $site->getConfig("tollfree"); ?></span></a></div></div>            
</div>

<div class="shadow box">
    <div class="box-title">
        <h3><?php echo ($site->getLang() == "en") ? "Debt Consolidation" : "Consolidación de Deudas"; ?></h3>
    </div><!--inside_banner_title -->
    <div class="box-content">
    <img src="/assets/images/debt-help.jpg" style="margin-bottom:14px" class="img-border" />
    <p style="font-size: 14px"><?php echo ($site->getLang() == "en") ? "Our debt management programs consists of <b>consolidating</b> your credit card debts and others unsecured debts into a <b>single monthly payment that you can afford</b>." : "Nuestro programa consiste en consolidar sus tarjetas de crédito y otras deudas en <b>un solo pago mensual</b> que usted pueda cumplir, <b>para vivir mejor y más tranquilo</b>."; ?></p>
    <p style="text-align: center;"><a href="/debt-management-program"><?php echo ($site->getLang() == "en") ? "Learn more" : "Aprende más"; ?></a></p>
    </div>
</div>

<div class="shadow box">
                <div class="box-title">
                    <h3><?php echo $site->getLabel("short-form-title"); ?></h3>
                </div><!--inside_banner_title -->
                <div class="box-content">
                    <img src="/assets/images/<?php echo $site->getLang(); ?>/contact_banner.jpg" style="margin-bottom:14px" class="img-border" />
                    <form action="<?php echo $site->getFormUrl(); ?>short-contact.php" method="post" name="form1" class="custom" style="margin-bottom:0">
                    		<label><?php echo $site->getLabel("form-name"); ?>*</label>
                            <input type="text" name="FullName" id="FullName"/>
                        
                        	<label><?php echo $site->getLabel("form-email"); ?>*</label>
                            <input type="text" name="Email" id="Email"/>
                        
                        	<label><?php echo $site->getLabel("form-home-phone"); ?></label>
                            <div class="phone-verif-wrap">
                                <span class="valid-icon"></span>
                            	<input type="tel" id="Home1" name="Home1" class="phone-verif" maxlength="14"  />
                            </div>
                        
                        	<label><?php echo $site->getLabel("form-work-phone"); ?></label>
                            <div class="phone-verif-wrap">
                                <span class="valid-icon"></span>
                                <input type="tel" id="Work1" name="Work1" class="phone-verif" maxlength="14"  />
                            </div>
                        
                        
                        	<label><?php echo $site->getLabel("form-cell-phone"); ?></label>
                            <div class="phone-verif-wrap">
                                <span class="valid-icon"></span>
                                <input type="tel" id="Cell1" name="Cell1" class="phone-verif" maxlength="14"  />
                            </div>
                           
                        
                        	<label><?php echo $site->getLabel("form-best-time"); ?></label>
                            
                            	<select id="Availability" name="Availability" class="medium">
                                        <option value="Best Time: Morning"><?php echo $site->getLabel("form-morning"); ?></option>
                                        <option value="Best Time: Afternoon"><?php echo $site->getLabel("form-afternoon"); ?></option>
                                        <option selected="" value="Best Time: Evening"><?php echo $site->getLabel("form-evening"); ?></option>
                                    </select>
                            
                       		<?php if($visitor->isUSA){ ?>
                            	<a onclick="fnSubmit();return false;" class="btn blue full" href="javascript:void(0);"><?php echo $site->getLabel("free-analysis"); ?></a>
                            <?php }else{ ?>
                            	<div class="disclaimer" style="padding:5px 0px 0px 52px"><?php echo $site->getFormDisclaimer(); ?></div>
                            <?php } ?>
                    
                     <input type="hidden" name="ref" value="<?php if(isset($_SERVER['HTTP_REFERER'])){echo $_SERVER['HTTP_REFERER'];} ?>" />
                     	<script type="text/javascript">	
							var randomnumber=Math.floor(Math.random()*100);
							document.write('<input type="hidden" name="key" value="'+randomnumber+'" />');
						</script>
                    </form>
                </div><!--box-content -->
           
        </div><!--box-->
        
        
       