<?php
//Session
session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

// User ID
$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_array($result);
$role_id = $line['role'];
$user_status = $line['user_status'];
if ($user_status == "pending") {
    echo "<script>alert('You have to change your password immediately to protect your account.')</script>";
}

$result = mysqli_query($con, $query);
$line = mysqli_fetch_array($result);
$email = $line['email'];

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
    <link rel="icon" href="../../Assets/Icons/1080.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../../Pages/Home/CSS/notification.css">
    <link rel="stylesheet" href="../../Pages/Home/CSS/setting.css">
    <script src="../../Pages/Home/jQuery/notification.js"></script>


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
                        <td><?php echo $line['name']; ?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td><?php echo $line['email']; ?></td>
                    </tr>
                    <tr>
                        <td>Account Type</td>
                        <td>:</td>
                        <td><?php echo $role; ?></td>
                    </tr>
                    <tr>
                        <td>Character Set</td>
                        <td>:</td>
                        <td id="editChara"><?php echo $line['character_set']; ?></td>
                        <td class="right" onclick="changeCharacterSet()" style="cursor: pointer;">edit</td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>:</td>
                        <td>******</td>
                        <td class="right"><a href="../Login/newpassword.php?email=<?php echo $email; ?>">reset password</a></td>
                    </tr>
                    <!-- <tr>
                        <td>Card Meaning</td>
                        <td>:</td>
                        <td>Indonesia, English</td>
                        <td class="right"><a href="#">edit</a></td>
                    </tr> -->
                </table>
            </div>

            <script>
                function changeCharacterSet() {
                    const xhr = new XMLHttpRequest();
                    xhr.open("GET", "jQuery/ajax.php", true);

                    xhr.onload = function() {
                        if (xhr.status == 200) {
                            // alert("You Have Change Your Character Set!")
                            document.getElementById("editChara").innerHTML = xhr.responseText;
                        } else {
                            alert("Failed to change Character Set");
                        }
                    };

                    xhr.send();
                }
            </script>

            <!-- <div class="notification">
                <h2>Notification</h2>
                <div class="toggle-reminder">
                    <span>Study Reminder</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="days">
                    <span class="ask">Which Days?</span>
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
            </div> -->
        </div>
    </div>

</body>

</html>