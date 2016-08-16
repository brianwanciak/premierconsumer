<?php
date_default_timezone_set('America/New_York');

if(isset($_POST['key'])){
	require_once("includes/functions.php");
	require_once("includes/postmark.php");
	$key = $_POST['key'];
	
	$postArr = array("name", "email", "phone", "best-time", "total-debt", "ref");
	
	foreach($postArr as $p){
		$form[$p] = strip_tags($_POST[$p]);
	}
	
	$form['IP'] = $_SERVER['REMOTE_ADDR'];
	$form['WHOIS'] = "http://www.ipchecking.com/?ip=".$form['IP'];
	
	
	if($form['name'] == ""){
		header("Location: http://www.librededeudas.com");
	}


	//print_r($form);
	
	//$to = "brian.wanciak@htmlproductions.com";
	$to = "leads@premierconsumer.org";
	$from = "admin@premierconsumer.org";
	$subject = "Main Contact Form Analysis Request (librededeudas) from ".$form['Name'];
	
	
	$html = "<table cellspacing='1' border='1' cellpadding='5'>";
	$html .= "<tr><td colspan='2'><p>You've got requests as below sent on ".date("m/d/Y")." at ".date("g:i a")."</p></td></tr>";
	$html .= "<tr><td>Name</td><td>".$form['name']."</td></tr>";
	$html .= "<tr><td>Email</td><td>".$form['email']."</td></tr>";
	$html .= "<tr><td>Phone</td><td>".$form['phone']."</td></tr>";
	$html .= "<tr><td>Availability</td><td>".$form['best-time']."</td></tr>";
	$html .= "<tr><td>Total Debt</td><td>".$form['total-debt']."</td></tr>";
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
	$uhtml .= "<tr><td>Nombre</td><td>".$form['name']."</td></tr>";
	$uhtml .= "<tr><td>Email</td><td>".$form['email']."</td></tr>";
	$uhtml .= "<tr><td>Teléfono</td><td>".$form['phone']."</td></tr>";
	$uhtml .= "<tr><td>Mejor hora para contactarle</td><td>".$form['best-time']."</td></tr>";
	$uhtml .= "<tr><td>Total de la Deuda</td><td>".$form['total-debt']."</td></tr>";
	$uhtml .= "</table>";
	
	
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
			$umail->send();
		}
		
		//************* Build Database Info
	
		$db["name"] = $form['name'];
		$db["best_time_to_contact"] = str_replace("Best Time: ", "", $form['best-time']);
		$db["total_debt"] = $form['TotalDebt'];
		$db["email"] = $form['email'];
		$db["h_phone"] = $form['phone'];
		$db["w_phone"] = "";
		$db["c_phone"] = "";
		$db["ip"] = $form['IP'];
		$db["referrer"] = $form['ref'];
		$db["whois"] = $form['WHOIS'];
		$db["email_body"] = $html;
		$db["form_id"] = 2;
		$db["language"] = "spanish";
		
		if($db["name"] != ""){
			require_once("includes/leads-db.php");
			insertLead($db);
		}
		//*********************************
	
		header("Location: ".$base_url_libre."thankyou1.php");
		
	}else{
		header("Location: http://www.librededeudas.com");
	}
	


}else{
	header("Location: http://www.librededeudas.com");
}

?>