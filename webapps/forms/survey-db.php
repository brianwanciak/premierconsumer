<?php
date_default_timezone_set('America/New_York');
if(isset($_POST['key'])){
	require_once("includes/functions.php");
	require_once("includes/postmark.php");
	$key = $_POST['key'];
	
	$postArr = array("1s", "2s", "3s", "4s", "5s", "6s", "7s", "8s", "9s", "10s", "11s", "Suggestions", "ID", "Name", "ContactMe", "ref");
	
	foreach($postArr as $p){
		$form[$p] = strip_tags($_POST[$p]);
	}
	
	$form['IP'] = $_SERVER['REMOTE_ADDR'];
	$form['WHOIS'] = "http://www.ipchecking.com/?ip=".$form['IP'];
	
	//print_r($form);
	
	//$to = "brian.wanciak@htmlproductions.com";
	$to = "corporate@premierconsumer.org";
	$from = "admin@premierconsumer.org";
	$subject = "Survey Form (premierconsumer) from ".$form['Name'];

	$html = "<table cellspacing='1' border='1' cellpadding='5'>";
	$html .= "<tr><td colspan='2'><p>You've got requests as below sent on ".date("m/d/Y")." at ".date("g:i a")."</p></td></tr>";
	$html .= "<tr><td>Name</td><td>".$form['Name']."</td></tr>";
	$html .= "<tr><td>ID</td><td>".$form['ID']."</td></tr>";
	$html .= "<tr><td>Contact Me?</td><td>".$form['ContactMe']."</td></tr>";
	$html .= "<tr><td>Suggestions</td><td>".$form['Suggestions']."</td></tr>";
	$html .= "<tr><td>1. Do you feel Premier Consumer Credit Counseling has provided you with useful information on how to become debt free and financially educated?</td><td>".$form['1s']."</td></tr>";
	$html .= "<tr><td>2. Do you feel that our certified credit counselors are helpful, knowledgeable and courteous?</td><td>".$form['2s']."</td></tr>";
	$html .= "<tr><td>3. Have you found this website easy to navigate?</td><td>".$form['3s']."</td></tr>";
	$html .= "<tr><td>4. Have you found the learning center useful and informative?</td><td>".$form['4s']."</td></tr>";
	$html .= "<tr><td>5. Has Premier Consumer Credit Counseling met your expectations and your needs?</td><td>".$form['5s']."</td></tr>";
	$html .= "<tr><td>6. Have our counselor and staff responded to your concerns by internet or phone fast enough?</td><td>".$form['6s']."</td></tr>";
	$html .= "<tr><td>7. Has our organization improved its services since you started with our program?</td><td>".$form['7s']."</td></tr>";
	$html .= "<tr><td>8. Have you found the office hours of our organization convenient?</td><td>".$form['8s']."</td></tr>";
	$html .= "<tr><td>9. Would you recommend family and friends to our organization?</td><td>".$form['9s']."</td></tr>";
	$html .= "<tr><td>10. Have you felt that our organization is keeping track of your progress?</td><td>".$form['10s']."</td></tr>";
	$html .= "<tr><td>11. Do you feel you are the road to your financial freedom?</td><td>".$form['11s']."</td></tr>";
	$html .= "<tr><td>IP Address</td><td>".$form['IP']."</td></tr>";
	$html .= "<tr><td>Referrer Link</td><td>".$form['ref']."</td></tr>";
	$html .= "<tr><td>Whois Link</td><td><a href='".$form['WHOIS']."'>Click Here</a></td></tr>";
	$html .= "</table>";

	if(($key > 0) && ($key < 100)){
		define('POSTMARKAPP_API_KEY', 'dbf4b719-e472-4d8c-9d44-a8effb46ab5d');
		
		if(checkEmail($uto)){
			$mail = new Mail_Postmark();
			$mail->addTo($to);
			$mail->from($from);
			$mail->subject($subject);
			$mail->messageHtml($html);
			$mail->send();
		}
	
		//header("Location: <?php echo $base_url; ?>thankyou1.php");
	}else{
		//header("Location: http://www.premierconsumer.org");
	}
	


}else{
	//header("Location: http://www.premierconsumer.org");
}

?>