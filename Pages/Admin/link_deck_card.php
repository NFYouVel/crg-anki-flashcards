<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Deck</title>
    <link rel="icon" href="../../Logo/circle.png">
    <style>
        h1 {
            color: white;
        }
        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #form {
            display: flex;
            gap: 16px;
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
            align-self: flex-start;
        }
        th {
            color: white;
            background-color: #003b58;
        }
        th, td {
            border: 2px solid black;
            padding: 5px 10px;
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
        caption {
            color: white;
            border: 2px solid black;
        }
        th {
            position: sticky;
            z-index: 200;
            top: 0;
        }
        select {
            appearance: none;
            width: 250px;
            padding: 10px 16px;
            border: 2px solid #e9a345;
            border-radius: 12px;
            background-color: white;
            font-size: 18px;
            color: #333;
            cursor: pointer;
        }
        select:focus {
            outline: none;
            border-color: #ffa72a;
            box-shadow: 0 0 5px #ffa72a;
        }
        select option[disabled] {
            color: #999;
        }
        #loadingScreen {
            background-color: #262626;
            position: fixed;
            border-radius: 24px;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 25%;
            height: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            display: none;
        }
        #menu {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        select {
            appearance: none;
            width: 250px;
            padding: 10px 16px;
            border: 2px solid #e9a345;
            border-radius: 12px;
            background-color: white;
            font-size: 18px;
            color: #333;
            cursor: pointer;
        }
        select:focus {
            outline: none;
            border-color: #ffa72a;
            box-shadow: 0 0 5px #ffa72a;
        }
        select option[disabled] {
            color: #999;
        }
    </style>
    <script>
        function uploadCards() {
            document.getElementById("loadingScreen").style.display = "flex";
            var xmlhttp;
            if (window.XMLHttpRequest != null) {
                xmlhttp = new XMLHttpRequest();
            }
            else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("tables").innerHTML = xmlhttp.responseText;
                    document.getElementById("cancel").innerText = "Back";
                    document.getElementById("loadingScreen").style.display = "none";
                    document.getElementById("importButton").style.display = "none";
                    document.getElementById("filter").style.display = "none";
                }
            }
            xmlhttp.open("GET", "AJAX/batch_junction_deck_card.php?deckID=<?php $deckID = $_GET["deckID"]; echo $deckID; ?>", true);
            xmlhttp.send();
        }

        function previewModes(str) {
            var xmlhttp;
            if (window.XMLHttpRequest != null) {
                xmlhttp = new XMLHttpRequest();
            }
            else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("tables").innerHTML = xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET", "AJAX/deckCardPreviewMode.php?mode=" + str, true);
            xmlhttp.send();
        }
    </script>
</head>
<body>
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
    ?>
    <div id="loadingScreen">
        <img src="Components/loading.gif" alt="">
        <h1>Importing</h1>
    </div>
    <div id="container">
        <div id="header">
            <h1>Update Deck (Preview)</h1>
        </div>
        <div id="menu">
            <h1>
                <?php
                    $ancestor = [];
                    while($deckID) {
                        $getDecks = mysqli_query($con, "SELECT parent_deck_id, name FROM decks WHERE deck_id = '$deckID'");
                        $getDecks = mysqli_fetch_assoc($getDecks);
                        array_unshift($ancestor, $getDecks["name"]);
                        $deckID = $getDecks["parent_deck_id"];
                    }
                    $decks = "";
                    foreach($ancestor as $index => $deck) {
                        $decks .= $deck;
                        if ($index < count($ancestor) - 1) {
                            $decks .= "<span style='margin-inline: 8px;'>&gt;</span> ";
                        }
                    }
                    echo $decks;
                    ?>
            </h1>
            <select id = "filter" onchange = 'previewModes(this.value)'>
                <option value="preview">Preview (Default)</option>
                <option value="valid">Valid Users</option>
                <option value="invalid">Invalid Invalid</option>
            </select>
            <div id = "form">
                <a href="deck.php" id = "cancel" class="button">Cancel</a>
                <button class="button" id = "importButton" onclick = "uploadCards()">Save</button>
            </div>
        </div>
        <div id="tables">
            <table>
                <caption style = "background-color: white; color: black;">Preview</caption>
                <tr>
                    <th>No</th>
                    <th>Card ID</th>
                    <th>Traditional</th>
                    <th>Simplified</th>
                    <th>Prio</th>
                    <th>Pinyin</th>
                    <th>Word Class</th>
                    <th>English</th>
                    <th>Indo</th>
                </tr>
                <?php
                    $deckID = $_GET["deckID"];
                    require '../../Composer_Excel/vendor/autoload.php';
                    use PhpOffice\PhpSpreadsheet\IOFactory;
                    //jika page ke refresh, tidak perlu nge read ulang file, tapi mengambil dari session yang dibuat sebelum ke refresh
                    if (isset($_FILES['excel_file'])) {
                        $fileTmpPath = $_FILES['excel_file']['tmp_name'];
                        $fileName = $_FILES['excel_file']['name'];
                        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                        $allowedExtensions = ['xls', 'xlsx'];

                        if (in_array($fileExtension, $allowedExtensions)) {
                            try {
                                $spreadsheet = IOFactory::load($fileTmpPath);
                                $sheet = $spreadsheet->getSheet(3);
                                $allCards = [];
                                $validCards = [];
                                $invalidCards = [];
                                $count = 1;
                                $id = 1;
                                foreach ($sheet->getRowIterator() as $row) {
                                    $index = $row->getRowIndex();
                                    //skip index 1 karena itu header
                                    if($index == 1) {
                                        continue;
                                    }
                                    //mengambil data dari tiap komumn dan index tertentu (index akan terus bertambah)
                                    $cardID = $sheet->getCell("B$index")->getValue();
                                    $cardInfo = mysqli_query($con, "SELECT * FROM cards WHERE card_id = $cardID");
                                    $cardInfo = mysqli_fetch_assoc($cardInfo);

                                    echo "
                                    <tr>
                                        <td>" . $id++ . "</td>
                                        <td>" . $cardInfo["card_id"] . "</td>
                                        <td>" . $cardInfo["chinese_tc"] . "</td>
                                        <td>" . $cardInfo["chinese_sc"] . "</td>
                                        <td>" . $cardInfo["priority"] . "</td>
                                        <td>" . $cardInfo["pinyin"] . "</td>
                                        <td>" . $cardInfo["word_class"] . "</td>
                                        <td class = 'long'>" . $cardInfo["meaning_eng"] . "</td>
                                        <td class = 'long'>" . $cardInfo["meaning_ina"] . "</td>
                                    </tr>";

                                    $reason = "";
                                    //check if card id exists
                                    if(mysqli_num_rows(mysqli_query($con, "SELECT card_id FROM cards WHERE card_id = $cardID")) == 0) {
                                        $reason .= "<p id = 'invalid'>Card ID Not Found</p>";
                                    }

                                    //check if deck id exists
                                    if(mysqli_num_rows(mysqli_query($con, "SELECT deck_id FROM decks WHERE deck_id = '$deckID'")) == 0) {
                                        $reason .= "<p id = 'invalid'>Deck ID Not Found</p>";
                                    }

                                    //membangun session untuk semua kartu
                                    $allCards[$cardID] = [
                                        "cardID" => $cardID, 
                                        "traditional" => $cardInfo["chinese_tc"], 
                                        "simplified" => $cardInfo["chinese_sc"], 
                                        "priority" => $cardInfo["priority"],
                                        "pinyin" => $cardInfo["pinyin"],
                                        "class" => $cardInfo["word_class"],
                                        "english" => $cardInfo["meaning_eng"],
                                        "indo" => $cardInfo["meaning_ina"]
                                    ];
                                    //logika valid / tidak valid
                                    if($reason == "") {
                                        //membantun session untuk kartu yang valid
                                        $validCards[$cardID] = [
                                            "cardID" => $cardID,
                                            "traditional" => $cardInfo["chinese_tc"], 
                                            "simplified" => $cardInfo["chinese_sc"], 
                                            "priority" => $cardInfo["priority"],
                                            "pinyin" => $cardInfo["pinyin"],
                                            "class" => $cardInfo["word_class"],
                                            "english" => $cardInfo["meaning_eng"],
                                            "indo" => $cardInfo["meaning_ina"]
                                        ];
                                    }
                                    else {
                                        //membantun session untuk kartu yang tidak valid
                                        $invalidCards[$cardID] = [
                                            "cardID" => $cardID,
                                            "traditional" => $cardInfo["chinese_tc"], 
                                            "simplified" => $cardInfo["chinese_sc"], 
                                            "priority" => $cardInfo["priority"],
                                            "pinyin" => $cardInfo["pinyin"],
                                            "class" => $cardInfo["word_class"],
                                            "english" => $cardInfo["meaning_eng"],
                                            "indo" => $cardInfo["meaning_ina"],
                                            "reason" => $reason
                                        ];
                                    }
                                }
                            } catch (Exception $e) {
                                echo "<h1>Error loading file: " . $e->getMessage() . "</h1>";
                            }
                        } else {
                            echo "<h1>Invalid file type. Only .xls and .xlsx files are allowed.</h1>";
                        }
                        //membuat session   
                        $_SESSION["allCards"] = $allCards;
                        $_SESSION["validCards"] = $validCards;
                        $_SESSION["invalidCards"] = $invalidCards;
                    } 
                ?>
            </table>
        </div>
    </div>
</body>
<style>
    #deck {
        color: #ffa72a;
    }
</style>
</html>