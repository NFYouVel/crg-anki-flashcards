<?php
//Session
session_start();
include "../../../SQL_Queries/connection.php";
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
    <link rel="icon" href="../../../Assets/Icons/1080.png">
    <link rel="stylesheet" href="../../../Pages/Home/CSS/home_page.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../Home/jQuery/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        .wrapper-header {
            display: flex;
            position: relative;
        }

        .wrapper-header .header {
            display: flex;
            background-color: rgb(255, 165, 5);
            width: 100%;
            height: 9.65vh;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            height: 90%;
            align-items: center;
            margin: 8px 0 8px 0.6%;
        }

        .wrapper-header .header .logo img {
            object-fit: cover;
            height: 87%;

            filter:
                drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white) drop-shadow(0 0 1.5px white);
        }

        .right-bar {
            display: flex;
            height: 90%;
            width: auto;
        }

        .right-bar span {
            color: rgb(20, 61, 89);
            font-size: 2.7vh;
            text-align: right;
            font-weight: bold;
        }

        .right-bar .account-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .navbar {
            display: flex;
            width: 13vh;
            align-items: center;
            justify-content: center;
        }

        .navbar .icon {
            font-size: 6.65vh;
            cursor: pointer;
        }

        @media screen and (max-width: 768px) {
            .navbar .icon {
                font-size: 4vh;
            }

            .right-bar span {
                font-size: 1.7vh;
            }

            .logo {
                height: 62%;
                margin-left: 10px;
            }

            .navbar {
                display: flex;
                width: 8vh;
                align-items: center;
                justify-content: center;
            }
        }

        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #143D59;
            user-select: none;
        }

        .cardSwiperWrapper {
            width: 100%;
            height: calc(100% - 9.65vh);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cardSwiper {
            padding: 24px;
            position: relative;
            border-inline: 1px solid white;
            overflow: hidden;
            width: 450px;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            height: 75%;
            position: relative;
        }

        .card-face {
            border-radius: 8px;
            position: relative;
            background-color: white;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .intro {
            padding: 12px 24px;
            justify-content: space-evenly;
        }

        .intro-title {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .intro-instructions {
            font-size: 17px;
            color: #143D59;
            text-align: center;
            line-height: 1.9;
        }

        .intro-instructions .highlight-remember {
            color: #548235;
            font-weight: bold;
        }

        .intro-instructions .highlight-forgot {
            color: #FD5D5D;
            font-weight: bold;
        }

        .intro-actions {
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 80%;
            margin-top: 32px;
        }

        .button {
            border: none;
            padding: 10px 16px;
            width: 100%;
            font-size: 17px;
            cursor: pointer;
            border-radius: 8px;
            text-align: center;
            display: flex;
            justify-content: center;
        }

        .continue {
            background-color: rgb(255, 165, 5);
            color: white;
        }

        .actions {
            position: relative;
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: end;
            width: calc(100% + 48px);
        }

        .counter {
            height: 48px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-width: 50px;
            padding-inline: 12px;
            font-size: 14px;
        }

        .counter span {
            letter-spacing: 0.8px;
        }

        .forgot {
            background-color: #FD5D5D;
            color: white;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .remember {
            background-color: #E2F0D9;
            color: #548235;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        @media screen and (max-width: 768px) {
            .cardSwiper {
                padding: 12px 24px
            }

            .card {
                height: 420px;
            }

            .counter {
                height: 42px;
            }

            .intro-actions {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->

    <?php
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

        setcookie('user_id', '', time() - (86400 * 30), '/', '', false, true);
        setcookie('loginAt', '', time() - (86400 * 30), '/', '', false, true);

        header("Location: ../../../Login/");
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

    <?php
    $deckId = $_GET["deckId"];
    ?>

    <div class="wrapper-header">
        <div class="header">
            <div class="logo">
                <img src="../../../Logo/1080.png" alt="CRG Logo" style="cursor: pointer;"
                    onclick="<?php echo $userRole; ?>">
            </div>

            <script>
                function BackHome() {
                    window.location.href = "../home_page_card_swipe.php"
                }

                function BackHomeTeacher() {
                    window.location.href = "../home_page_card_swipe.php"
                }
            </script>

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
    <div class="account-logout">
        <a href="../setting.php" class="account">Settings</a>
        <a href="../exit.php" class="logout">Logout</a>
    </div>

    <div class="cardSwiperWrapper">
        <div class="cardSwiper">
            <div class="card">
                <div class="card-face intro">
                    <h1 class="intro-title">Flashcard Swipe</h1>
                    <p class="intro-instructions">
                        Tap the card to see the pinyin and meaning<br>
                        &bull; Swipe <span class="highlight-remember">Right</span> if you remember it<br>
                        &bull; Swipe <span class="highlight-forgot">Left</span> if you want to study again
                    </p>
                    <div class="intro-actions">
                        <button class="button continue" onclick="window.location.href = 'cardSwipe.php?deckId=<?php echo $deckId ?>'">Start Swiping</button>
                        <p style="text-align: center; font-size: 15px; color: #143D59;">
                            Show the meaning in:
                            <span style="color: #FFA500; font-weight: bold; cursor: pointer;" onclick="toggleLanguage()">
                                <span id="langLabel">Indonesian</span>
                                <span style="font-size: 18px;">⇄</span>
                            </span>
                        </p>

                        <script>
                            function toggleLanguage() {
                                const current = localStorage.getItem("cardSwipeMeaningLanguage") || "meaning_ina";
                                const next = current === "meaning_ina" ? "meaning_eng" : "meaning_ina";
                                localStorage.setItem("cardSwipeMeaningLanguage", next);
                                document.getElementById("langLabel").textContent = next === "meaning_ina" ? "Indonesian" : "English";
                            }

                            // Set initial label on load
                            const saved = localStorage.getItem("cardSwipeMeaningLanguage") || "meaning_ina";
                            document.getElementById("langLabel").textContent = saved === "meaning_ina" ? "Indonesian" : "English";
                        </script>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>