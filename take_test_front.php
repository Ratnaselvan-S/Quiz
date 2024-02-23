<?php
session_start(); // Start the session

function validateCode($pdo, $code, $email)
{
    // Fetch quiz details based on the provided code
    $stmt = $pdo->prepare("SELECT * FROM scheduled_quizzes WHERE code = ?");
    $stmt->execute([$code]);
    $quizDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($quizDetails) {
        $currentDateTime = new DateTime("now", new DateTimeZone("Asia/Kolkata"));
        $currentDate = $currentDateTime->format('Y-m-d');
        $quizStartTime = new DateTime($quizDetails['start_time'], new DateTimeZone("Asia/Kolkata"));
        $quizEndTime = new DateTime($quizDetails['end_time'], new DateTimeZone("Asia/Kolkata"));
        $quizDate = $quizDetails['quiz_date'];

        if ($currentDate < $quizDate) {
            return "Quiz is scheduled for a later date: $quizDate";
        } elseif ($currentDate > $quizDate) {
            return "Quiz was scheduled for a past date: $quizDate";
        } elseif ($currentDateTime < $quizStartTime) {
            $interval = $currentDateTime->diff($quizStartTime);
            $timeRemaining = $interval->format('%H:%I:%S');
            return "Quiz will start on $quizDate at {$quizDetails['start_time']}. Time remaining: $timeRemaining";
        } elseif ($currentDateTime > $quizEndTime) {
            return "Quiz ended on $quizDate at {$quizDetails['end_time']}.";
        } elseif ($currentDateTime < $quizEndTime && $currentDateTime > $quizStartTime) {
            // Check if the user has already attended the quiz with the given code
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM quiz_attendance WHERE email = ? AND code = ?");
            $stmt->execute([$email, $code]);
            $attendanceCount = $stmt->fetchColumn();

            if ($attendanceCount > 0) {
                return "You have already completed this quiz.";
            }

            // Redirect user to test_page.php
            header("Location: test_page.php?code=$code");
            exit();
        }
    }

    return "Invalid code or quiz not started.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST["code"];
    $email = $_SESSION['user_email']; // Fetch user email from session
    $error = "";

    try {

        $host = "localhost";
        $dbname = "u475858870_quiz";
        $username = "u475858870_root";
        $dbPassword = "Kalasalingam@339";

        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Validate code
        if (empty($code)) {
            $error = "Please enter a code.";
        } else {
            $error = validateCode($pdo, $code, $email);
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Entry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        #quizCard {
            width: 300px;
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        #quizCard h2 {
            margin-top: 0;
            color: #333;
        }

        #codeInput {
            width: calc(100% - 40px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        #takeTestBtn {
            width: calc(100% - 40px);
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #takeTestBtn:hover {
            background-color: #0056b3;
        }

        .error {
            color: #ff0000;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div id="quizCard">
        <h2>Quiz Entry</h2>
        <form id="quizForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="email" value="<?php echo $_SESSION['user_email']; ?>">
            <input type="text" name="code" id="codeInput" maxlength="6" placeholder="Enter 6-letter code">
            <button type="submit" id="takeTestBtn">Take Test</button>
        </form>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($error)) : ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>

    <script>
        
        let tabOpened = false;

        // Check if any other tab is opened in the same browser
        window.onblur = function() {
            tabOpened = true;
        };

        window.onfocus = function() {
            tabOpened = false;
        };

        // Prevent form submission if another tab is open
        document.getElementById('quizForm').onsubmit = function(event) {
            if (tabOpened) {
                event.preventDefault();
                alert("Please close any other tabs before proceeding.");
            }
        };
    </script>
</body>

</html>