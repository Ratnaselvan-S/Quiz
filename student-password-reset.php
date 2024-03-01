<?php

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
        require 'path/to/PHPMailer/src/Exception.php';
        require 'path/to/PHPMailer/src/PHPMailer.php';
        require 'path/to/PHPMailer/src/SMTP.php';

        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.hostinger.com"; // Update with Hostinger SMTP host
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Username = "your_email@example.com"; // Update with your Hostinger email username
        $mail->Password = "your_email_password"; // Update with your Hostinger email password
        $mail->isHTML(true);
        $mail->setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = '<p><a href="https://yourwebsite.com/student-reset-password.php?token='.$token.'">Click here</a> to reset your password.</p>';

        $mail->send();
        echo "Message sent, please check your inbox.";
    } else {
        echo "No records updated. Email not found.";
    }
} catch (PDOException $e) {
    echo "error|Connection failed: " . $e->getMessage();
} catch (Exception $e) {
    echo "error|Mailer error: " . $mail->ErrorInfo;
}
?>
