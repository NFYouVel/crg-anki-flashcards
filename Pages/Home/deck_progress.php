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
    <link rel="stylesheet" href="../Home//CSS/deck_progress.css">
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

    <!-- Colored Title -->
    <div class="title-user">
        <!-- Deck Title -->
        <div class="deck">
            <span class="title">Deck Progress</span>
            <span class="nama">Oden Toby Tenggana</span>
        </div>
        <!-- To Review Green Red Blue-->
        <div class="to-review">
            <span class="click">Delete Deck</span>
        </div>

    </div>
    <div class="title-to-review">
        <!-- Deck Title -->
        <span class="title-name">Active Chinese 1.3</span>
        <!-- To Review Green Red Blue-->
        <div class="to-review">
            <span class="green">169</span>
            <span class="red">28</span>
            <span class="blue">1638</span>
        </div>
    </div>
    <div class="wrapper-delete">
        <div class="delete">
            <div class="title-delete"><span>Delete Deck</span></div>
            <div class="explanation">
                <span>Delete Deck</span>
                <span class="delete-deck">"Active Chinese 1.3"</span>
                <span class="delete-deck">From Student</span>
                <span class="delete-deck-to-user">"Oden Toby Tenggana"?</span>
                <span class="br">This action cannot be undone.</span>
            </div>
            <div class="button">
                <button class="button-cancel">Cancel</button>
                <button class="button-delete">Delete</button>
            </div>
        </div>
    </div>

    <table>
        <tr class="title">
            <th></th>
            <th class="title-words">Words/Terms</th>
            <th>Pinyin and Meaning</th>
        </tr>
        <tr>
            <td class="no-border" style="color: green;">
                <div class="review-color">.</div>
            </td>
            <td class="words">朋友</td>
            <td>
                <div class="words-contain">
                    <span class="pinyin">peng2you3</span>
                    <span>friend</span>
                </div>
            </td>
        </tr>
        <tr>
            <td class="no-border" style="color: red;">
                <div class="review-color">.</div>
            </td>
            <td class="words">有</td>
            <td>
                <div class="words-contain">
                    <span class="pinyin">you3</span>
                    <span>has;have</span>
                </div>
            </td>
        </tr>
    </table>
</body>