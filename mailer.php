<?php
echo "suganya";die;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer-master\src\Exception.php';
require 'PHPMailer-master\src\PHPMailer.php';
require 'PHPMailer-master\src\SMTP.php';

$mail = new PHPMailer(true);


$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = "smtp.gmail.com"; 
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
$mail->Port = 587; 
$mail->Username = "vijaykmr2422@gmail.com"; 
$mail->Password = "sdgsstvteyjydgan"; 
$mail->isHTML(true);

return $mail;
