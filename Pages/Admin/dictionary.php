<?php
    session_start();
    include_once "../../SQL_Queries/connection.php";
    $user_id = $_SESSION["user_id"];
    if(mysqli_fetch_assoc(mysqli_query($con, "SELECT role FROM users WHERE user_id = '$user_id'"))["role"] != 1) {
        header("Location: ../Login");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictionary</title>
    <link rel="icon" href="../../Assets/Icons/1080.png">
    <style>
        h1 {
            color: white;
            font-size: 35px;
            margin: 16px 0;
        }
        #header {
            background-color: #262626;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 85%;
            left: calc(15% + 24px);
            top: 0;
            padding-right: 48px;
            box-sizing: border-box;
        }
        input {
            height: 50px;
            width: 275px;
            border-radius: 12px;
            border: 2px solid #e9a345;
            font-size: 20px;
        }
        input::placeholder {
            color: #e9a345;
            font-size: 20px;
        }
        .button {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            width: 200px;
            height: 50px;
            background-color: #ffa72a;
            border-radius: 25px;
            font-size: 24px;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        table {
            width: 100%;
            font-size: 20px;
            border-collapse: collapse;
            margin-bottom: 48px;
            margin-top: 80px;
        }
        th {
            color: white;
            background-color: #003b58;
        }
        th, td {
            border: 2px solid black;
            padding: 5px 10px;
        }
        tr {
            transition: box-shadow 0.5s ease;
        }
        .highlighted {
            box-shadow: 0 0 0 4px white inset;
        }
        #long {
            white-space: normal;
            word-break: break-word;
        }
        td {
            padding: 5px;
        }
        tr:nth-child(even) {
            background-color: #838383;
        }
        tr:nth-child(odd) {
            background-color: #a5a5a5;
        }
        #pageNav {
            width: 100%;
        }
        #actions {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }
        #actions input {
            width: 50px;
            text-align: center;
        }
        #actions a {
            font-size: 18px;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
        th {
            position: sticky;
            z-index: 200;
            top: 70px;
        }
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
        include "convertPinyin.php";
    ?>
    <div id="header">
        <h1>Dictionary (Card) Overview</h1>
        <input type="text" placeholder="&#128269;Search" value="<?php echo $_GET['search'] ?? ($_GET['cardID'] ?? ''); ?>" onkeyup="searchCards(this.value)">
        <form action="addCards.php" method="POST" enctype="multipart/form-data">
            <input id="file" name="excel_file" style="display: none" class="button" type="file" onchange="this.form.submit()">
            <label class="button" for="file">Import</label>
        </form>
    </div>
    <div id="container">
        <table id="cardsTable">
            <tr id="tableHeader">
                <th>ID</th>
                <th>Traditional</th>
                <th>Simplified</th>
                <th>Priority</th>
                <th>Pinyin</th>
                <th>Word Class</th>
                <th>English</th>
                <th>Indo</th>
                <th>Sentence Count</th>
            </tr>
            <?php
                $limit = $_GET["page"] ?? 0;
                $bottomLimit = $limit * 100;
                $upperLimit = ($limit + 1) * 100;
                $cards = mysqli_query($con, "SELECT * FROM cards WHERE card_id > $bottomLimit AND card_id <= $upperLimit ORDER BY card_id ASC");
                while($card = mysqli_fetch_array($cards)) {
                    $cardID = $card["card_id"];
                    $countLinked = mysqli_query($con, "SELECT COUNT(*) as total FROM junction_card_sentence WHERE card_id = $cardID");
                    $countLinked = mysqli_fetch_assoc($countLinked)["total"];
                    echo "<tr id='card-$cardID'>";
                    echo "<td>{$card['card_id']}</td>";
                    echo "<td>{$card['chinese_tc']}</td>";
                    echo "<td>{$card['chinese_sc']}</td>";
                    echo "<td>{$card['priority']}</td>";
                    echo "<td>" . convert($card['pinyin']) . "</td>";
                    echo "<td>{$card['word_class']}</td>";
                    echo "<td id='long'>{$card['meaning_eng']}</td>";
                    echo "<td id='long'>{$card['meaning_ina']}</td>";
                    echo "<td>$countLinked</td>";
                    echo "</tr>";
                }
            ?>
        </table>
        <div id="pageNav">
            <div id="actions">
                <a href="dictionary.php?page=0"><span><<</span></a>
                <a href="dictionary.php?page=<?php echo $limit - 1 ?>"><span><</span></a>
                <div><input type="number" id="pageInput" value="<?php echo $limit + 1; ?>"> <span style="color: white; font-size: 18px;"> / <?php 
                    $maxPage = mysqli_query($con, "SELECT MAX(card_id) as total FROM cards;");
                    echo (int)(mysqli_fetch_array($maxPage)["total"] / 100) + 1;
                ?></span></div>
                <a href="dictionary.php?page=<?php echo $limit + 1 ?>"><span>></span></a>
                <a href="dictionary.php?page=<?php echo (int)(mysqli_fetch_array(mysqli_query($con, "SELECT MAX(card_id) as total FROM cards;"))["total"] / 100); ?>"><span>>></span></a>
            </div>
        </div>
    </div>
    <script>
        document.getElementById("pageInput").addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                let limit = parseInt(document.getElementById("pageInput").value);
                if (!isNaN(limit)) {
                    window.location.href = "dictionary.php?page=" + (limit - 1);
                }
            }
        });

        let searchTimeout = null;

        function searchCards(str) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                str = str.trim();
                if (str === "") return;

                if (isNaN(str)) {
                    const xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
                    xmlhttp.onreadystatechange = function () {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                            let cardID = parseInt(xmlhttp.responseText);
                            if (!cardID) {
                                alert("Card not found.");
                                return;
                            }
                            handleSearch(cardID, str);
                        }
                    };
                    xmlhttp.open("GET", "AJAX/getCardID.php?hanzi=" + encodeURIComponent(str), true);
                    xmlhttp.send();
                } else {
                    const cardID = parseInt(str);
                    handleSearch(cardID);
                }
            }, 300);
        }

        function handleSearch(cardID, originalSearch = null) {
            const page = Math.floor((cardID - 1) / 100);
            const currentPage = <?php echo $limit; ?>;

            const url = new URL(window.location.href);
            url.searchParams.set("page", page);
            url.searchParams.set("cardID", cardID);
            if (originalSearch) {
                url.searchParams.set("search", originalSearch);
            }

            if (currentPage !== page) {
                window.location.href = url.toString();
                return;
            }

            const row = document.getElementById("card-" + cardID);
            if (row) {
                row.scrollIntoView({ behavior: "smooth", block: "center" });
                row.classList.add("highlighted");
                setTimeout(() => row.classList.remove("highlighted"), 2500);
            }
        }

        window.addEventListener("DOMContentLoaded", () => {
            const highlightID = <?php echo json_encode($_GET["cardID"] ?? 0); ?>;
            if (highlightID) {
                const row = document.getElementById("card-" + highlightID);
                if (row) {
                    row.scrollIntoView({ behavior: "smooth", block: "center" });
                    row.classList.add("highlighted");
                    setTimeout(() => row.classList.remove("highlighted"), 2500);
                }
            }
        });
    </script>
</body>
<style>
    #dictionary {
        color: #ffa72a;
    }
</style>
</html>