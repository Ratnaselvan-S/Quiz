<?php
session_start(); // Start the session

if (isset($_POST['quizData'])) {
    $quizData = json_decode($_POST['quizData'], true);

    try {
        $host = "localhost";
        $dbname = "u475858870_quiz";
        $username = "u475858870_root";
        $dbPassword = "Kalasalingam@339";

        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch user email from session
        if (isset($_SESSION['user_email'])) {
            $email = $_SESSION['user_email'];

            // Fetch user information from student_info table
            $stmt = $pdo->prepare("SELECT register, name, section, stream FROM student_info WHERE email = ?");
            $stmt->execute([$email]);
            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userInfo) {
                $registerId = $userInfo['register'];
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

                $totalQuestions = count($quizData);
                $totalMarks = 0;

                foreach ($quizData as $questionIndex => $questionData) {
                    $userAnswer = isset($_POST['question_' . ($questionIndex + 1)]) ? $_POST['question_' . ($questionIndex + 1)] : null;
                    $correctAnswer = $questionData['CorrectOption1'];

                    if (strtoupper($questionData['type']) == 'MULTIPLE') {
                        $totalCorrectOptions = count(array_filter(explode(",", $correctAnswer)));
                        $selectedCorrectOptions = count(array_intersect($userAnswer, explode(",", $correctAnswer)));
                        if ($selectedCorrectOptions == $totalCorrectOptions) {
                            $totalMarks += 1;
                        } else {
                            $totalMarks += $selectedCorrectOptions / $totalCorrectOptions;
                        }
                    } else {
                        if ($userAnswer === $correctAnswer) {
                            $totalMarks += 1;
                        }
                    }
                }

                if ($totalQuestions > 0) {
                    $userScore = ($totalMarks / $totalQuestions) * 100;

                    // Execute the SQL query to insert quiz attendance
                    $stmt->execute([$email, $name, $registerId, $section, $stream, $code, $title, $userScore]);

                    // Set the session variable flag to indicate that the quiz has been submitted
                    $_SESSION['quiz_submitted'] = true;

                    // Redirect the user to another page after successful quiz attendance recording
                    header("Location: student_dashboard.php");
                    exit; // Make sure to exit after the redirect
                } else {
                    echo "Error: No quiz data received.";
                }
            } else {
                echo "Error: User information not found.";
            }
        } else {
            echo "Error: User email not found in session.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    echo 'Error: No quiz data received.';
}
