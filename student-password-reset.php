<?php

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);


$host = "localhost";
$dbname = "u475858870_quiz";
$username = "u475858870_root";
$dbPassword = "Kalasalingam@339";
print_r($email);die;

?>
