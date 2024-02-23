<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['quizDetails']) || !isset($_SESSION['quiz_questions'])) {
    header("Location: take_quiz.php");
    exit();
}

$quizDetails = $_SESSION['quizDetails'];
$questions = $_SESSION['quiz_questions'];

function validateQuiz($submittedAnswers, $questions)
{
    $score = 0;

    foreach ($questions as $index => $question) {
        $correctOption = $question['CorrectOption1'];

        if (isset($submittedAnswers[$index]) && $submittedAnswers[$index] === $correctOption) {
            $score++;
        }
    }

    return $score;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-quiz'])) {
    $submittedAnswers = $_POST['answers'];
    $score = validateQuiz($submittedAnswers, $questions);

    echo "<h2>Your Score: $score out of " . count($questions) . "</h2>";
    exit();
}

if (!isset($_SESSION['quiz_questions_shuffled'])) {
    shuffle($questions);
    $_SESSION['quiz_questions_shuffled'] = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
</head>

<body>
    <h1>Quiz</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <?php foreach ($questions as $index => $question) : ?>
            <div>
                <p><?php echo $question['Question']; ?></p>
                <?php foreach ($question['Options'] as $option) : ?>
                    <label>
                        <input type="radio" name="answers[<?php echo $index; ?>]" value="<?php echo $option; ?>">
                        <?php echo $option; ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" name="submit-quiz">Submit Quiz</button>
    </form>
</body>

</html>