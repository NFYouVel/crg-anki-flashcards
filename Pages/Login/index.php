<?php
include "../../SQL_Queries/connection.php";

$name = 'Herodian Petro Marlim';
$email = 'herodianpm@gmail.com';
$role = 1;
$user_status = 'active';
$character_set = 'simplified';

// 1. Cek dulu, apakah user dengan email ini udah ada?
$check = $con->prepare("SELECT email FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows == 0) {
    // User belum ada, lanjut buat password hash dan insert
    $pw = password_hash('Bloomingwordpress8!', PASSWORD_BCRYPT);

    $stmt = $con->prepare("INSERT INTO users(name,email,password_hash,role,user_status,character_set,created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssiss", $name, $email, $pw, $role, $user_status, $character_set);
    
    if ($stmt->execute()) {
        echo "User inserted successfully!";
    } else {
        echo "Error inserting user: " . $stmt->error;
    }
    
    $stmt->close();
}

$check->close();
$con->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../../Logo/circle.png">
    <link rel="stylesheet" href="CSS/index.css">
</head>

<body>
    <!-- Header -->
    <?php include "../Global Assets/header.php"; ?>

    <!-- Form -->
    <?php
    // Save email
    include "../../SQL_Queries/connection.php";
    if (isset($_POST['email'])) {
        $_SESSION['sv_email'] = $_POST['email'];
    }
    ?>
    <div class="wrapper">
        <div class="form-side">
            <span class="h2">Login</span>
            <span class="description">Login in to your account</span>
            <form method="post">
                <input type="email" name="email" placeholder="Email"
                    <?php
                    if (isset($_SESSION['sv_email'])) {
                        $temp_email = $_SESSION['sv_email'];
                        echo 'value="' . $temp_email . '"';
                    }
                    ?>> <br>
                <input type="password" name="password" placeholder="Password" required> <br>
                <div class="remember">
                    <div class="in-remember">
                        <input type="checkbox" name="cookie" value="check">
                        <span>Remember Me</span> <br>
                    </div>

                </div>
                <input type="submit" value="Log In" name="submit" class="submit" id="submit">
            </form>
            <span class='alert' id="error" style='visibility: hidden;'>Wrong email or password. Please contact our admin</span>
            <a href="forgetpassword.php">Reset Password</a>

            <!-- Script PHP -->
            <?php
            session_start();

            // Kalo ada cookie
            if (isset($_COOKIE['user_id'])) {
                $_SESSION['user_id'] = $_COOKIE['user_id'];
                $user_id = $_SESSION['user_id'];
                $result = mysqli_query($con, "SELECT * FROM users WHERE user_id = '$user_id'");
                $row = mysqli_fetch_assoc($result);
                if ($row['role'] == 2) {
                    header("Location: ../Home/home_page.php");
                } else if ($row['role'] == 1) {
                    header("Location: ../Admin/homepage.php");
                } else {
                    header("Location: ../Home/home_page_students.php");
                }
                exit;
            }
            // Kalo cookie brute force is set
            date_default_timezone_set('Asia/Jakarta');
            $time_now_plus_10 = date('l, d F Y H:i:s', strtotime('+10 minutes'));
            $md5 = md5('exp');
            if (isset($_COOKIE[$md5])) {
                echo "<script>document.getElementById('error').innerHTML = 'Too many failed attempts. Please try again 10 minutes later. Unlock: $time_now_plus_10.';</script>";
                echo "<script>document.getElementById('error').style.visibility = 'visible';</script>";
                echo "<script>document.getElementById('submit').style.display = 'none';</script>";
                $_SESSION['count_brute_force'] = 1;
            }

            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password_raw = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
            $submit = filter_input(INPUT_POST, 'submit');
            $cookie = filter_input(INPUT_POST, 'cookie');

            if ($email && $password_raw && $submit) {
                $password = password_hash($password_raw, PASSWORD_BCRYPT);
                $query = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($con, $query);

                if (!isset($_SESSION['count_brute_force'])) {
                    $_SESSION['count_brute_force'] = 1;
                }

                // echo "Chance remaining: ";
                // echo 4 - $_SESSION['count_brute_force'];
                if ($_SESSION['count_brute_force'] >= 5) {
                    setcookie(md5('fake1'), md5("funlock1"), time() + (600), "/");
                    setcookie(md5('fake2'), md5("funlock2"), time() + (600), "/");
                    setcookie(md5('exp'), md5("unlock"), time() + (600), "/");
                    $_SESSION['count_brute_force'] = 1;
                    echo "<script>
                    document.getElementById('error').innerHTML = 'Too many failed attempts. Please try again 10 minutes later. Unlock: $time_now_plus_10';
                    document.getElementById('error').style.visibility = 'visible';
                    document.getElementById('submit').style.display = 'none';</script>";
                    header("Location: index.php");
                    exit;
                }

                if ($line = mysqli_fetch_array($result)) {
                    if (password_verify($password_raw, $line["password_hash"])) {
                        if ($line['user_status'] == "suspended") { // Kalo account suspended
                            echo "<script>document.getElementById('error').innerHTML = 'Your account is suspended. Contact admin for more information';</script>";
                            echo "<script>document.getElementById('error').style.visibility = 'visible';</script>";
                        } else if ($line['user_status'] == "deleted") { //Kalo account deleted
                            echo "<script>document.getElementById('error').innerHTML = 'Your account is deleted. Contact admin for more information';</script>";
                            echo "<script>document.getElementById('error').style.visibility = 'visible';</script>";
                        } else { // Kalo berhasil
                            $_SESSION['count_brute_force'] = 0;
                            $_SESSION["user_id"] = $line['user_id'];
                            if (filter_input(INPUT_POST, 'cookie', FILTER_UNSAFE_RAW)) { // Kalo remember me dichecklist
                                setcookie('user_id', $line['user_id'], time() + (86400 * 30), '/', '', false, true);
                            } else { // Kalo remember me ga dichecklist
                                setcookie('user_id', $line['user_id'], time() + (86400), '/', '', false, true);
                            }

                            if ($line['role'] == 2) {
                                header("Location: ../Home/home_page.php");
                            } else if ($line['role'] == 1) {
                                header("Location: ../Admin/homepage.php");
                            } else {
                                header("Location: ../Home/home_page_students.php");
                            }
                            exit;
                        }
                    } else { // Kalo password salah
                        $_SESSION['count_brute_force']++;
                        echo "<script>document.getElementById('error').style.visibility = 'visible';</script>";
                        exit;
                    }
                } else { // Kalo data tidak ditemukan
                    $_SESSION['count_brute_force']++;
                    echo "<script>document.getElementById('error').style.visibility = 'visible';</script>";
                    exit;
                }
            }
            ?>
        </div>
    </div>

</body>

</html>