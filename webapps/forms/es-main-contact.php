<?php
date_default_timezone_set('America/New_York');


if(isset($_POST['key'])){
	require_once("includes/functions.php");
	require_once("includes/postmark.php");
	$key = $_POST['key'];
	
	$postArr = array("FirstName", "LastName", "Email", "Availability", "TotalDebt", "Home1", "Home2", "Home3", "Work1", "Work2", "Work3", "Cell1", "Cell2", "Cell3", "ref");
	
	foreach($postArr as $p){
		$form[$p] = strip_tags($_POST[$p]);
	}
	
	$form['IP'] = $_SERVER['REMOTE_ADDR'];
	$form['WHOIS'] = "http://www.ipchecking.com/?ip=".$form['IP'];
	
	$form['Work'] = $form['Work1'];
	$form['Home'] = $form['Home1'];
	$form['Cell'] = $form['Cell1'];
	$form['Name'] = $form['FirstName'].' '.$form['LastName'];
	
	if(!checkPhones($form['Work'], $form['Home'], $form['Cell']) || $form['Email'] == ""){
		header("Location: http://www.librededeudas.com");
		exit();
	}
	//print_r($form);
	
	//$to = "brian.wanciak@htmlproductions.com";
	$to = "leads@premierconsumer.org";
	$from = "admin@premierconsumer.org";
	$subject = "Main Contact Form Analysis Request (librededeudas) from ".$form['Name'];
	
	
	$html = "<table cellspacing='1' border='1' cellpadding='5'>";
	$html .= "<tr><td colspan='2'><p>You've got requests as below sent on ".date("m/d/Y")." at ".date("g:i a")."</p></td></tr>";
	$html .= "<tr><td>Name</td><td>".$form['Name']."</td></tr>";
	$html .= "<tr><td>Email</td><td>".$form['Email']."</td></tr>";
	$html .= "<tr><td>Home Phone</td><td>".$form['Home']."</td></tr>";
	$html .= "<tr><td>Work Phone</td><td>".$form['Work']."</td></tr>";
	$html .= "<tr><td>Cell Phone</td><td>".$form['Cell']."</td></tr>";
	$html .= "<tr><td>Availability</td><td>".$form['Availability']."</td></tr>";
	$html .= "<tr><td>Total Debt</td><td>".$form['TotalDebt']."</td></tr>";
	$html .= "<tr><td>IP Address</td><td>".$form['IP']."</td></tr>";
	$html .= "<tr><td>Referrer Link</td><td>".$form['ref']."</td></tr>";
	$html .= "<tr><td>Whois Link</td><td><a href='".$form['WHOIS']."'>Click Here</a></td></tr>";
	$html .= "</table>";
	
	
	
	//Start user contact confirmation email information
	$uto = $form['Email'];
	$ufrom = "donotreply@premierconsumer.org";
	$usubject = "Premier Consumer Contact Confirmation";
	
	$uhtml = "<p>Gracias por contactar a Premier Consumer Credit Counseling. La siguiente información ha sido ingresada en nuestro sistema y un consejero financiero certificado le contactará lo mas pronto posible. Por favor revise la información que aparece abajo para verificar su exactitud. Si usted se da cuenta de algún error le pedimos que por favor la ingrese otra vez en www.libredeDeudas.com o llámenos al 1.800.296.4950 Opción 3.<br /><br />Recuerde visitar nuestro centro de aprendizaje en www.libredeDeudas.com donde va a encontrar artículos educativos en temas de mucho interés.<br /><br />Gracias otra vez.</p><br /><br />";
	$uhtml .= "<table cellspacing='1' border='1' cellpadding='5'>";
	$uhtml .= "<tr><td>Nombre</td><td>".$form['Name']."</td></tr>";
	$uhtml .= "<tr><td>Email</td><td>".$form['Email']."</td></tr>";
	$uhtml .= "<tr><td>Teléfono de la Casa</td><td>".$form['Home']."</td></tr>";
	$uhtml .= "<tr><td>Teléfono del trabajo</td><td>".$form['Work']."</td></tr>";
	$uhtml .= "<tr><td>Teléfono celular</td><td>".$form['Cell']."</td></tr>";
	$uhtml .= "<tr><td>Mejor hora para contactarle</td><td>".$form['Availability']."</td></tr>";
	$uhtml .= "<tr><td>Total de la Deuda</td><td>".$form['TotalDebt']."</td></tr>";
	$uhtml .= "</table>";
	
	$uhtml = spanishEncode($uhtml);
	
	if(($key > 0) && ($key < 100)){
		
		define('POSTMARKAPP_API_KEY', 'dbf4b719-e472-4d8c-9d44-a8effb46ab5d');
		
		if(checkEmail($uto)){
			//$mail = new Mail_Postmark();
//			$mail->addTo($to);
//			$mail->from($from);
//			$mail->subject($subject);
//			$mail->messageHtml($html);
//			$mail->send();
		}
		
		if(checkEmail($uto)){
			$umail = new Mail_Postmark();
			$umail->addTo($uto);
			$umail->from($ufrom);
			$umail->subject($usubject);
			$umail->messageHtml($uhtml);
			try{
				$umail->send();
			}catch (Exception $e) {

			}
		}
		
		//************* Build Database Info
	
		$db["name"] = $form['Name'];
		$db["best_time_to_contact"] = str_replace("Best Time: ", "", $form['Availability']);
		$db["total_debt"] = $form['TotalDebt'];
		$db["email"] = $form['Email'];
		$db["h_phone"] = $form['Home'];
		$db["w_phone"] = $form['Work'];
		$db["c_phone"] = $form['Cell'];
		$db["ip"] = $form['IP'];
		$db["referrer"] = $form['ref'];
		$db["whois"] = $form['WHOIS'];
		$db["email_body"] = $html;
		$db["form_id"] = 2;
		$db["language"] = "spanish";
		
		require_once("includes/leads-db.php");
		insertLead($db);
		
		//*********************************
		
		
		header("Location: http://www.librededeudas.com/thank-you");
		
	}else{
		header("Location: http://www.librededeudas.com");
	}
	


}else{
	header("Location: http://www.librededeudas.com");
}

?>