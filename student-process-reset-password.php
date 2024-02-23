<?php
$token = $_POST["token"];
$password = $_POST["password"];


$host = "https://kare-quiz.alphadevsx.com";
$dbname = "u475858870_quiz";
$username = "u475858870_root";
$dbPassword = "Kalasalingam@339";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $token_hash = hash("sha256", $token);

    $sql = "SELECT * FROM student_db
            WHERE reset_token_hash = :token_hash";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':token_hash', $token_hash, PDO::PARAM_STR); 
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
        die("Token not found");
    }

    if (isset($user["reset_token_expires_at"]) && strtotime($user["reset_token_expires_at"]) <= time()) {
        die("Token has expired");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $update_sql = "UPDATE student_db
                   SET password = :hashed_password,
                       reset_token_hash = NULL,
                       reset_token_expires_at = NULL
                   WHERE email = :email";

    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':hashed_password', $hashed_password, PDO::PARAM_STR);
    $update_stmt->bindParam(':email', $user["email"], PDO::PARAM_INT);
    $update_stmt->execute();

    echo "Password updated. You can now <a href='index.html'>login</a>.";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
