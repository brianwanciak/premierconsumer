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
	
	//print_r($form);
	
	//$to = "brian.wanciak@htmlproductions.com";
	$to = "leads@premierconsumer.org";
	$from = "admin@premierconsumer.org";
	$subject = "Main Contact Form Analysis Request (premierconsumer) from ".$form['Name'];
	
	
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
	
	$uhtml = "<p>Thank you for contacting Premier Consumer Credit Counseling. The following information has been submitted to a certified counselor who will get back to you shortly. Please review the information below to verify accuracy. If you notice any mistakes please fill out the form again by visiting www.premierconsumer.org or call us toll free at 1-800-296-4950 Option 3.<br /><br />Remember to visit our learning center at www.premierconsumer.org where you will find personal finance articles and calculators!<br /><br />Thank you again!</p><br /><br />";
	$uhtml .= "<table cellspacing='1' border='1' cellpadding='5'>";
	$uhtml .= "<tr><td>Name</td><td>".$form['Name']."</td></tr>";
	$uhtml .= "<tr><td>Email</td><td>".$form['Email']."</td></tr>";
	$uhtml .= "<tr><td>Home Phone</td><td>".$form['Home']."</td></tr>";
	$uhtml .= "<tr><td>Work Phone</td><td>".$form['Work']."</td></tr>";
	$uhtml .= "<tr><td>Cell Phone</td><td>".$form['Cell']."</td></tr>";
	$uhtml .= "<tr><td>Availability</td><td>".$form['Availability']."</td></tr>";
	$uhtml .= "<tr><td>Total Debt</td><td>".$form['TotalDebt']."</td></tr>";
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
		$db["language"] = "english";
		
		require_once("includes/leads-db.php");
		insertLead($db);
		
		//*********************************
	
		header("Location: ".$base_url."thank-you");
		
	}else{
		header("Location: http://www.premierconsumer.org");
	}
	


}else{
	header("Location: http://www.premierconsumer.org");
}

?>