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
if($role_id != 2) {
    header("Location: ../Login");
}
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
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
        exit;
    }
    ?>

    <div class="wrapper-main">
        <div class="deck-layout">
            <!-- Example: For Teacher -->
            <ul>
                <!-- First Main -->
                <!-- Active Chinese Senin Kamis 20.30-->
                <?php
                $query = "SELECT * FROM junction_classroom_user WHERE user_id = '$user_id'";
                $result = mysqli_query($con, $query);

                while ($rowClassRaw = mysqli_fetch_array($result)) {
                    $classroom = mysqli_real_escape_string($con, $rowClassRaw['classroom_id']);
                    $query = "SELECT * FROM classroom WHERE classroom_id = '$classroom'";
                    $classroomResult = mysqli_query($con, $query);
                    $rowClass = mysqli_fetch_array($classroomResult);
                    $temp = $rowClass['classroom_id'];
                    echo "<li class='class-title' onclick='goToClassroom(this)' data-id='$temp'>"; // ????
                    $temp_classroom = $rowClass['classroom_id'];
                    // Colored Title
                    echo "<div class='title-to-review'>";
                    // Deck Title
                    echo "<span class='title'>";
                    echo $rowClass['name'];
                    echo "</span>";

                    // To Review Green Red Blue
                    echo
                    "<div class='to-review'>
                            <span class='click'>Click for More Information</span>
                        </div>
                        </div>";

                    echo "</li>";
                }
                ?>

                <!-- Third Main -->



                <!-- </li> -->
                <!-- Sampe Sini (Second)-->



                <!-- Sampe Sini (First)-->
            </ul>
        </div>
    </div>
    <script>
        function goToClassroom(elem) {
            const classroomId = elem.getAttribute("data-id");
            window.location.href = `classroom_information.php?classroom_id=${classroomId}`;
        }
    </script>


</body>

</html>