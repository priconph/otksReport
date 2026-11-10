<?php
require_once("../media/PHPMailer/class.phpmailer.php"); // for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);
/* 
	For Testing
*/
$recipient = "teamiss@pricon.ph";
$from = "ISSInfoService@pricon.ph";
$fromName = "OTKS Report System"; //can be a title of a system
$CCrecipient = "";
$subject  = "Unauthorized access of OTKS Report";
$message  = "This is to inform you that there is an unauthorized access of OTKS Report System. <br><br>";

$message .= "<b>Client Information</b> <br>";
$message .= "Client IP Address: ".$_GET['ip_address'];

$send_mail = new SENDMAIL;
$send_mail->emailMultipleRecipients($from,$fromName,$recipient,$CCrecipient,$subject,$message);

class SENDMAIL{
	//function emailMultipleRecipients($from,$fromName,$recipient,$CCrecipient,$subject,$message,$attachment){ //reserved if there are attachments
	function emailMultipleRecipients($from,$fromName,$recipient,$CCrecipient,$subject,$message){ //without attachement
		$recipientArray = explode(",",$recipient);
		$ccArray = explode(',',$CCrecipient);
		//$attachmentArray = explode(',',$attachment);
		$mail = new PHPMailer();
		$mail->From = $from;
		$mail->FromName = $fromName; //from name name that will displayed when sending mail
		
		//loop to all recipients
		$ctr = 0;
		while($ctr<count($recipientArray)){
			$mail->AddAddress($recipientArray[$ctr]);
			$ctr++;
		}
		//loop for all CC
		$ctr = 0;
		while($ctr<count($ccArray)){
			$mail->AddCC($ccArray[$ctr]);
			$ctr++;
		}
		//$mail->AddBCC("marlope@pricon.ph");
		//$mail->AddReplyTo("marlope@pricon.com", "Information"); //reply goes thru here
		$mail->WordWrap = 50;                             // set word wrap to 50 characters
		/*
		$ctr = 0;
		while($ctr<count($attachment)){	//reserved for attachments;
			$mail->AddAttachment($attachment[$ctr]);
			$ctr++;
		}
		*/
		//$mail->AddAttachment("./testAttach2.txt");        // add attachments
		$mail->IsHTML(true);    // set email format to HTML
		$mail->Subject = $subject;
		$mail->Body    = $message;

		//validation if mail has been sent
		if(!$mail->Send())
		{
		   echo "Message could not be sent. <p>";
		   echo "Mailer Error: " . $mail->ErrorInfo;
		   exit;
		}else{
			echo "Message Sent!. <p>";
			header("Location: http://systemone"); /* Redirect browser */
		}
	}
}
?>
    