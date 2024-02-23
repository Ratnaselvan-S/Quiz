<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_path'])) {
    $user_email = $_SESSION['user_email'];
    $file_path = $_POST['file_path'];

    $conn = new mysqli('https://kare-quiz.alphadevsx.com', 'u475858870_root', 'Kalasalingam@339
', 'u475858870_quiz');
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }

    $stmt_schedule = $conn->prepare("SELECT * FROM scheduled_quizzes WHERE user_email = ? AND file_path = ?");
    $stmt_schedule->bind_param("ss", $user_email, $file_path);
    $stmt_schedule->execute();
    $result_schedule = $stmt_schedule->get_result();

    if ($result_schedule->num_rows > 0) {
        echo '<script>alert("Cannot delete. Quizzes are scheduled for this file.");</script>';
        $stmt_schedule->close();
        $conn->close();

        echo '<script>setTimeout(function() { window.location.href = "schedulequiz.php"; }, 2000);</script>';
        exit();
    }

    if (unlink($file_path)) {
        $stmt_delete = $conn->prepare("DELETE FROM uploaded_files WHERE user_email = ? AND file_path = ?");
        $stmt_delete->bind_param("ss", $user_email, $file_path);
        $stmt_delete->execute();
        $stmt_delete->close();
        $conn->close();

        echo '<script>alert("File deleted successfully.");</script>';

        header("Location: schedulequiz.php");
        exit();
    } else {
        echo 'Error deleting file.';
    }
} else {
    echo 'Invalid request.';
}
