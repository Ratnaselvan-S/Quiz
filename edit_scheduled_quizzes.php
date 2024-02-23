    <?php
    session_start();

    $conn = new mysqli('localhost', 'u475858870_root', 'Kalasalingam@339', 'u475858870_quiz');

    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }

    $user_email = $_SESSION['user_email'];

    $query = "SELECT * FROM scheduled_quizzes WHERE user_email = '$user_email'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $quizzes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $quizzes[] = $row;
        }
    } else {
        echo 'Error fetching scheduled quizzes';
        exit;
    }

    mysqli_close($conn);
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Scheduled Quizzes</title>
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
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-top: 20px;
            }

            #content {
                flex: 1;
                padding: 20px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            #content h2 {
                margin-left: 10%;
                margin-bottom: 20px;
                border-bottom: 2px solid black;

            }

            #sidebar img {
                margin-top: 10px;
                margin-left: 10px;
                border-radius: 25px;
                margin-bottom: 30px;
            }

            .card {
                background-color: #fff;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                padding: 20px;
                width: 50%;
                margin-bottom: 30px;
                text-align: left;
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-left: 10%;


                box-shadow:
                    0 2.8px 2.2px rgba(0, 0, 0, 0.034),
                    0 6.7px 5.3px rgba(0, 0, 0, 0.048),
                    0 12.5px 10px rgba(0, 0, 0, 0.06),
                    0 22.3px 17.9px rgba(0, 0, 0, 0.072),
                    0 41.8px 33.4px rgba(0, 0, 0, 0.086),
                    0 100px 80px rgba(0, 0, 0, 0.12);
            }

            .card p {
                margin-bottom: 10px;
            }

            .action-buttons {
                margin-top: 10px;
            }

            button {
                background-color: #3498db;
                color: white;
                padding: 10px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                margin-right: 10px;
            }

            button:hover {
                background-color: #2980b9;
            }

            .delete {
                color: red;
                background-color: white;
                border: 1px solid red;
            }

            .delete:hover {
                background-color: red;
                color: white;
            }

            .reschedule {
                color: green;
                background-color: white;
                border: 1px solid green;
            }

            .reschedule:hover {
                background-color: green;
                color: white;
            }
        </style>
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
            <h2>Scheduled Quizzes</h2>
            <hr />

            <?php if (!empty($quizzes)) : ?>
                <?php foreach ($quizzes as $quiz) : ?>
                    <div class="card">
                        <h3><?= $quiz['title']; ?></h3>
                        <p><strong>Quiz Date:</strong> <?= $quiz['quiz_date']; ?></p>
                        <p><strong>Start Time:</strong> <?= $quiz['start_time']; ?></p>
                        <p><strong>End Time:</strong> <?= $quiz['end_time']; ?></p>
                        <p><strong>Number of Questions:</strong> <?= isset($quiz['number_of_questions']) ? $quiz['number_of_questions'] : 'N/A'; ?></p>


                        <p><strong>Generated Code:</strong> <?= $quiz['code']; ?></p>
                        <div class="action-buttons">
                            <button class="reschedule" onclick="location.href='reschedule_quiz.php?title=<?= urlencode($quiz['title']); ?>'">Reschedule</button>
                            <button class="delete" onclick="location.href='delete_quiz.php?title=<?= urlencode($quiz['title']); ?>'">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No quizzes scheduled.</p>
            <?php endif; ?>
        </div>
    </body>

    </html>