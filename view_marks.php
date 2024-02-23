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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the quiz code from the form
    $code = $_POST['code'];

    try {
        // Prepare and execute the SQL query to fetch quiz attendance records for the given code
        $stmt = $pdo->prepare("SELECT * FROM quiz_attendance WHERE code = ?");
        $stmt->execute([$code]);
        $quizRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch quiz schedule based on the entered code
        $stmt = $pdo->prepare("SELECT quiz_date, end_time FROM scheduled_quizzes WHERE code = ?");
        $stmt->execute([$code]);
        $Schedule = $stmt->fetch(PDO::FETCH_ASSOC);


        // If there are no records found, display an error message
        if (empty($quizRecords)) {
            $errorMessage = "Invalid code. Please check the code.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
// Set the time zone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');
$showMarksButton = false;
if (!empty($Schedule)) {
    $dateTimeNow = new DateTime('now', new DateTimeZone('Asia/Kolkata'));  // Current date and time
    $quizEndDateTime = new DateTime($Schedule['quiz_date'] . ' ' . $Schedule['end_time']); // End date and time of the quiz

    if ($dateTimeNow >= $quizEndDateTime) {
        $showMarksButton = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quiz Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"] {
            padding: 8px;
            margin: 0 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        button[type="submit"] {
            padding: 8px 20px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: #f00;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>View Quiz Attendance</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="code">Enter Quiz Code:</label>
            <input type="text" id="code" name="code">
            <button type="submit">Search</button>
        </form>

        <?php if (isset($errorMessage)) : ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if (!empty($quizRecords)) : ?>
            <h3>Quiz Attendance Records for Code: <?php echo $code; ?></h3>
            <table>
                <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Register ID</th>
                    <th>Section</th>
                    <th>Stream</th>
                    <th>Title</th>
                    <th>Marks</th>
                </tr>
                <?php foreach ($quizRecords as $record) : ?>
                    <tr>
                        <td><?php echo $record['email']; ?></td>
                        <td><?php echo $record['name']; ?></td>
                        <td><?php echo $record['register']; ?></td>
                        <td><?php echo $record['section']; ?></td>
                        <td><?php echo $record['stream']; ?></td>
                        <td><?php echo $record['title']; ?></td>
                        <td><?php echo $record['marks']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <?php if ($showMarksButton && !empty($quizRecords)) : ?>
                <div style="text-align: center; margin-top: 20px;">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="code" value="<?php echo $code; ?>">
                        <?php
                        // Fetch current status for the quiz records with the entered code
                        $statusStmt = $pdo->prepare("SELECT status FROM quiz_attendance WHERE code = ?");
                        $statusStmt->execute([$code]);
                        $currentStatus = $statusStmt->fetchColumn();

                        // Determine the color and text based on the current status
                        $statusColor = ($currentStatus === 'yes') ? 'red' : 'green';
                        $statusText = ($currentStatus === 'yes') ? 'Hide Marks from Students' : 'Show Marks to Students';
                        ?>
                        <button type="submit" name="show_marks_to_students" style="background-color: <?php echo $statusColor; ?>"><?php echo $statusText; ?></button>
                    </form>
                    <a href="dashboard.php"><button>Go to Dashboard</button></a>
                </div>
            <?php else : ?>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="dashboard.php"><button>Go to Dashboard</button></a>
                </div>

            <?php endif; ?>
        <?php endif; ?>
    </div>


    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['show_marks_to_students'])) {
        // Get the quiz code from the form
        $code = $_POST['code'];

        try {
            // Check the current status for the quiz records with the entered code
            $statusStmt = $pdo->prepare("SELECT status FROM quiz_attendance WHERE code = ?");
            $statusStmt->execute([$code]);
            $currentStatus = $statusStmt->fetchColumn();

            // Toggle the status for the quiz records with the entered code
            $newStatus = ($currentStatus === 'yes') ? 'no' : 'yes';
            $updateStmt = $pdo->prepare("UPDATE quiz_attendance SET status = ? WHERE code = ?");
            $updateStmt->execute([$newStatus, $code]);

            if ($updateStmt->rowCount() > 0) {
                // If records are updated successfully, show success message
                $_SESSION['success_message'] = "The changes you made have been applied. To change again, Enter the code and make changes.";
            } else {
                // If no records are updated, show error message
                $_SESSION['error_message'] = "Failed to update status. Please try again.";
            }

            // Redirect to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        // Display success or error messages
        if (isset($_SESSION['success_message'])) {
            echo '<p style="color: green; text-align: center;">' . $_SESSION['success_message'] . '</p>';
            unset($_SESSION['success_message']); // Clear the success message
        }

        if (isset($_SESSION['error_message'])) {
            '<p style="color: red; text-align: center;">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']); // Clear the error message
        }
    }
    ?>
</body>

</html>