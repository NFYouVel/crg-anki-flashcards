<?php
//Session
session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
$user_id = $_SESSION["user_id"];


$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_array($result);


//Cancel and updates
$password = filter_input(INPUT_POST, 'password');
$character = filter_input(INPUT_POST, "character-set");
$cancel = filter_input(INPUT_POST, "cancel");
$update = filter_input(INPUT_POST, "update");
if ($cancel) {
    header("Location: home_page.php");
    exit;
}

if ($update && ($password || $character)) {
    if ($password && strlen($password) < 6) {
        echo "<script>alert('Your password must be more than 6 character!')</script>";
    } else {
        $check = false;
        if ($password && $character) { // Klo ganti pw dan chara set
            $password_hashed = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE users SET password_hash = '$password_hashed', character_set = '$character' WHERE user_id = '$user_id'";
            $check = true;
        } else if ($password) { // Klo ganti pw
            $password_hashed = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE users SET password_hash = '$password_hashed' WHERE user_id = '$user_id'";
            $check = true;
        } else { // Klo ganti chara set
            $query = "UPDATE users SET character_set = '$character' WHERE user_id = '$user_id'";
        }
        
        $update_result = mysqli_query($con, $query);
        // Check ganti password
        if ($check) {
            $query = "UPDATE users SET user_status = 'active' WHERE user_id = '$user_id'";
        }

        // Message klo udah ganti pw
        $update_result = mysqli_query($con, $query);
        if ($update_result) {
            echo "<script>alert('Update success!'); window.location.href='home_page.php';</script>";
        } else {
            echo "<script>alert('Update failed. Please try again!')</script>";
        }
    }
}

// Ngambil data dari user untuk hader
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_assoc($result);
$role_id = $line['role'];
$result2 = mysqli_query($con, "SELECT * FROM user_role WHERE role_id = '$role_id'");
$line2 = mysqli_fetch_assoc($result2);
$role = $line2['role_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../Logo/circle.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../../Pages/Home/CSS/setting.css">

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

    <div class="wrapper-setting">
        <div class="container">
            <h1 class="title-setting">Settings</h1>
            <div class="information">
                <h2 class="title-setting-information">Personal Information</h2>
                <table>
                    <tr>
                        <td>Account Name</td>
                        <td>:</td>
                        <td>Herodian Petro Marlim</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td>herodianpm@gmail.com</td>
                    </tr>
                    <tr>
                        <td>Account Type</td>
                        <td>:</td>
                        <td>Teacher</td>
                    </tr>
                    <tr>
                        <td>Character Set</td>
                        <td>:</td>
                        <td>Herodian Petro Marlim</td>
                        <td class="right"><a href="#">edit</a></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>:</td>
                        <td>******</td>
                        <td class="right"><a href="#">reset password</a></td>
                    </tr>
                    <tr>
                        <td>Card Meaning</td>
                        <td>:</td>
                        <td>Indonesia, English</td>
                        <td class="right"><a href="#">edit</a></td>
                    </tr>
                </table>
            </div>
            <div class="notification">
                <div class="toggle-reminder">
                    <span>Study Reminder</span>
                    <input type="radio" name="" id="">
                </div>
                <div class="days">
                    <span>Which Days</span>
                    <div class="button-days">
                        <span class="day-name">M</span>
                        <span class="day-name">T</span>
                        <span class="day-name">W</span>
                        <span class="day-name">T</span>
                        <span class="day-name">F</span>
                        <span class="day-name">S</span>
                        <span class="day-name" style='color: red;'>S</span>
                    </div>
                </div>
                <div class="time">
                    <span>Time</span>
                    <span>17:00</span>
                </div>
            </div>
        </div>
    </div>

</body>

</html>