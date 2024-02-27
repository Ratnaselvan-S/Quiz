<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['Email']);
    $password = htmlspecialchars($_POST['Password']);

    if (empty($email) || empty($password)) {
        echo "Invalid input. Please fill all fields";
        exit();
    }

    $conn = new mysqli('localhost', 'u475858870_root', 'Kalasalingam@339', 'u475858870_quiz');

    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("SELECT email, password FROM student_db WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_email'] = $user['email'];
                header("Location: student_dashboard.php");
                exit();
            } elseif ($password === $user['password']) {
                session_start();
                $_SESSION['user_email'] = $user['email'];
                header("Location: student_dashboard.php");
                exit();
            } else {
                // Password is incorrect
                echo "<script>alert('Login failed. Invalid email or password.'); window.location.href='index.html';</script>";
            }
        } else {
            echo "<script>alert('Login failed. Invalid email or password.'); window.location.href='index.html';</script>";
        }

        $stmt->close();
        $conn->close();
    }
}
