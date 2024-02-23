<?php
session_start();

$conn = new mysqli('https://kare-quiz.alphadevsx.com', 'u475858870_root', 'Kalasalingam@339', 'u475858870_quiz');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['title'])) {
    $user_email = $_SESSION['user_email'];
    $title = urldecode($_GET['title']);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete Quiz</title>
    </head>

    <body>
        <script>
            var result = confirm('Are you sure you want to delete this quiz?');
            if (result) {
                window.location.href = 'delete_quiz_handler.php?user_email=<?= $user_email ?>&title=<?= $title ?>';
            } else {
                window.location.href = 'edit_scheduled_quizzes.php';
            }
        </script>
    </body>

    </html>
<?php
} else {
    echo 'Invalid request';
}

mysqli_close($conn);
?>