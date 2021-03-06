<?php
date_default_timezone_set('America/New_York');
if(isset($_POST['key'])){
	require_once("includes/functions.php");
	require_once("includes/postmark.php");
	$key = $_POST['key'];
	
	$postArr = array("FullName", "Address1", "Address2", "City", "State", "Zip", "Email", "BestTime", "BestPlace", "comment", "Home1", "Home2", "Home3", "Work1", "Work2", "Work3", "Cell1", "Cell2", "Cell3", "ref", "creditor1", "creditor2", "creditor3", "creditor4", "creditor5", "creditor6", "interest1", "interest2", "interest3", "interest4", "interest5", "interest6", "payment1", "payment2", "payment3", "payment4", "payment5", "payment6", "account1", "account2", "account3", "account4", "account5", "account6", "amount1", "amount2", "amount3", "amount4", "amount5", "amount6");
	
	foreach($postArr as $p){
		$form[$p] = strip_tags($_POST[$p]);
	}
	
	$form['IP'] = $_SERVER['REMOTE_ADDR'];
	$form['WHOIS'] = "http://www.ipchecking.com/?ip=".$form['IP'];
	
	$form['Work'] = $form['Work1'].'-'.$form['Work2'].'-'.$form['Work3'];
	$form['Home'] = $form['Home1'].'-'.$form['Home2'].'-'.$form['Home3'];
	$form['Cell'] = $form['Cell1'].'-'.$form['Cell2'].'-'.$form['Cell3'];
	$form['Name'] = $form['FullName'];
	
	//print_r($form);
	
	//$to = "brian.wanciak@htmlproductions.com";
	$to = "leads@premierconsumer.org";
	$from = "admin@premierconsumer.org";
	$subject = "Free Analysis Request (librededeudas) from ".$form['Name'];
	
	$html = "<table cellspacing='1' border='1' cellpadding='5'>";
	$html .= "<tr><td colspan='2'><p>You've got requests as below sent on ".date("m/d/Y")." at ".date("g:i a")."</p></td></tr>";
	$html .= "<tr><td>Name</td><td>".$form['Name']."</td></tr>";
	$html .= "<tr><td>Email</td><td>".$form['Email']."</td></tr>";
	$html .= "<tr><td>Home Phone</td><td>".$form['Home']."</td></tr>";
	$html .= "<tr><td>Work Phone</td><td>".$form['Work']."</td></tr>";
	$html .= "<tr><td>Cell Phone</td><td>".$form['Cell']."</td></tr>";
	$html .= "<tr><td>Address</td><td>".$form['Address1']." <br /> ".$form['Address2']."<br />".$form['City'].", ".$form['State']." ".$form['Zip']."</td></tr>";
	$html .= "<tr><td>Best time to contact</td><td>".$form['BestTime']."</td></tr>";
	$html .= "<tr><td>Best place to contact</td><td>".$form['BestPlace']."</td></tr>";
	$html .= "<tr><td>IP Address</td><td>".$form['IP']."</td></tr>";
	$html .= "<tr><td>Referrer Link</td><td>".$form['ref']."</td></tr>";
	$html .= "<tr><td>Whois Link</td><td><a href='".$form['WHOIS']."'>Click Here</a></td></tr>";
	$html .= "</table><br />";
	
	$html .= "<table cellspacing='1' border='1' cellpadding='5'>";
	$html .= "<tr><td>Creditor Name</td><td>Interest Rate</td><td>Monthly Payment</td><td>Account Number</td><td>Amount</td></tr>";
	for($i=1; $i<=6; $i++){
		$html .= "<tr><td>".$form['creditor'.$i]."</td><td>".$form['interest'.$i]."</td><td>".$form['payment'.$i]."</td><td>".$form['account'.$i]."</td><td>".$form['amount'.$i]."</td></tr>";
	}
	$html .= "</table>";
	
	//Start user contact confirmation email information
	$uto = $form['Email'];
	$ufrom = "donotreply@premierconsumer.org";
	$usubject = "Premier Consumer Contact Confirmation";
	
	$uhtml = "<p>Gracias por contactar a Premier Consumer Credit Counseling. La siguiente informaci�n ha sido ingresada en nuestro sistema y un consejero financiero certificado le contactar� lo mas pronto posible. Por favor revise la informaci�n que aparece abajo para verificar su exactitud. Si usted se da cuenta de alg�n error le pedimos que por favor la ingrese otra vez en www.libredeDeudas.com o ll�menos al 1.800.296.4950 Opci�n 3.<br /><br />Recuerde visitar nuestro centro de aprendizaje en www.libredeDeudas.com donde va a encontrar art�culos educativos en temas de mucho inter�s.<br /><br />Gracias otra vez.</p><br /><br />";
	$uhtml .= "<table cellspacing='1' border='1' cellpadding='5'>";
	$uhtml .= "<tr><td>Nombre</td><td>".$form['Name']."</td></tr>";
	$uhtml .= "<tr><td>Email</td><td>".$form['Email']."</td></tr>";
	$uhtml .= "<tr><td>Tel�fono de la Casa</td><td>".$form['Home']."</td></tr>";
	$uhtml .= "<tr><td>Tel�fono del trabajo</td><td>".$form['Work']."</td></tr>";
	$uhtml .= "<tr><td>Tel�fono celular</td><td>".$form['Cell']."</td></tr>";
	$uhtml .= "<tr><td>Address</td><td>".$form['Address1']." <br /> ".$form['Address2']."<br />".$form['City'].", ".$form['State']." ".$form['Zip']."</td></tr>";
	$uhtml .= "<tr><td>Best time to contact</td><td>".$form['BestTime']."</td></tr>";
	$uhtml .= "<tr><td>Best place to contact</td><td>".$form['BestPlace']."</td></tr>";
	$uhtml .= "</table><br />";
	
	$uhtml .= "<table cellspacing='1' border='1' cellpadding='5'>";
	$uhtml .= "<tr><td>Creditor Name</td><td>Interest Rate</td><td>Monthly Payment</td><td>Account Number</td><td>Amount</td></tr>";
	for($i=1; $i<=6; $i++){
		$uhtml .= "<tr><td>".$form['creditor'.$i]."</td><td>".$form['interest'.$i]."</td><td>".$form['payment'.$i]."</td><td>".$form['account'.$i]."</td><td>".$form['amount'.$i]."</td></tr>";
	}
	$uhtml .= "</table><br />";
	
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
	
		$db["name"] = $form['FullName'];
		$db["email"] = $form['Email'];
		$db["best_time_to_contact"] = str_replace("Best Time: ", "", $form['Availability']);
		$db["comments"] = $form["comment"];
		$db["h_phone"] = $form['Home'];
		$db["w_phone"] = $form['Work'];
		$db["c_phone"] = $form['Cell'];
		$db["address"] = $form['Address1']."<br />".$form['Address2'];
		$db["city"] = $form['City'];
		$db["state"] = $form['State'];
		$db["zip"] = $form['Zip'];
		$db["ip"] = $form['IP'];
		$db["referrer"] = $form['ref'];
		$db["whois"] = $form['WHOIS'];
		$db["email_body"] = $html;
		$db["form_id"] = 4;
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