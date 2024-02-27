<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href='https://fonts.googleapis.com/css?family=League Spartan' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://fonts.googleapis.com/css?family=Commissioner' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Trade Winds' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Outfit' rel='stylesheet'>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <link rel="icon" type="image/x-icon" href="/images/logo.jpg">
    <link rel="stylesheet" href="./student-dashboard.css">
    <title>DashBoard</title>
</head>

<body>
    <div class="header_section">
        <div class="header">
            <h3>STUDENT ASSESSMENT PORTAL(STUDENT LOGIN)</h3>
        </div>
        <div class="header_logo">
            <i id="clockIcon" class="fa-regular fa-bell" style="color: #5A57FF;"></i>
            <button id="logoutButton" class="logout_button">Logout</button>
        </div>
    </div>
    <button id="sidebarToggle" class="sidebar_toggle">&#9776;</button>

    <section class="center_part" id="sidebar">
        <div class="Links">
            <img src="./images/large_Kalasalingam_Academy_of_Research_and_Education_Virudhunagar_aeb7350844_a1649b2e88 (1).png" height="70%" width="90%">
            <a href="#" class="links">Dashboard </a>
            <a href="./take_test_front.php" class="mobile">Take Quiz</a>

            <a href="./View Marks to Students.php">View Marks</a>

        </div>
    </section>
    <h3 class="body_heading">Dashboard</h3>
    <div class="cards">
        <a href="./take_test_front.php">
            <div class="cards1">
                <img src="./images/Quiz-comic-pop-art-style-illustration-on-transparent-background-PNG.png" width="90px" height="90px">
                <h3>Take Quiz</h3>
            </div>
        </a>

        <a href="./View Marks to Students.php">
            <div class="cards2">
                <img src="./images/result.png" width="90px" height="90px">
                <h3>view marks</h3>
            </div>
        </a>
    </div>


    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        document.getElementById('clockIcon').addEventListener('click', function() {
            showCurrentTime();
        });

        function showCurrentTime() {
            const currentDate = new Date();
            const hours = currentDate.getHours();
            const minutes = currentDate.getMinutes();
            const seconds = currentDate.getSeconds();
            const formattedTime = `${hours}:${minutes}:${seconds}`;

            alert(`Current time is: ${formattedTime}`);
        }
        document.getElementById('logoutButton').addEventListener('click', function() {
            fetch('logout.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'logout': '1'
                    })
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);

                    window.location.href = 'index.html';
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>

</html>