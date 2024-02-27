<!DOCTYPE html>
<html lang="en">

<head>
    <title>Schedule Quiz</title>
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        body {
            display: flex;
            font-family: Arial, sans-serif;
        }

        .links {
            display: block;
            color: white;
            text-decoration: none;
            font-size: 20px;
            margin-bottom: 50px;
            margin-left: 20px;
        }

        .links:hover {
            color: black;
        }

        #sidebar {
            width: 20%;
            background-color: #9328FF;
            color: #fff;
            border-radius: 0px 40px 40px 0px;
            position: fixed;
            height: 100vh;
        }

        #content {
            flex: 1;
            padding: 20px;
            margin-left: 25%;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        #sidebar img {
            margin-top: 10px;
            margin-left: 10px;
            border-radius: 25px;
            margin-bottom: 30px;
        }

        .card {
            width: 300px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
            text-align: center;
        }

        .card:hover {

            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        }

        .card a.view-document {
            background-color: #007BFF;
        }

        .card a:hover {
            border: 2px solid black;

        }

        .card a.schedule {
            background-color: #FFC107;
        }



        .card a {
            display: inline-block;
            margin: 10px;
            padding: 10px 15px;
            background: linear-gradient(to right, #4CAF50, #45a049);
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .card a.schedule:hover {
            border: 2px solid black;
            color: black;
            background: linear-gradient(to right, #45a049, #409141);

        }

        .delete {
            background: linear-gradient(to right, #DC3545, #a83232);
            padding: 7px;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .delete:hover {
            border: 2px solid black;
            background: linear-gradient(to right, #C82333, #a12525);
        }
    </style>
    <link rel="icon" type="image/x-icon" href="/images/logo.jpg">

</head>

<body>

    <div id="sidebar">
        <img src="./images/large_Kalasalingam_Academy_of_Research_and_Education_Virudhunagar_aeb7350844_a1649b2e88 (1).png" height="20%" width="90%">
        <div class="link">
            <a href="./dashboard.php" class="links">Dashboard </a>
            <a href="./generateQuiz.php" class="links">Generate Quiz</a>
            <a href="./edit_scheduled_quizzes.php" class="links">Edit Schedule Quiz</a>
            <a href="./schedulequiz.php" class="links">Schedule Quiz</a>
            <a href="#" class="links">View Marks</a>
        </div>
    </div>

    <div id="content">
        <h1>Schedule Quiz</h1><br><br>
        <?php
        session_start();

        if (!isset($_SESSION['user_email'])) {
            header("Location: login.html");
            exit();
        }

        $conn = new mysqli('localhost', 'u475858870_root', 'Kalasalingam@339', 'u475858870_quiz');
        if ($conn->connect_error) {
            die('Connection Failed: ' . $conn->connect_error);
        }

        $user_email = $_SESSION['user_email'];
        $stmt = $conn->prepare("SELECT * FROM uploaded_files WHERE user_email = ?");
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="card-container">';
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card" data-file-path="' . $row['file_path'] . '">';
                echo '<h4>File Name: ' . $row['original_file_name'] . '</h4>';
                echo '<p>Uploaded date and Time: ' . $row['upload_timestamp'] . '</p>';
                echo '<a href="view_document.php?file_path=' . $row['file_path'] . '">View Document</a>';
                echo '<a href="schedule_front.html">Schedule</a>';
                echo '<form method="post" action="delete_file.php" style="margin-top: 10px;">';
                echo '<input type="hidden" name="file_path" value="' . $row['file_path'] . '">';
                echo '<button type="submit" onclick="return confirm(\'Are you sure you want to delete this file?\')" class="delete">Delete</button>';
                echo '</form>';
                echo '</div>';
            }

            echo '</div>';
        } else {
            echo '<p>No uploaded documents available.</p>';
        }

        $stmt->close();
        $conn->close();
        ?>


    </div>

</body>

</html>