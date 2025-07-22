<?php
require_once __DIR__ . '/../vendor/PHPMailer-6.10.0/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer-6.10.0/src/SMTP.php';
require_once __DIR__ . '/../vendor/PHPMailer-6.10.0/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send Email function
 * 
 * @param array $email_details An array of the reqired email details
 * 
 * @return bool
 */
function sendMail($email_details):bool
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.zoho.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'williamsondivine82@zohomail.com';                     //SMTP username
        $mail->Password   = 'Onyinye@56$';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        // Disable SSL verification for testing connectivity
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
    
        //Recipients
        $mail->setFrom('williamsondivine82@zohomail.com', 'no-reply');
        $mail->addAddress($email_details["email"], $email_details["name"]);     //Add a recipient
        //$mail->addAddress($email_details["name"]);               //Name is optional
        $mail->addReplyTo('williamsondivine82@zohomail.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');
    
        //Attachments
       // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $email_details["subject"];
        $mail->Body    = $email_details["message"];
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
        $send = $mail->send();
        return $send ? true : false;
    } catch (Exception $e) {
        return false;
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}