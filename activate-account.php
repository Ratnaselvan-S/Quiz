<?php

$token = isset($_GET["token"]) ? $_GET["token"] : null;

if (empty($token)) {
    die("Invalid token");
}

$token_hash = hash("sha256", $token);

$pdo = require __DIR__ . "/database.php";

try {
    $sqlSelect = "SELECT * FROM registration WHERE account_activation_hash = ?";
    $stmtSelect = $pdo->prepare($sqlSelect);
    $stmtSelect->bindParam(1, $token_hash);
    $stmtSelect->execute();

    $user = $stmtSelect->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Invalid token or user not found");
    }

    $sqlUpdate = "UPDATE registration SET account_activation_hash = NULL WHERE email = ?";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(1, $user["email"]); 
    $stmtUpdate->execute();

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activated</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Account Activated</h1>
    <p>Account activated successfully. You can now <a href="login.html">log in</a>.</p>
</body>
</html>
HTML;
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
