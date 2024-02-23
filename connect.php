<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['Email']);
    $password = htmlspecialchars($_POST['Password']);

    if (empty($email) || empty($password)) {
        echo "Invalid input. Please fill all fields";
        exit();
    }

    $conn = new mysqli('https://kare-quiz.alphadevsx.com', 'u475858870_root', 'Kalasalingam@339
', 'u475858870_quiz');

    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("SELECT email, password FROM registration WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_email'] = $user['email'];
                header("Location: dashboard.php");
                exit();
            } elseif ($password === $user['password']) {
                session_start();
                $_SESSION['user_email'] = $user['email'];
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<script>alert('Login failed. Invalid email or password.'); window.location.href='login.html';</script>";
            }
        } else {
            echo "<script>alert('Login failed. Invalid email or password.'); window.location.href='login.html';</script>";
        }

        $stmt->close();
        $conn->close();
    }
}
