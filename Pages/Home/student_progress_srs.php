<?php
session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

// User ID Teacher
$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_array($result);
$role_id = $line['role'];
$result2 = mysqli_query($con, "SELECT * FROM user_role WHERE role_id = '$role_id'");
$line2 = mysqli_fetch_array($result2);
$role = $line2['role_name'];
if ($role_id != 2) {
    header("Location: ../Login");
}

if (isset($_POST['hide'])) {
    $name = $line['name'];
    echo "<script>alert('You are login with $name Account as Teacher')</script>";
}

// User ID Student
$user_id_student = $_GET['user_id'];
$_SESSION['user_id_student'] = $user_id_student;
$query_user_id_student = mysqli_query($con, "SELECT * FROM users WHERE user_id = '$user_id_student'");
$line_student = mysqli_fetch_assoc($query_user_id_student);
$student_name = $line_student['name'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../Logo/circle.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../../Pages/Home/CSS/student_progress.css?v=1337">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
    <style>
        .nav-menu span:nth-child(3) {
            font-size: 20px;
            color: #2f6ba1;
            background-color: #deebf7;
            border-radius: 6px;
            border: 2px solid #9cb4cb;
        }
        html,
        body {
            overflow-y: scroll !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include "Component/header_login.php"; ?>

    <div class="right-bar">
        <div class="account-info">
            <span class="username"><?php echo $line['name'] ?></span>
            <span class="as" style="cursor: pointer;" onclick="Mode()"><?php echo $role ?> Mode</span>
        </div>
        <script>
            function Mode() {
                window.location.href = "home_page_teacher_student.php";
            }
        </script>

        <div class="navbar">
            <span class="icon">&#9776;</span>
        </div>
    </div>
    </div>
    </div>
    <?php include "Component/account_logout.php"; ?>

    <!-- Main Deck -->
    <div class="title-sp">
        <span>Student Progress</span>
        <span><?php echo $student_name ?></span>
    </div>

    <?php
    echo "<script>";
        echo "function Overview() {window.location.href = 'student_progress_overview.php?user_id=$user_id_student'}";
        echo "function Deck() {window.location.href = 'student_progress_deck.php?user_id=$user_id_student'}";
        echo "function SRS() {window.location.href = 'student_progress_srs.php?user_id=$user_id_student'}";
        echo "function Matching() {window.location.href = 'student_progress_matching.php?user_id=$user_id_student'}";
        echo "function Quiz() {window.location.href = 'student_progress_quiz.php?user_id=$user_id_student'}";
    echo "</script>";
    ?>
    
    <div class="wrapper-menu">
        <div class="container-menu">
            <div class="nav-menu">
                <span onclick="Overview()">Overview</span>
                <span onclick="Deck()">Deck</span>
                <span onclick="SRS()">SRS</span>
                <span onclick="Matching()">Matching</span>
                <span onclick="Quiz()">Quiz</span>
            </div>

            <div class="container-student-progress">
                <span>Card Levels</span>
            </div>
            <div class="graph-sp">

            </div>
            <div class="table-sp-information">
                <table class="review">
                    <tr>
                        <td>Review Streak</td>
                        <td>:</td>
                        <td>7</td>
                    </tr>
                    <tr>
                        <td>Last Active</td>
                        <td>:</td>
                        <td>8 Jul 2025 12:08</td>
                    </tr>
                </table>

                <!-- SRS Review -->
                <h3>SRS Review</h3>
                <table class="srs-review">
                    <tr>
                        <td>Total reviews</td>
                        <td>:</td>
                        <td>1638 cards</td>
                    </tr>
                    <tr>
                        <td>Total time</td>
                        <td>:</td>
                        <td>5h 3m</td>
                    </tr>
                </table>

                <!-- Matching Game -->
                <h3>Matching Game</h3>
                <table class="matching-game">
                    <tr>
                        <td>Total unranked</td>
                        <td>:</td>
                        <td>5 times (accuracy: 79%)</td>
                    </tr>
                    <tr>
                        <td>Total ranked</td>
                        <td>:</td>
                        <td>26 times (accuracy: 85%)</td>
                    </tr>
                </table>

                <!-- Student Quiz -->
                <h3>Student Quiz</h3>
                <table class="student-quiz">
                    <tr>
                        <td>Quiz done</td>
                        <td>:</td>
                        <td>5 times</td>
                    </tr>
                    <tr>
                        <td>Average score</td>
                        <td>:</td>
                        <td>78.5</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

  
</body>

</html>