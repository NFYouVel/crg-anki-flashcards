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
    <title>Update Deck</title>
    <link rel="icon" href="../../Assets/Icons/1080.png">
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
                    $sheet = $spreadsheet->getActiveSheet();
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
                        $cardID = $sheet->getCell("B$index")->getValue() ?? ""; 
                        $priority = $sheet->getCell("A$index")->getValue();
    
                        // echo "
                        // <tr>
                        //     <td>" . $id++ . "</td>
                        //     <td>" . $cardInfo["card_id"] . "</td>
                        //     <td>" . $cardInfo["chinese_tc"] . "</td>
                        //     <td>" . $cardInfo["chinese_sc"] . "</td>
                        //     <td>" . $cardInfo["priority"] . "</td>
                        //     <td>" . $cardInfo["pinyin"] . "</td>
                        //     <td>" . $cardInfo["word_class"] . "</td>
                        //     <td class = 'long'>" . $cardInfo["meaning_eng"] . "</td>
                        //     <td class = 'long'>" . $cardInfo["meaning_ina"] . "</td>
                        // </tr>";
    
                        $reason = "";
                        echo "<script>console.log('$cardID')</script>";
                        //check if card id exists
                        if($cardID == null || $cardID == "") {
                            $reason .= "<p id = 'invalid'>Card ID Empty</p>";
                        }
                        else if(mysqli_num_rows(mysqli_query($con, "SELECT card_id FROM cards WHERE card_id = $cardID")) == 0) {
                            $reason .= "<p id = 'invalid'>Card ID Not Found</p>";
                        }
                        else {
                            $cardInfo = mysqli_query($con, "SELECT * FROM cards WHERE card_id = $cardID"); // 1954
                            $cardInfo = mysqli_fetch_assoc($cardInfo);
                        }
    
                        //check if deck id exists
                        if(mysqli_num_rows(mysqli_query($con, "SELECT deck_id FROM decks WHERE deck_id = '$deckID'")) == 0) {
                            $reason .= "<p id = 'invalid'>Deck ID Not Found</p>";
                        }
    
                        //membangun session untuk semua kartu
                        $allCards[$cardID] = [
                            "cardID" => $cardID ?? "not found",
                            "traditional" => $cardInfo["chinese_tc"] ?? "not found", 
                            "simplified" => $cardInfo["chinese_sc"] ?? "not found", 
                            "priority" => $cardInfo["priority"] ?? "not found",
                            "pinyin" => $cardInfo["pinyin"] ?? "not found",
                            "class" => $cardInfo["word_class"] ?? "not found",
                            "english" => $cardInfo["meaning_eng"] ?? "not found",
                            "indo" => $cardInfo["meaning_ina" ?? "not found"]
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
                                "indo" => $cardInfo["meaning_ina"],
                                "fl_priority" => $priority
                            ];
                        }
                        else {
                            //membantun session untuk kartu yang tidak valid
                            $invalidCards[$cardID] = [
                                "cardID" => $cardID ?? "not found",
                                "traditional" => $cardInfo["chinese_tc"] ?? "not found", 
                                "simplified" => $cardInfo["chinese_sc"] ?? "not found", 
                                "priority" => $cardInfo["priority"] ?? "not found",
                                "pinyin" => $cardInfo["pinyin"] ?? "not found",
                                "class" => $cardInfo["word_class"] ?? "not found",
                                "english" => $cardInfo["meaning_eng"] ?? "not found",
                                "indo" => $cardInfo["meaning_ina"] ?? "not found",
                                "reason" => $reason ?? "not found"
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
                <option value="valid">Valid Cards</option>
                <option value="invalid">Invalid Cards</option>
            </select>
            <div id = "form">
                <a href="deck.php" id = "cancel" class="button">Cancel</a>
                <button class="button" id = "importButton" onclick = "uploadCards()">Save</button>
            </div>
        </div>
        <h1 style = "display: flex; justify-content: space-evenly;">
            <span>Total Cards: <?php echo count($_SESSION["allCards"]); ?></span>
            <span>Valid Cards: <?php echo count($_SESSION["validCards"]); ?></span>
            <span>Invalid Cards: <?php echo count($_SESSION["invalidCards"]); ?></span>
        </h1>
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
                    $id = 1;
                    foreach($_SESSION["allCards"] as $key => $value) {
                        if(isset($_SESSION["validCards"][$key])) {
                            echo "<tr style = 'background-color: green;'>";
                        }
                        else if(isset($_SESSION["invalidCards"][$key])) {
                            echo "<tr style = 'background-color: red;'>";
                        }
                        echo "
                            <td>" . $id++ . "</td>
                            <td>" . $value["cardID"] . "</td>
                            <td>" . $value["traditional"] . "</td>
                            <td>" . $value["simplified"] . "</td>
                            <td>" . $value["priority"] . "</td>
                            <td>" . $value["pinyin"] . "</td>
                            <td>" . $value["class"] . "</td>
                            <td class = 'long'>" . $value["english"] . "</td>
                            <td class = 'long'>" . $value["indo"] . "</td>
                        </tr>";
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
    #deckList {
        color: #ffa72a;
    }
    #deck + ul{
        display: block;
    }
</style>
</html>