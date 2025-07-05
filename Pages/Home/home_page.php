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
$role_id = $line['role'];
$result2 = mysqli_query($con,"SELECT * FROM user_role WHERE role_id = '$role_id'");
$line2 = mysqli_fetch_assoc($result2);
$role = $line2['role_name'];

if (isset($_POST['hide'])) {
    $name = $line['name'];
    echo "<script>alert('You are login with $name Account as Teacher')</script>";
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
</head>

<body>
    <!-- Header -->
    <?php include "Component/header_login.php";?>

    <div class="right-bar">
        <div class="account-info">
            <span class="username"><?php echo $line['name'] ?></span>
            <span class="as" style="cursor: pointer;" onclick="Mode()"><?php echo $role ?> Mode</span>
        </div>
        <script>
            function Mode(){
                window.location.href = "home_page_students.php";
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
                                            <a href="deck_progress.php" class="title-third">Active Chinese 1.1</a>
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
                                            <a href="deck_progress.php" class="title-third">Active Chinese 1.2</a>
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