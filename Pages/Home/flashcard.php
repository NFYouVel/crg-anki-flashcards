<?php
// User ID
session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_assoc($result);
if ($line['role'] == 0) {
    $role = "Admin";
} else if ($line['role'] == 1) {
    $role = "Teacher";
} else {
    $role = "Student";
}
include "../Admin/convertPinyin.php";

// Deck ID
if (!isset($_GET['deck_id'])) {
    $deckID = $_SESSION['temp_deck_id'];
} else {
    $deckID = $_GET['deck_id'];
}
$_SESSION['temp_deck_id'] = $deckID;

// Blue Green Red Count
include_once "repetition_flashcard.php";
$counts = mysqli_fetch_assoc($query_flashcard_rbg_count);
$blue = $counts['blue'];
$green = $counts['green'];
$red = $counts['red'];

// Algorithm Flashcard
if ($green !== 0) {
    $query_flashcard_algorithm = mysqli_query($con, "
    SELECT card.*, cp.*
    FROM cards AS card
    JOIN junction_deck_card AS jdc
        ON card.card_id = jdc.card_id
    JOIN junction_deck_user AS jdu
        ON jdc.deck_id = jdu.deck_id
    JOIN card_progress AS cp
        ON cp.user_id = jdu.user_id AND cp.card_id = card.card_id
    WHERE jdc.deck_id = '$deckID' AND jdu.user_id = '$user_id' AND cp.review_due <= NOW() LIMIT 1
    ");
} else {
    $query_flashcard_algorithm = mysqli_query($con, "
    SELECT card.*, cp.*
    FROM cards AS card
    JOIN junction_deck_card AS jdc
        ON card.card_id = jdc.card_id
    JOIN junction_deck_user AS jdu
        ON jdc.deck_id = jdu.deck_id
    JOIN card_progress AS cp
        ON cp.user_id = jdu.user_id AND cp.card_id = card.card_id
    WHERE jdc.deck_id = '$deckID' AND jdu.user_id = '$user_id' AND cp.total_review = 0 LIMIT 1
    ");
}

?>
<!-- ------------------------------------------------------------------ -->
<!-- DOCTYPE HTML -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../Logo/circle.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../../Pages/Home/CSS/flashcard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
    <script src="../Home/jQuery/script_flashcard.js"></script>
    <style>
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3c91e6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        #flashcard-message {
            position: fixed;
            margin-bottom: 10em;
            justify-content: center;
            align-items: center;
            background-color: #222;
            color: #fff;
            font-size: 20px;
            padding: 20px 30px;
            border-radius: 12px;
            z-index: 10000;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            animation: fadeInOnly 0.6s ease-out;
            animation-fill-mode: forwards;
        }

        @keyframes fadeInOnly {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
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

    <!-- + - and to review -->
    <div class="title-to-review">
        <!-- Deck Title -->
        <div>
            <span class="calc" id="zoomIn">+</span>
            <span class="calc" id="zoomDisplay">100%</span>
            <span class="calc" id="zoomOut">-</span>
        </div>
        <!-- To Review Green Red Blue-->
        <div class="to-review">
            <span class="green"><?php echo $green ?></span>
            <span class="red"><?php echo $red ?></span>
            <span class="blue"><?php echo $blue ?></span>
        </div>
    </div>

    <!-- Cards -->
    <div class="wrapper-flashcard" id="target">
        <div class="wrapper-mid">
            <?php
            if ($row = mysqli_fetch_assoc($query_flashcard_algorithm)) {
                $pinyin = convert($row["pinyin"]);
            ?>
                <div class="vocab-card">
                    <span class="hanzi"><?php echo htmlspecialchars($row['chinese_sc']); ?></span>
                    <span class="pinyin"><?php echo htmlspecialchars($pinyin); ?></span>
                    <span class="word-class"><?php echo htmlspecialchars($row['word_class']); ?></span>
                    <table>
                        <tr>
                            <td class="sub">EN</td>
                            <td class="colon">:</td>
                            <td class="meaning"><?php echo htmlspecialchars($row['meaning_eng']); ?></td>
                        </tr>
                        <tr>
                            <td class="sub">ID</td>
                            <td class="colon">:</td>
                            <td class="meaning"><?php echo htmlspecialchars($row['meaning_ina']); ?></td>
                        </tr>
                    </table>
                </div>

                <?php
                $temp_card_id = $row['card_id'];
                $query_sentence = mysqli_query($con, "
                SELECT sentence.*
                FROM junction_card_sentence AS jcs
                JOIN example_sentence AS sentence
                    ON sentence.sentence_code = jcs.sentence_code
                WHERE jcs.card_id = $temp_card_id
                ");

                while ($line = mysqli_fetch_array($query_sentence)) {
                    echo "<div class='sentence'>
                    <div class='chinese-sentence'>
                        <span class='sentence'>{$line['chinese_tc']}</span>
                        <a class='report'>Report Sentence</a>
                    </div>
                    <span class='pinyin'>{$line['pinyin']}</span>
                    <table>
                        <tr>
                            <td class='sub'>EN</td>
                            <td class='colon'>:</td>
                            <td class='meaning'>{$line['meaning_eng']}</td>
                        </tr>
                        <tr>
                            <td class='sub'>ID</td>
                            <td class='colon'>:</td>
                            <td class='meaning'>{$line['meaning_ina']}</td>
                        </tr>
                    </table>
                </div>";
                }
                ?>

            <?php
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <button class="wrapper-show-answer">
        <span href="#" class="show">Show Answer</span>
    </button>
    <div class="wrapper-show-answer" id="flashcard-form" data-card-id="<?php echo $temp_card_id; ?>">
        <button type="button" id="criteria" class="criteria forgot" data-status="forgot" data-cs="<?php echo $row['current_stage'] ?>">
            <span>X</span><span>Forgot</span>
        </button>
        <button type="button" id="criteria" class="criteria hard" data-status="hard" data-cs="<?php echo $row['current_stage'] ?>">
            <span>...</span><span>Hard</span>
        </button>
        <button type="button" id="criteria" class="criteria remember" data-status="remember" data-cs="<?php echo $row['current_stage'] ?>">
            <span>V</span><span>Remember</span>
        </button>
    </div>

    <script>
        let fontSize = 100;
        const zoomStep = 10;
        const minZoom = 50;
        const maxZoom = 200;

        const zoomTarget = document.querySelector('.wrapper-flashcard');
        const zoomDisplay = document.getElementById('zoomDisplay');

        function applyZoom() {
            zoomTarget.style.fontSize = fontSize + '%';
            zoomDisplay.textContent = fontSize + '%';
        }

        document.getElementById('zoomIn').addEventListener('click', () => {
            if (fontSize < maxZoom) {
                fontSize += zoomStep;
                applyZoom();
            }
        });

        document.getElementById('zoomOut').addEventListener('click', () => {
            if (fontSize > minZoom) {
                fontSize -= zoomStep;
                applyZoom();
            }
        });

        applyZoom();
    </script>

    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="flashcard-message" style="display:none;">Niceee</div>
    </div>
</body>

</html>