<?php
session_start();
include "../../../SQL_Queries/connection.php";

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
    <link rel="icon" href="../../../Assets/Icons/1080.png">
    <link rel="stylesheet" href="../../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../../../Pages/Home/CSS/finish_card_matching.css">
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

    <div class="container-finish">

        <div class="wrapper-finish">
            <div class="finish-card">
                <div class="finish-header">
                    <h1>Congratulations! You completed the card matching in:</h1>
                    <span class="finish-time">16.38s</span>
                </div>

                <div class="finish-deck">
                    TOCFL > Band A > Deck 05
                </div>

                <button class="btn-words" onclick="showWords(this)">See Words Used</button>

                <div class="leaderboard-section">
                    <h2>Ranking Leaderboard</h2>

                    <table class="leaderboard-table">
                        <tr>
                            <td class="rank">1</td>
                            <td>Clayton Hazel Gwyneth</td>
                            <td class="score">5.01s</td>
                        </tr>

                        <tr>
                            <td class="rank">2</td>
                            <td>Malvin Owen Hardicar</td>
                            <td class="score">7.02s</td>
                        </tr>

                        <tr>
                            <td class="rank">3</td>
                            <td>Gwenaelle Kiechi Zea Wangjaya</td>
                            <td class="score">10.33s</td>
                        </tr>

                        <tr class="you">
                            <td class="rank">4</td>
                            <td>Herodian Petro Marlim</td>
                            <td class="score">16.38s</td>
                        </tr>

                        <tr>
                            <td class="rank">5</td>
                            <td>Lidya Simon</td>
                            <td class="score">17.00s</td>
                        </tr>
                    </table>
                </div>

                <button class="btn-play">Play Again</button>
                <a href="#" class="back-menu">Back to Main Menu</a>

            </div>
        </div>
    </div>

    <!-- POPUP -->
    <div class="popup-words" id="popupWords">

        <!-- Background Blur -->
        <div class="popup-overlay" onclick="closePopup()"></div>

        <!-- Box Tengah -->
        <div class="popup-box">

            <div class="popup-close" onclick="closePopup()">✕</div>

            <table class="popup-table">
                <tr>
                    <th>No</th>
                    <th>Char</th>
                    <th>Pinyin and Meaning</th>
                    <th>Pair Attempt</th>
                </tr>

                <tr>
                    <td>1</td>
                    <td>吗</td>
                    <td>ma<br>(partikel untuk pertanyaan iya/tidak); apakah</td>
                    <td>1x</td>
                </tr>

                <tr class="wrong">
                    <td>2</td>
                    <td>妈妈</td>
                    <td>mā ma<br>Ibu; mama</td>
                    <td>2x</td>
                </tr>

                <tr>
                    <td>3</td>
                    <td>自行车</td>
                    <td>zì xíng chē<br>sepeda</td>
                    <td>1x</td>
                </tr>

                <tr class="wrong">
                    <td>4</td>
                    <td>公共汽车</td>
                    <td>gōng gòng qì chē<br>bus umum</td>
                    <td>5x</td>
                </tr>

                <tr>
                    <td>5</td>
                    <td>花钱如流水</td>
                    <td>huā qián rú liú shuǐ<br>menghabiskan uang layaknya air mengalir</td>
                    <td>1x</td>
                </tr>
            </table>

        </div>
    </div>

    <script>
        function showWords(button) {
            button.classList.add("animate");
            setTimeout(() => {
                button.classList.remove("animate");
            }, 250);
            document.getElementById("popupWords").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("popupWords").style.display = "none";
        }
    </script>
</body>

</html>