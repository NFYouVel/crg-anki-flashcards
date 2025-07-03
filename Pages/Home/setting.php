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

    <div class="wrapper-setting">
        <div class="wrapper-mid">
            <div class="title-setting">
                <span>Setting</span>
            </div>
            <form method="post">
                <div class="form">
                    <span class="title-contain">Full Name <span style="color: red;">*</span></span>
                    <input type="text" name="name" placeholder="Your Full Name...">
                    <span class="title-contain">Password <span style="color: red;">*</span></span>
                    <input type="password" name="password" placeholder="Your Password...">
                    <span class="title-contain">Character Set</span>
                    <select name="character-set">
                        <option value="simplified">Simplified</option>
                        <option value="traditional">Traditional</option>
                    </select>
                </div>
                <div class="action">
                    <input type="submit" value="Cancel">
                    <input type="submit" value="Update">
                </div>
            </form>

        </div>
    </div>
</body>

</html>