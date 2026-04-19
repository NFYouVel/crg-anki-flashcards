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

    <div class="wrapper-card-matching">
        <div class="wrapper-decks">
            <table class="card-matching">
                <caption>
                    <div class="wrapper-timer">
                        <img src="../../../Assets/Icons/x icon.png" class="close" onclick="stopMatchingCard()">
                        <span class="stopwatch">0.00s</span>
                        <img src="../../../Assets/Icons/reset icon.png" class="reset" onClick="resetMatchingCard()">
                    </div>
                </caption>
                <tr class="cards">
                    <td>吗</td>
                    <td>zì xíngchē</td>
                    <td>Bus umum</td>
                </tr>
                <tr class="cards">
                    <td>妈妈</td>
                    <td>gōng gòng qì chē</td>
                    <td>Ibu; mama</td>
                </tr>
                <tr class="cards">
                    <td>自行车</td>
                    <td>ma</td>
                    <td>(partikel untuk pertanyaan yang jawabannya iya/tidak); apakah</td>
                </tr>
                <tr class="cards">
                    <td>公共汽车</td>
                    <td>huā qián rú liú shu</td>
                    <td>sepeda</td>
                </tr>
                <tr class="cards">
                    <td>花钱如流水</td>
                    <td>mā ma</td>
                    <td>menghabiskan uang layaknya air mengalir; sangat boros</td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        document.querySelector('.language').textContent = localStorage.getItem('meaning');
        
        let time = 0;
        let interval;
        function startMatchingCard() {
            document.querySelector('.wrapper-card-matching-tutorial').style.display = 'none';
            document.querySelector('.wrapper-card-matching').style.display = 'flex';

            clearInterval(interval);
            time = 0;
            interval = setInterval(() => {
                time += 0.01;
                document.querySelector(".stopwatch").innerText =
                    time.toFixed(2) + "s";
            }, 10);
        }

        function resetMatchingCard() {
            clearInterval(interval);
            time = 0;
            interval = setInterval(() => {
                time += 0.01;
                document.querySelector(".stopwatch").innerText =
                    time.toFixed(2) + "s";
            }, 10);
        }

        function stopMatchingCard() {
            clearInterval(interval);
            document.querySelector('.wrapper-card-matching-tutorial').style.display = 'flex';
            document.querySelector('.wrapper-card-matching').style.display = 'none';
        }

        function changeMeaning() {
            if (document.querySelector('.language').textContent == 'English') {
                localStorage.setItem('meaning', 'Indonesia');
                document.querySelector('.language').textContent = 'Indonesia';
            } else {
                localStorage.setItem('meaning', 'English');
                document.querySelector('.language').textContent = 'English';
            }
        }

        function Mode() {
            window.location.href = "../home_page.php";
        }
    </script>
</body>

</html>