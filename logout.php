<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

if (isset($_POST['logout'])) {
    $_SESSION = array();

    session_destroy();

    echo 'Logout successful';
    exit();
} else {
    header("Location: login.html");
    exit();
}
