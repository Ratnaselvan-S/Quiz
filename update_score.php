<?php
session_start(); // Start the session

// Check if the quiz submission flag is set in the session
if (isset($_SESSION['quiz_submitted']) && $_SESSION['quiz_submitted'] === true) {
    // Quiz has already been submitted, redirect the user
    header("Location: student_dashboard.php");
    exit;
}

if (isset($_SESSION['user_email'])) {
    // Fetch user email from session
    $email = $_SESSION['user_email'];

    // Fetch user information from student_info table
    try {
        $host = "localhost";
        $dbname = "quiz";
        $username = "root";
        $dbPassword = "";

        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmtUserInfo = $pdo->prepare("SELECT register, name, section, stream FROM student_info WHERE email = ?");
        $stmtUserInfo->execute([$email]);
        $userInfo = $stmtUserInfo->fetch(PDO::FETCH_ASSOC);

        if ($userInfo) {
            $register = $userInfo['register'];
            $name = $userInfo['name'];
            $section = $userInfo['section'];
            $stream = $userInfo['stream'];

            // Retrieve the code from the URL parameter
            $code = isset($_GET['code']) ? $_GET['code'] : '';

            // Fetch title from scheduled_quizzes table
            $stmt = $pdo->prepare("SELECT title FROM scheduled_quizzes WHERE code = ?");
            $stmt->execute([$code]);
            $titleData = $stmt->fetch(PDO::FETCH_ASSOC);
            $title = $titleData['title'];

            // Insert quiz attendance into quiz_attendance table
            $stmt = $pdo->prepare("INSERT INTO quiz_attendance (email, name, register, section, stream, code, title, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([$email, $name, $register, $section, $stream, $code, $title, 0]); // Initialize marks as 0

            // Set the session variable flag to indicate that the quiz has been submitted
            $_SESSION['quiz_submitted'] = true;

            // Redirect to student dashboard after successful recording
            header("Location: student_dashboard.php");
            exit; // Make sure to exit after the redirect
        } else {
            echo "Error: User information not found.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    echo "Error: User email not found in session.";
}
