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
                    </div>


                    <div class="subdeck">
                        <?php
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

                        function showDecks($parentID = null)
                        {
                            global $con, $user_id, $rootDecks;
                            if ($parentID == null) {
                                $getDecks = mysqli_query($con, "SELECT name, deck_id FROM decks WHERE parent_deck_id IS NULL ORDER BY name");
                            } else {
                                $getDecks = mysqli_query($con, "SELECT name, deck_id FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name");
                            }
                            while ($deck = mysqli_fetch_assoc($getDecks)) {
                                $deckID = $deck["deck_id"];
                                if (in_array($deckID, $rootDecks)) {
                                    $countRGB = countRGB($deckID);
                                    $green = $countRGB["green"];
                                    $red = $countRGB["red"];
                                    $blue = $countRGB["blue"];
                                    echo "<li class='contain' data-id='$deckID'>";
                                    echo "<div class='container-deck'>";
                                    $hasChild = mysqli_query($con, "SELECT * FROM decks WHERE parent_deck_id = '$deckID'");
                                    if (mysqli_num_rows($hasChild) === 0) {
                                        echo "<div class='md5qdw8dq' style='width: 30px; display: flex; align-items: center;'></div>";
                                    } else {
                                        echo "<div class='plus'><i class='bx  bxs-caret-down bx-flip-horizontal' style='color:#8e8e8e;font-size: 24px'></i> </div>";
                                    }
                                    echo "<div class='title-to-review-second' onclick=\"window.location.href='flashcard.php?deck_id=$deckID'\">";
                                    echo "<span class='title-second'>" . htmlspecialchars($deck['name']) . "</span>";
                                    echo "<div class='to-review'>
                                            <span class='red'>$red</span>
                                            <span class='green'>$green</span>
                                            <span class='blue' style = 'color: #8497B0;'>/$blue</span>
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