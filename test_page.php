<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        #quiz-container {
            width: 80%;
            max-width: 800px;
            border-radius: 8px;
            padding: 20px;
        }

        .question-card {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .question-card h5 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .question-card label {
            display: inline-block;
            margin-bottom: 10px;
            margin-right: 10px;
        }

        .question-card input[type="radio"],
        .question-card input[type="checkbox"] {
            margin-right: 10px;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
            position: relative;
            display: flex;
            justify-content: space-between;
        }

        .button-container button {
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: auto;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #007bff;
            color: #fff;
        }

        #prev-button {
            position: absolute;
            left: 20px;
        }

        #next-button {
            position: absolute;
            right: 20px;
        }

        #submit-button {
            position: absolute;
            padding: 10px 20px;
            right: 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        #submit-button:hover {
            background-color: #007bff;
            color: #fff;
        }

        #clock {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #007bff;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 18px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .unselectable {
            user-select: none;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: transparent;
            pointer-events: none;
            /* Allow events to propagate through overlay */
            z-index: 9999;
            /* Ensure overlay is on top of other content */
        }
    </style>
    <link rel="icon" type="image/x-icon" href="/images/logo.jpg">
</head>

<body>
    <div class="overlay"></div>
    <div id="quiz-container">
        <?php
        // Start the session to access session variables
        session_start();
        if (!isset($_SESSION['user_email'])) {
            header("Location: index.html");
            exit();
        }

        // Check if the quiz has already been submitted
        if (isset($_SESSION['quiz_submitted']) && $_SESSION['quiz_submitted']) {
            // Redirect to the dashboard or any other page
            header("Location: student_dashboard.php");
            exit; // Make sure to exit after the redirect
        }

        // Check if the code parameter exists in the URL
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            try {

                $host = "localhost";
                $dbname = "u475858870_quiz";
                $username = "u475858870_root";
                $dbPassword = "Kalasalingam@339";

                // Connect to the database
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Prepare and execute query to fetch quiz data based on code
                $query = "SELECT file_path, number_of_questions, end_time FROM scheduled_quizzes WHERE code = :code";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':code', $code, PDO::PARAM_STR);
                $stmt->execute();
                $quizData = $stmt->fetch(PDO::FETCH_ASSOC);
                date_default_timezone_set('Asia/Kolkata');

                if ($quizData) {
                    $quizFilePath = $quizData['file_path'];
                    $numOfQuestions = $quizData['number_of_questions'];
                    $endTime = strtotime($quizData['end_time']);


                    // Check if questions are already selected for this user session
                    if (!isset($_SESSION['selected_questions'][$code])) {
                        // Fetch all questions from the file
                        $csvData = array_map('str_getcsv', file($quizFilePath));
                        $headers = array_shift($csvData);

                        if (!$headers || !$csvData) {
                            die("Error: CSV file has no headers or data.");
                        }

                        $totalQuestions = count($csvData);

                        if ($totalQuestions < $numOfQuestions) {
                            die("Error: Number of questions requested exceeds the total number of questions available in the CSV file.");
                        }

                        $selectedIndices = [];
                        while (count($selectedIndices) < $numOfQuestions) {
                            $randomIndex = mt_rand(0, $totalQuestions - 1); // Generate random index
                            if (!in_array($randomIndex, $selectedIndices)) {
                                $selectedIndices[] = $randomIndex; // Add unique index to the list
                            }
                        }

                        // Fetch selected questions based on random indices
                        $selectedQuestions = [];
                        foreach ($selectedIndices as $index) {
                            $row = $csvData[$index];
                            $questionData = [
                                'QuestionNumber' => $row[0],
                                'Question' => $row[1],
                                'type' => $row[2],
                                'options' => $row[3],
                                'CorrectOption1' => $row[4]
                            ];
                            $selectedQuestions[] = $questionData;
                        }

                        // Store selected questions in session for this user session
                        $_SESSION['selected_questions'][$code] = $selectedQuestions;
                    } else {
                        // Retrieve selected questions from session
                        $selectedQuestions = $_SESSION['selected_questions'][$code];
                    }

                    // Display the quiz form as cards with Next, Previous, and Submit buttons
                    echo '<form id="quiz-form" method="post" action="process_test.php?code=' . htmlspecialchars($_GET['code']) . '" onsubmit="return submitQuiz()">';
                    echo '<input type="hidden" name="quizData" value="' . htmlspecialchars(json_encode($selectedQuestions)) . '">';

                    // Display questions as cards
                    foreach ($selectedQuestions as $questionIndex => $questionData) {
                        echo '<div id="question-card-' . $questionIndex . '" class="question-card unselectable" style="display: ' . ($questionIndex > 0 ? 'none' : 'block') . '">';
                        echo '<h5>' . ($questionIndex + 1) . '. ' . htmlspecialchars($questionData['Question']) . '</h5>';
                        $options = explode(",", $questionData['options']);
                        foreach ($options as $optionIndex => $option) {
                            echo '<div class="form-check">';
                            if (strtoupper($questionData['type']) == 'MULTIPLE') {
                                echo '<input type="checkbox" class="form-check-input unselectable" name="question_' . ($questionIndex + 1) . '[]" value="' . htmlspecialchars($option) . '">';
                            } elseif (strtoupper($questionData['type']) == 'SINGLE' || strtoupper($questionData['type']) == 'TRUEFALSE') {
                                echo '<input type="radio" class="form-check-input unselectable" name="question_' . ($questionIndex + 1) . '" value="' . htmlspecialchars($option) . '">';
                            }
                            echo '<label class="form-check-label unselectable">' . htmlspecialchars($option) . '</label>';
                            echo '</div>';
                        }
                        echo '</div>';
                    }

                    // Display Previous, Next, and Submit buttons
                    echo '<div class="button-container">';
                    echo '<button id="prev-button" type="button" class="btn btn-secondary left" onclick="showPrevQuestion()" style="display: none;">Previous</button>';
                    echo '<button id="next-button" type="button" class="btn btn-primary right" onclick="showNextQuestion()">Next</button>';
                    echo '<input type="submit" id="submit-button" value="Submit" onclick="document.getElementById("submit-button").disabled = true; class="btn btn-primary right" style="display: none;">';
                    echo '</div>';
                    echo '</form>';
                } else {
                    echo "File path or number_of_questions not found for the given code.";
                }
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        } else {
            echo '<p>No code provided.</p>';
        }
        ?>
    </div>
    <div id="clock"></div>
    <script>
        let currentQuestionIndex = 0;
        const totalQuestions = <?php echo count($selectedQuestions); ?>;
        let tabSwitchCount = 0;

        if (window.history && window.history.pushState) {
            window.addEventListener('load', function() {
                // Prevent navigating back to the previous page
                window.history.pushState(null, '', window.location.href);
                window.onpopstate = function() {
                    window.history.pushState(null, '', window.location.href);
                };
            });
        }
        document.addEventListener("visibilitychange", function() {
            if (document.visibilityState === 'hidden') {
                tabSwitchCount++;
                if (tabSwitchCount === 3) {
                    // If the user switches tabs three times, update the score in the database with zero marks
                    window.location.href = "update_score.php?code=<?php echo $_GET['code']; ?>&reset=1";
                } else if (tabSwitchCount > 0) {
                    alert("Warning: You've switched tabs. Please return to complete the quiz.");
                }
            }
        });

        function showNextQuestion() {
            if (currentQuestionIndex < totalQuestions - 1) {
                document.getElementById('question-card-' + currentQuestionIndex).style.display = 'none';
                currentQuestionIndex++;
                document.getElementById('question-card-' + currentQuestionIndex).style.display = 'block';
                document.getElementById('prev-button').style.display = 'block';
                if (currentQuestionIndex === totalQuestions - 1) {
                    document.getElementById('next-button').style.display = 'none';
                    document.getElementById('submit-button').style.display = 'block';
                }
            }
        }

        function showPrevQuestion() {
            if (currentQuestionIndex > 0) {
                document.getElementById('question-card-' + currentQuestionIndex).style.display = 'none';
                currentQuestionIndex--;
                document.getElementById('question-card-' + currentQuestionIndex).style.display = 'block';
                document.getElementById('next-button').style.display = 'block';
                document.getElementById('submit-button').style.display = 'none';
                if (currentQuestionIndex === 0) {
                    document.getElementById('prev-button').style.display = 'none';
                }
            }
        }

        function submitQuiz() {
            const unansweredQuestions = document.querySelectorAll('.question-card:not(:has(input:checked))');
            if (unansweredQuestions.length > 0) {
                alert("Please answer all questions before submitting.");
                return false;
            } else {
                return true;
            }
        }

        var endTime = <?php echo strtotime($quizData['end_time']); ?>;
        var currentTime = Math.floor(new Date().getTime() / 1000);
        var remainingTime = Math.max(0, endTime - currentTime);

        // Function to format time
        function formatTime(seconds) {
            var hours = Math.floor(seconds / 3600);
            var minutes = Math.floor((seconds % 3600) / 60);
            var remainingSeconds = seconds % 60;
            return ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2) + ':' + ('0' + remainingSeconds).slice(-2);
        }

        // Display remaining time
        document.getElementById('clock').innerText = 'Time Remaining: ' + formatTime(remainingTime);

        // Update clock every second
        var timer = setInterval(function() {
            remainingTime--;
            if (remainingTime <= 0) {
                clearInterval(timer); // Stop the timer when time is up
                document.getElementById('clock').innerText = 'Time Up!';
                document.getElementById("quiz-form").submit(); // Auto-submit quiz when time is up
            } else {
                document.getElementById('clock').innerText = 'Time Remaining: ' + formatTime(remainingTime);
            }
        }, 1000);
    </script>

</body>

</html>