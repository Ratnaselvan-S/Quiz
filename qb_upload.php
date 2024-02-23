<?php
session_start();

$upload_dir = 'uploads/';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true); 
}

if (isset($_FILES['quiz_file']) && $_FILES['quiz_file']['error'] == UPLOAD_ERR_OK) {
    $user_email = $_SESSION['user_email'];

    $original_filename = basename($_FILES['quiz_file']['name']);
    $original_filename = preg_replace('/[^a-zA-Z0-9.-]/', '', $original_filename);

    $file_type = mime_content_type($_FILES['quiz_file']['tmp_name']);
    if ($file_type != 'text/csv') {
        echo '<script>alert("Invalid file type. Please upload a CSV file."); window.location.href="generateQuiz.html";</script>';
    } else {
        $unique_key = $_SESSION['user_email'] . '_' . time(); 
        $target_path = $upload_dir . $unique_key . '.csv';

        if (move_uploaded_file($_FILES['quiz_file']['tmp_name'], $target_path)) {
            echo 'File uploaded successfully!';

            $conn = new mysqli('https://kare-quiz.alphadevsx.com', 'u475858870_root', 'Kalasalingam@339', 'u475858870_quiz');

            if ($conn->connect_error) {
                die('Connection Failed: ' . $conn->connect_error);
            }

            $stmt = $conn->prepare("INSERT INTO uploaded_files (user_email, original_file_name, unique_key, file_path, upload_timestamp) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $user_email, $original_filename, $unique_key, $target_path);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            header("Location: schedulequiz.php");
            exit();
        } else {
            die('Error uploading file.');
        }
    }
} else {
    echo 'Please select a CSV file to upload.';
}
