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
            margin-top: 24px;
            height: calc(100% - 24px);
            height: 75%;
            position: relative;
        }

        .card-inner {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.6s;
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
            text-align: center;
            width: 80%;
        }

        .actions {
            position: relative;
            margin-top: auto;
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
            transition: background-color 0.1s, color 0.1s;
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

        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            background-color: #ccc;
            border-radius: 34px;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            transition: 0.4s;
        }

        .slider:before {
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            position: absolute;
            border-radius: 50%;
            transition: 0.4s;
        }

        input:checked+.slider {
            background-color: orange;
        }

        input:checked+.slider:before {
            transform: translateX(16px);
        }

        .swipe-label {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 22px;
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.1s;
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

        // Hapus kedua cookie-nya
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
    if (!isset($deckId)) {
        echo "<script>alert('Error fetching deck')</script>";
        echo "<script>window.location.href = '../../../Login/'</script>";
        exit();
    }

    //check if is available session
    $checkSession = mysqli_query($con, "SELECT card_swipe_id, session_started_at FROM card_swipe_session WHERE user_id = '$user_id' AND deck_id = '$deckId'");
    if (mysqli_num_rows($checkSession) > 0) {
        $session = mysqli_fetch_assoc($checkSession);
        $cardSwipeId = $session['card_swipe_id'];

        if (strtotime($session["session_started_at"]) < strtotime('-3 days')) {
    ?>
            <script>
                alert('It has been 3 days since you last reviewed, please start a new session.');
                $.ajax({
                    url: "AJAX/resetSession.php",
                    type: "POST",
                    data: {
                        cardSwipeId: '<?php echo $cardSwipeId ?>'
                    },
                    dataType: "json",
                    success: function(data) {
                        location.reload();
                    }
                });
            </script>
    <?php
            exit();
        }

        //update session
        $updateSession = mysqli_query($con, "
            UPDATE card_swipe_session 
            SET session_started_at = NOW() 
            WHERE card_swipe_id = '$cardSwipeId'
        ");

        $getCards = mysqli_query($con, "
            SELECT card_id, status FROM card_swipe_progress csp
            WHERE card_swipe_id = '$cardSwipeId' AND status != 'inactive'
        ");

        $cards = array();
        while ($row = mysqli_fetch_assoc($getCards)) {
            $cards[] = array(
                'cardId' => $row['card_id'],
                'status' => $row['status']
            );
        }

        echo "<script>const hasSession = true;</script>";
    } else {
        //create new session
        $cardSwipeId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );

        $insertSession = mysqli_query($con, "
            INSERT INTO card_swipe_session 
            (card_swipe_id, user_id, deck_id, session_started_at) 
            VALUES 
            ('$cardSwipeId', '$user_id', '$deckId', NOW())
        ");

        if ($deckId == "main") {
            $getCards = mysqli_query($con, "
                SELECT DISTINCT jdc.card_id
                FROM junction_deck_user jdu
                INNER JOIN decks d ON jdu.deck_id = d.deck_id
                INNER JOIN junction_deck_card jdc ON jdu.deck_id = jdc.deck_id
                WHERE jdu.user_id = '$user_id' AND d.is_leaf = 1
            ");
        } else {
            $getCards = mysqli_query(
                $con,
                "
                SELECT DISTINCT jdc.card_id
                FROM junction_deck_card jdc
                INNER JOIN leaf_deck_map ldm ON jdc.deck_id = ldm.leaf_deck_id
                WHERE ldm.deck_id = '$deckId'"
            );
        }

        $cards = array();
        while ($card = mysqli_fetch_array($getCards)) {
            $cards[] = array(
                'cardId' => $card['card_id'],
                'status' => 'unseen'
            );
        }

        if (!empty($cards)) {
            mysqli_query(
                $con,
                "INSERT IGNORE INTO card_swipe_progress (card_swipe_id, card_id, status) VALUES " .
                    implode(", ", array_map(function ($card) use ($cardSwipeId) {
                        return "('$cardSwipeId', '{$card['cardId']}', 'unseen')";
                    }, $cards))
            );
        }

        echo "<script>const hasSession = false;</script>";
    }

    echo "<script>
        const cards = " . json_encode($cards) . ";
        const cardSwipeId = '$cardSwipeId';
        const deckId = '$deckId';
    </script>";
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
            <div class="progress">
                <div style="display: flex; align-items: center; position: relative;">
                    <h3 class="progressText" style="flex: 1; text-align: center;">0/0</h3>
                    <div style="position: absolute; right: 0; display: flex; align-items: center; gap: 6px;">
                        <span style="color: white; font-family: 'Nunito', sans-serif; font-size: 13px;">Shuffle Cards</span>
                        <label class="switch">
                            <input type="checkbox" id="shuffleToggle"
                                onchange="localStorage.setItem('useShuffle', this.checked); location.reload();">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                <div class="progressBar">
                    <div class="progressBarFill"></div>
                </div>
            </div>

            <script>
                document.getElementById("shuffleToggle").checked = localStorage.getItem("useShuffle") === "true";
            </script>
            <div class="card">
                <div class="card-inner">
                    <div class="card-face front">
                        <span class="hanzi">我</span>
                        <span style="font-size: 13px; color: #aaa; position: absolute; bottom: 20px;">Tap to reveal</span>
                    </div>
                    <div class="card-face back">
                        <span class="hanzi">我</span>
                        <span class="pinyin">wo</span>
                        <span class="meaning">saya; aku</span>
                    </div>
                    <div class="card-face finish">
                        <h1 style="font-size: 22px;">Congratulations!</h1>
                        <h2 class="finish-text" style="text-align: center; font-size: 16px;">You just studied <span
                                class="studied">0</span> terms in this session! Continue reviewing to learn the
                            remaining <span class="to-learn">0</span></h2>
                        <div class="finish-actions">
                            <button class="button continue">Continue Review</button>
                            <button class="button restart">Restart Flashcard</button>
                        </div>
                    </div>
                </div>
                <div class="swipe-label" id="swipeLabel"></div>
            </div>
            <div class="actions">
                <div class="counter forgot"><span class="forgot-number" data-count="0">0</span></div>
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
                <div class="counter remember"><span class="remember-number" data-count="0">0</span></div>
            </div>
        </div>
</body>

<script>
    const useShuffle = localStorage.getItem('useShuffle') === 'true';

    console.log(cards)

    let cardList = cards.filter(card => card.status === "forgot" || card.status === "unseen");

    if (useShuffle) {
        cardList = cardList.sort(() => Math.random() - 0.5);
    }

    const card = document.querySelector(".card");
    const cardInner = document.querySelector(".card-inner");
    const swiper = document.querySelector(".card");

    const hammer = new Hammer(swiper);

    hammer.get('pan').set({
        direction: Hammer.DIRECTION_ALL
    });

    let currentX = 0;
    let isDragging = false;
    let count = 0;
    const cardListTotal = cardList.length;

    var isDone = false;

    let isFlipped = false;
    let hasRevealed = false;
    let wasDragging = false;

    $(document).ready(function() {
        $(".restart").click(function() {
            $.ajax({
                url: "AJAX/resetSession.php",
                type: "POST",
                data: {
                    cardSwipeId: cardSwipeId
                },
                dataType: "json",
                success: function(data) {
                    location.reload();
                }
            });
        })

        $(".continue").click(function() {
            location.reload();
        })

        if (cardList.length === 0) {
            finishSession();
            return;
        }

        nextCard();

        $(".finish").hide();

        $(".wrong").click(function() {
            forgot();
        })
        $(".correct").click(function() {
            remember();
        })
    })

    function updateCounters() {
        $(".remember-number").attr("data-count", $(".remember-number").text());
        $(".forgot-number").attr("data-count", $(".forgot-number").text());
    }

    function resetCounterHighlight() {
        $(".counter.remember").css("background-color", "");
        $(".counter.remember").css("color", "");
        $(".counter.forgot").css("background-color", "");
        $(".counter.forgot").css("color", "");
        $(".remember-number").text($(".remember-number").attr("data-count") || "0");
        $(".forgot-number").text($(".forgot-number").attr("data-count") || "0");
    }

    function forgot() {
        if (isDone || !hasRevealed) return;

        $.ajax({
            url: "AJAX/updateProgress.php",
            type: "POST",
            data: {
                cardId: cardList[count - 1].cardId,
                cardSwipeId: cardSwipeId,
                status: "forgot"
            },
            dataType: "json",
            success: function(data) {
                animateOut("left");
                $(".forgot-number").text(parseInt($(".forgot-number").attr("data-count")) + 1);
                updateCounters();
                isFlipped = false;
                hasRevealed = false;
                cardList[count - 1].status = "forgot";
            }
        });
    }

    function remember() {
        if (isDone || !hasRevealed) return;

        $.ajax({
            url: "AJAX/updateProgress.php",
            type: "POST",
            data: {
                cardId: cardList[count - 1].cardId,
                cardSwipeId: cardSwipeId,
                status: "remember"
            },
            dataType: "json",
            success: function(data) {
                animateOut("right");
                $(".remember-number").text(parseInt($(".remember-number").attr("data-count")) + 1);
                updateCounters();
                isFlipped = false;
                hasRevealed = false;
                cardList[count - 1].status = "remember";
            }
        });
    }

    function finishSession() {
        isDone = true;
        if ($(".forgot-number").attr("data-count") === "0") {
            $(".finish-text").text("You have studied all of them! Continue to Smart Review Mode for more in-depth learning.")
            $(".continue").text("Continue to Smart Review")

            $(".continue").click(function() {
                window.location.href = "../flashcard.php?deck_id=" + deckId;
            })
        } else {
            $(".studied").text($(".remember-number").attr("data-count"));
            $(".to-learn").text($(".forgot-number").attr("data-count"));
        }

        cardInner.classList.remove("flipped");

        $(".front").hide();
        $(".back").hide();
        $(".finish").show();
        $(".wrong").css("opacity", 0);
        $(".correct").css("opacity", 0);
    }

    function flipCard() {
        isFlipped = cardInner.classList.toggle("flipped");
        if (isFlipped) hasRevealed = true;
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
        if (count >= cardListTotal) {
            isDone = true;
            finishSession();
            return;
        }
        $.ajax({
            url: "AJAX/getCardData.php",
            type: "POST",
            data: {
                cardId: cardList[count].cardId
            },
            dataType: "json",
            success: function(data) {
                $(".hanzi").text(data.hanzi);
                $(".pinyin").text(data.pinyin);
                $(".meaning").text(localStorage.getItem("cardSwipeMeaningLanguage") === "meaning_ina" ? data.meaning_ina : data.meaning_eng);

                $(".hanzi").css("opacity", 1);
                $(".pinyin").css("opacity", 1);
                $(".meaning").css("opacity", 1);

                count++;

                updateProgress(count, cardListTotal);

                isFlipped = false;
                hasRevealed = false;
                $(".card-inner").css("transition", "none");
                cardInner.classList.remove("flipped");
                setTimeout(() => {
                    $(".card-inner").css("transition", "transform 0.6s");
                }, 50)
            }
        });
    }

    function resetCardStyles() {
        card.style.transition = "none";
        card.style.transform = "";
        card.style.opacity = 1;
        document.querySelector(".card-face.front").style.backgroundColor = "";
        document.querySelector(".card-face.back").style.backgroundColor = "";
        document.querySelector(".card-face.front").style.border = "";
        document.querySelector(".card-face.back").style.border = "";
        document.querySelectorAll(".card-face .hanzi, .card-face .pinyin, .card-face .meaning").forEach(el => el.style.color = "");
        $("#swipeLabel").css("opacity", 0);
    }

    function animateOut(direction) {
        const moveX = direction === "right" ? 500 : -500;

        card.style.transition = "transform 0.3s";
        card.style.transform = `
            translateX(${moveX}px)
            rotate(${moveX * 0.05}deg)
        `;

        setTimeout(() => {
            resetCardStyles();
            resetCounterHighlight();

            $(".hanzi").css("opacity", 0);
            $(".pinyin").css("opacity", 0);
            $(".meaning").css("opacity", 0);

            nextCard(direction);
        }, 300);
    }

    hammer.on("panstart", function() {
        isDragging = true;
        wasDragging = true;
    });

    hammer.on("panmove", function(ev) {
        if (!isDone && hasRevealed) {

            const maxSwipe = 150;
            currentX = Math.max(-maxSwipe, Math.min(maxSwipe, ev.deltaX));

            const opacity = 1 - Math.min(Math.abs(currentX) / maxSwipe, 1);
            const progress = Math.abs(currentX) / maxSwipe;

            const r = currentX > 0 ? Math.round(255 - (255 - 130) * progress) : 255;
            const g = currentX > 0 ? 255 : Math.round(255 - (255 - 93) * progress);
            const b = currentX > 0 ? Math.round(255 - (255 - 130) * progress) : Math.round(255 - (255 - 93) * progress);

            const activeFace = isFlipped ?
                document.querySelector(".card-face.back") :
                document.querySelector(".card-face.front");
            activeFace.style.backgroundColor = `rgb(${r}, ${g}, ${b})`;

            const textColor = currentX > 0 ?
                `rgb(${Math.round(60 * progress)}, 180, ${Math.round(60 * progress)})` :
                `rgb(220, ${Math.round(60 * progress)}, ${Math.round(60 * progress)})`;
            activeFace.querySelectorAll(".hanzi, .pinyin, .meaning").forEach(el => el.style.color = textColor);

            if (currentX > 0) {
                $("#swipeLabel").text("Remember").css({
                    color: "#6ca944",
                    opacity: 1,
                    fontWeight: "lighter"
                });
                $(".counter.remember").css({
                    "background-color": "white",
                    "color": "#548235"
                });
                $(".remember-number").text("+1");
                $(".counter.forgot").css({
                    "background-color": "",
                    "color": ""
                });
                $(".forgot-number").text($(".forgot-number").attr("data-count") || "0");
            } else if (currentX < 0) {
                $("#swipeLabel").text("Study Again").css({
                    color: "#FD5D5D",
                    opacity: 1,
                    fontWeight: "lighter"
                });
                $(".counter.forgot").css({
                    "background-color": "white",
                    "color": "#FD5D5D"
                });
                $(".forgot-number").text("+1");
                $(".counter.remember").css({
                    "background-color": "",
                    "color": ""
                });
                $(".remember-number").text($(".remember-number").attr("data-count") || "0");
            }

            card.style.transform = `
            translateX(${currentX}px)
            rotate(${currentX * 0.05}deg)
        `;
            card.style.opacity = opacity;
        }
    });

    hammer.on("panend", function(ev) {
        isDragging = false;
        setTimeout(() => wasDragging = false, 50);
        if (!isDone && hasRevealed) {
            const threshold = 20;

            if (ev.deltaX > threshold) {
                remember();
            } else if (ev.deltaX < -threshold) {
                forgot();
            } else {
                card.style.transition = "transform 0.3s";
                card.style.transform = "";
                card.style.opacity = 1;
                resetCardStyles();
                resetCounterHighlight();
            }
        }
    });

    hammer.on("tap", function() {
        if (isDone || wasDragging) {
            return;
        }
        flipCard();
    });
</script>

</html>