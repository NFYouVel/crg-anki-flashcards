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
                    <div class="title-to-review" onclick="window.location.href='CardSwipe/index.php?deckId=main'">
                        <!-- Deck Title -->
                        <span class="title">Main Deck</span>
                        <!-- To Review Green Red Blue-->
                        <div class="to-review">
                            <?php
                            $getCount = mysqli_query($con, "SELECT COUNT(*) AS card_count
                                FROM card_swipe_session css
                                INNER JOIN card_swipe_progress csp
                                    ON css.card_swipe_id = csp.card_swipe_id
                                WHERE css.deck_id = 'Main' AND user_id = '$user_id' AND csp.status != 'inactive'
                            ");
                            $cardCount = mysqli_fetch_assoc($getCount);
                            $cardCount = $cardCount['card_count'];
                            echo "<span class='count-cards' style='color: #8e8e8e; width: 100px; text-align: right;'>$cardCount cards</span>";
                            ?>
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

                            $rootDecksSet = array_flip($rootDecks);

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
                                global $rootDecks, $decksWithChildren, $decksByParent, $allDecks, $user_id, $con;
                                $key = $parentID ?? 'root';
                                if (!isset($decksByParent[$key])) return;

                                $children = $decksByParent[$key];
                                usort($children, fn($a, $b) => strcmp($allDecks[$a]['name'], $allDecks[$b]['name']));

                                foreach ($children as $deckID) {
                                    if (in_array($deckID, $rootDecks)) {
                                        $name  = htmlspecialchars($allDecks[$deckID]['name']);

                                        $getCount = mysqli_query($con, "SELECT COUNT(*) AS card_count
                                            FROM card_swipe_session css
                                            INNER JOIN card_swipe_progress csp
                                                ON css.card_swipe_id = csp.card_swipe_id
                                            WHERE css.deck_id = '$deckID' AND user_id = '$user_id' AND csp.status != 'inactive'
                                        ");
                                        $cardCount = mysqli_fetch_assoc($getCount);
                                        $cardCount = $cardCount['card_count'];

                                        if ($cardCount == 0) {
                                            $getCount = mysqli_query($con, "SELECT COUNT(*) AS card_count
                                                FROM leaf_deck_map ldm
                                                INNER JOIN junction_deck_card jdc
                                                    ON ldm.leaf_deck_id = jdc.deck_id
                                                WHERE ldm.deck_id = '$deckID'
                                            ");
                                            $cardCount = mysqli_fetch_assoc($getCount);
                                            $cardCount = $cardCount["card_count"];
                                        }

                                        echo "<li class='contain' data-id='$deckID'>";
                                        echo "<div class='container-deck'>";
                                        if (!isset($decksWithChildren[$deckID])) {
                                            echo "<div class='md5qdw8dq' style='width: 30px; display: flex; align-items: center;'></div>";
                                        } else {
                                            echo "<div class='plus'><i class='bx bxs-caret-down bx-flip-horizontal' style='color:#8e8e8e;font-size: 24px'></i> </div>";
                                        }
                                        echo "<div class='title-to-review-second' onclick=\"window.location.href='CardSwipe/index.php?deckId=$deckID'\">";
                                        echo "<span class='title-second'>$name</span>";
                                        echo "<div class='to-review'>
                                            <span class='count-cards' style='color: #8e8e8e;'>$cardCount cards</span>
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

    <!-- ==== FAB ==== -->
    <div class="fab-container">
        <div class="fab-options" id="fabOptions">
            <div class="fab-option" onclick="window.location.href='home_page_card_swipe.php'">
                <span class="fab-label">Flashcard Swipe</span>
                <div class="fab-icon"><img src="../../Assets/Icons/flashcard-logo.png" alt=""></div>
            </div>
            <div class="fab-option" onclick="window.location.href='home_page_students.php'">
                <span class="fab-label">SRS Review</span>
                <div class="fab-icon"><img src="../../Assets/Icons/srs-logo.png" alt=""></div>
            </div>
        </div>

        <div class="fab-main-row">
            <span class="fab-mode-label" id="fabModeLabel">Current Mode:<br><b>Flashcard Swipe</b></span>
            <div class="fab-main" id="fabMain" onclick="toggleFab()">
                <div id="fab-icon" style="display: flex; align-items: center; justify-content: center;">
                    <img id="fabImg" src="../../Assets/Icons/flashcard-logo.png" style="max-width: 75%; max-height: 75%" alt="">
                    <span id="fabX" style="display:none; color:white; font-size:22px;">&#10005;</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fab-container {
            position: fixed;
            bottom: 32px;
            right: 24px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
            z-index: 999;
        }

        .fab-main-row {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .fab-mode-label {
            background-color: #ffe699;
            color: #1c3a50;
            padding: 8px 20px 8px 14px;
            padding-right: 50px;
            margin-right: -30px;
            /* border-radius: 30px 0 0 30px; */
            font-family: 'Nunito', sans-serif;
            font-size: 13px;
            line-height: 1.4;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .fab-main {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: #FFA500;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            transition: background-color 0.2s;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .fab-main:hover {
            background-color: #e69500;
        }

        #fabIcon {
            color: white;
            font-size: 20px;
        }

        .fab-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: flex-end;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transition: max-height 0.35s ease, opacity 0.25s ease;
        }

        .fab-options.open {
            max-height: 300px;
            opacity: 1;
        }

        .fab-option {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .fab-label {
            background-color: #ffe699;
            color: #1c3a50;
            padding: 6px 12px;
            /* border-radius: 20px; */
            padding-right: 30px;
            margin-right: -30px;
            font-family: 'Nunito', sans-serif;
            font-weight: bold;
            font-size: 14px;
            white-space: nowrap;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .fab-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: #FFA500;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            flex-shrink: 0;
        }

        .fab-icon img {
            max-width: 75%;
            max-height: 75%;
            /* width: 26px;
            height: 26px;
            filter: brightness(0) invert(1); */
        }
    </style>

    <script>
        let fabOpen = false;

        function toggleFab() {
            fabOpen = !fabOpen;
            document.getElementById("fabOptions").classList.toggle("open", fabOpen);
            document.getElementById("fabMain").style.backgroundColor = fabOpen ? "#c0392b" : "#FFA500";
            document.getElementById("fabModeLabel").style.display = fabOpen ? "none" : "inline-block";
            document.getElementById("fabImg").style.display = fabOpen ? "none" : "block";
            document.getElementById("fabX").style.display = fabOpen ? "block" : "none";
        }
    </script>

    <!-- ==== SCRIPT ==== -->
    <script>
        function Mode() {
            window.location.href = "home_page.php";
        }
    </script>
</body>

</html>