<?php
date_default_timezone_set('America/New_York');

if(isset($_POST['key'])){
	require_once("includes/functions.php");
	require_once("includes/postmark.php");
	$key = $_POST['key'];
	
	$postArr = array("name", "email", "availability", "totalDebt", "home1", "home2", "home3", "work1", "work2", "work3", "cell1", "cell2", "cell3", "ref");
	
	foreach($postArr as $p){
		$form[$p] = strip_tags($_POST[$p]);
	}
	
	$form['IP'] = $_SERVER['REMOTE_ADDR'];
	$form['WHOIS'] = "http://www.ipchecking.com/?ip=".$form['IP'];
	
	$form['work'] = $form['work1'].'-'.$form['work2'].'-'.$form['work3'];
	$form['home'] = $form['home1'].'-'.$form['home2'].'-'.$form['home3'];
	$form['cell'] = $form['cell1'].'-'.$form['cell2'].'-'.$form['cell3'];
	
	//print_r($form);
	
	//$to = "brian.wanciak@htmlproductions.com";
	$to = "leads@premierconsumer.org";
	$from = "admin@premierconsumer.org";
	$subject = "Contact Form Analysis Request (librededeudas.net) from ".$form['Name'];
	
	
	$html = "<table cellspacing='1' border='1' cellpadding='5'>";
	$html .= "<tr><td colspan='2'><p>You've got requests as below sent on ".date("m/d/Y")." at ".date("g:i a")."</p></td></tr>";
	$html .= "<tr><td>Name</td><td>".$form['name']."</td></tr>";
	$html .= "<tr><td>Email</td><td>".$form['email']."</td></tr>";
	$html .= "<tr><td>Home Phone</td><td>".$form['home']."</td></tr>";
	$html .= "<tr><td>Work Phone</td><td>".$form['work']."</td></tr>";
	$html .= "<tr><td>Cell Phone</td><td>".$form['cell']."</td></tr>";
	$html .= "<tr><td>Availability</td><td>".$form['availability']."</td></tr>";
	$html .= "<tr><td>Total Debt</td><td>".$form['totalDebt']."</td></tr>";
	$html .= "<tr><td>IP Address</td><td>".$form['IP']."</td></tr>";
	$html .= "<tr><td>Referrer Link</td><td>".$form['ref']."</td></tr>";
	$html .= "<tr><td>Whois Link</td><td><a href='".$form['WHOIS']."'>Click Here</a></td></tr>";
	$html .= "</table>";
	
	
	
	//Start user contact confirmation email information
	$uto = $form['email'];
	$ufrom = "donotreply@premierconsumer.org";
	$usubject = "LibreDeDeudas.net Contact Confirmation";
	
	$uhtml = "<p>Gracias por contactar a LibreDeDeudas.net. La siguiente información ha sido ingresada en nuestro sistema y un consejero financiero certificado le contactará lo mas pronto posible. Por favor revise la información que aparece abajo para verificar su exactitud. Si usted se da cuenta de algún error le pedimos que por favor la ingrese otra vez en www.libredeDeudas.net.<br /><br />Gracias otra vez.</p><br /><br />";
	$uhtml .= "<table cellspacing='1' border='1' cellpadding='5'>";
	$uhtml .= "<tr><td>Name</td><td>".$form['name']."</td></tr>";
	$uhtml .= "<tr><td>Email</td><td>".$form['email']."</td></tr>";
	$uhtml .= "<tr><td>Home Phone</td><td>".$form['home']."</td></tr>";
	$uhtml .= "<tr><td>Work Phone</td><td>".$form['work']."</td></tr>";
	$uhtml .= "<tr><td>Cell Phone</td><td>".$form['cell']."</td></tr>";
	$uhtml .= "<tr><td>Availability</td><td>".$form['availability']."</td></tr>";
	$uhtml .= "<tr><td>Total Debt</td><td>".$form['totalDebt']."</td></tr>";
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
			//$umail = new Mail_Postmark();
//			$umail->addTo($uto);
//			$umail->from($ufrom);
//			$umail->subject($usubject);
//			$umail->messageHtml($uhtml);
//			$umail->send();
		}
		
		//************* Build Database Info
	
		$db["name"] = $form['name'];
		$db["best_time_to_contact"] = str_replace("Best Time: ", "", $form['availability']);
		$db["total_debt"] = $form['totalDebt'];
		$db["email"] = $form['email'];
		$db["h_phone"] = $form['home'];
		$db["w_phone"] = $form['work'];
		$db["c_phone"] = $form['cell'];
		$db["ip"] = $form['IP'];
		$db["referrer"] = $form['ref'];
		$db["whois"] = $form['WHOIS'];
		$db["email_body"] = $html;
		$db["form_id"] = 6;
		$db["language"] = "spanish";
		
		require_once("includes/leads-db.php");
		insertLead($db);
		
		//*********************************
	
		header("Location: http://www.librededeudas.net/thankyou.html");
		
	}else{
		header("Location: http://www.librededeudas.net");
	}
	


}else{
	header("Location: http://www.librededeudas.net");
}

?>