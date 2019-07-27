<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
if (file_exists('../vendor/autoload.php')) {
	require '../vendor/autoload.php';
} else {
	require './vendor/autoload.php';
}

function sendmail($address, $recipient_name, $subject, $message, $file_loc, $file_name, $redirect)
{
	// Instantiation and passing `true` enables exceptions
	$mail = new PHPMailer();

	$body = '
	<!DOCTYPE html>
	<html lang="en">
	  <head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<style>
		  body {
			font-family: Arial, Helvetica, sans-serif;
			margin: 0;
		  }
		  h4 {
			margin: 0;
		  }
		  .bg-info {
			background-color: #5bc0de;
		  }
		  .text-white {
			color: white;
		  }
		  .p-1 {
			padding: 5px;
		  }
		  .p-2 {
			padding: 10px;
		  }
		</style>
	  </head>
	  <body>
		<div>
		  <h4 class="bg-info text-white p-1">S S Consulting Services LLP Alerts</h4>
		  ' . $message . '
		  <hr />
		</div>
	  </body>
	</html>
	';

	try {
		// Server settings
		// $mail->SMTPDebug = 2;                                       // Enable verbose debug output
		$mail->isSMTP();                                            // Set mailer to use SMTP
		$mail->Host       = 'smtp.gmail.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		$mail->Username   = 'thealumnet.system@gmail.com';                     // SMTP username
		$mail->Password   = 'T@NetM444';                               // SMTP password
		$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
		$mail->Port       = 587;                                    // TCP port to connect to

		//Recipients
		$mail->setFrom('thealumnet.system@gmail.com', 'Job Portal');
		$mail->addAddress($address, $recipient_name);     // Add a recipient
		// $mail->addAddress('ellen@example.com');               // Name is optional
		// $mail->addReplyTo('info@example.com', 'Information');
		// $mail->addCC('cc@example.com');
		// $mail->addBCC('bcc@example.com');

		// Attachments
		if ($file_name != null) {
			$mail->addAttachment($file_loc, strtolower($file_name) . ".pdf");    // Optional name
		}

		// Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body    = $body;
		// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		$mail->send();
		// echo 'Message has been sent';
	} catch (Exception $e) {
		// print_r("Exception: " . $e . " | " . $mail->ErrorInfo);
		return false;
	}

	if ($redirect != null) {
		header($redirect);
		return true;
	} else {
		return true;
	}
}
