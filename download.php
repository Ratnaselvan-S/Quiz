<?php
session_start();

// Database connection parameters
$host = "localhost";
$dbname = "u475858870_quiz";
$username = "u475858870_root";
$dbPassword = "Kalasalingam@339";


// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch data from the database
try {
    // Get the quiz title from the URL parameter
    $title = $_GET['title'];

    // Prepare and execute the SQL query to fetch quiz attendance records for the given title
    $stmt = $pdo->prepare("SELECT * FROM quiz_attendance WHERE title = ?");
    $stmt->execute([$title]);
    $quizRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Generate CSV data
$csvData = "Email,Name,Register ID,Section,Stream,Title,Marks\n";
foreach ($quizRecords as $record) {
    $csvData .= "{$record['email']},{$record['name']},{$record['register']},{$record['section']},{$record['stream']},{$record['title']},{$record['marks']}\n";
}

// Set the headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=quiz_attendance.csv');

// Output CSV data
echo $csvData;
