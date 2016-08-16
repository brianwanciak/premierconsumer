<div class="shadow box">
            <div class="box-title">
                <h3><?php echo $site->getLabel("nav-contact-us"); ?></h3>
            </div><!--inside_banner_title -->
        <div class="box-content">
             <form action="<?php echo $site->getFormUrl(); ?>contact-Form.php" method="post" class="custom full-width" name="frmContactUs">
							<input type="hidden" value="English" name="Language"/><input type="hidden" value="PremierConsumer" name="Website"/>
							<label><?php echo $site->getLabel("form-department"); ?>:</label>
                            <select name="Department">
                                <option value="1"><?php echo $site->getLabel("form-corporate-relations"); ?></option>
                                <option value="2"><?php echo $site->getLabel("form-member-services"); ?></option>
                            </select>
                            
                            <label><?php echo $site->getLabel("form-name"); ?>*:</label>
                            <input type="text" name="Name" style="max-width:325px"/>
                            
                            <label><?php echo $site->getLabel("form-email"); ?>*:</label>
                            <input type="text" name="Email"  style="max-width:325px"/>
                            
                            <label><?php echo $site->getLabel("form-phone"); ?>:</label>
                            <div class="phone-verif-wrap" style="max-width: 325px;">
                                <span class="valid-icon"></span>
                                <input type="tel" name="Phone" class="phone-verif" maxlength="14" style="max-width:325px"/>
                            </div>
                            <label><?php echo $site->getLabel("form-subject"); ?>:</label>
                            <input type="text" name="Subject"/>
                            
                            <label><?php echo $site->getLabel("form-message"); ?>:</label>
                            <textarea name="Message" style="height: 6em"></textarea>
                            <a href="#" class="btn blue full" onClick="frmContactUs.submit(); return false;"><?php echo $site->getLabel("nav-contact-us"); ?></a>
                                    
							</form>
        </div><!--box-content -->
    </div><!--box-->