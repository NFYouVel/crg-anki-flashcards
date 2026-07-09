<?php
session_start();
include "../../../SQL_Queries/connection.php";
include "./newConvertPinyin.php";

// INITIALIZE SESSION
if (!isset($_COOKIE["user_id"])) {
    header("Location: ../Login");
    exit;
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
// END INITIALIZE SESSION

$user_id = $_COOKIE["user_id"];

// ==== QUERY : USER INFO ====
$stmtUser = $con->prepare("SELECT u.name, u.role, u.user_status, u.character_set, ur.role_name FROM users AS u
    JOIN user_role AS ur ON u.role = ur.role_id
    WHERE u.user_id = ?");
$stmtUser->bind_param("s", $user_id);
$stmtUser->execute();
$result = $stmtUser->get_result();
$line = $result->fetch_array();
$stmtUser->close();

// ==== MODEL : USER INFO ====
$name = $line['name'];
$roleId = $line['role'];
$user_status = $line['user_status'];
$roleName = $line['role_name'];

// ==== ACCESS CONTROL ====
$roleId = $line['role'];
if ($roleId != 3 && $roleId != 2 && $roleId != 1) {
    header("Location: ../Login");
    exit;
}
if ($roleId == 2) {
    $_SESSION["rolePage"] = "Student";
}
if (isset($line["user_status"]) && $line["user_status"] === "pending") {
    header("Location: setting.php");
    exit;
}
// ==== END QUERY : USER INFO ====

// ==== QUERY : CARDS & DECK INFO ====
$deckId = isset($_GET['deckId']) ? $_GET['deckId'] : null;
if (!$deckId) {
    echo json_encode(['error' => 'deckId is required']);
    exit;
}

$sqlCard = "SELECT 
            c.card_id,
            c.chinese_tc,
            c.chinese_sc,
            c.pinyin,
            c.word_class,
            c.meaning_eng,
            c.meaning_ina
        FROM junction_deck_card jdc
        INNER JOIN leaf_deck_map ldm ON jdc.deck_id = ldm.leaf_deck_id
        INNER JOIN cards c ON jdc.card_id = c.card_id
        WHERE ldm.deck_id = ?
        ORDER BY RAND()
        LIMIT 5";

$stmtCards = $con->prepare($sqlCard);
$stmtCards->bind_param("s", $deckId);
$stmtCards->execute();
$resultCards = $stmtCards->get_result();
$cards = [];
while ($row = $resultCards->fetch_assoc()) {
    $row['pinyin'] = convert($row['pinyin']);
    $cards[] = $row;
}

$stmtCards->close();

// Output sebagai JSON biar bisa di console.log di JS
// header('Content-Type: application/json');
// echo json_encode($cards);
// ==== END QUERY : CARDS & DECK INFO ====

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../../Assets/Icons/1080.png">
    <link rel="stylesheet" href="../../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../../../Pages/Home/CSS/card_matching.css">
    <link href='https://cdn.boxicons.com/3.0.7/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../Home/jQuery/script.js"></script>
</head>

<body>
    <!-- Header -->
    <?php include "../Component/functional_header_matching.php"; ?>

    <div class="right-bar">
        <div class="account-info">
            <span class="username"><?php echo $line['name'] ?></span>
            <?php
            if (isset($_SESSION["rolePage"])) { ?>
                <span class="as" style="cursor: pointer;" onclick="Mode()"><?= $_SESSION["rolePage"] ?> Mode</span>
            <?php } else { ?>
                <span class="as">Student</span>
            <?php } ?>
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

    <div class="wrapper-card-matching-tutorial">
        <div class="wrapper-tutorial">
            <div class="wrapper-instruction">
                <div class="title-instruction">
                    <span>Ready To Play?</span>
                </div>
                <div class="content-instruction">
                    <span>Match all the Chinese words with the correct pinyin and meaning</span>
                </div>
            </div>
            <div class="wrapper-shuffle">
                <span>Show the meaning in: <span class="language"></span><img
                        src="../../../Assets/Icons/switch icon.png" onclick="changeMeaning()"></span>
            </div>
            <div class="wrapper-button-start">
                <button onclick="startMatchingCard()">Start New Game</button>
            </div>
        </div>
    </div>

    <div class="wrapper-card-matching" style="display: none">
        <div class="wrapper-decks">
            <table class="card-matching">
                <caption>
                    <div class="wrapper-timer">
                        <img src="../../../Assets/Icons/x icon.png" class="close" onclick="stopMatchingCard()">
                        <div class="timer-display">
                            <span class="stopwatch">0.00s</span>
                            <span class="penalty-display"></span>
                        </div>
                        <img src="../../../Assets/Icons/reset icon.png" class="reset" onClick="resetMatchingCard()">
                    </div>
                </caption>
                <tbody id="matching-table-body">
                    <!-- Rows generated by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // ===== DATA DARI PHP =====
        const cardsData = <?php echo json_encode($cards); ?>;

        console.log(cardsData);
        const deckId = '<?php echo $deckId; ?>';
        console.log('Random 5 Cards:', cardsData);

        // Ambil preference user
        let charSet = '<?php echo $line['character_set']; ?>';
        let meaningLang = localStorage.getItem('meaning') || 'Indonesia';

        // ===== GAME STATE =====
        let selectedItems = {
            chinese: null,
            pinyin: null,
            meaning: null
        };
        let matchedCount = 0;
        let penalty = 0;
        let cardAttempts = {}; // card_id -> attempt count

        // ===== SHUFFLE =====
        function shuffle(array) {
            const arr = [...array];
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
            return arr;
        }

        // ===== GENERATE TABLE =====
        function generateTable() {
            const tbody = document.getElementById('matching-table-body');
            tbody.innerHTML = '';
            selectedItems = {
                chinese: null,
                pinyin: null,
                meaning: null
            };
            matchedCount = 0;
            cardAttempts = {};

            const chineseList = shuffle(cardsData.map(card => ({
                id: card.card_id,
                text: charSet === 'traditional' ? card.chinese_tc : card.chinese_sc
            })));

            const pinyinList = shuffle(cardsData.map(card => ({
                id: card.card_id,
                text: card.pinyin
            })));

            const meaningList = shuffle(cardsData.map(card => ({
                id: card.card_id,
                text: meaningLang === 'English'
                    ? card.meaning_eng
                    : card.meaning_ina
            })));

            // const shuffledChinese = shuffle(chineseList);
            // const pinyinListShuffled = shuffle(pinyinList);
            // const meaningListShuffled = shuffle(meaningList);

            for (let i = 0; i < 5; i++) {
                console.log(
                    `Row ${i}: Chinese=${chineseList[i].text}, Pinyin=${pinyinList[i].text}, Meaning=${meaningList[i].text}`
                );
            }

            for (let i = 0; i < 5; i++) {
                const tr = document.createElement('tr');
                tr.className = 'cards';

                tr.innerHTML = `
        <td
            class="col-chinese"
            data-id="${chineseList[i].id}"
            data-text="${chineseList[i].text}"
            data-type="chinese"
            onclick="handleClick(this)"
        >
            ${chineseList[i].text}
        </td>

        <td
            class="col-pinyin"
            data-id="${pinyinList[i].id}"
            data-text="${pinyinList[i].text}"
            data-type="pinyin"
            onclick="handleClick(this)"
        >
            ${pinyinList[i].text}
        </td>

        <td
            class="col-meaning"
            data-id="${meaningList[i].id}"
            data-text="${meaningList[i].text}"
            data-type="meaning"
            onclick="handleClick(this)"
        >
            ${meaningList[i].text}
        </td>
    `;

                tbody.appendChild(tr);
            }
            adjustCellFontSizes();
        }

        // ===== HANDLE CLICK =====
        function handleClick(element) {
            if (element.classList.contains('hidden')) return;

            const type = element.dataset.type;

            if (selectedItems[type] && selectedItems[type] !== element) {
                selectedItems[type].classList.remove('selected');
            }

            if (selectedItems[type] === element) {
                selectedItems[type] = null;
                element.classList.remove('selected');
                return;
            }

            element.classList.add('selected');
            selectedItems[type] = element;

            checkIfComplete();
        }

        // ===== CEK KALO UDAH 3 SELECTED =====
        function checkIfComplete() {
            if (selectedItems.chinese && selectedItems.pinyin && selectedItems.meaning) {
                validateMatch();
            }
        }

        // ===== VALIDATE MATCH =====
        function validateMatch() {
            const chineseText = selectedItems.chinese.dataset.text;
            const pinyinText = selectedItems.pinyin.dataset.text;
            const meaningText = selectedItems.meaning.dataset.text;

            const chineseCard = cardsData.find(card =>
                (charSet === 'traditional'
                    ? card.chinese_tc
                    : card.chinese_sc) === chineseText
            );

            const id = chineseCard.card_id;

            // Setiap kali player mencoba kartu ini, hitung attempt sekali.
            cardAttempts[id] = (cardAttempts[id] || 0) + 1;

            const pinyinCorrect =
                chineseCard.pinyin === pinyinText;

            const meaningCorrect =
                (meaningLang === 'English'
                    ? chineseCard.meaning_eng
                    : chineseCard.meaning_ina) === meaningText;

            if (pinyinCorrect && meaningCorrect) {
                handleCorrectMatch();

            } else {
                handleIncorrectMatch();
            }

        }

        // ===== CORRECT MATCH =====
        function handleCorrectMatch() {
            // Hijau
            selectedItems.chinese.classList.add('correct');
            selectedItems.pinyin.classList.add('correct');
            selectedItems.meaning.classList.add('correct');

            matchedCount++;
            console.log(`✅ Correct! ${matchedCount}/5 matched`);

            // Simpan referensi ke element yang mau di-hide
            const toHide = [selectedItems.chinese, selectedItems.pinyin, selectedItems.meaning];

            // LANGSUNG reset selectedItems biar user bisa klik item lain
            selectedItems = {
                chinese: null,
                pinyin: null,
                meaning: null
            };

            // Delay hide tetep jalan di background
            setTimeout(() => {
                toHide.forEach(el => el.classList.add('hidden'));
            }, 500);

            if (matchedCount === 5) {
                setTimeout(() => handleGameComplete(), 50);
            }
        }


        // ===== INCORRECT MATCH =====
        function handleIncorrectMatch() {
            // Merah
            selectedItems.chinese.classList.add('incorrect');
            selectedItems.pinyin.classList.add('incorrect');
            selectedItems.meaning.classList.add('incorrect');

            penalty += 1;
            const currentTime = time + penalty;
            document.querySelector(".stopwatch").innerText = currentTime.toFixed(2) + "s";

            showPenaltyText();

            // Simpan referensi ke element yang mau di-reset
            const toReset = [selectedItems.chinese, selectedItems.pinyin, selectedItems.meaning];

            // LANGSUNG reset selectedItems biar user bisa klik item lain
            selectedItems = {
                chinese: null,
                pinyin: null,
                meaning: null
            };

            // Delay reset warna tetep jalan di background
            setTimeout(() => {
                toReset.forEach(el => el.classList.remove('incorrect', 'selected'));
            }, 300);
        }

        function showPenaltyText() {
            const penaltyDisplay = document.querySelector('.penalty-display');
            penaltyDisplay.textContent = '+1s';

            // Reset animasi
            penaltyDisplay.style.animation = 'none';
            void penaltyDisplay.offsetWidth;
            penaltyDisplay.style.animation = 'penaltyFade 0.8s ease-out forwards';

            setTimeout(() => {
                penaltyDisplay.textContent = '';
            }, 800);
        }

        // ===== GAME COMPLETE =====
        function handleGameComplete() {
            clearInterval(interval);
            const finalTime = time + penalty;

            const cardsWithAttempts = cardsData.map((card) => ({
                card_id: card.card_id,
                chinese_sc: card.chinese_sc,
                chinese_tc: card.chinese_tc,
                pinyin: card.pinyin,
                meaning_eng: card.meaning_eng,
                meaning_ina: card.meaning_ina,
                pairAttempts: cardAttempts[card.card_id] || 1
            }));

            const cardsJson = JSON.stringify(cardsWithAttempts);
            const cardsEncoded = encodeURIComponent(cardsJson);

            window.location.href = `finish.php?deckId=${deckId}&time=${finalTime.toFixed(2)}&cards=${cardsEncoded}`;
        }

        // ===== SET LANGUAGE LABEL =====
        document.querySelector('.language').textContent = meaningLang;

        // ===== TIMER =====
        let time = 0;
        let interval;

        function startMatchingCard() {
            document.querySelector('.wrapper-card-matching-tutorial').style.display = 'none';
            document.querySelector('.wrapper-card-matching').style.display = 'flex';

            penalty = 0;
            matchedCount = 0;
            isProcessing = false;
            cardAttempts = {};

            generateTable();

            clearInterval(interval);
            time = 0;
            interval = setInterval(() => {
                time += 0.01;
                const displayTime = time + penalty;
                document.querySelector(".stopwatch").innerText = displayTime.toFixed(2) + "s";
            }, 10);
        }

        function resetMatchingCard() {
            clearInterval(interval);
            time = 0;
            penalty = 0;
            matchedCount = 0;
            isProcessing = false;
            cardAttempts = {};
            generateTable();
            interval = setInterval(() => {
                time += 0.01;
                const displayTime = time + penalty;
                document.querySelector(".stopwatch").innerText = time.toFixed(2) + "s";
            }, 10);
        }

        function stopMatchingCard() {
            clearInterval(interval);
            window.location.reload();
        }

        // ===== TOGGLE MEANING =====
        function changeMeaning() {
            if (meaningLang === 'English') {
                meaningLang = 'Indonesia';
                localStorage.setItem('meaning', 'Indonesia');
                document.querySelector('.language').textContent = 'Indonesia';
            } else {
                meaningLang = 'English';
                localStorage.setItem('meaning', 'English');
                document.querySelector('.language').textContent = 'English';
            }

            // Regenerate table kalo game lagi jalan
            const gameVisible = document.querySelector('.wrapper-card-matching').style.display === 'flex';
            if (gameVisible) {
                // Reset selection state
                selectedItems = {
                    chinese: null,
                    pinyin: null,
                    meaning: null
                };

                // Regenerate table dengan meaning baru
                generateTable();
            }
        }

        function Mode() {
            window.location.href = "../home_page.php";
        }

        function adjustCellFontSizes() {
            const TARGET_HEIGHT = 100;
            document.querySelectorAll('tr.cards td.col-meaning').forEach(td => {
                let fontSize = 15;
                td.style.fontSize = fontSize + 'px';

                while (td.scrollHeight > TARGET_HEIGHT && fontSize > 8) {
                    fontSize -= 0.5;
                    td.style.fontSize = fontSize + 'px';
                }
            });
        }
    </script>
</body>

</html>