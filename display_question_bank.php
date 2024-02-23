<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Question Bank</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .question {
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <h1>Quiz Question Bank</h1>

    <?php
    $quizDataFile = 'uploads/question_bank.json';

    if (file_exists($quizDataFile)) {
        $quizDataJson = file_get_contents($quizDataFile);
        $quiz = json_decode($quizDataJson, true);

        echo '<h2>' . $quiz['title'] . '</h2>';
        foreach ($quiz['questions'] as $question) {
            echo '<div class="question">';
            echo '<p>' . $question['text'] . '</p>';
            echo '<ul>';
            foreach ($question['options'] as $option) {
                echo '<li>' . $option . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    } else {
        echo '<p>No quiz data available. Please upload a Word document.</p>';
    }
    ?>

</body>
</html>
