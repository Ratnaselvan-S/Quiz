<?php
session_start();
// Database connection parameters
$host = "localhost";
$dbname = "quiz";
$username = "root";
$dbPassword = "";

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the quiz title from the form
    $title = $_POST['title'];

    try {
        // Prepare and execute the SQL query to fetch quiz attendance records for the given title
        $stmt = $pdo->prepare("SELECT * FROM quiz_attendance WHERE title = ?");
        $stmt->execute([$title]);
        $quizRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch quiz schedule based on the entered title
        $stmt = $pdo->prepare("SELECT quiz_date, end_time FROM scheduled_quizzes WHERE title = ?");
        $stmt->execute([$title]);
        $Schedule = $stmt->fetch(PDO::FETCH_ASSOC);

        // If there are no records found, display an error message
        if (empty($quizRecords)) {
            $errorMessage = "Invalid title. Please check the title.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Toggle marks visibility
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['show_marks_to_students'])) {
    // Fetch the current status
    $statusStmt = $pdo->prepare("SELECT status FROM quiz_attendance WHERE title = ?");
    $statusStmt->execute([$title]);
    $currentStatus = $statusStmt->fetchColumn();

    // Determine the new status
    $newStatus = ($currentStatus === 'yes') ? 'no' : 'yes';

    // Update the status in the database
    $updateStmt = $pdo->prepare("UPDATE quiz_attendance SET status = ? WHERE title = ?");
    $updateStmt->execute([$newStatus, $title]);

    // Reload the page to reflect the change
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]) . "?title=" . urlencode($title));
    exit;
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

// Function to generate CSV data
function generateCSVData($data)
{
    $csvData = "Email,Name,Register ID,Section,Stream,Title,Marks\n";
    foreach ($data as $record) {
        $csvData .= "{$record['email']},{$record['name']},{$record['register']},{$record['section']},{$record['stream']},{$record['title']},{$record['marks']}\n";
    }
    return $csvData;
}

// Handle CSV download
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['download_csv'])) {
    if (!empty($quizRecords)) {
        // Generate CSV data
        $csvData = generateCSVData($quizRecords);

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=quiz_attendance.csv');

        // Output CSV data
        echo $csvData;
        exit; // Stop further execution
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


        .download-form {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>View Quiz Attendance</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="title">Enter Quiz Title:</label>
            <input type="text" id="title" name="title">
            <button type="submit">Search</button>
        </form>

        <?php if (isset($errorMessage)) : ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if (!empty($quizRecords)) : ?>
            <h3>Quiz Attendance Records for Title: <?php echo $title; ?></h3>
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

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="download-form">
                <input type="hidden" name="title" value="<?php echo $title; ?>">
                <button type="submit" name="download_csv">Download CSV</button>
            </form>

            <?php if ($showMarksButton && !empty($quizRecords)) : ?>
                <div style="text-align: center; margin-top: 20px;">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="title" value="<?php echo $title; ?>">
                        <?php
                        // Fetch current status for the quiz records with the entered title
                        $statusStmt = $pdo->prepare("SELECT status FROM quiz_attendance WHERE title = ?");
                        $statusStmt->execute([$title]);
                        $currentStatus = $statusStmt->fetchColumn();

                        // Determine the color and text based on the current status
                        $statusColor = ($currentStatus === 'yes') ? 'red' : 'green';
                        $statusText = ($currentStatus === 'yes') ? 'Hide Marks from Students' : 'Show Marks to Students';
                        ?>
                        <button type="submit" name="show_marks_to_students" style="background-color: <?php echo $statusColor; ?>"><?php echo $statusText; ?></button>
                    </form>
                    <a href="dashboard.php"><button style="padding: 8px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;">Go to Dashboard</button></a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>