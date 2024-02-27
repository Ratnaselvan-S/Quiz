<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Quiz</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
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

        #sidebar img {
            margin-top: 10px;
            margin-left: 10px;
            border-radius: 25px;
            margin-bottom: 30px;
        }

        .upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;

            margin-left: 34%;
        }

        .upload2 {
            max-width: 600px;
            /* Increased the max-width */
            width: 90%;
            /* Adjusted width */
            text-align: center;
            padding: 40px;
            /* Increased padding */
            border: 2px solid #9328FF;
            /* Added border */
            border-radius: 20px;
            /* Added border radius */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Stylish font */
        }

        .upload2 label {
            display: block;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .upload2 input {
            width: calc(100% - 20px);
            /* Adjusted input width */
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Stylish font */
        }

        .upload2 button {
            background-color: #9328FF;
            color: white;
            padding: 15px 30px;

            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Stylish font */
        }

        .upload2 button:hover {
            background-color: #6F1CFF;
        }

        #response {
            margin-top: 20px;
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
    <div class="upload">

        <div class="upload2">
            <form action="./qb_upload.php" method="post" enctype="multipart/form-data">
                <label for="quiz_file">Select a csv file:</label>
                <input type="file" id="quiz_file" name="quiz_file">
                <button type="submit">Upload and Generate Quiz</button>
            </form>
            <div id="response"></div>
        </div>
    </div>
</body>

</html>