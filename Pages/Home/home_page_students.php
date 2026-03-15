<?php
session_start();
include "../../SQL_Queries/connection.php";

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
$stmtUser = $con->prepare("SELECT u.name, u.role, u.user_status, ur.role_name FROM users AS u
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
if ($roleId != 3) {
    header("Location: ../Login");
    exit;
}

$_SESSION["rolePage"] = "Student";

if (isset($line["user_status"]) && $line["user_status"] === "pending") {
    header("Location: setting.php");
    exit;
}
// ==== END QUERY : USER INFO ====

// ==== QUERY : MAIN DECK RGB ====
$stmtMain = $con->prepare("
    SELECT
        SUM(CASE WHEN cp.current_stage = 0                             THEN 1 ELSE 0 END) AS blue,
        SUM(CASE WHEN cp.current_stage != 0 AND cp.review_due <= NOW() THEN 1 ELSE 0 END) AS green,
        SUM(CASE WHEN cp.review_due > NOW()                            THEN 1 ELSE 0 END) AS red
    FROM card_progress AS cp
    WHERE cp.user_id = ?
");
$stmtMain->bind_param("s", $user_id);
$stmtMain->execute();
$countMain = $stmtMain->get_result()->fetch_assoc();
$stmtMain->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../Assets/Icons/1080.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <link href='https://cdn.boxicons.com/3.0.7/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
</head>

<body>
    <!-- Header -->
    <?php include "Component/header_login.php"; ?>

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
    <?php include "Component/account_logout.php"; ?>

    <!-- Main Deck -->
    <?php
    $query = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($con, $query);
    $line = mysqli_fetch_array($result);

    // ==== Original getRoot() — untouched ====
    $rootDecks = [];
    function getRoot($parentID = null)
    {
        global $con, $user_id, $rootDecks;
        if ($parentID == null) {
            $getDecks = mysqli_query($con, "SELECT deck.deck_id, deck.parent_deck_id
                                            FROM junction_deck_user AS deck_user
                                            JOIN decks AS deck 
                                            ON deck_user.deck_id = deck.deck_id
                                            WHERE deck_user.user_id = '$user_id' ORDER BY deck.name");
        } else {
            $getDecks = mysqli_query($con, "SELECT deck_id, parent_deck_id FROM decks WHERE deck_id = '$parentID' ORDER BY name");
        }
        while ($deck = mysqli_fetch_assoc($getDecks)) {
            if (!in_array($deck["deck_id"], $rootDecks)) {
                $rootDecks[] = $deck["deck_id"];
            }
            if ($deck["parent_deck_id"] !== null) {
                getRoot($deck["parent_deck_id"]);
            }
        }
    }
    getRoot();

    // ==== BULK RGB QUERY — seeded from $rootDecks, not junction_deck_user ====
    // This matches the original per-deck query exactly:
    // for each deck in $rootDecks, expand downward to leaf decks and sum card_progress
    $rgbMap = [];
    if (!empty($rootDecks)) {
        $escaped   = array_map(fn($id) => "'" . mysqli_real_escape_string($con, $id) . "'", $rootDecks);
        $inClause  = implode(',', $escaped);

        $rgbResult = mysqli_query($con, "
            WITH RECURSIVE subtree AS (
                SELECT deck_id AS root_deck_id, deck_id, is_leaf
                FROM decks
                WHERE deck_id IN ($inClause)

                UNION ALL

                SELECT st.root_deck_id, d.deck_id, d.is_leaf
                FROM decks AS d
                JOIN subtree AS st ON d.parent_deck_id = st.deck_id
            )
            SELECT
                st.root_deck_id AS deck_id,
                SUM(CASE WHEN cp.current_stage = 0                             THEN 1 ELSE 0 END) AS blue,
                SUM(CASE WHEN cp.current_stage != 0 AND cp.review_due <= NOW() THEN 1 ELSE 0 END) AS green,
                SUM(CASE WHEN cp.review_due > NOW()                            THEN 1 ELSE 0 END) AS red
            FROM subtree AS st
            JOIN junction_deck_card AS jdc ON jdc.deck_id = st.deck_id AND st.is_leaf = 1
            JOIN card_progress      AS cp  ON cp.card_id  = jdc.card_id AND cp.user_id = '$user_id'
            GROUP BY st.root_deck_id
        ");

        while ($row = mysqli_fetch_assoc($rgbResult)) {
            $rgbMap[$row['deck_id']] = $row;
        }
    }

    // ==== showDecks() — original structure, $rgbMap lookup instead of countRGB() ====
    function showDecks($parentID = null)
    {
        global $con, $user_id, $rootDecks, $rgbMap;
        if ($parentID == null) {
            $getDecks = mysqli_query($con, "SELECT name, deck_id FROM decks WHERE parent_deck_id IS NULL ORDER BY name");
        } else {
            $getDecks = mysqli_query($con, "SELECT name, deck_id FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name");
        }
        while ($deck = mysqli_fetch_assoc($getDecks)) {
            $deckID = $deck["deck_id"];
            if (in_array($deckID, $rootDecks)) {
                $rgb   = $rgbMap[$deckID] ?? ['red' => 0, 'green' => 0, 'blue' => 0];
                $green = $rgb["green"];
                $red   = $rgb["red"];
                $blue  = $rgb["blue"];

                echo "<li class='contain' data-id='$deckID'>";
                echo "<div class='container-deck'>";
                $hasChild = mysqli_query($con, "SELECT * FROM decks WHERE parent_deck_id = '$deckID'");
                if (mysqli_num_rows($hasChild) === 0) {
                    echo "<div class='md5qdw8dq' style='width: 30px; display: flex; align-items: center;'></div>";
                } else {
                    echo "<div class='plus'><i class='bx bxs-caret-down bx-flip-horizontal' style='color:#8e8e8e;font-size: 24px'></i></div>";
                }
                echo "<div class='title-to-review-second' onclick=\"window.location.href='flashcard.php?deck_id=$deckID'\">";
                echo "<span class='title-second'>" . htmlspecialchars($deck['name']) . "</span>";
                echo "<div class='to-review'>
                        <span class='red'>$red</span>
                        <span class='green'>$green</span>
                        <span class='blue' style='color: #8497B0;'>/$blue</span>
                      </div>";
                echo "</div>";
                echo "</div>";
                echo "<div class='line'></div>";

                echo "<ul>";
                showDecks($deckID);
                echo "</ul>";

                echo "</li>";
            }
        }
    }
    ?>

    <div class="wrapper-main">
        <div class="deck-layout">
            <ul>
                <li class="class-title">
                    <!-- Colored Title -->
                    <div class="title-to-review" onclick="window.location.href='flashcard.php?deck_id=main'">
                        <!-- Deck Title -->
                        <span class="title">Main Deck</span>
                        <!-- To Review Green Red Blue-->
                        <div class="to-review">
                            <span class="red"><?= $countMain["red"]; ?></span>
                            <span class="green"><?= $countMain["green"]; ?></span>
                            <span class="blue">/<?= $countMain["blue"]; ?></span>
                        </div>
                    </div>

                    <div class="subdeck">
                        <?php showDecks(); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- ==== SCRIPT ==== -->
    <script>
        function Mode() {
            window.location.href = "home_page.php";
        }
    </script>
</body>

</html>