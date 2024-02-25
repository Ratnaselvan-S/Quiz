<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=u475858870_quiz", "u475858870_root", "", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

return $pdo;
?>