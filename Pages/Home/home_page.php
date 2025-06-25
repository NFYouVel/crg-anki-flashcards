<?php
session_start();
include "../../SQL_Queries/connection.php";
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

    <!-- Main Deck -->
    <div class="wrapper-main">
        <div class="deck-layout">
            <!-- Example: For Teacher -->
            <ul>
                <!-- First Main -->
                <!-- Active Chinese Senin Kamis 20.30-->
                <li class="class-title">
                    <!-- Colored Title -->
                    <div class="title-to-review">
                        <!-- Deck Title -->
                        <span class="title">Active Chinese Senin Kamis 20.30</span>
                        <!-- To Review Green Red Blue-->
                        <div class="to-review">
                            <span class="click">Deck</span>
                        </div>
                    </div>


                    <div class="subdeck">
                        <ul>
                            <!-- Second Main -->
                            <li class="contain">
                                <div class="title-to-review-second">
                                    <!-- Deck Title -->
                                    <span class="title-second">Eric Lim</span>
                                    <!-- To Review Green Red Blue-->
                                    <div class="to-review">
                                        <span class="green">169</span>
                                        <span class="red">28</span>
                                        <span class="blue">1638</span>
                                    </div>
                                </div>

                                <!-- Third Main -->
                                <ul>
                                    <li class="contain-third">
                                        <div class="title-to-review-third">
                                            <!-- Deck Title -->
                                            <span class="title-third">Active Chinese 1.1</span>
                                            <!-- To Review Green Red Blue-->
                                            <div class="to-review">
                                                <span class="green">169</span>
                                                <span class="red">28</span>
                                                <span class="blue">1638</span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>

                            <!-- Copas Second Main Dari Sini -->
                            <li class="contain">
                                <div class="title-to-review-second">
                                    <!-- Deck Title -->
                                    <span class="title-second">Marvel Nathanael Lie</span>
                                    <!-- To Review Green Red Blue-->
                                    <div class="to-review">
                                        <span class="green">169</span>
                                        <span class="red">28</span>
                                        <span class="blue">1638</span>
                                    </div>
                                </div>

                                <!-- Third Main -->
                                <ul>
                                    <!-- Copas Third Main Dari Sini -->
                                    <li class="contain-third">
                                        <div class="title-to-review-third">
                                            <!-- Deck Title -->
                                            <span class="title-third">Active Chinese 1.1</span>
                                            <!-- To Review Green Red Blue-->
                                            <div class="to-review">
                                                <span class="green">169</span>
                                                <span class="red">28</span>
                                                <span class="blue">1638</span>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- Sampe Sini (Third)-->
                                </ul>
                            </li>
                            <!-- Sampe Sini (Second)-->
                        </ul>
                    </div>
                </li>
                <!-- Sampe Sini (First)-->
            </ul>
        </div>
    </div>

</body>

</html>