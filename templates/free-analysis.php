
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			
            <?php echo $page->getNode("contentPage"); ?>
            
            
             <div class="cat_box">
        	<h2 class="title"><a class="title"><?php echo ($site->getLang() == "en") ? "Free Analysis" : "Análisis Gratuito"; ?></a></h2>
            <div class="body">
        		<form action="<?php echo $site->config->formUrl ?>free-analysis.php" method="post" name="form1" class="custom full-width">
				<input type="hidden" value="English" name="Language"/>
                
                <label><?php echo $site->getLabel("form-full-name"); ?>: *</label>
                <input type="text" size="30" id="First Name" name="FullName" class="full-width-field" />
                <label><?php echo $site->getLabel("form-home-phone"); ?>: </label>
                
                  <div class="phone-verif-wrap" style="max-width: 325px">
                      <span class="valid-icon"></span>
                      <input type="tel" size="30" id="Home1" name="Home1" class="phone-verif full-width-field" maxlength="14"  />
                  </div>
                    
                
       			<label><?php echo $site->getLabel("form-cell-phone"); ?>: </label>
                <div class="phone-verif-wrap" style="max-width: 325px">
                      <span class="valid-icon"></span>
                      <input type="tel" size="30" id="Cell1" name="Cell1" class="phone-verif full-width-field" maxlength="14"  />
                  </div>
                   
                <label><?php echo $site->getLabel("form-work-phone"); ?>:</label>
                <div class="phone-verif-wrap" style="max-width: 325px">
                    <span class="valid-icon"></span>
                    <input type="tel" size="30" id="Work1" name="Work1" class="phone-verif full-width-field" maxlength="14"  />
                </div>
                        
       
                 <label><?php echo $site->getLabel("form-address"); ?>: *</label>
                 <input type="text" size="17" id="Address1" name="Address1" class="full-width-field" />
				 <input type="text" size="17" id="Address2" name="Address2" class="full-width-field" />
                    
                 <label><?php echo $site->getLabel("form-city"); ?>: *</label>
				 <input type="text" size="17" id="City" name="City" class="full-width-field" />
                   
                 <label><?php echo $site->getLabel("form-state"); ?>:</label>
                <select name="State" >
                  <option value="Alabama">Alabama</option>
                  <option value="Alaska">Alaska</option>
                  <option value="Arizona">Arizona</option>
                  <option value="Arkansas">Arkansas</option>
                  <option value="California">California</option>
                  <option value="Colorado">Colorado</option>
                  <option value="Connecticut">Connecticut</option>
                  <option value="Delaware">Delaware</option>
                  <option value="Florida">Florida</option>
                  <option value="Georgia">Georgia</option>
                  <option value="Hawaii">Hawaii</option>
                  <option value="Idaho">Idaho</option>
                  <option value="Illinois">Illinois</option>
                  <option value="Indiana">Indiana</option>
                  <option value="Iowa">Iowa</option>
                  <option value="Kansas">Kansas</option>
                  <option value="Kentucky">Kentucky</option>
                  <option value="Louisiana">Louisiana</option>
                  <option value="Maine">Maine</option>
                  <option value="Maryland">Maryland</option>
                  <option value="Massachusetts    ">Massachusetts</option>
                  <option value="Michigan">Michigan</option>
                  <option value="Minnesota">Minnesota</option>
                  <option value="Mississippi">Mississippi</option>
                  <option value="Missouri">Missouri</option>
                  <option value="Montana">Montana</option>
                  <option value="Nebraska">Nebraska</option>
                  <option value="Nevada">Nevada</option>
                  <option value="New Hampshire">New Hampshire</option>
                  <option value="New Jersey">New Jersey</option>
                  <option value="New Mexico">New Mexico</option>
                  <option value="New York">New York</option>
                  <option value="North Carolina">North Carolina</option>
                  <option value="North Dakota">North Dakota</option>
                  <option value="Ohio">Ohio</option>
                  <option value="Oklahoma">Oklahoma</option>
                  <option value="Oregon">Oregon</option>
                  <option value="Pennsylvania">Pennsylvania</option>
                  <option value="Rhode Island">Rhode Island</option>
                  <option value="South Carolina">South Carolina</option>
                  <option value="South Dakota">South Dakota</option>
                  <option value="Tennessee">Tennessee</option>
                  <option value="Texas">Texas</option>
                  <option value="Utah">Utah</option>
                  <option value="Vermont">Vermont</option>
                  <option value="Virginia">Virginia</option>
                  <option value="Washington">Washington</option>
                  <option value="West Virginia">West Virginia</option>
                  <option value="Wisconsin">Wisconsin</option>
                  <option value="Wyoming">Wyoming</option>
                </select> 
                   
                  <label><?php echo $site->getLabel("form-zip"); ?>:</label>
                  <input type="text" size="17" id="Zip" name="Zip" class="full-width-field" />
                   
                  <label>Email: *</label>
                      <input type="text" size="30" id="Email" name="Email" class="full-width-field" />
                   
                 <label><?php echo $site->getLabel("form-best-time"); ?>: </label>
                 
                        <select id="select2" name="BestTime">
                          <option value="Best Time: Morning"><?php echo $site->getLabel("form-morning"); ?></option>
                          <option value="Best Time: Afternoon"><?php echo $site->getLabel("form-afternoon"); ?></option>
                          <option selected="" value="Best Time: Evening"><?php echo $site->getLabel("form-evening"); ?></option>
                        </select>
                   
                  <label><?php echo $site->getLabel("form-best-place"); ?>:</label>
                  
                        <select id="select3" name="BestPlace">
                          <option selected="" value="Home"><?php echo $site->getLabel("form-home-phone"); ?></option>
                          <option value="Cell Phone"><?php echo $site->getLabel("form-cell-phone"); ?></option>
                          <option value="Work"><?php echo $site->getLabel("form-work-phone"); ?></option>
                        </select>
                   
                   <label><?php echo $site->getLabel("form-tell-us-more"); ?>: </label>
                   <textarea rows="5" cols="52" style="height:115px" name="comment"></textarea></td>
                  
<table align="center" style="margin: 10px 0px; border-collapse:separate" class="free-a">
<tbody>
                  
                  <tr>
                    <td valign="top"><table style="width: 100%">
                      <tbody><tr bgcolor="#cccccc">
						<td><div align="center" style="padding: 3px 0px"><strong><?php echo $site->getLabel("form-creditor-name"); ?></strong></div></td>
                      <td><div align="center"><strong><?php echo $site->getLabel("form-interest-rate"); ?></strong> </div></td>
                      <td><div align="center"><strong><?php echo $site->getLabel("form-monthly-payment"); ?></strong></div></td>
                      <td><div align="center"><strong><?php echo $site->getLabel("form-account-number"); ?></strong></div></td>
                      <td><div align="center"><strong><?php echo $site->getLabel("form-amount"); ?></strong> </div></td>
                    </tr>
                      <tr>
                        <td><input type="text" size="17" id="creditor1" name="creditor1"/></td>
                        <td><input type="text" size="17" id="interest1" name="interest1"/></td>
                        <td><input type="text" size="17" id="payment1" name="payment1"/></td>
                        <td><input type="text" size="17" id="account1" name="account1"/></td>
                        <td style="padding-right:0px">
                          <select id="Creditor1" name="amount1" style="size:150px">
                            <option selected="" value="$2,000 - $3,999">$2,000 - $3,999</option>
                            <option value="$4,000 - $5,999">$4,000 - $5,999</option>
                            <option value="$6,000 - $7,999">$6,000 - $7,999</option>
                            <option value="$8,000 - $9,999">$8,000 - $9,999</option>
                            <option value="$10,000 - $11,999">$10,000 - $11,999</option>
                            <option value="$12,000 - $13,999">$12,000 - $13,999</option>
                            <option value="$14,000 - $15,999">$14,000 - $15,999</option>
                            <option value="$16,000 - $17,999">$16,000 - $17,999</option>
                            <option value="$18,000 - $19,999">$18,000 - $19,999</option>
                            <option value="$20,000 - $21,999">$20,000 - $21,999</option>
                            <option value="$22,000 - $23,999">$22,000 - $23,999</option>
                            <option value="$24,000 - $25,999">$24,000 - $25,999</option>
                            <option value="$26,000 - $27,999">$26,000 - $27,999</option>
                            <option value="$28,000 - $29,999">$28,000 - $29,999</option>
                            <option value="$30,000 - $31,999">$30,000 - $31,999</option>
                            <option value="$32,000 - $33,999">$32,000 - $33,999</option>
                            <option value="$34,000 - $35,999">$34,000 - $35,999</option>
                            <option value="$36,000 - $37,999">$36,000 - $37,999</option>
                            <option value="$38,000 - $39,999">$38,000 - $39,999</option>
                            <option value="$40,000 - $41,999">$40,000 - $41,999</option>
                            <option value="$42,000 - $43,999">$42,000 - $43,999</option>
                            <option value="$44,000 - $45,999">$44,000 - $45,999</option>
                            <option value="$46,000 - $47,999">$46,000 - $47,999</option>
                            <option value="$48,000 - $49,999">$48,000 - $49,999</option>
                            <option value="$50,000 - $51,999">$50,000 - $51,999</option>
                            <option value="$52,000 - $53,999">$52,000 - $53,999</option>
                            <option value="$54,000 - $55,999">$54,000 - $55,999</option>
                            <option value="$56,000 - $57,999">$56,000 - $57,999</option>
                            <option value="$58,000 - $59,999">$58,000 - $59,999</option>
                            <option value="$60,000 - $69,999">$60,000 - $69,999</option>
                            <option value="$70,000 - $79,999">$70,000 - $79,999</option>
                            <option value="$80,000 - $89,999">$80,000 - $89,999</option>
                            <option value="$90,000 - $99,999">$90,000 - $99,999</option>
                            <option value="$100,000+">$100,000+</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td><input type="text" size="17" id="creditor2" name="creditor2"/></td>
                        <td><input type="text" size="17" id="interest2" name="interest2"/></td>
                        <td><input type="text" size="17" id="payment2" name="payment2"/></td>
                        <td><input type="text" size="17" id="account2" name="account2"/></td>
                        <td style="padding-right:0px">
                          <select id="select" name="amount2" style="size:150px">
                            <option selected="" value="$2,000 - $3,999">$2,000 - $3,999</option>
                            <option value="$4,000 - $5,999">$4,000 - $5,999</option>
                            <option value="$6,000 - $7,999">$6,000 - $7,999</option>
                            <option value="$8,000 - $9,999">$8,000 - $9,999</option>
                            <option value="$10,000 - $11,999">$10,000 - $11,999</option>
                            <option value="$12,000 - $13,999">$12,000 - $13,999</option>
                            <option value="$14,000 - $15,999">$14,000 - $15,999</option>
                            <option value="$16,000 - $17,999">$16,000 - $17,999</option>
                            <option value="$18,000 - $19,999">$18,000 - $19,999</option>
                            <option value="$20,000 - $21,999">$20,000 - $21,999</option>
                            <option value="$22,000 - $23,999">$22,000 - $23,999</option>
                            <option value="$24,000 - $25,999">$24,000 - $25,999</option>
                            <option value="$26,000 - $27,999">$26,000 - $27,999</option>
                            <option value="$28,000 - $29,999">$28,000 - $29,999</option>
                            <option value="$30,000 - $31,999">$30,000 - $31,999</option>
                            <option value="$32,000 - $33,999">$32,000 - $33,999</option>
                            <option value="$34,000 - $35,999">$34,000 - $35,999</option>
                            <option value="$36,000 - $37,999">$36,000 - $37,999</option>
                            <option value="$38,000 - $39,999">$38,000 - $39,999</option>
                            <option value="$40,000 - $41,999">$40,000 - $41,999</option>
                            <option value="$42,000 - $43,999">$42,000 - $43,999</option>
                            <option value="$44,000 - $45,999">$44,000 - $45,999</option>
                            <option value="$46,000 - $47,999">$46,000 - $47,999</option>
                            <option value="$48,000 - $49,999">$48,000 - $49,999</option>
                            <option value="$50,000 - $51,999">$50,000 - $51,999</option>
                            <option value="$52,000 - $53,999">$52,000 - $53,999</option>
                            <option value="$54,000 - $55,999">$54,000 - $55,999</option>
                            <option value="$56,000 - $57,999">$56,000 - $57,999</option>
                            <option value="$58,000 - $59,999">$58,000 - $59,999</option>
                            <option value="$60,000 - $69,999">$60,000 - $69,999</option>
                            <option value="$70,000 - $79,999">$70,000 - $79,999</option>
                            <option value="$80,000 - $89,999">$80,000 - $89,999</option>
                            <option value="$90,000 - $99,999">$90,000 - $99,999</option>
                            <option value="$100,000+">$100,000+</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td><input type="text" size="17" id="creditor3" name="creditor3"/></td>
                        <td><input type="text" size="17" id="interest3" name="interest3"/></td>
                        <td><input type="text" size="17" id="payment3" name="payment3"/></td>
                        <td><input type="text" size="17" id="account3" name="account3"/></td>
                        <td style="padding-right:0px">
                          <select id="select4" name="amount3" style="size:150px">
                            <option selected="" value="$2,000 - $3,999">$2,000 - $3,999</option>
                            <option value="$4,000 - $5,999">$4,000 - $5,999</option>
                            <option value="$6,000 - $7,999">$6,000 - $7,999</option>
                            <option value="$8,000 - $9,999">$8,000 - $9,999</option>
                            <option value="$10,000 - $11,999">$10,000 - $11,999</option>
                            <option value="$12,000 - $13,999">$12,000 - $13,999</option>
                            <option value="$14,000 - $15,999">$14,000 - $15,999</option>
                            <option value="$16,000 - $17,999">$16,000 - $17,999</option>
                            <option value="$18,000 - $19,999">$18,000 - $19,999</option>
                            <option value="$20,000 - $21,999">$20,000 - $21,999</option>
                            <option value="$22,000 - $23,999">$22,000 - $23,999</option>
                            <option value="$24,000 - $25,999">$24,000 - $25,999</option>
                            <option value="$26,000 - $27,999">$26,000 - $27,999</option>
                            <option value="$28,000 - $29,999">$28,000 - $29,999</option>
                            <option value="$30,000 - $31,999">$30,000 - $31,999</option>
                            <option value="$32,000 - $33,999">$32,000 - $33,999</option>
                            <option value="$34,000 - $35,999">$34,000 - $35,999</option>
                            <option value="$36,000 - $37,999">$36,000 - $37,999</option>
                            <option value="$38,000 - $39,999">$38,000 - $39,999</option>
                            <option value="$40,000 - $41,999">$40,000 - $41,999</option>
                            <option value="$42,000 - $43,999">$42,000 - $43,999</option>
                            <option value="$44,000 - $45,999">$44,000 - $45,999</option>
                            <option value="$46,000 - $47,999">$46,000 - $47,999</option>
                            <option value="$48,000 - $49,999">$48,000 - $49,999</option>
                            <option value="$50,000 - $51,999">$50,000 - $51,999</option>
                            <option value="$52,000 - $53,999">$52,000 - $53,999</option>
                            <option value="$54,000 - $55,999">$54,000 - $55,999</option>
                            <option value="$56,000 - $57,999">$56,000 - $57,999</option>
                            <option value="$58,000 - $59,999">$58,000 - $59,999</option>
                            <option value="$60,000 - $69,999">$60,000 - $69,999</option>
                            <option value="$70,000 - $79,999">$70,000 - $79,999</option>
                            <option value="$80,000 - $89,999">$80,000 - $89,999</option>
                            <option value="$90,000 - $99,999">$90,000 - $99,999</option>
                            <option value="$100,000+">$100,000+</option>
                          </select>
                        </td>
                      </tr>
 <tr>
                        <td><input type="text" size="17" id="creditor4" name="creditor4"/></td>
                        <td><input type="text" size="17" id="interest4" name="interest4"/></td>
                        <td><input type="text" size="17" id="payment4" name="payment4"/></td>
                        <td><input type="text" size="17" id="account4" name="account4"/></td>
                        <td style="padding-right:0px">
                          <select id="select5" name="amount4" style="size:150px">
                            <option selected="" value="$2,000 - $3,999">$2,000 - $3,999</option>
                            <option value="$4,000 - $5,999">$4,000 - $5,999</option>
                            <option value="$6,000 - $7,999">$6,000 - $7,999</option>
                            <option value="$8,000 - $9,999">$8,000 - $9,999</option>
                            <option value="$10,000 - $11,999">$10,000 - $11,999</option>
                            <option value="$12,000 - $13,999">$12,000 - $13,999</option>
                            <option value="$14,000 - $15,999">$14,000 - $15,999</option>
                            <option value="$16,000 - $17,999">$16,000 - $17,999</option>
                            <option value="$18,000 - $19,999">$18,000 - $19,999</option>
                            <option value="$20,000 - $21,999">$20,000 - $21,999</option>
                            <option value="$22,000 - $23,999">$22,000 - $23,999</option>
                            <option value="$24,000 - $25,999">$24,000 - $25,999</option>
                            <option value="$26,000 - $27,999">$26,000 - $27,999</option>
                            <option value="$28,000 - $29,999">$28,000 - $29,999</option>
                            <option value="$30,000 - $31,999">$30,000 - $31,999</option>
                            <option value="$32,000 - $33,999">$32,000 - $33,999</option>
                            <option value="$34,000 - $35,999">$34,000 - $35,999</option>
                            <option value="$36,000 - $37,999">$36,000 - $37,999</option>
                            <option value="$38,000 - $39,999">$38,000 - $39,999</option>
                            <option value="$40,000 - $41,999">$40,000 - $41,999</option>
                            <option value="$42,000 - $43,999">$42,000 - $43,999</option>
                            <option value="$44,000 - $45,999">$44,000 - $45,999</option>
                            <option value="$46,000 - $47,999">$46,000 - $47,999</option>
                            <option value="$48,000 - $49,999">$48,000 - $49,999</option>
                            <option value="$50,000 - $51,999">$50,000 - $51,999</option>
                            <option value="$52,000 - $53,999">$52,000 - $53,999</option>
                            <option value="$54,000 - $55,999">$54,000 - $55,999</option>
                            <option value="$56,000 - $57,999">$56,000 - $57,999</option>
                            <option value="$58,000 - $59,999">$58,000 - $59,999</option>
                            <option value="$60,000 - $69,999">$60,000 - $69,999</option>
                            <option value="$70,000 - $79,999">$70,000 - $79,999</option>
                            <option value="$80,000 - $89,999">$80,000 - $89,999</option>
                            <option value="$90,000 - $99,999">$90,000 - $99,999</option>
                            <option value="$100,000+">$100,000+</option>
                          </select>
                        </td>
 </tr>
 <tr>
                        <td><input type="text" size="17" id="creditor5" name="creditor5"/></td>
                        <td><input type="text" size="17" id="interest5" name="interest5"/></td>
                        <td><input type="text" size="17" id="payment5" name="payment5"/></td>
                        <td><input type="text" size="17" id="account5" name="account5"/></td>
                        <td style="padding-right:0px">
                          <select id="select6" name="amount5" style="size:150px">
                            <option selected="" value="$2,000 - $3,999">$2,000 - $3,999</option>
                            <option value="$4,000 - $5,999">$4,000 - $5,999</option>
                            <option value="$6,000 - $7,999">$6,000 - $7,999</option>
                            <option value="$8,000 - $9,999">$8,000 - $9,999</option>
                            <option value="$10,000 - $11,999">$10,000 - $11,999</option>
                            <option value="$12,000 - $13,999">$12,000 - $13,999</option>
                            <option value="$14,000 - $15,999">$14,000 - $15,999</option>
                            <option value="$16,000 - $17,999">$16,000 - $17,999</option>
                            <option value="$18,000 - $19,999">$18,000 - $19,999</option>
                            <option value="$20,000 - $21,999">$20,000 - $21,999</option>
                            <option value="$22,000 - $23,999">$22,000 - $23,999</option>
                            <option value="$24,000 - $25,999">$24,000 - $25,999</option>
                            <option value="$26,000 - $27,999">$26,000 - $27,999</option>
                            <option value="$28,000 - $29,999">$28,000 - $29,999</option>
                            <option value="$30,000 - $31,999">$30,000 - $31,999</option>
                            <option value="$32,000 - $33,999">$32,000 - $33,999</option>
                            <option value="$34,000 - $35,999">$34,000 - $35,999</option>
                            <option value="$36,000 - $37,999">$36,000 - $37,999</option>
                            <option value="$38,000 - $39,999">$38,000 - $39,999</option>
                            <option value="$40,000 - $41,999">$40,000 - $41,999</option>
                            <option value="$42,000 - $43,999">$42,000 - $43,999</option>
                            <option value="$44,000 - $45,999">$44,000 - $45,999</option>
                            <option value="$46,000 - $47,999">$46,000 - $47,999</option>
                            <option value="$48,000 - $49,999">$48,000 - $49,999</option>
                            <option value="$50,000 - $51,999">$50,000 - $51,999</option>
                            <option value="$52,000 - $53,999">$52,000 - $53,999</option>
                            <option value="$54,000 - $55,999">$54,000 - $55,999</option>
                            <option value="$56,000 - $57,999">$56,000 - $57,999</option>
                            <option value="$58,000 - $59,999">$58,000 - $59,999</option>
                            <option value="$60,000 - $69,999">$60,000 - $69,999</option>
                            <option value="$70,000 - $79,999">$70,000 - $79,999</option>
                            <option value="$80,000 - $89,999">$80,000 - $89,999</option>
                            <option value="$90,000 - $99,999">$90,000 - $99,999</option>
                            <option value="$100,000+">$100,000+</option>
                          </select>
                        </td>
 </tr>
 <tr>
                        <td><input type="text" size="17" id="creditor6" name="creditor6"/></td>
                        <td><input type="text" size="17" id="interest6" name="interest6"/></td>
                        <td><input type="text" size="17" id="payment6" name="payment6"/></td>
                        <td><input type="text" size="17" id="account6" name="account6"/></td>
                        <td style="padding-right:0px">
                          <select id="select7" name="amount6" style="size:150px">
                            <option selected="" value="$2,000 - $3,999">$2,000 - $3,999</option>
                            <option value="$4,000 - $5,999">$4,000 - $5,999</option>
                            <option value="$6,000 - $7,999">$6,000 - $7,999</option>
                            <option value="$8,000 - $9,999">$8,000 - $9,999</option>
                            <option value="$10,000 - $11,999">$10,000 - $11,999</option>
                            <option value="$12,000 - $13,999">$12,000 - $13,999</option>
                            <option value="$14,000 - $15,999">$14,000 - $15,999</option>
                            <option value="$16,000 - $17,999">$16,000 - $17,999</option>
                            <option value="$18,000 - $19,999">$18,000 - $19,999</option>
                            <option value="$20,000 - $21,999">$20,000 - $21,999</option>
                            <option value="$22,000 - $23,999">$22,000 - $23,999</option>
                            <option value="$24,000 - $25,999">$24,000 - $25,999</option>
                            <option value="$26,000 - $27,999">$26,000 - $27,999</option>
                            <option value="$28,000 - $29,999">$28,000 - $29,999</option>
                            <option value="$30,000 - $31,999">$30,000 - $31,999</option>
                            <option value="$32,000 - $33,999">$32,000 - $33,999</option>
                            <option value="$34,000 - $35,999">$34,000 - $35,999</option>
                            <option value="$36,000 - $37,999">$36,000 - $37,999</option>
                            <option value="$38,000 - $39,999">$38,000 - $39,999</option>
                            <option value="$40,000 - $41,999">$40,000 - $41,999</option>
                            <option value="$42,000 - $43,999">$42,000 - $43,999</option>
                            <option value="$44,000 - $45,999">$44,000 - $45,999</option>
                            <option value="$46,000 - $47,999">$46,000 - $47,999</option>
                            <option value="$48,000 - $49,999">$48,000 - $49,999</option>
                            <option value="$50,000 - $51,999">$50,000 - $51,999</option>
                            <option value="$52,000 - $53,999">$52,000 - $53,999</option>
                            <option value="$54,000 - $55,999">$54,000 - $55,999</option>
                            <option value="$56,000 - $57,999">$56,000 - $57,999</option>
                            <option value="$58,000 - $59,999">$58,000 - $59,999</option>
                            <option value="$60,000 - $69,999">$60,000 - $69,999</option>
                            <option value="$70,000 - $79,999">$70,000 - $79,999</option>
                            <option value="$80,000 - $89,999">$80,000 - $89,999</option>
                            <option value="$90,000 - $99,999">$90,000 - $99,999</option>
                            <option value="$100,000+">$100,000+</option>
                          </select>
                        </td>
										 </tr>
										 <tr>
										   <td colspan="5">
                                           <div style="max-width:300px; margin:0 auto">
                                           <?php
												echo ($visitor->isUSA) ? '<a onclick="fnSubmit();return false;" class="btn blue full" href="javascript:void(0);">Get My Free Analysis</a>' : '<span class="disclaimer">' . $site->config->disclaimer .'</span>'; 
											?>  
										     </div>
										  </td>
										 </tr>
                      
                    </tbody></table></td>
                  </tr>
                  <tr>
						   			<td>* - field required. </td>
                  </tr>
                </tbody></table>
                <script type="text/javascript">
							var randomnumber=Math.floor(Math.random()*100);
							document.write('<input type="hidden" name="key" value="'+randomnumber+'" />');
						</script>
                <input type="hidden" name="ref" value="<?php echo $_SERVER['HTTP_REFERER'] ?>" />
              </form>
              </div><!--body -->
        </div>
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		 <div class="shadow box dl-and-share">
            <div class="box-title">
                <h3><?php echo $site->getLabel("form-contact-us"); ?></h3>
            </div><!--inside_banner_title -->
        <div class="box-content">
             <p style="margin-bottom:8px">
                  <?php echo ($site->getLang() == "en") ? "If you have any questions. Please do not hesitate to call us toll free:" : "Si tiene alguna pregunta,  por favor no dude en llamarnos a nuestra línea gratis:"; ?>
                	 <br /><strong><?php echo $site->config->tollfree ?></strong>
                </p>
                <p><strong><?php echo $site->config->company ?></strong><br />
                    <?php echo $site->config->address ?><br />
                     <?php echo $site->config->address2 ?></p>
        </div><!--box-content -->
    </div><!--box-->

       </div><!--side_col_right -->
       
