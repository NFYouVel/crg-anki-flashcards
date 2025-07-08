<?php
session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_assoc($result);

if ($line['role'] == 0) {
    $role = "Admin";
} else if ($line['role'] == 1) {
    $role = "Teacher";
} else {
    $role = "Student";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../Logo/circle.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../../Pages/Home/CSS/flashcard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
</head>

<body>

    <!-- Header -->
    <?php include "Component/header_login.php" ?>

    <div class="right-bar">
        <div class="account-info">
            <span class="username"><?php echo $line['name'] ?></span>
            <span class="as"><?php echo $role ?></span>
        </div>

        <div class="navbar">
            <span class="icon">&#9776;</span>
        </div>
    </div>
    </div>
    </div>
    <?php include "Component/account_logout.php"; ?>

    <!-- + - and to review -->
    <div class="title-to-review">
        <!-- Deck Title -->
        <div>
            <span class="calc">+</span>
            <span class="calc">-</span>
        </div>
        <!-- To Review Green Red Blue-->
        <div class="to-review">
            <span class="green">169</span>
            <span class="red">28</span>
            <span class="blue">1638</span>
        </div>
    </div>

    <!-- Cards -->
    <div class="wrapper-flashcard">
        <div class="wrapper-mid">
            <div class="vocab-card">
                <span class="hanzi">老师</span>
                <span class="pinyin">lao3shi1</span>
                <span class="word-class">noun</span>
                <table>
                    <!-- Meaning English -->
                    <tr>
                        <td class="sub">EN</td>
                        <td class="colon">:</td>
                        <td class="meaning">teacher</td>
                    </tr>
                    <!-- Meaning Indonesia -->
                    <tr>
                        <td class="sub">ID</td>
                        <td class="colon">:</td>
                        <td class="meaning">guru;pengajar</td>
                    </tr>
                </table>
            </div>
            <div class="sentence">
                <span class="sentence">他是老师。</span>
                <span class="pinyin">Ta1 shi2 lao3shi1</span>
                <table>
                    <!-- Meaning English -->
                    <tr>
                        <td class="sub">EN</td>
                        <td class="colon">:</td>
                        <td class="meaning">He is a teacher</td>
                    </tr>
                    <!-- Meaning Indonesia -->
                    <tr>
                        <td class="sub">ID</td>
                        <td class="colon">:</td>
                        <td class="meaning">Dia adalah guru</td>
                    </tr>
                </table>
            </div>
            <div class="sentence">
                <span class="sentence">我要当中文老师</span>
                <span class="pinyin">wo3 yao3 dang1 zhong1wen2 lao3shi1</span>
                <table>
                    <!-- Meaning English -->
                    <tr>
                        <td class="sub">EN</td>
                        <td class="colon">:</td>
                        <td class="meaning">I want to be a chinese teacher.</td>
                    </tr>
                    <!-- Meaning Indonesia -->
                    <tr>
                        <td class="sub">ID</td>
                        <td class="colon">:</td>
                        <td class="meaning">Saya ingin menjadi guru bahasa Mandarin.</td>
                    </tr>
                </table>
            </div>
            <div class="sentence">
                <span class="sentence">老师好！</span>
                <span class="pinyin">lao3shi1 hao3</span>
                <table>
                    <!-- Meaning English -->
                    <tr>
                        <td class="sub">EN</td>
                        <td class="colon">:</td>
                        <td class="meaning">Hello teacher!</td>
                    </tr>
                    <!-- Meaning Indonesia -->
                    <tr>
                        <td class="sub">ID</td>
                        <td class="colon">:</td>
                        <td class="meaning">Halo guru!</td>
                    </tr>
                </table>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <button class="wrapper-show-answer">
        <span href="#" class="show">Show Answer</span>
    </button>
    <div class="wrapper-show-answer">
        <button class="forgot" id="criteria">
            <span>
                X</span>
                    <span>Forgot</span>
        </button>
        <button class="hard" id="criteria">
            <span>
                ...</span>
                    <span>Hard</span>
        </button>
        <button class="remember" id="criteria">
            <span>
                V</span>
                    <span>Remember</span>
        </button>
    </div>


</body>

</html>