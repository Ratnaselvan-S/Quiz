<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
        }

        /* Styling for questions */
        .question-container {
            margin: 20px auto;
            width: 80%;
            padding: 20px;
            height: 300px;
            /* Changed height to auto to accommodate variable content */
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .question {
            position: absolute;
            font-size: 24px;
            font-weight: bold;
            top: 10%;
            font-size: 30px;
        }

        .options {
            position: absolute;
            left: 20%;
            top: 30%;
            /* Adjusted the top position */
        }

        /* Styling for options */
        .options label {
            font-size: 30px;
            display: block;
            /* Ensures True and False options are displayed on separate lines */
        }

        .options input[type="radio"] {
            transform: scale(2);
            /* Increased the size of the radio buttons */
            margin-right: 10px;
        }

        .options input[type="checkbox"] {
            transform: scale(2);
            /* Increased the size of the checkboxes */
            margin-right: 10px;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
            /* Added margin to separate buttons */
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            /* Removed underlines for button */
            background-image: linear-gradient(to right, #007bff, #0056b3);
            /* Linear gradient background */
        }

        .btn:hover {
            background-image: linear-gradient(to right, #0056b3, #003e80);
            /* Adjusted hover gradient colors */
        }
    </style>
</head>

<body>
    <h1>Preview</h1>

    <?php
    if (isset($_GET['file_path'])) {
        $file_path = $_GET['file_path'];

        $csv_content = file_get_contents($file_path);

        $csv_rows = array_map('str_getcsv', explode("\n", $csv_content));

        $headers = array_shift($csv_rows);

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $questions_per_page = 1;
        $start_index = ($page - 1) * $questions_per_page;
        $end_index = $start_index + $questions_per_page;

        for ($i = $start_index; $i < $end_index && $i < count($csv_rows); $i++) {
            $row = $csv_rows[$i];
            echo '<div class="question-container">';
            foreach ($row as $cellIndex => $cell) {
                $header = $headers[$cellIndex];
                switch ($header) {
                    case 'Question':
                        // Extracting just the question number
                        $question_number = $i + 1;
                        echo '<div class="question">' . $question_number . ': ' . htmlspecialchars($cell) . '</div>';
                        break;
                    case 'type':
                        if (strtoupper($cell) === 'MULTIPLE') {
                            echo '<div class="options">';
                            $options = explode(",", $row[$cellIndex + 1]);
                            foreach ($options as $optionIndex => $option) {
                                echo '<label><input type="checkbox" name="question_' . ($i + 1) . '[]" value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</label><br>';
                            }
                            echo '</div>';
                        } elseif (strtoupper($cell) === 'SINGLE') {
                            echo '<div class="options">';
                            $options = explode(",", $row[$cellIndex + 1]);
                            foreach ($options as $optionIndex => $option) {
                                echo '<label><input type="radio" name="question_' . ($i + 1) . '" value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</label><br>';
                            }
                            echo '</div>';
                        } elseif (strtoupper($cell) === 'TRUEFALSE') {
                            echo '<div class="options">';
                            echo '<label><input type="radio" name="question_' . ($i + 1) . '" value="True">True</label><br>'; // Moved True to a new line
                            echo '<label><input type="radio" name="question_' . ($i + 1) . '" value="False">False</label><br>'; // Moved False to a new line
                            echo '</div>';
                        } else {
                            echo '<p class="question">' . htmlspecialchars($cell) . '</p>';
                        }
                        break;
                }
            }
            echo '</div>'; // closing question-container
        }

        // Previous and Next buttons
        echo '<div class="btn-container">';
        if ($page > 1) {
            echo '<a href="?file_path=' . urlencode($file_path) . '&page=' . ($page - 1) . '" class="btn">Previous</a>';
        }
        // Check if there are more questions to display
        if ($end_index < count($csv_rows)) {
            echo '<a href="?file_path=' . urlencode($file_path) . '&page=' . ($page + 1) . '" class="btn">Next</a>';
        }
        // New button for schedulequiz.php
        echo '<a href="schedulequiz.php" class="btn">Close</a>';
        echo '</div>';
    } else {
        echo '<p>No file path provided.</p>';
    }
    ?>
</body>

</html>