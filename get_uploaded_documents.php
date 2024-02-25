<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'quiz');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$user_email = $_SESSION['user_email'];
$stmt = $conn->prepare("SELECT * FROM registration WHERE email = ? AND unique_key IS NOT NULL");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<div class="card-container">';

    while ($row = $result->fetch_assoc()) {
        echo '<div class="card" data-file-path="' . $row['file_path'] . '">';
        echo '<h4>Document: ' . $row['unique_key'] . '</h4>';
        echo '<p>Email: ' . $row['email'] . '</p>';
        echo '<p>File Path: ' . $row['file_path'] . '</p>';
        echo '<p>Upload Timestamp: ' . $row['upload_timestamp'] . '</p>'; 
        echo '<a href="view_document.php?file_path=' . $row['file_path'] . '">View Document</a>';
        echo '</div>';
    }

    echo '</div>';
} else {
    echo '<p>No uploaded documents available.</p>';
}

$stmt->close();
$conn->close();
