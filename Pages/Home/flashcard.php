<?php
include "../../SQL_Queries/connection.php";
// User ID
session_start();
if (!isset($_COOKIE["user_id"])) {
    header("Location: ../..");
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
$chara_set = ($chara_set == "simplified") ? "chinese_sc" : "chinese_tc";

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

date_default_timezone_set('Asia/Jakarta');
//all decks: key: deck_id, value: parent_deck_id
$allDecks = [];
//list of all decks including deck_id, parent_deck_id, name, is_leaf
$decksList = [];

//get and save all decks to session for optimization
if (!isset($_SESSION['all_decks']) || !isset($_SESSION['decks_list']) || $_SESSION['all_decks_expires'] < time()) {
    $getAllDecks = mysqli_query($con, "
        SELECT deck_id, parent_deck_id, name, is_leaf
        FROM decks
        ORDER BY name ASC
    ");

    while ($row = mysqli_fetch_assoc($getAllDecks)) {
        $allDecks[$row['deck_id']] = $row['parent_deck_id'];
        $decksList[] = $row;
    }

    $_SESSION['all_decks'] = $allDecks;
    $_SESSION['decks_list'] = $decksList;
    $_SESSION['all_decks_expires'] = time() + 1800;
} else {
    $allDecks = $_SESSION['all_decks'];
    $decksList = $_SESSION['decks_list'];
}

//function to check if a deck is a decendant of another deck
function isDescendant($deckId, $targetParent, $allDecks) {
    while (isset($allDecks[$deckId]) && $allDecks[$deckId]) {
        if ($allDecks[$deckId] === $targetParent) {
            return true;
        }
        $deckId = $allDecks[$deckId];
    }
    return false;
}

$firstLeafDeck;
if ($deckID === "main") {
    //if user is opening main deck, no need to filter
    $firstLeafDeck = null;
} else {
    $firstLeafDeck = null;

    //check if selected deck is already leaf deck, if true, then firstLeafDeck = selected deck
    $selectedDeck = mysqli_query($con, "SELECT * FROM decks WHERE deck_id = '$deckID'");
    $selectedDeck = mysqli_fetch_assoc($selectedDeck);

    if ($selectedDeck['is_leaf']) {
        $firstLeafDeck = $deckID;
    }

    //if selected deck is not leaf deck, then find first leaf deck child of selected deck and sort by deck name
    if ($firstLeafDeck === null) {
        $leafDecks = [];

        foreach ($decksList as $deck) {
            if ($deck['is_leaf'] && isDescendant($deck['deck_id'], $deckID, $allDecks)) {
                $leafDecks[] = $deck;
            }
        }

        usort($leafDecks, fn($a, $b) => strcmp($a['name'], $b['name']));
        $firstLeafDeck = $leafDecks[0]['deck_id'] ?? null;
    }
}

// Blue Green Red Count
include_once "repetition_flashcard.php";

$counts = mysqli_fetch_assoc($query_flashcard_rbg_count);
$blue = $counts['blue'];
$green = $counts['green'];
$red = $counts['red'];

// Algorithm Flashcard
$allCards = [];
$chosenCard;
$cardIds;
$getAllCards;

//if user select main deck, then select from all decks, if not, only select from first leaf deck
$deckCondition = $deckID !== "main" && $firstLeafDeck !== null ? "AND d.deck_id = '$firstLeafDeck'" : "";

if($green != 0) {
    $getAllCards = mysqli_query($con, "
    SELECT c.pinyin, c.$chara_set, c.word_class, c.meaning_eng, c.meaning_ina, c.card_id, cp.current_stage, cp.review_due
    FROM junction_deck_user AS du 
    JOIN decks AS d ON d.deck_id = du.deck_id
    JOIN junction_deck_card AS dc ON d.deck_id = dc.deck_id
    JOIN cards AS c ON dc.card_id = c.card_id
    JOIN card_progress AS cp ON c.card_id = cp.card_id AND cp.user_id = du.user_id
    WHERE du.user_id = '$user_id' AND d.is_leaf = 1 $deckCondition AND cp.review_due <= NOW() ORDER BY d.name ASC, dc.priority ASC
    ");
} else {
    $getAllCards = mysqli_query($con, "
        SELECT c.pinyin, c.$chara_set, c.word_class, c.meaning_eng, c.meaning_ina, c.card_id, cp.current_stage, cp.review_due, cp.total_review
        FROM junction_deck_user AS du 
        JOIN decks AS d ON d.deck_id = du.deck_id
        JOIN junction_deck_card AS dc ON d.deck_id = dc.deck_id
        JOIN cards AS c ON dc.card_id = c.card_id
        JOIN card_progress AS cp ON c.card_id = cp.card_id AND cp.user_id = du.user_id
        WHERE du.user_id = '$user_id' AND d.is_leaf = 1 $deckCondition AND cp.total_review = 0 ORDER BY d.name ASC, dc.priority ASC
    ");
}

$cardIds = [];

//get all cards from query above
while($card = mysqli_fetch_assoc($getAllCards)) {
    $allCards[$card['card_id']] = $card;
    $cardIds[] = $card['card_id'];
}

$cardIds = implode(",", $cardIds);

$getSentences = mysqli_query($con, "
    SELECT jcs.card_id, es.*
    FROM junction_card_sentence jcs
    INNER JOIN example_sentence es 
        ON jcs.sentence_code = es.sentence_code
    WHERE jcs.card_id IN ($cardIds)
");

while ($row = mysqli_fetch_assoc($getSentences)) {
    $allCards[$row['card_id']]['sentences'][] = $row;
}

$getParentDecks = mysqli_query($con, "
    SELECT card_id, deck_id
    FROM junction_deck_card
    WHERE card_id IN ($cardIds)
");

while ($row = mysqli_fetch_assoc($getParentDecks)) {
    $cardId = $row['card_id'];

    $allCards[$cardId]['parent_decks'] ??= [];
    $allCards[$cardId]['parent_decks'][] = $row['deck_id'];

    //get ancestors
    $parent = $row['deck_id'];
    while (isset($allDecks[$parent]) && !empty($allDecks[$parent])) {
        $parent = $allDecks[$parent];
        if(!in_array($parent, $allCards[$row['card_id']]['parent_decks'], true)) {
            $allCards[$row['card_id']]['parent_decks'][] = $parent;
        }
    }
}

$chosenCard = $allCards;
$key = array_key_first($chosenCard);
$chosenCard = $chosenCard[$key];

?>

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../Assets/Icons/1080.png">
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
        <div class="wrapper-zoom">
            <span class="calc" id="zoomOut">-</span>
            <span class="calc" id="zoomDisplay">100%</span>
            <span class="calc" id="zoomIn">+</span>
        </div>
        <!-- To Review Green Red Blue-->
        <div class="to-review">
            <span class="red" style="color: #ab0b01;"><?php echo $red ?></span>
            <!-- <span> -->
            <span class="green" style="color: #26940a;"><?php echo $green ?></span>
            <span class="blue">/<?php echo $blue ?></span>
            <!-- </span> -->
        </div>
    </div>

    <div class="wrapper-flashcard" id="target">
        <div class="wrapper-mid">
                <div class="vocab-card">
                    <span class="hanzi"><?php echo htmlspecialchars($chosenCard[$chara_set]); ?></span>
                    <span class="pinyin"><?php echo htmlspecialchars(convert($chosenCard["pinyin"])); ?></span>
                    <span class="word-class"><?php echo htmlspecialchars($chosenCard['word_class']); ?></span>
                    <table>
                        <tr>
                            <td class="sub"><div>EN</div></td>
                            <td class="colon"><div>:</div></td>
                            <td class="meaning"><div><?php echo htmlspecialchars($chosenCard['meaning_eng']); ?></div></td>
                        </tr>
                        <tr>
                            <td class="sub"><div>ID</div></td>
                            <td class="colon"><div>:</div></td>
                            <td class="meaning"><div><?php echo htmlspecialchars($chosenCard['meaning_ina']); ?></div></td>
                        </tr>
                    </table>
                </div>

                <?php
                if(isset($chosenCard["sentences"])) {
                    foreach($chosenCard["sentences"] as $sentence) {
                        echo "<div class='sentence'>
                        <div class='chinese-sentence'>
                            <span class='sentence'>{$sentence[$chara_set]}</span>
                            <span class='report text-report' onclick=\"Report('{$sentence['sentence_code']}','{$sentence[$chara_set]}')\">Report Sentence</a>
                        </div>
                        <div class='wrapper-pinyin'>
                        <span class='pinyin'>{$sentence['pinyin']}</span>
                        </div>
                        <table>
                            <tr>
                                <td class='sub'><div>EN</div></td>
                                <td class='colon'><div>:</div></td>
                                <td class='meaning'><div>{$sentence['meaning_eng']}</div></td>
                            </tr>
                            <tr>
                                <td class='sub'><div>ID</div></td>
                                <td class='colon'><div>:</div></td>
                                <td class='meaning'><div>{$sentence['meaning_ina']}</div></td>
                            </tr>
                        </table>
                    </div>";
                    }
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
                    <input type="hidden" name="sentence-id" id="hidden">
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
    <div class="wrapper-show-answer" id="flashcard-form" data-card-id="<?php echo $chosenCard["card_id"]; ?>">
        <button type="button" id="criteria" class="criteria forgot" data-status="forgot" data-cs="<?php echo $chosenCard['current_stage'] ?>">
            <span>X</span><span>Forgot</span>
        </button>
        <button type="button" id="criteria" class="criteria hard" data-status="hard" data-cs="<?php echo $chosenCard['current_stage'] ?>">
            <span>...</span><span>Hard</span>
        </button>
        <button type="button" id="criteria" class="criteria remember" data-status="remember" data-cs="<?php echo $chosenCard['current_stage'] ?>">
            <span>V</span><span>Remember</span>
        </button>
    </div>

    <script>
        function Report(sentence_code, sentence) {
            document.getElementById('report-sentence').reset();

            // Set new sentence code
            document.getElementById('hidden').value = sentence_code;

            // Update sentence di modal
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
            console.log(fontSize)
            $(".hanzi").css("font-size", (fontSize + 250) + "%");
            $(".hanzi").css("margin-bottom", (fontSize - 100) + "px");
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
<?php
            // } else {
            //     echo "<div class='end-deck'>";
            //     echo "<p style='color: white; font-size:26px; font-weight: bold; margin: 10px 0;'>Great job! You've completed this deck for now.</p>";
            //     echo "<p style='color: white;'>You can take a break, or review another deck.</p>";
            //     echo "<button class='wrapper-show-answer' onclick='BackHomePage()'>
            //     <span class='show'>Back To Your Decks</span>
            //     </button>";
            //     echo "</div>";
            // }
?>
    <script>
        function BackHomePage() {
            window.location.href = "home_page_students.php";
        }
    </script>
</body>

</html>