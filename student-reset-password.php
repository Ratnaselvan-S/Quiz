<?php
$token = $_GET["token"];

$token_hash = hash("sha256", $token);


                                                    $host = "alphadevsx.com";
                                                    $dbname = "u475858870_quiz";
                                                    $username = "u475858870_root";
                                                    $dbPassword = "Kalasalingam@339";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>Reset Password</title>
</head>

<body>

    <h1>Reset Password</h1>

    <form method="post" action="student-process-reset-password.php">

        <input type="hidden" name="token" value="<?= isset($token) ? htmlspecialchars($token) : '' ?>">

        <label for="password">New password</label>
        <input type="password" id="password" name="password" required>

        <label for="password_confirmation">Repeat password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>

        <button type="submit">Reset Password</button>
    </form>

</body>

</html>