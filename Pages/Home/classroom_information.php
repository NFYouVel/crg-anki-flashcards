<?php
session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_array($result);
$role_id = $line['role'];
$result2 = mysqli_query($con, "SELECT * FROM user_role WHERE role_id = '$role_id'");
$line2 = mysqli_fetch_array($result2);
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
    <link rel="stylesheet" href="../../Pages/Home/CSS/classroom_information.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script_classroom.js"></script>
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
    <?php
    $query = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($con, $query);
    $line = mysqli_fetch_array($result);

    if (isset($line["user_status"]) && $line["user_status"] === "pending") {
        header("Location: setting.php");
        //     echo "<div class='wrapper-update'>
        //     <div class='update'>
        //         <div class='title-update'><span>Update Your Password!</span></div>
        //         <div class='explanation'>
        //             <span>Important!</span>
        //             <span>To keep your account, you should change your password immediately!</span>
        //             <span class='br'>You will be moved to user setting page!</span>
        //         </div>
        //         <div class='button'>
        //             <button class='button-update'>Update</button>
        //         </div>
        //     </div>
        // </div>";

    }
    ?>

    <div class="wrapper-main">
        <div class="deck-layout">
            <!-- Example: For Teacher -->
            <ul>
                <!-- First Main -->
                <!-- Active Chinese Senin Kamis 20.30-->
                <li class="class-title">
                    <!-- Colored Title -->
                    <div class="title-to-review classroom-information-toggle">
                        <!-- Deck Title -->
                         <div class="container-classroom-information">
                             <span class="title">
                                 Classroom Information
                             </span>
                             <span class="title" id="contain">
                                 Active Chinese Senin Kamis 20.30
                             </span>
                         </div>
                         <div class="icon-ci" id="content">
                            <div class="arrow">▶</div>
                         </div>
                    </div>
                    <div class="subdeck">
                        <ul>
                            <!-- Second Main -->
                            <li class="contain-ci">
                                <table>
                                    <tr>
                                        <td>Level</td>
                                        <td>:</td>
                                        <td>Beginner</td>
                                    </tr>
                                    <tr>
                                        <td>Material</td>
                                        <td>:</td>
                                        <td>Active Chinese</td>
                                    </tr>
                                    <tr>
                                        <td>Class Type</td>
                                        <td>:</td>
                                        <td>Regular</td>
                                    </tr>
                                    <tr>
                                        <td>Access Type</td>
                                        <td>:</td>
                                        <td>Online</td>
                                    </tr>
                                    <tr>
                                        <td>Teacher</td>
                                        <td>:</td>
                                        <td>Laoshi Xinyue</td>
                                    </tr>
                                    <tr>
                                        <td>Schedule</td>
                                        <td>:</td>
                                        <td>Monday 20.30 - 21.30 (Online)</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>:</td>
                                        <td>Monday 20.30 - 21.30 (Online)</td>
                                    </tr>
                                    <tr>
                                        <td>Meet Link</td>
                                        <td>:</td>
                                        <td>https: zoom.id</td>
                                    </tr>
                                    <tr>
                                        <td>Class Notes</td>
                                        <td>:</td>
                                        <td>-</td>
                                    </tr>
                                </table>


                            </li>

                            <!-- Second Main Dari Sini -->

                        </ul>
                    </div>

                    <!-- Student List -->
                    <div class="title-to-review">
                        <!-- Deck Title -->
                        <span class="title">
                            Student List (7)
                        </span>
                        <!-- To Review Green Red Blue-->
                        <div class="to-review">
                            <span class="click">Add Deck to Classroom</span>
                        </div>
                    </div>

                </li>
                <!-- Sampe Sini (First)-->
            </ul>
        </div>
    </div>

</body>

</html>