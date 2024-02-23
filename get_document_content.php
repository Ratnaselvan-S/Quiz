<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['file_path'])) {
        $filePath = $_POST['file_path'];

        function convertDocxToHtml($filePath)
        {
            $result = shell_exec("mammoth " . escapeshellarg($filePath) . " --output-format=html");
            return $result;
        }

        function addRadioButtons($htmlContent)
        {
            $htmlContent = preg_replace_callback('/<p>\s*[A-D]\.\s*(.*?)<\/p>/', function ($match) {
                $options = explode("\n", $match[1]);
                array_shift($options); 

                $radioButtons = '';
                foreach ($options as $option) {
                    $radioButtons .= '<input type="radio" name="question_' . uniqid() . '" value="' . htmlspecialchars(trim($option)) . '">' . htmlspecialchars(trim($option)) . '<br>';
                }

                return '<p>' . $radioButtons . '</p>';
            }, $htmlContent);

            return $htmlContent;
        }

        $htmlContent = convertDocxToHtml($filePath);
        $htmlContentWithRadioButtons = addRadioButtons($htmlContent);
        echo $htmlContentWithRadioButtons;
        exit();
    }
}
