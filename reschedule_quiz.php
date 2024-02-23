<?php
session_start();

$conn = new mysqli('https://kare-quiz.alphadevsx.com', 'u475858870_root', 'Kalasalingam@339', 'u475858870_quiz');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_email = $_SESSION['user_email'];
    $new_quiz_date = filter_input(INPUT_POST, 'new_quiz_date', FILTER_SANITIZE_STRING);
    $new_start_time = filter_input(INPUT_POST, 'new_start_time', FILTER_SANITIZE_STRING);
    $new_end_time = filter_input(INPUT_POST, 'new_end_time', FILTER_SANITIZE_STRING);
    $current_title = filter_input(INPUT_POST, 'current_title', FILTER_SANITIZE_STRING);
    $new_number_of_questions = filter_input(INPUT_POST, 'new_number_of_questions', FILTER_VALIDATE_INT);

    if (
        !validateDateFormat($new_quiz_date) ||
        !validateTimeFormat($new_start_time) ||
        !validateTimeFormat($new_end_time) ||
        $new_number_of_questions === false
    ) {
        echo 'Invalid date, time, or number of questions format.';
        exit;
    }

    if (!isFutureDateTime($new_quiz_date, $new_start_time)) {
        echo 'The entered value is in the past. Please select a future time.';
        exit;
    }

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

        if ($new_number_of_questions <= $numOfQuestionsInCSV) {
            $new_startDateTime = "$new_quiz_date $new_start_time";
            $new_endDateTime = "$new_quiz_date $new_end_time";
            $new_code = generateQuizCode();

            $update_query = "UPDATE scheduled_quizzes SET quiz_date=?, start_time=?, end_time=?, code=?, number_of_questions=? WHERE user_email=? AND title=?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssssiss", $new_quiz_date, $new_startDateTime, $new_endDateTime, $new_code, $new_number_of_questions, $user_email, $current_title);

            if ($stmt->execute()) {
                echo '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Reschedule Quiz: ' . $current_title . '</title>
                    <style>
                         body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: #f5f5f5;
                }

                .card {
                    background-color: #fff;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    border: 2px solid black;
                    background-color: rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                    padding: 20px;
                    width: 50%;
                    text-align: left;
                    transition: transform 0.3s ease;
                }

                .card h2 {
                    margin-bottom: 20px;
                    border-bottom: 2px solid black;
                    color: black;
                }

                .card p {
                    margin-bottom: 10px;
                }

                .form-label {
                    margin-bottom: 5px;
                    color: black;
                }

                .form-input {
                    padding: 10px;
                    width: 100%;
                    margin-bottom: 15px;
                    box-sizing: border-box;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }

                .form-submit {
                    background-color: black;
                    color: white;
                    padding: 10px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-left: 40%;
                    transition: background-color 0.3s ease;
                }

                .form-submit:hover {
                    background-color: white;
                    border: 2px solid black;
                    color: black;
                }

                #copyCodeBtn {
                    background-color: #2ecc71;
                    color: white;
                    padding: 10px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                #copyCodeBtn:hover {
                    background-color: white;
                }

                .success-message {
                    background-color: rgba(0, 0, 0, 0.1);
                    color: black;
                    padding: 10px;
                    border-radius: 5px;
                    margin-top: 20px;
                    text-align: center;
                }

                .success-message p {
                    margin: 10px 0;
                }

                .success-message #copyCodeBtn {
                    background-color: black;
                    color: white;
                    padding: 10px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                .success-message #copyCodeBtn:hover {
                    background-color: white;
                    color:black;
                    border:2px solid black;
                }
                .error-message {
                    color: red;
                }
                    </style>
                </head>
                <body>
                    <div class="card">
                        <div class="success-message">
                            <p>Quiz rescheduled successfully.</p>
                            <p>New Code: ' . $new_code . '</p>
                            <button id="copyCodeBtn" onclick="copyCodeToClipboard()">Copy Code</button>
                            <p id="copyMessage"></p>
                        </div>
                    </div>
                    <script>
                        // Add your script here
                        function copyCodeToClipboard() {
                            var codeToCopy = "' . $new_code . '";
                            navigator.clipboard.writeText(codeToCopy)
                                .then(function () {
                                    alert("Code successfully copied!");
                                    // Redirect to dashboard.php
                                    window.location.href = "dashboard.php";
                                })
                                .catch(function (err) {
                                    console.error("Unable to copy code", err);
                                });
                        }
                    </script>
                </body>
                </html>';
            } else {
                echo 'Error rescheduling quiz: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            echo '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Error Rescheduling Quiz</title>
                    <style>
                        .error-container {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            margin: 0;
                            background-color: #f5f5f5;
                        }

                        .error-card {
                            background-color: #fff;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                            border: 2px solid black;
                            background-color: rgba(0, 0, 0, 0.1);
                            border-radius: 8px;
                            padding: 20px;
                            width: 50%;
                            text-align: center;
                            transition: transform 0.3s ease;
                        }

                        .error-card h2 {
                            margin-bottom: 20px;
                            border-bottom: 2px solid black;
                            color: black;
                        }

                        .error-message {
                            color: red;
                            margin-bottom: 20px;
                        }

                        .available-questions {
                            margin-bottom: 20px;
                        }

                        .back-link {
                            text-decoration: none;
                            color: black;
                            background-color: #2ecc71;
                            padding: 10px 20px;
                            border-radius: 5px;
                            transition: background-color 0.3s ease;
                        }

                        .back-link:hover {
                            background-color: white;
                            border: 2px solid black;
                        }
                    </style>
                </head>
                <body>
                    <div class="error-container">
                        <div class="error-card">
                            <h2>Error Rescheduling Quiz</h2>
                            <p class="error-message">The entered number of questions exceeds the available questions in the CSV file.</p>
                            <p class="available-questions">Available Questions in CSV: ' . $numOfQuestionsInCSV . '</p>
                             <a href="javascript:history.back()" class="back-link">Back to Previous Page</a>
                        </div>
                    </div>
                </body>
                </html>';
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error fetching file path']);
    }
} else {
    $user_email = $_SESSION['user_email'];
    $title = urldecode($_GET['title']);

    $query = "SELECT * FROM scheduled_quizzes WHERE user_email='$user_email' AND title='$title'";
    $result = mysqli_query($conn, $query);

    if ($result && $quiz = mysqli_fetch_assoc($result)) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reschedule Quiz: ' . $title . '</title>
            <style>
                 body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: #f5f5f5;
                }

                .card {
                    background-color: #fff;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    border: 2px solid black;
                    background-color: rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                    padding: 20px;
                    width: 50%;
                    text-align: left;
                    transition: transform 0.3s ease;
                }

                .card h2 {
                    margin-bottom: 20px;
                    border-bottom: 2px solid black;
                    color: black;
                }

                .card p {
                    margin-bottom: 10px;
                }

                .form-label {
                    margin-bottom: 5px;
                    color: black;
                }

                .form-input {
                    padding: 10px;
                    width: 100%;
                    margin-bottom: 15px;
                    box-sizing: border-box;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }

                .form-submit {
                    background-color: black;
                    color: white;
                    padding: 10px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-left: 40%;
                    transition: background-color 0.3s ease;
                }

                .form-submit:hover {
                    background-color: white;
                    border: 2px solid black;
                    color: black;
                }

                #copyCodeBtn {
                    background-color: #2ecc71;
                    color: white;
                    padding: 10px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                #copyCodeBtn:hover {
                    background-color: white;
                }
            </style>
        </head>
        <body>
            <div class="card">
                <h2>Reschedule Quiz: ' . $title . '</h2>
                <p><strong>Current Details:</strong></p>
                <p>Quiz Date: ' . $quiz['quiz_date'] . '</p>
                <p>Start Time: ' . $quiz['start_time'] . '</p>
                <p>End Time: ' . $quiz['end_time'] . '</p>
                <p>Number of Questions: ' . $quiz['number_of_questions'] . '</p>

                <!-- Display a form for rescheduling -->
                <form method="post" action="reschedule_quiz.php">
                    <label class="form-label" for="new_quiz_date">New Quiz Date:</label>
                    <input class="form-input" type="date" name="new_quiz_date" required>

                    <label class="form-label" for="new_start_time">New Start Time:</label>
                    <input class="form-input" type="time" name="new_start_time" required>

                    <label class="form-label" for="new_end_time">New End Time:</label>
                    <input class="form-input" type="time" name="new_end_time" required>

                    <label class="form-label" for="new_number_of_questions">Number of Questions:</label>
                    <input class="form-input" type="number" name="new_number_of_questions" required>

                    <!-- Add a hidden field to pass the current title -->
                    <input type="hidden" name="current_title" value="' . $title . '">

                    <input class="form-submit" type="submit" class="rescheduleb" value="Reschedule">
                </form>
            </div>
        </body>
        </html>';
    } else {
        echo 'Error fetching quiz details';
    }
}

mysqli_close($conn);

function validateDateFormat($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function validateTimeFormat($time)
{
    $t = DateTime::createFromFormat('H:i', $time);
    return $t && $t->format('H:i') === $time;
}

function isFutureDateTime($date, $time)
{
    $dateTime = strtotime("$date $time");
    $currentTime = time();
    return $dateTime > $currentTime;
}

function generateQuizCode()
{
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
}
