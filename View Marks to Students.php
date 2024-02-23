<?php
        // Database connection parameters

        $host = "https://kare-quiz.alphadevsx.com";
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

// Start the session to access session variables
session_start();

$showForm = true; // Variable to control showing the form or the attendance record

// if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the student's code from the form
    $studentCode = $_POST['student_code'];
    $studentEmail = $_SESSION['user_email']; // Get the student's email from the session

    try {
        $stmt = $pdo->prepare("SELECT * FROM quiz_attendance WHERE code = ? AND email = ?");
        $stmt->execute([$studentCode, $studentEmail]);

        $studentRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($studentRecord)) {
            $showForm = false; // Set the flag to false if attendance record is found
        } else {
            echo "No attendance record found for your email and the entered code.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}


// if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the student's code from the form
    $studentCode = $_POST['student_code'];
    $studentEmail = $_SESSION['user_email']; // Get the student's email from the session

    try {
        $stmt = $pdo->prepare("SELECT * FROM quiz_attendance WHERE code = ? AND email = ?");
        $stmt->execute([$studentCode, $studentEmail]);

        $studentRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($studentRecord)) {
            // Check the status for the student's record
            if ($studentRecord['status'] === 'yes') {
                // Show the attendance details
                $showForm = false; // Set the flag to false as attendance record is found
            } else {
                $errorMessage = "Staff has not allowed to view marks for this quiz.";
            }
        } else {
            $errorMessage = "No attendance record found for the entered code and your email.";
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
    <title>View Attendance</title>
    <style>
        /* Add your CSS styles */
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

        .container {
            width: 80%;
            text-align: center;
        }

        .card {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 15px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .back-link,
        button[type="submit"] {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #0066cc;
        }

        button[type="submit"] {
            padding: 8px 20px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        label,
        input[type="text"] {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 style="margin-bottom: 20px;">View Your Attendance</h2>
        <?php if ($showForm) { ?>
            <div class="card">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="student_code">Enter Quiz Code:</label>
                    <input type="text" id="student_code" name="student_code">
                    <button type="submit">View</button>
                </form>
            </div>
        <?php } else { ?>
            <?php if (isset($errorMessage)) { ?>
                <div class="card error">
                    <p><?php echo $errorMessage; ?></p>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="back-link">Back</a>
                </div>
            <?php } else { ?>
                <div class="card">
                    <h3>Your attendance record:</h3>
                    <p>Email: <?php echo $studentRecord['email']; ?></p>
                    <p>Name: <?php echo $studentRecord['name']; ?></p>
                    <p>Register: <?php echo $studentRecord['register']; ?></p>
                    <p>Section: <?php echo $studentRecord['section']; ?></p>
                    <p>Stream: <?php echo $studentRecord['stream']; ?></p>
                    <p>Code: <?php echo $studentRecord['code']; ?></p>
                    <p>Title: <?php echo $studentRecord['title']; ?></p>
                    <?php if ($studentRecord['status'] === 'yes') { ?>
                        <p>Marks: <?php echo $studentRecord['marks']; ?></p>
                    <?php } else { ?>
                        <p>Staff has not allowed to view marks for this quiz.</p>
                    <?php } ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="back-link">Back</a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</body>

</html>