<?php
session_start();
include "../../SQL_Queries/connection.php";

// INITIALIZE SESSION
if (!isset($_COOKIE['user_id'])) {
    header("Location: ../Login");
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
// END INITIALIZE SESSION

$user_id = $_COOKIE["user_id"];

// ==== QUERY : USER INFO ====
$stmtUser = $con->prepare("SELECT u.name, u.role, u.user_status, ur.role_name FROM users AS u
JOIN user_role AS ur ON u.role = ur.role_id
WHERE u.user_id = ?");
$stmtUser->bind_param("s", $user_id);
$stmtUser->execute();
$result = $stmtUser->get_result();
$line = $result->fetch_assoc();
$stmtUser->close();

// ==== MODEL : USER INFO ====
$name = $line['name'];
$roleId = $line['role'];
$userStatus = $line['user_status'];
$roleName = $line['role_name'];

// ==== ACCESS CONTROL ====
if ($roleId != 2) {
    header("Location: ../Login");
} 
if ($roleId == 2 && $userStatus == "active") {
    $_SESSION["rolePage"] = "Teacher";
}
if (isset($line["user_status"]) && $line["user_status"] === "pending") {
    header("Location: setting.php");
    exit;
}
// ==== END QUERY : USER INFO ====
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?= $name ?></title>
    <link rel="icon" href="../../Assets/Icons/1080.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
</head>

<body>
    <!-- Header -->
    <?php include "Component/header_login.php"; ?>

    <div class="right-bar">
        <div class="account-info">
            <span class="username"><?= $name ?></span>
            <span class="as" style="cursor: pointer;" onclick="Mode()"><?= $_SESSION["rolePage"] ?> Mode</span>
        </div>
        <script>

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
            <ul>
                <?php
                if ($_SESSION["rolePage"] === "Teacher") {
                    // Initialize variables
                    $classrooms = [];

                    // ==== QUERY : CLASSROOMS ====
                    $stmtClassroom = $con->prepare("SELECT * FROM junction_classroom_user AS jcu JOIN classroom AS c ON jcu.classroom_id = c.classroom_id WHERE jcu.user_id = ? ORDER BY c.name ASC");
                    $stmtClassroom->bind_param("s", $user_id);
                    $stmtClassroom->execute();
                    $result = $stmtClassroom->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $classrooms[] = $row;
                    }

                    $stmtClassroom->close();
                    // ==== END QUERY CLASSROOMS ====

                    // ==== VIEW : CLASSROOMS ====
                    foreach ($classrooms as $rowClass) {
                ?>
                        <li class="class-title" onclick="goToClassroom(this)" data-id="<?= $rowClass['classroom_id']; ?>">
                            <div class="title-to-review">
                                <span class="title"> <?= $rowClass['name']; ?> </span>
                                <div class="to-review">
                                    <span class="click">Click for More Information</span>
                                </div>
                            </div>
                        </li>
                <?php
                    }
                    // ==== END VIEW CLASSROOMS ==== 
                } else if ($_SESSION["rolePage"] === "Student") {

                }
                ?>
            </ul>
        </div>
    </div>

    <!-- ==== SCRIPT ==== -->
    <script>
        function goToClassroom(elem) {
            const classroomId = elem.getAttribute("data-id");
            window.location.href = `classroom_information.php?classroom_id=${classroomId}`;
        }

        function Mode() {
            window.location.href = "home_page_students.php";
        }
    </script>
</body>

</html>