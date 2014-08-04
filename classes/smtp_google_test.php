<?php

/*
 * How to setup your sendmail for sending mail via gmail relay
 *
 	yum install sendmail
 	vim /etc/mail/sendmail.mc
 	Add following one by one:

 	define(`SMART_HOST', `smtp.gmail.com')dnl
	define(`RELAY_MAILER_ARGS', `TCP $h 587')
	define(`ESMTP_MAILER_ARGS', `TCP $h 587')

	define(`confAUTH_MECHANISMS', `EXTERNAL GSSAPI DIGEST-MD5 CRAM-MD5 LOGIN PLAIN')dnl

	EXPOSED_USER(`root')dnl
	FEATURE(`authinfo',`hash /etc/mail/auth/client-info')dnl

	Save the file and exit

	mkdir -p /etc/mail/auth
	chmod 700 /etc/mail/auth
	cat /etc/mail/auth/client-info

	Add following in client-info file:
	AuthInfo:smtp.gmail.com "U:root" "I:rockmetoo" "P:password" "M:PLAIN"
	AuthInfo:smtp.gmail.com:587 "U:root" "I:rockmetoo" "P:password" "M:PLAIN"

	Save and exit
	chmod 600 client-info
	makemap -r hash client-info.db < client-info
	cd /etc/mail
	m4 sendmail.mc > sendmail.cf
	service sendmail restart

 */

include "class.phpmailer.php";

$mail = new PHPMailer();
$mail->IsSMTP(); // set mailer to use SMTP
$mail->SMTPDebug = 2;
$mail->Host = "smtp.gmail.com"; // specify main and backup server
$mail->Port = 465; // set the port to use
$mail->SMTPAuth = true; // turn on SMTP authentication
$mail->SMTPSecure = "ssl";
$mail->Username = "rockmetoo@gmail.com"; // your SMTP username or your gmail username
$mail->Password = "password"; // your SMTP password or your gmail password
$from = "rockmetoo@gmail.com"; // Reply to this email
$to = "rockmetoo@gmail.com"; // Recipients email ID
$name = "Tanvir Shahid"; // Recipient's name
$mail->From = $from;
$mail->FromName = "OCRM"; // Name to indicate where the email came from when the recepient received
$mail->AddAddress($to, $name);
$mail->AddReplyTo($from, "OCRM");
$mail->WordWrap = 50; // set word wrap
$mail->IsHTML(true); // send as HTML
$mail->Subject = "Sending Email From Php Using Gmail";
$mail->Body = "This Email Send through phpmailer, This is the HTML BODY "; //HTML Body
$mail->AltBody = "This is the body when user views in plain text format"; //Text Body
if(!$mail->Send()){
	echo "Mailer Error: " . $mail->ErrorInfo;
}else{
	echo "Message has been sent";
}