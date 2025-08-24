<?php
include "../../SQL_Queries/connection.php";
// User ID
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
// Saved Zoom
if (isset($_SESSION['zoom'])) {
    $savedZoom = $_SESSION['zoom'];
} else {
    $savedZoom = 100;
}

// Call the user
$user_id = $_SESSION["user_id"];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_assoc($result);
$chara_set = $line['character_set'];

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
    if ($deckID == "main") {
        $query_flashcard_algorithm = mysqli_query($con, "
        SELECT c.pinyin, c.chinese_tc, c.chinese_sc, c.word_class, c.meaning_eng, c.meaning_ina, c.card_id, cp.current_stage, cp.review_due
            FROM junction_deck_user AS du 
            JOIN decks AS d ON d.deck_id = du.deck_id
            JOIN junction_deck_card AS dc ON d.deck_id = dc.deck_id
            JOIN cards AS c ON dc.card_id = c.card_id
            JOIN card_progress AS cp ON c.card_id = cp.card_id AND cp.user_id = du.user_id
            WHERE du.user_id = '$user_id' AND d.is_leaf = 1 AND cp.review_due <= NOW() ORDER BY dc.priority ASC LIMIT 1
        ");
    } else {
        $query_flashcard_algorithm = mysqli_query($con, "
        WITH RECURSIVE child_decks AS (
            SELECT deck_id, is_leaf
            FROM decks WHERE deck_id = '$deckID'

            UNION ALL

            SELECT d.deck_id, d.is_leaf
            FROM decks AS d 
            JOIN child_decks AS cd 
            ON d.parent_deck_id = cd.deck_id
        ),
        leaf_decks AS (
            SELECT deck_id FROM child_decks WHERE is_leaf = 1
        ),
        flashcard AS (
            SELECT c.pinyin, c.chinese_tc, c.chinese_sc, c.word_class, c.meaning_eng, c.meaning_ina, c.card_id, cp.current_stage, cp.review_due, dc.priority
            FROM junction_deck_user AS du
            JOIN junction_deck_card AS dc ON du.deck_id = dc.deck_id
            JOIN cards AS c ON c.card_id = dc.card_id
            JOIN card_progress AS cp ON c.card_id = cp.card_id AND cp.user_id = du.user_id
            WHERE du.deck_id IN (SELECT deck_id FROM leaf_decks) AND du.user_id = '$user_id'
        )
        SELECT * FROM flashcard WHERE review_due <= NOW() ORDER BY priority ASC LIMIT 1
        ");
    }
} else {
    if ($deckID == "main") {
        $query_flashcard_algorithm = mysqli_query($con, "
        SELECT c.pinyin, c.chinese_tc, c.chinese_sc, c.word_class, c.meaning_eng, c.meaning_ina, c.card_id, cp.current_stage, cp.review_due, cp.total_review
            FROM junction_deck_user AS du 
            JOIN decks AS d ON d.deck_id = du.deck_id
            JOIN junction_deck_card AS dc ON d.deck_id = dc.deck_id
            JOIN cards AS c ON dc.card_id = c.card_id
            JOIN card_progress AS cp ON c.card_id = cp.card_id AND cp.user_id = du.user_id
            WHERE du.user_id = '$user_id' AND d.is_leaf = 1 AND cp.total_review = 0 ORDER BY dc.priority ASC LIMIT 1
        ");
    } else {
        $query_flashcard_algorithm = mysqli_query($con, "
        WITH RECURSIVE child_decks AS (
            SELECT deck_id, is_leaf
            FROM decks WHERE deck_id = '$deckID'

            UNION ALL
    
            SELECT d.deck_id, d.is_leaf
            FROM decks AS d 
            JOIN child_decks AS cd 
            ON d.parent_deck_id = cd.deck_id
        ),
        leaf_decks AS (
            SELECT deck_id FROM child_decks WHERE is_leaf = 1
        ),
        flashcard AS (
            SELECT c.pinyin, c.chinese_tc, c.chinese_sc, c.word_class, c.meaning_eng, c.meaning_ina, c.card_id, cp.current_stage, cp.review_due, cp.total_review, dc.priority
            FROM junction_deck_user AS du
            JOIN junction_deck_card AS dc ON du.deck_id = dc.deck_id
            JOIN cards AS c ON c.card_id = dc.card_id
            JOIN card_progress AS cp ON c.card_id = cp.card_id AND cp.user_id = du.user_id
            WHERE du.deck_id IN (SELECT deck_id FROM leaf_decks) AND du.user_id = '$user_id'
        )
        SELECT * FROM flashcard WHERE total_review = 0 ORDER BY priority ASC LIMIT 1
        ");
    }
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
            <span class="calc" id="zoomOut">-</span>
            <span class="calc" id="zoomDisplay">100%</span>
            <span class="calc" id="zoomIn">+</span>
        </div>
        <!-- To Review Green Red Blue-->
        <div class="to-review">
            <span class="red" style="color: #ab0b01;"><?php echo $red ?></span>
            <span>
                <span class="green" style="color: #26940a;"><?php echo $green ?></span><span class="blue" style=color: #8497B0'>/<?php echo $blue ?></span>
            </span>
        </div>
    </div>

    <!-- Cards -->
    <div class="wrapper-flashcard" id="target">
        <div class="wrapper-mid">
            <?php
            if ($row = mysqli_fetch_assoc(result: $query_flashcard_algorithm)) {
                $pinyin = convert($row["pinyin"]);
                if ($chara_set == "traditional") {
                    $temp_charaset = 'chinese_tc';
                } else {
                    $temp_charaset = 'chinese_sc';
                }

            ?>
                <div class="vocab-card">
                    <span class="hanzi"><?php echo htmlspecialchars($row[$temp_charaset]); ?></span>
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
                        <span class='sentence'>{$line[$temp_charaset]}</span>
                        <span class='report text-report' onclick=\"Report('{$line['sentence_code']}','{$line[$temp_charaset]}')\">Report Sentence</a>
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

    <div class='wrapper-report'>
        <div class='report'>
            <div class='title-report'><span>Report Sentence</span></div>
            <div class='explanation'>
                <span class="ex-sentence">Sentence:</span>
                <span class="ex-sentence">这个城市很小啊，也很多黑人</span>
                <span style="width: 100%;">Reason: </span>
                <form id="report-sentence">
                    <div class="checkbox">
                        <span>Bad Sentence</span>
                        <input type="checkbox" name="reason[]" value="Bad Sentence">
                    </div>
                    <div class="checkbox">
                        <span>Bad Pinyin</span>
                        <input type="checkbox" name="reason[]" value="Bad Pinyin">
                    </div>
                    <div class="checkbox">
                        <span>Bad Translation ENG</span>
                        <input type="checkbox" name="reason[]" value="Bad Translation ENG">
                    </div>
                    <div class="checkbox">
                        <span>Bad Translation INA</span>
                        <input type="checkbox" name="reason[]" value="Bad Translation INA">
                    </div>

                    <span class="textarea">Let Us Know More:</span>
                    <textarea name="details"></textarea>
                </form>
            </div>
            <div class='button'>
                <button class='button-cancel'>Cancel</button>
                <button type="submit" id='button-report' form="report-sentence">Report</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <button class="wrapper-show-answer" id="click-show">
        <span class="show">Show Answer</span>
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
        function Report(sentence_code, sentence) {
            document.getElementById('button-report').setAttribute("data-sentence-id", sentence_code);
            const spans = document.getElementsByClassName('ex-sentence')[1];
            spans.innerHTML = sentence;
        }

        // Minimum 1 of selection checkboxes
        document.getElementById('report-sentence').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="reason[]"]');
            let checked = false;

            checkboxes.forEach(cb => {
                if (cb.checked) checked = true;
            });

            if (!checked) {
                e.preventDefault(); // stop form submit
                alert('Please select at least one reason!');
            }
        });

        let fontSize = <?php echo $savedZoom; ?>;
        const zoomStep = 10;
        const minZoom = 50;
        const maxZoom = 200;

        const zoomTarget = document.querySelector('.wrapper-flashcard');
        const zoomDisplay = document.getElementById('zoomDisplay');

        function applyZoom() {
            zoomTarget.style.fontSize = fontSize + '%';
            zoomDisplay.textContent = fontSize + '%';

            $.post("savezoom.php", {
                zoom: fontSize
            });
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

        window.onload = function() {
            history.pushState(null, "", location.href);

            window.onpopstate = function() {
                history.go(1);
            };
        };
    </script>

    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="flashcard-message" style="display:none;">Niceee</div>
    </div>
</body>

</html>