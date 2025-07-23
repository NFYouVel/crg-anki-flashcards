<?php
session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
$user_id = $_SESSION["user_id"];
$classroom_id = $_GET["classroom_id"];
$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_array($result);
$role_id = $line['role'];
$result2 = mysqli_query($con, "SELECT * FROM user_role WHERE role_id = '$role_id'");
$line2 = mysqli_fetch_array($result2);
$role = $line2['role_name'];

if (isset($_POST['hide'])) {
    $name = $line['name'];
    echo "<script>alert('You are login with $name Account as Teacher')</script>";
}

$query = "SELECT * FROM junction_classroom_user WHERE classroom_id = '$classroom_id' AND classroom_role_id = '3'";
$result = mysqli_query($con, $query);
$count = 0;
while ($classroom_line = mysqli_fetch_array($result)) {
    $count++;
}

$query_classroom = "SELECT * FROM classroom WHERE classroom_id = '$classroom_id'";
$result_classroom = mysqli_query($con, $query_classroom);
$classroom_name = mysqli_fetch_array($result_classroom);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $line['name'] ?></title>
    <link rel="icon" href="../../Logo/circle.png">
    <link rel="stylesheet" href="../../Pages/Home/CSS/home_page.css">
    <link rel="stylesheet" href="../../Pages/Home/CSS/classroom_information.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../Home/jQuery/script_classroom.js"></script>
    <script>
        function searchDeck(str) {
            var xmlhttp;
            if (window.XMLHttpRequest != null) {
                xmlhttp = new XMLHttpRequest();
            } else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.querySelector(".explanationWrapper").innerHTML = xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET", "jQuery/ajax_search_deck.php?deckName=" + str + "&classroomID=<?php echo $classroom_id; ?>", true);
            xmlhttp.send();
        }
        function addDeck(str) {
            var xmlhttp;
            if (window.XMLHttpRequest != null) {
                xmlhttp = new XMLHttpRequest();
            } else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.querySelector(".explanationWrapper").innerHTML = xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET", "jQuery/ajax_addDeckClassroom.php?deckID=" + str + "&classroomID=<?php echo $classroom_id; ?>", true);
            xmlhttp.send();
        }
        function removeDeck(str) {
            var xmlhttp;
            if (window.XMLHttpRequest != null) {
                xmlhttp = new XMLHttpRequest();
            } else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.querySelector(".explanationWrapper").innerHTML = xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET", "jQuery/ajax_removeDeckClassroom.php?deckID=" + str + "&classroomID=<?php echo $classroom_id; ?>", true);
            xmlhttp.send();
        }
    </script>
</head>

<body>
    <!-- Header -->
    <?php include "Component/header_login.php"; ?>

    <div class="right-bar">
        <div class="account-info">
            <span class="username"><?php echo $line['name'] ?></span>
            <span class="as" style="cursor: pointer;" onclick="Mode()"><?php echo $role ?> Mode</span>
        </div>
        <script>
            function Mode() {
                window.location.href = "home_page_students.php";
            }
        </script>

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
    ?>
    <div class='wrapper-add'>
        <div class='add'>
            <div class='title-add'>
                <span>Add Premade Deck To Clasroom</span>
                <span>(If the student already has the deck, then the deck will not be added)</span>
            </div>
            <div class='search-bar'>
                <input type="text" onkeyup = "searchDeck(this.value)" placeholder="Search deck" name="search" class="search-bar">
                <div class="icon-add">🔍</div>
            </div><hr>
            <div class='explanation'>
                <div class="explanationWrapper">
                    <?php
                        function getDecks($parentID) {
        global $classroom_id;
        global $con;
        if ($parentID == "root") {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE parent_deck_id IS NULL ORDER BY name ASC");
        } else {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE deck_id = '$parentID'");
        }
        if (mysqli_num_rows($getDecks) > 0) {
            while ($deck = mysqli_fetch_assoc($getDecks)) {
                $deckID = $deck["deck_id"];
                $name = $deck["name"];
                $isLeaf = $deck["is_leaf"];

                if ($isLeaf == 0) {
                    echo "<div class = 'deck'>";
                        echo "<div class = 'labelWrapper'>";
                            echo "<div class = 'label'>";
                                echo "<div class = 'deckTitle'>";
                                    echo "<span class = 'expand'>▶</span>";
                                    echo "<span class = 'deckName'>$name</span>";
                                echo "</div>";
                                if(mysqli_num_rows(mysqli_query($con, "SELECT 1 FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroom_id'")) > 0) {
                                    echo "<span onclick=\"removeDeck('$deckID')\" class = 'addDeck added'>+</span>";
                                }
                                else {
                                    echo "<span onclick=\"addDeck('$deckID')\" class='addDeck'>+</span>";
                                }
                            echo "</div>";
                        echo "</div>";
                        $getChildren = mysqli_query($con, "SELECT deck_id FROM decks WHERE parent_deck_id = '$deckID' ORDER BY name ASC");
                        while($children = mysqli_fetch_assoc($getChildren)) {
                            getDecks($children["deck_id"]);
                        }
                    echo "</div>";
                } else {
                    echo "<div class = 'deck'>";
                        echo "<div class = 'labelWrapper'>";
                            echo "<div class = 'label'>";
                                echo "<div class = 'deckTitle'>";
                                    echo "<span style = 'opacity: 0;' class = 'expand'>▶</span>";
                                    echo "<span class = 'deckName'>$name</span>";
                                echo "</div>";
                                if(mysqli_num_rows(mysqli_query($con, "SELECT classroom_id FROM junction_deck_classroom WHERE deck_id = '$deckID' AND classroom_id = '$classroom_id'")) > 0) {
                                    echo "<span onclick=\"removeDeck('$deckID')\" class = 'addDeck added'>+</span>";
                                }
                                else {
                                    echo "<span onclick=\"addDeck('$deckID')\" class='addDeck'>+</span>";
                                }
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                }
            }
        }
    }
                        getDecks("root");
                    ?>
                </div>
            </div>
            <div class='button'>
                <button class='button-cancel'>Cancel</button>
                <button class='button-add'>Add to Classroom</button>
            </div>
        </div>
    </div>

    <div class="wrapper-main">
        <div class="deck-layout">
            <!-- Example: For Teacher -->
            <ul>
                <!-- First Main -->
                <!-- Active Chinese Senin Kamis 20.30-->
                <li class="class-title">
                    <!-- Colored Title -->
                    <div class="title-to-review classroom-information-toggle">
                        <!-- Deck Title -->
                        <div class="container-classroom-information">
                            <span class="title">
                                Classroom Information
                            </span>
                            <span class="title" id="contain">
                                <?php 
                                echo $classroom_name['name'];
                                ?>
                            </span>
                        </div>
                        <div class="icon-ci" id="content">
                            <div class="arrow">▶</div>
                        </div>
                    </div>
                    <div class="subdeck">
                        <ul>
                            <!-- Second Main -->
                            <li class="contain-ci">
                                <table>
                                    <tr>
                                        <td>Level</td>
                                        <td>:</td>
                                        <td>Beginner</td>
                                    </tr>
                                    <tr>
                                        <td>Material</td>
                                        <td>:</td>
                                        <td>Active Chinese</td>
                                    </tr>
                                    <tr>
                                        <td>Class Type</td>
                                        <td>:</td>
                                        <td>Regular</td>
                                    </tr>
                                    <tr>
                                        <td>Access Type</td>
                                        <td>:</td>
                                        <td>Online</td>
                                    </tr>
                                    <tr>
                                        <td>Teacher</td>
                                        <td>:</td>
                                        <td>Laoshi Xinyue</td>
                                    </tr>
                                    <tr>
                                        <td>Schedule</td>
                                        <td>:</td>
                                        <td>Monday 20.30 - 21.30 (Online)</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>:</td>
                                        <td>Monday 20.30 - 21.30 (Online)</td>
                                    </tr>
                                    <tr>
                                        <td>Meet Link</td>
                                        <td>:</td>
                                        <td>https: zoom.id</td>
                                    </tr>
                                    <tr>
                                        <td>Class Notes</td>
                                        <td>:</td>
                                        <td>-</td>
                                    </tr>
                                </table>


                            </li>

                            <!-- Second Main Dari Sini -->

                        </ul>
                    </div>

                    <!-- Student List -->
                    <div class="students-to-review">
                        <!-- Deck Title -->
                        <span class="title">
                            Student List (<?php echo $count ?>)
                        </span>
                        <!-- To Review Green Red Blue-->
                        <div class="to-review">
                            <span class="click">Add Deck to Classroom</span>
                        </div>
                    </div>
                </li>
                <?php
                $query = "SELECT * FROM junction_classroom_user WHERE classroom_id = '$classroom_id' AND classroom_role_id = '3'";
                $result = mysqli_query($con, $query);
                while ($classroom_line = mysqli_fetch_array($result)) {
                    $user_id_student = $classroom_line['user_id'];
                    $query2 = "SELECT * FROM users WHERE user_id = '$user_id_student'";
                    $result2 = mysqli_query($con, $query2);
                    $line_name = mysqli_fetch_assoc($result2);
                    $temp_name = $line_name['name'];
                    echo " <div class='title-student'>
                    <!-- Deck Title -->
                    <span class='title'>$temp_name</span>
                    <!-- To Review Green Red Blue-->
                    <div class='to-review'>
                        <span class='green'>169</span>
                        <span class='red'>28</span>
                        <span class='blue'>1638</span>
                    </div>
                </div>";
                }
                ?>

                <!-- Sampe Sini (First)-->
            </ul>
        </div>
    </div>

</body>

</html>