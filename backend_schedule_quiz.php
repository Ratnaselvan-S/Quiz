<?php
session_start();

$conn = new mysqli('https://kare-quiz.alphadevsx.com', 'u475858870_root', 'Kalasalingam@339', 'u475858870_quiz');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_email = $_SESSION['user_email'];
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $quizDate = filter_var($_POST['quiz_date'], FILTER_SANITIZE_STRING);
    $startTime = filter_var($_POST['start_time'], FILTER_SANITIZE_STRING);
    $endTime = filter_var($_POST['end_time'], FILTER_SANITIZE_STRING);
    $numQuestions = filter_var($_POST['num_questions'], FILTER_VALIDATE_INT);

    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $quizDate)) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format']);
        exit();
    }

    if (!preg_match("/^\d{2}:\d{2}$/", $startTime) || !preg_match("/^\d{2}:\d{2}$/", $endTime)) {
        echo json_encode(['success' => false, 'message' => 'Invalid time format']);
        exit();
    }

    $checkTitleQuery = "SELECT * FROM scheduled_quizzes WHERE user_email = '$user_email' AND title = '$title'";
    $checkTitleResult = mysqli_query($conn, $checkTitleQuery);

    if (mysqli_num_rows($checkTitleResult) > 0) {
        echo json_encode(['success' => false, 'message' => 'Quiz with the same title already exists. Please choose another title.']);
        exit();
    }

    $startDateTime = "$quizDate $startTime";
    $endDateTime = "$quizDate $endTime";

    $code = generateQuizCode();

    $file_path_query = "SELECT file_path FROM uploaded_files WHERE user_email = '$user_email' ORDER BY upload_timestamp DESC LIMIT 1";
    $file_path_result = mysqli_query($conn, $file_path_query);

    if ($file_path_result && $file_path_row = mysqli_fetch_assoc($file_path_result)) {
        $file_path = $file_path_row['file_path'];

        if (!file_exists($file_path)) {
            echo json_encode(['success' => false, 'message' => 'CSV file not found']);
            exit();
        }

        $csvFile = fopen($file_path, 'r');
        $numOfQuestionsInCSV = 0;

        fgetcsv($csvFile);

        while (($row = fgetcsv($csvFile)) !== false) {
            $numOfQuestionsInCSV++;
        }

        fclose($csvFile);

        if ($numQuestions <= $numOfQuestionsInCSV) {
            mysqli_begin_transaction($conn);

            $insertQuery = "INSERT INTO scheduled_quizzes (user_email, title, quiz_date, start_time, end_time, code, file_path, number_of_questions) 
                            VALUES ('$user_email', '$title', '$quizDate', '$startDateTime', '$endDateTime', '$code', '$file_path', $numQuestions)";
            $result = mysqli_query($conn, $insertQuery);

            if ($result) {
                mysqli_commit($conn);
                echo json_encode(['success' => true, 'message' => 'Quiz scheduled successfully', 'code' => $code, 'end_time' => $endDateTime]);
            } else {
                mysqli_rollback($conn);
                echo json_encode(['success' => false, 'message' => 'Error scheduling quiz']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "There are only $numOfQuestionsInCSV questions in the CSV. Please choose a number within this range."]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error fetching file path']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);

function generateQuizCode()
{
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
}
