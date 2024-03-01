<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);


$host = "localhost";
$dbname = "u475858870_quiz";
$username = "u475858870_root";
$dbPassword = "Kalasalingam@339";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "UPDATE student_db
            SET reset_token_hash = :token_hash,
                reset_token_expires_at = :expiry
            WHERE email = :email";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':token_hash', $token_hash);
    $stmt->bindValue(':expiry', $expiry);
    $stmt->bindValue(':email', $email);

    $stmt->execute();

    if ($stmt->rowCount()) {
        // $mail = require __DIR__ . "/mailer.php";
        // $mail = "./mailer.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception; 
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
        $mail->setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        // $mail->Body = <<<END
        // Click <a href="https://kare-quiz.alphadevsx.com/student-reset-password.php?token=$token">here</a> 
        // to reset your password.
        // END;
        $mail->Body = '<p><a href="https://kare-quiz.alphadevsx.com/student-reset-password.php?token=$token">here</a>to reset your password.</p>';
        $mail->send();
        // try {
        //     $mail->send();
        //     echo "ratna2";print_r($mail->send());
        //     echo "Message sent, please check your inbox.";
        // } catch (Exception $e) {
        //     echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        // }
    }

    echo "Message sent, please check your inbox.";
} catch (PDOException $e) {
    echo "error|Connection failed: " . $e->getMessage();
}

?>
