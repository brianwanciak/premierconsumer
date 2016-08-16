<?php
date_default_timezone_set('America/New_York');
if(isset($_POST['key'])){
	require_once("includes/functions.php");
	require_once("includes/postmark.php");
	$key = $_POST['key'];
	
	$postArr = array("Department", "Name", "Email", "Phone", "Subject", "Message", "ref");
	
	foreach($postArr as $p){
		$form[$p] = strip_tags($_POST[$p]);
	}
	
	$form['IP'] = $_SERVER['REMOTE_ADDR'];
	$form['WHOIS'] = "http://www.ipchecking.com/?ip=".$form['IP'];
	
	//print_r($form);
	
	//$to = "brian.wanciak@htmlproductions.com";
	$to = "corporate@premierconsumer.org";
	$from = "admin@premierconsumer.org";
	$subject = "Contact Form Analysis Request (premierconsumer) from ".$form['Name'];
	
	$html = "<table cellspacing='1' border='1' cellpadding='5'>";
	$html .= "<tr><td colspan='2'><p>You've got requests as below sent on ".date("m/d/Y")." at ".date("g:i a")."</p></td></tr>";
	$html .= "<tr><td>Department</td><td>".$form['Department']."</td></tr>";
	$html .= "<tr><td>Name</td><td>".$form['Name']."</td></tr>";
	$html .= "<tr><td>Email</td><td>".$form['Email']."</td></tr>";
	$html .= "<tr><td>Phone</td><td>".$form['Phone']."</td></tr>";
	$html .= "<tr><td>Subject</td><td>".$form['Subject']."</td></tr>";
	$html .= "<tr><td>Message</td><td>".$form['Message']."</td></tr>";
	$html .= "<tr><td>IP Address</td><td>".$form['IP']."</td></tr>";
	$html .= "<tr><td>Referrer Link</td><td>".$form['ref']."</td></tr>";
	$html .= "<tr><td>Whois Link</td><td><a href='".$form['WHOIS']."'>Click Here</a></td></tr>";
	$html .= "</table>";
	
	
	
	//Start user contact confirmation email information
	$uto = $form['Email'];
	$ufrom = "donotreply@premierconsumer.org";
	$usubject = "Premier Consumer Contact Confirmation";
	
	$uhtml = "<p>Thank you for contacting Premier Consumer Credit Counseling. The following information has been submitted to a certified counselor who will get back to you shortly. Please review the information below to verify accuracy. If you notice any mistakes please fill out the form again by visiting www.premierconsumer.org or call us toll free at 1-800-296-4950 Option 3.<br /><br />Remember to visit our learning center at www.premierconsumer.org where you will find personal finance articles and calculators!<br /><br />Thank you again!</p><br /><br />";
	$uhtml .= "<table cellspacing='1' border='1' cellpadding='5'>";
	$uhtml .= "<tr><td>Department</td><td>".$form['Department']."</td></tr>";
	$uhtml .= "<tr><td>Name</td><td>".$form['Name']."</td></tr>";
	$uhtml .= "<tr><td>Email</td><td>".$form['Email']."</td></tr>";
	$uhtml .= "<tr><td>Phone</td><td>".$form['Phone']."</td></tr>";
	$uhtml .= "<tr><td>Subject</td><td>".$form['Subject']."</td></tr>";
	$uhtml .= "<tr><td>Message</td><td>".$form['Message']."</td></tr>";
	$uhtml .= "</table>";
	
	
	if(($key > 0) && ($key < 100)){
		define('POSTMARKAPP_API_KEY', 'dbf4b719-e472-4d8c-9d44-a8effb46ab5d');
		
		//$mail = new Mail_Postmark();
//		$mail->addTo($to);
//		$mail->from($from);
//		$mail->subject($subject);
//		$mail->messageHtml($html);
//		$mail->send();
//		
//		if(checkEmail($uto)){
//			$umail = new Mail_Postmark();
//			$umail->addTo($uto);
//			$umail->from($ufrom);
//			$umail->subject($usubject);
//			$umail->messageHtml($uhtml);
//			$umail->send();
//		}
		
		//************* Build Database Info
	
		$db["name"] = $form['Name'];
		$db["email"] = $form['Email'];
		$db["h_phone"] = $form['Phone'];
		$db["ip"] = $form['IP'];
		$db["referrer"] = $form['ref'];
		$db["whois"] = $form['WHOIS'];
		$db["email_body"] = $html;
		$db["comments"] = $form["Message"];
		$db["form_id"] = 3;
		$db["language"] = "english";
		
		require_once("includes/leads-db.php");
		insertLead($db);
		
		//*********************************
		header("Location: ".$base_url."thankyou1.php");
	}else{
		//header("Location: http://www.premierconsumer.org");
	}
	


}else{
	//header("Location: http://www.premierconsumer.org");
}

?>