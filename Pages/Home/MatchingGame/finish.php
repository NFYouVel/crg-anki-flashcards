<?php
session_start();
include "../../../SQL_Queries/connection.php";

// ===== AMBIL USER_ID =====
$user_id = $_COOKIE["user_id"] ?? $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: ../Login");
    exit;
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $user_id;
}

// ==== QUERY : USER INFO ====
$stmtUser = $con->prepare("SELECT u.name, u.role, u.user_status, ur.role_name FROM users AS u
    JOIN user_role AS ur ON u.role = ur.role_id
    WHERE u.user_id = ?");
$stmtUser->bind_param("s", $user_id);
$stmtUser->execute();
$result = $stmtUser->get_result();
$line = $result->fetch_array();
$stmtUser->close();

$name = $line['name'];
$roleId = $line['role'];

// ==== ACCESS CONTROL ====
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

// ==== GET DATA FROM URL =====
$deckId = $_GET['deckId'] ?? null;
$finalTime = isset($_GET['time']) ? (float) $_GET['time'] : 0;
$cardsEncoded = $_GET['cards'] ?? '';

// Decode cards data dari URL
$cardsData = [];
if ($cardsEncoded) {
    $cardsJson = urldecode($cardsEncoded);
    $cardsData = json_decode($cardsJson, true) ?: [];
}

if (!$deckId) {
    header("Location: index.php");
    exit;
}

// ==== GET DECK HIERARCHY ====
function getDeckHierarchy($con, $deckId)
{
    $sql = "WITH RECURSIVE deck_hierarchy AS (
                SELECT deck_id, name, parent_deck_id, CAST(name AS CHAR(500)) AS full_path, 0 AS level
                FROM decks WHERE deck_id = ?
                UNION ALL
                SELECT d.deck_id, d.name, d.parent_deck_id, CONCAT(d.name, ' > ', dh.full_path), dh.level + 1
                FROM decks d
                INNER JOIN deck_hierarchy dh ON d.deck_id = dh.parent_deck_id
            )
            SELECT full_path AS deck_hierarchy FROM deck_hierarchy WHERE parent_deck_id IS NULL LIMIT 1";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $deckId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['deck_hierarchy'];
    }
    return "Unknown Deck";
}

$deckHierarchy = getDeckHierarchy($con, $deckId);

// ==== SAVE/UPDATE LEADERBOARD ====
$checkStmt = $con->prepare("SELECT best_time FROM matching_leaderboard WHERE deck_id = ? AND user_id = ?");
$checkStmt->bind_param("ss", $deckId, $user_id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($finalTime < $row['best_time']) {
        $updateStmt = $con->prepare("UPDATE matching_leaderboard SET best_time = ?, created_at = NOW() WHERE deck_id = ? AND user_id = ?");
        $updateStmt->bind_param("dss", $finalTime, $deckId, $user_id);
        $updateStmt->execute();
        $updateStmt->close();
    }
} else {
    $insertStmt = $con->prepare("INSERT INTO matching_leaderboard (deck_id, user_id, best_time) VALUES (?, ?, ?)");
    $insertStmt->bind_param("ssd", $deckId, $user_id, $finalTime);
    $insertStmt->execute();
    $insertStmt->close();
}
$checkStmt->close();

// ==== SAVE LOGS MATCHING ATTEMPTS ====
if (!empty($cardsData)) {
    $logStmt = $con->prepare("INSERT INTO logs_matching_attempts (user_id, deck_id, card_id, session_time) VALUES (?, ?, ?, ?)");
    
    foreach ($cardsData as $card) {
        $cardId = (int)$card['card_id'];
        $logStmt->bind_param("ssid", $user_id, $deckId, $cardId, $finalTime);
        $logStmt->execute();
    }
    $logStmt->close();
}

// ==== GET LEADERBOARD ====
$sqlLeaderboard = "SELECT 
    ml.best_time,
    u.name AS user_name,
    ml.user_id,
    RANK() OVER (ORDER BY ml.best_time ASC) AS rank_position
FROM matching_leaderboard ml
JOIN users u ON ml.user_id = u.user_id
WHERE ml.deck_id = ?
ORDER BY ml.best_time ASC
LIMIT 10";

$stmtLb = $con->prepare($sqlLeaderboard);
$stmtLb->bind_param("s", $deckId);
$stmtLb->execute();
$resultLb = $stmtLb->get_result();

$leaderboard = [];
$userRank = null;
while ($row = $resultLb->fetch_assoc()) {
    $isCurrentUser = ($row['user_id'] === $user_id);
    if ($isCurrentUser)
        $userRank = (int) $row['rank_position'];

    $leaderboard[] = [
        'rank' => (int) $row['rank_position'],
        'user_name' => $row['user_name'],
        'best_time' => (float) $row['best_time'],
        'is_you' => $isCurrentUser
    ];
}
$stmtLb->close();

// Kalo user ga masuk top 10
if ($userRank === null) {
    $rankSql = "SELECT COUNT(*) + 1 AS rank FROM matching_leaderboard WHERE deck_id = ? AND best_time < ?";
    $rankStmt = $con->prepare($rankSql);
    $rankStmt->bind_param("sd", $deckId, $finalTime);
    $rankStmt->execute();
    $rankResult = $rankStmt->get_result();
    if ($r = $rankResult->fetch_assoc()) {
        $userRank = (int) $r['rank'];
    }
    $rankStmt->close();

    $leaderboard[] = [
        'rank' => $userRank,
        'user_name' => $name,
        'best_time' => $finalTime,
        'is_you' => true
    ];
}

// Ambil meaning language dari localStorage via JS nanti
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Complete - <?php echo htmlspecialchars($name); ?></title>
    <link rel="icon" href="../../../Assets/Icons/1080.png">
    <link rel="stylesheet" href="../../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../../../Pages/Home/CSS/finish_card_matching.css">
    <link href='https://cdn.boxicons.com/3.0.7/fonts/basic/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <!-- Header (sama) -->
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

    <div class="container-finish">
        <div class="wrapper-finish">
            <div class="finish-card">
                <div class="finish-header">
                    <h1>Congratulations! You completed the card matching in:</h1>
                    <span class="finish-time"><?php echo number_format($finalTime, 2); ?>s</span>
                </div>

                <div class="finish-deck">
                    <?php echo htmlspecialchars($deckHierarchy); ?>
                </div>

                <button class="btn-words" onclick="showWords()">See Words Used</button>

                <div class="leaderboard-section">
                    <h2>Ranking Leaderboard</h2>
                    <table class="leaderboard-table">
                        <?php foreach ($leaderboard as $entry): ?>
                            <tr class="<?php echo $entry['is_you'] ? 'you' : ''; ?>">
                                <td class="rank"><?php echo $entry['rank']; ?></td>
                                <td><?php echo htmlspecialchars($entry['user_name']); ?></td>
                                <td class="score"><?php echo number_format($entry['best_time'], 2); ?>s</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <button class="btn-play" onclick="playAgain()">Play Again</button>
                <a href="../home_page.php" class="back-menu">Back to Main Menu</a>
            </div>
        </div>
    </div>

    <!-- POPUP -->
    <div class="popup-words" id="popupWords">
        <div class="popup-overlay" onclick="closePopup()"></div>
        <div class="popup-box">
            <div class="popup-close" onclick="closePopup()">✕</div>
            <table class="popup-table" id="wordsTable">
                <tr>
                    <th>No</th>
                    <th>Char</th>
                    <th>Pinyin and Meaning</th>
                    <th>Pair Attempt</th>
                </tr>
                <!-- JS populate -->
            </table>
        </div>
    </div>

    <script>
        // Data dari PHP
        const cardsData = <?php echo json_encode($cardsData); ?>;
        const meaningLang = localStorage.getItem('meaning') || 'Indonesia';
        const charSet = localStorage.getItem('characterSet') || 'simplified';
        const deckId = '<?php echo $deckId; ?>';

        console.log('Cards with attempts:', cardsData);

        function showWords() {
            const tbody = document.getElementById('wordsTable');
            // Clear existing rows except header
            while (tbody.rows.length > 1) {
                tbody.deleteRow(1);
            }

            cardsData.forEach((card, index) => {
                const attempts = card.pairAttempts || 1;
                const isWrong = attempts > 1;

                const row = tbody.insertRow();
                if (isWrong) row.classList.add('wrong');

                const chinese = charSet === 'traditional' ? card.chinese_tc : card.chinese_sc;
                const meaning = meaningLang === 'English' ? card.meaning_eng : card.meaning_ina;

                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${chinese}</td>
                    <td>${card.pinyin}<br>${meaning}</td>
                    <td>${attempts}x</td>
                `;
            });

            document.getElementById("popupWords").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("popupWords").style.display = "none";
        }

        function playAgain() {
            window.location.href = `index.php?deckId=${deckId}`;
        }

        function Mode() {
            window.location.href = "../home_page.php";
        }
    </script>
</body>

</html>