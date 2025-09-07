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
    <link rel="stylesheet" href="../../Pages/Home/CSS/student_progress.css?v=123">
    <link rel="stylesheet" href="../../Pages/Home/CSS/deck.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
    <style>
        .nav-menu span:nth-child(2) {
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
    echo "function Overview() { window.location.href = 'student_progress_overview.php?user_id=$user_id_student'; }";
    echo "function Deck() { window.location.href = 'student_progress_deck.php?user_id=$user_id_student'; }";
    echo "function SRS() { window.location.href = 'student_progress_srs.php?user_id=$user_id_student'; }";
    echo "function Matching() { window.location.href = 'student_progress_matching.php?user_id=$user_id_student'; }";
    echo "function Quiz() { window.location.href = 'student_progress_quiz.php?user_id=$user_id_student'; }";
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

            <div class="wrapper-main">
                <div class="deck-layout">
                    <!-- Example: For Teacher -->
                    <ul>
                        <!-- First Main -->
                        <!-- Active Chinese Senin Kamis 20.30-->
                        <li class="class-title">
                            <!-- Colored Title -->
                            <div class="title-to-review" onclick="window.location.href='deck_progress.php?deck_id=main'">
                                <!-- Deck Title -->
                                <span class="title">Main Deck</span>
                                <!-- To Review Green Red Blue-->
                                <div class="to-review">
                                    <span class="green">169</span>
                                    <span class="red">28</span>
                                    <span class="blue">1638</span>
                                </div>
                            </div>

                            <?php
                            $rootDecks = [];
                            function getRoot($parentID = null)
                            {
                                global $con, $user_id_student, $rootDecks;
                                if ($parentID == null) {
                                    $getDecks = mysqli_query($con, "SELECT deck.deck_id, deck.parent_deck_id
                                                                    FROM junction_deck_user AS deck_user
                                                                    JOIN decks AS deck 
                                                                    ON deck_user.deck_id = deck.deck_id
                                                                    WHERE deck_user.user_id = '$user_id_student'");
                                } else {
                                    $getDecks = mysqli_query($con, "SELECT deck_id, parent_deck_id FROM decks WHERE deck_id = '$parentID'");
                                }
                                while ($deck = mysqli_fetch_assoc($getDecks)) {
                                    if (!in_array($deck["deck_id"], $rootDecks)) {
                                        $rootDecks[] = $deck["deck_id"];
                                    }
                                    if ($deck["parent_deck_id"] !== null) {
                                        getRoot($deck["parent_deck_id"]);
                                    }
                                }
                            }
                            getRoot();

                            function showDecks($parentID = null)
                            {
                                global $con, $user_id, $rootDecks;
                                if ($parentID == null) {
                                    $getDecks = mysqli_query($con, "SELECT name, deck_id FROM decks WHERE parent_deck_id IS NULL");
                                } else {
                                    $getDecks = mysqli_query($con, "SELECT name, deck_id FROM decks WHERE parent_deck_id = '$parentID'");
                                }
                                while ($deck = mysqli_fetch_assoc($getDecks)) {
                                    $deckID = $deck["deck_id"];
                                    if (in_array($deckID, $rootDecks)) {
                                        echo "<li class='contain'>";
                                        echo "<div class='container-deck'>";
                                        echo "<div class='plus'>+</div>";
                                        echo "<div class='title-to-review-second'  onclick=\"window.location.href='deck_progress.php?deck_id=$deckID'\">";
                                        echo "<span class='title-second'>" . htmlspecialchars($deck['name']) . "</span>";
                                        echo "<div class='to-review'>
                                                        <span class='green'>169</span>
                                                        <span class='red'>28</span>
                                                        <span class='blue'>1638</span>
                                                        </div>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "<div class='line'></div>";

                                        echo "<ul class='child-deck'>";
                                        showDecks($deckID);
                                        echo "</ul>";

                                        echo "</li>";
                                    }
                                }
                            }

                            showDecks();
                            ?>
                </div>
            </div>

        </div>


</body>

<script>
    function syncLineWidth() {
  const title = document.querySelector('.class-title');
  const line = document.querySelector('.line');
  const width = title.offsetWidth;

  console.log('Current title width:', width); // <-- Cek log-nya

  line.style.width = width + 'px';
}

window.addEventListener('load', syncLineWidth);
window.addEventListener('resize', syncLineWidth);
</script>

</html>