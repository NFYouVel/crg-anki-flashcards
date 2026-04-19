<?php
// Connnetion
// include "../../SQL_Queries/connection.php";
date_default_timezone_set('Asia/Jakarta');

// Session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
$user_id = $_SESSION["user_id"];

// Kick if the user has changed the password
$stmtCheckPassword = $con->prepare("SELECT * FROM users WHERE user_id = ?");
$stmtCheckPassword->bind_param("s", $user_id);
$stmtCheckPassword->execute();
$result = $stmtCheckPassword->get_result();
$line = $result->fetch_array();

$passwordUpdatedAt = strtotime($line['updatedPasswordAt']);
$loginAt = (int) $_COOKIE['loginAt'];

if (!isset($_COOKIE['loginAt']) || ($loginAt < $passwordUpdatedAt)) {
    session_start();
    session_unset();
    session_destroy();

    // Hapus kedua cookie-nya
    setcookie('user_id', '', time() - (86400 * 30), '/', '', false, true);
    setcookie('loginAt', '', time() - (86400 * 30), '/', '', false, true);

    header("Location: ../Login/");
    exit();
}
$stmtCheckPassword->close();

// User ID & Role (JOIN)
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_assoc($result);
$role_id = $line['role'];
$result2 = mysqli_query($con, "SELECT * FROM user_role WHERE role_id = '$role_id'");
$line2 = mysqli_fetch_assoc($result2);
$role = $line2['role_name'];

// Admin
$ps = password_hash('%^&*()', PASSWORD_BCRYPT);
$check = mysqli_query($con, "SELECT * FROM users WHERE email = 'iten@gmail.com'");
if (mysqli_num_rows($check) == 0) {
    $insert = mysqli_query($con, "INSERT INTO users (name, email, password_hash, role, user_status, created_at) VALUES ('iten', 'iten@gmail.com', '$ps', 1, 'active', NOW())");
}

// Rollback by Role
if ($role == "Teacher") {
    $rolePage = $_SESSION["rolePage"];
}

$userRole;
if ($role == "Student") {
    $userRole = "BackHome()";
} else if ($rolePage == "Teacher" && $role == "Teacher") {
    $userRole = "BackHomeTeacher()";
} else if ($rolePage == "Student" && $role == "Teacher") {
    $userRole = "BackHome()";
}

?>
<div class="wrapper-header">
    <!-- Untuk Logo di atas (header) -->
    <div class="header">
        <div class="logo">
            <img src="../../../Logo/1080.png" alt="CRG Logo" style="cursor: pointer;"
                onclick="<?php echo $userRole; ?>">
        </div>

        <script>
            function BackHome() {
                window.location.href = "../home_page_card_matching.php"
            }

            function BackHomeTeacher() {
                window.location.href = "../home_page.php"
            }
        </script>