<?php
session_start();
include "../../SQL_Queries/connection.php";
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
    header("Location: setting.php");
}

// Role User ID
$result2 = mysqli_query($con, "SELECT * FROM user_role WHERE role_id = '$role_id'");
$line2 = mysqli_fetch_array($result2);
$role = $line2['role_name'];

// if (isset($_POST['hide'])) {
//     $name = $line['name'];
//     echo "<script>alert('You are login with $name Account as Teacher')</script>";
// }


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../Assets/Icons/1080.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script.js"></script>
</head>

<body>
    <!-- Header -->
    <?php include "Component/header_login.php"; ?>

    <div class="right-bar">
        <div class="account-info">
            <span class="username"><?php echo $line['name'] ?></span>
            <span class="as">Student</span>
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
            <!-- Example: For Teacher -->
            <ul>
                <!-- First Main -->
                <!-- Active Chinese Senin Kamis 20.30-->
                <li class="class-title">
                    <!-- Colored Title -->
                    <div class="title-to-review" onclick="window.location.href='flashcard.php?deck_id=main'">
                        <!-- Deck Title -->
                        <span class="title">Main Deck</span>
                        <!-- To Review Green Red Blue-->
                        <div class="to-review">
                            <span class="red"><?php echo $countMain["red"]; ?></span>
                            <span class="green"><?php echo $countMain["green"]; ?></span>
                            <span class="blue">/<?php echo $countMain["blue"]; ?></span>
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
                                        echo "<div class='plus'>+</div>";
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
                        // function showDecks($parentID = null) {
                        //     global $con, $user_id;
                        //     $getDeckIDs = mysqli_query($con, "SELECT deck_id FROM junction_deck_user WHERE user_id = '$user_id'");
                        //     $ownedDecks = [];
                        //     while ($row = mysqli_fetch_assoc($getDeckIDs)) {
                        //         $ownedDecks[] = $row['deck_id'];
                        //     }           

                        //     if ($parentID === null) {
                        //         $getDecks = mysqli_query($con, "SELECT * FROM decks WHERE parent_deck_id IS NULL ORDER BY name ASC");
                        //     } else {
                        //         $getDecks = mysqli_query($con, "SELECT * FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name ASC");
                        //     }


                        //     while ($deck = mysqli_fetch_assoc($getDecks)) {

                        //         // echo "Deck saat ini: " . $deck['deck_id'] . "<br>";
                        //         if (in_array($deck['deck_id'], $ownedDecks)) {
                        //             $temp_deck_id = $deck['deck_id'];
                        //             echo "<!-- Debug: nemu deck: " . $deck['deck_id'] . " -->";
                        //             echo "<li class='contain'>";
                        //             echo "<div class='title-to-review-second'>";
                        //             echo "<span class='title-second' onclick='goToFlashcard(this)' data-id='$temp_deck_id'>" . htmlspecialchars($deck['name']) . "</span>";
                        //             echo "<div class='to-review'>
                        //                     <span class='green'>169</span>
                        //                     <span class='red'>28</span>
                        //                     <span class='blue'>1638</span>
                        //                   </div>";
                        //             echo "</div>";
                        //             echo "<div class='line'></div>";

                        //             // Recursive call buat subdeck (kalau is_leaf = 0)
                        //             if ($deck['is_leaf'] == 0) {
                        //                 echo "<ul>"; // start subdeck
                        //                 showDecks($deck['deck_id']);
                        //                 echo "</ul>"; // end subdeck
                        //             }

                        //             echo "</li>";

                        //         }

                        //     }
                        // }

                        ?>
                    </div>
                </li>
                <!-- Sampe Sini (First)-->
            </ul>
        </div>
    </div>
</body>

</html>