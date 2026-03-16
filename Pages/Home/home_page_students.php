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

    function countRGB($deckID)
    {
        global $con, $user_id;
        include "repetition_flashcard.php";
        return mysqli_fetch_assoc($query_flashcard_rbg_count);
    }

    $countMain = countRGB("main");
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


                        <div class="subdeck">
                            <?php
                            // ==== STEP 1: Load ALL decks for tree structure — 1 query ====
                            $allDecks = [];
                            $decksByParent = [];
                            $allDecksResult = mysqli_query($con, "SELECT deck_id, parent_deck_id, name FROM decks ORDER BY name");
                            while ($deck = mysqli_fetch_assoc($allDecksResult)) {
                                $allDecks[$deck['deck_id']] = $deck;
                                $decksByParent[$deck['parent_deck_id'] ?? 'root'][] = $deck['deck_id'];
                            }

                            // ==== STEP 2: Load user-assigned decks + trace to root — 1 query ====
                            $rootDecks = [];
                            $userDecksResult = mysqli_query($con, "
    SELECT deck.deck_id, deck.parent_deck_id
    FROM junction_deck_user AS deck_user
    JOIN decks AS deck ON deck_user.deck_id = deck.deck_id
    WHERE deck_user.user_id = '$user_id'
");
                            function getRoot($deckID)
                            {
                                global $allDecks, $rootDecks;
                                if (in_array($deckID, $rootDecks)) return;
                                $rootDecks[] = $deckID;
                                $parentID = $allDecks[$deckID]['parent_deck_id'] ?? null;
                                if ($parentID !== null && isset($allDecks[$parentID])) {
                                    getRoot($parentID);
                                }
                            }
                            while ($deck = mysqli_fetch_assoc($userDecksResult)) {
                                getRoot($deck['deck_id']);
                            }

                            // ==== STEP 3: Batch RGB query ====
                            $rgbCounts = [];
                            if (!empty($rootDecks)) {
                                $deckIdList = implode(',', array_map(function ($id) use ($con) {
                                    return "'" . mysqli_real_escape_string($con, $id) . "'";
                                }, $rootDecks));

                                $batchRGB = mysqli_query($con, "
        SELECT
            ldm.deck_id,
            COUNT(DISTINCT cp.card_id) AS blue,
            COUNT(DISTINCT CASE WHEN cp.current_stage != 0 THEN cp.card_id END) AS green,
            COUNT(DISTINCT CASE WHEN cp.review_due <= NOW() AND cp.review_due != cp.review_first THEN cp.card_id END) AS red
        FROM card_progress cp
        INNER JOIN junction_deck_card jdc ON cp.card_id = jdc.card_id
        INNER JOIN junction_deck_user jdu ON jdu.deck_id = jdc.deck_id
        INNER JOIN leaf_deck_map ldm ON ldm.leaf_deck_id = jdu.deck_id
        WHERE cp.user_id = '$user_id' AND jdu.user_id = '$user_id'
        AND ldm.deck_id IN ($deckIdList)
        GROUP BY ldm.deck_id
    ");
                                while ($row = mysqli_fetch_assoc($batchRGB)) {
                                    $rgbCounts[$row['deck_id']] = $row;
                                }
                            }

                            // ==== STEP 4: hasChild from memory — no query ====
                            $decksWithChildren = [];
                            foreach ($allDecks as $deck) {
                                if ($deck['parent_deck_id'] !== null) {
                                    $decksWithChildren[$deck['parent_deck_id']] = true;
                                }
                            }

                            // ==== STEP 5: showDecks from memory — no queries ====
                            function showDecks($parentID = null)
                            {
                                global $rootDecks, $rgbCounts, $decksWithChildren, $decksByParent, $allDecks;
                                $key = $parentID ?? 'root';
                                if (!isset($decksByParent[$key])) return;

                                $children = $decksByParent[$key];
                                usort($children, fn($a, $b) => strcmp($allDecks[$a]['name'], $allDecks[$b]['name']));

                                foreach ($children as $deckID) {
                                    if (in_array($deckID, $rootDecks)) {
                                        $countRGB = $rgbCounts[$deckID] ?? ['green' => 0, 'red' => 0, 'blue' => 0];
                                        $green = $countRGB["green"];
                                        $red   = $countRGB["red"];
                                        $blue  = $countRGB["blue"];
                                        $name  = htmlspecialchars($allDecks[$deckID]['name']);

                                        echo "<li class='contain' data-id='$deckID'>";
                                        echo "<div class='container-deck'>";
                                        if (!isset($decksWithChildren[$deckID])) {
                                            echo "<div class='md5qdw8dq' style='width: 30px; display: flex; align-items: center;'></div>";
                                        } else {
                                            echo "<div class='plus'><i class='bx bxs-caret-down bx-flip-horizontal' style='color:#8e8e8e;font-size: 24px'></i> </div>";
                                        }
                                        echo "<div class='title-to-review-second' onclick=\"window.location.href='flashcard.php?deck_id=$deckID'\">";
                                        echo "<span class='title-second'>$name</span>";
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

                            showDecks();
                            ?>
                        </div>
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