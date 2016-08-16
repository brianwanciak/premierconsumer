
      
       <div id="side_col_left">
       <div class="box shadow">
       <div class="box-title"><h1><?php echo $page->getPageTitle(); ?></h1></div>
       <div class="box-content">
			
            <?php echo $page->getNode("contentPage"); ?>
            
            
            <?php if($site->getLang() == "en"){ ?>
            
            	<form name="form2" method="post" action="<?php echo $site->getFormUrl(); ?>survey.php">
                <table width="100%" class="survey">
                  <tr valign="top">
                    <td width="63%" bgcolor="#e5efff">1. Do you feel Premier Consumer Credit Counseling has provided you with useful information on how to become debt free and financially educated?</td>
                    <td width="37%" bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1 <input name="1s" type="radio" value="Poor">
                          </td>
                          <td>2 <input name="1s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3 <input name="1s" type="radio" value="Acceptable">
                          </td>
                          <td>4 <input name="1s" type="radio" value="Satisfied">
                          </td>
                          <td>5 <input name="1s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>2. Do you feel that our certified credit counselors are helpful, knowledgeable and courteous?</td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="2s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="2s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="2s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="2s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="2s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">3. Have you found this website easy to navigate?</td>
                    <td bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="3s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="3s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="3s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="3s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="3s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>4. Have you found the learning center useful and informative?</td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="4s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="4s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="4s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="4s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="4s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">5. Has Premier Consumer Credit Counseling met your expectations and your needs?</td>
                    <td bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="5s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="5s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="5s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="5s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="5s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>6. Have our counselor and staff responded to your concerns by internet or phone fast enough?</td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="6s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="6s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="6s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="6s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="6s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">7. Has our organization improved its services since you started with our program?</td>
                    <td bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="7s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="7s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="7s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="7s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="7s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>8. Have you found the office hours of our organization convenient?</td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="8s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="8s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="8s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="8s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="8s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">9. Would you recommend family and friends to our organization?</td>
                    <td bgcolor="#e5efff">
                      <table bgcolor="#e5efff" width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="9s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="9s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="9s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="9s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="9s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>10. Have you felt that our organization is keeping track of your progress?</td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="10s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="10s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="10s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="10s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="10s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">11. Do you feel you are the road to your financial freedom?</td>
                    <td bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="11s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="11s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="11s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="11s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="11s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td></td>
                    <td></td>
                  </tr>
                  <tr valign="top">
                    <td>Please add any suggestion in the following box.</td>
                    <td>
                      <textarea name="Suggestions" cols="20" id="Suggestions"></textarea>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>If you are a member, please indicate your client ID number and name.</td>
                    <td>
                      <input name="ID" type="text" id="ID" value="ID" size="20">
                      <br>
                      <input name="Name" type="text" id="Name" value="Name" size="20">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>If you wish to be contacted, please click on the following box.</td>
                    <td>
                      <input name="ContactMe" type="checkbox" id="Contact Me" value="Yes">
                    </td>
                  </tr>
                   <!--<tr>
                    <td>
                      <img src="aspcaptcha.php" alt="CAPTCHA image" title="CAPTCHA image" width="86" height="21" />
                      <p>Type the characters shown in image for verification.</p>
                      <input name="strCAPTCHA" type="text" id="strCAPTCHA" maxlength="8" />
                    </td>
                  </tr> -->
                  <tr valign="top">
                    <td><!--<img src="images/lock5.jpg" width="15" height="15"> --></td>
                    <td>                      <input type="submit" name="Submit" value="Submit">
                    </td>
                  </tr>
                </table>
                <p align="justify"></p>
                <script type="text/javascript">
	var randomnumber=Math.floor(Math.random()*100);
	document.write('<input type="hidden" name="key" value="'+randomnumber+'" />');
</script>
              </form> 
            
            <?php }else{ ?>
            
            	<form name="form2" method="post" action="<?php echo $site->getFormUrl(); ?>survey.php">
                <table width="100%" class="survey">
                  <tr valign="top">
                    <td width="63%" bgcolor="#e5efff">1. &iquest;Usted piensa que Premier Consumer Credit Counseling le ha proveido con informaci&oacute;n &uacute;til en como liberarse de las deudas y estar educado financieramente? </td>
                    <td width="37%" bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1<input name="1s" type="radio" value="Poor">
                          </td>
                          <td>2<input name="1s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3<input name="1s" type="radio" value="Acceptable">
                          </td>
                          <td>4<input name="1s" type="radio" value="Satisfied">
                          </td>
                          <td>5<input name="1s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>2. &iquest; Usted piensa que nuestros consejeros certificados han sido corteses, le han ayudado y tiene buenos conocimientos? </td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="2s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="2s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="2s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="2s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="2s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">3. &iquest;Usted ha encontrado nuestro portal de Internet f&aacute;cil de navegar? </td>
                    <td bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="3s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="3s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="3s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="3s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="3s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>4. &iquest;Ha encontrado nuestro centro de aprendizaje &uacute;til e informativo? </td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="4s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="4s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="4s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="4s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="4s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">5. &iquest;Ha Premier Consumer Credit Counseling cumplido con sus expectativas y necesidades? </td>
                    <td bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="5s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="5s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="5s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="5s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="5s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>6. &iquest;Han nuestros consejeros respondido sus inquietudes por tel&eacute;fono o por Internet lo suficientemente r&aacute;pido? </td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="6s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="6s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="6s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="6s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="6s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">7. &iquest;Ha nuestra organizaci&oacute;n mejorado los servicios desde que usted ha empezado con nuestro programa? </td>
                    <td bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="7s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="7s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="7s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="7s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="7s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>8. &iquest;Encuentra usted nuestro horario de oficinas conveniente? </td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="8s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="8s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="8s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="8s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="8s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">9. &iquest;Recomendar&iacute;a usted un familiar o amigo a nuestra organizaci&oacute;n?</td>
                    <td bgcolor="#e5efff">
                      <table bgcolor="#e5efff" width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="9s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="9s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="9s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="9s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="9s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>10. &iquest;Ha sentido usted que nuestra organizaci&oacute;n esta manteni&eacute;ndose informada de su progreso? </td>
                    <td>
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="10s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="10s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="10s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="10s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="10s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td bgcolor="#e5efff">11. &iquest;Piensa usted que esta en su camino a su libertad financiera? </td>
                    <td bgcolor="#e5efff">
                      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>1
                                <input name="11s" type="radio" value="Poor">
                          </td>
                          <td>2
                                <input name="11s" type="radio" value="Unsatisfied">
                          </td>
                          <td>3
                                <input name="11s" type="radio" value="Acceptable">
                          </td>
                          <td>4
                                <input name="11s" type="radio" value="Satisfied">
                          </td>
                          <td>5
                                <input name="11s" type="radio" value="Very Satisfied">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td></td>
                    <td></td>
                  </tr>
                  <tr valign="top">
                    <td>Por favor a&ntilde;ada cualquier comentario en la siguiente cajita</td>
                    <td>
                      <textarea name="Suggestions" cols="20" id="Suggestions"></textarea>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td>Si usted es nuestro cliente, por favor indique su n&uacute;mero de cliente con nosotros. </td>
                    <td>
                      <input name="ID" type="text" id="ID" value="ID" size="20">
                      <br>
                      <input name="Name" type="text" id="Name" value="Nombre" size="20">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td> Si usted desea ser contactado por favor marque la siguiente casilla.</td>
                    <td>
                      <input name="ContactMe" type="checkbox" id="Contact Me" value="Yes">
                    </td>
                  </tr>
                   <!--<tr>
                    <td>
                      <img src="aspcaptcha.php" alt="CAPTCHA image" title="CAPTCHA image" width="86" height="21" />
                      <p>Type the characters shown in image for verification.</p>
                      <input name="strCAPTCHA" type="text" id="strCAPTCHA" maxlength="8" />
                    </td>
                  </tr> -->
                  <tr valign="top">
                    <td><!--<img src="images/lock5.jpg" width="15" height="15"> --></td>
                    <td>                      <input type="submit" name="Submit" value="Enviar informaci&oacute;n">
                    </td>
                  </tr>
                </table>
                <p align="justify"></p>
                <script type="text/javascript">
	var randomnumber=Math.floor(Math.random()*100);
	document.write('<input type="hidden" name="key" value="'+randomnumber+'" />');
</script>
              </form>              
            
            <?php } ?>
            
       </div><!--side_col_left -->
       </div><!--box-content-->
       </div><!--box shadow-->
       <div id="side_col_right">

   		<?php 
			if($page->getPath() == "/employment-opportunities"){
				include("includes/generalcontact.inc.php");
			}else{
				include("includes/shortcontact.inc.php");  
			}				
		?>

       </div><!--side_col_right -->
       
