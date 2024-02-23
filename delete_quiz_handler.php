<?php
session_start();

$conn = new mysqli('alphadevsx.com', 'u475858870_root', 'Kalasalingam@339', 'u475858870_quiz');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_email']) && isset($_GET['title'])) {
    $user_email = $_GET['user_email'];
    $title = $_GET['title'];

    $query = "DELETE FROM scheduled_quizzes WHERE user_email='$user_email' AND title='$title'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        header('Location: edit_scheduled_quizzes.php');
        exit;
    } else {
        echo 'Error deleting quiz';
    }
} else {
    echo 'Invalid request';
}

mysqli_close($conn);
