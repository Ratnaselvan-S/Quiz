<?php

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$host = "alphadevsx.com";
$dbname = "u475858870_quiz";
$username = "u475858870_root";
$dbPassword = "Kalasalingam@339";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "UPDATE registration
            SET reset_token_hash = :token_hash,
                reset_token_expires_at = :expiry
            WHERE email = :email";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':token_hash', $token_hash);
    $stmt->bindValue(':expiry', $expiry);
    $stmt->bindValue(':email', $email);

    $stmt->execute();

    if ($stmt->rowCount()) {
        $mail = require __DIR__ . "/mailer.php";

        $mail->setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END
        Click <a href="http://localhost/vijay/reset-password.php?token=$token">here</a> 
        to reset your password.
        END;

        try {
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
    }

    echo "Message sent, please check your inbox.";
} catch (PDOException $e) {
    echo "error|Connection failed: " . $e->getMessage();
}
?>