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
    <link rel="icon" href="../../Logo/circle.png">
    <title>Add Cards</title>
    <style>
        html {
            scroll-behavior: smooth;
        }
        h1 {
            color: white;
        }
        #header {
            display: flex;
            justify-content: space-between;
            align-items: center
        }
        #header > div {
            display: flex;
            gap: 16px;
        }
        .button {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            width: 150px;
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
        th {
            position: sticky;
            z-index: 200;
            top: 0;
        }
        caption {
            color: white;
            border: 2px solid black;
        }
        #invalid {
            background-color: red;
        }
        #loadingScreen {
            background-color: #262626;
            position: fixed;
            z-index: 999;
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
        //function ajax untuk upload cards
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
                }
            }
            xmlhttp.open("GET", "AJAX/batch_cards.php", true);
            xmlhttp.send();
        }

        function previewModes(str) {
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
                    document.getElementById("loadingScreen").style.display = "none";
                }
            }
            xmlhttp.open("GET", "AJAX/cardPreviewMode.php?mode=" + str, true);
            xmlhttp.send();
        }
    </script>
</head>
<body>
    <?php
        include "convertPinyin.php";
        include "Components/sidebar.php";
        if(isset($_POST["import"])) {
            $date = date("ymd_Hi");
            $fileExtension = pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION);
            $fileName = "CRG_backup_card_$date" . "_" . $_COOKIE["user_id"] . "." . $fileExtension;
            move_uploaded_file($_FILES['excel_file']['tmp_name'], "../../../Backup/card" . $fileName);
            echo "
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                uploadCards();
            });
            </script>";
        }

        include "../../SQL_Queries/connection.php";
        require '../../Composer_Excel/vendor/autoload.php';
        use PhpOffice\PhpSpreadsheet\IOFactory;
        //jika page ke refresh, tidak perlu nge read ulang file, tapi mengambil dari session yang dibuat sebelum ke refresh
        //read excel
        if (isset($_FILES['excel_file'])) {
            $id = 1;
            $invaliID = 1;
            //variabel untuk membangun variabel session (allCards, validCards, invalidCards)
            $allCards = [];
            $validCards = [];
            $invalidCards = [];
            $fileTmpPath = $_FILES['excel_file']['tmp_name'];
            $fileName = $_FILES['excel_file']['name'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    
            $allowedExtensions = ['xls', 'xlsx'];
    
            if (in_array($fileExtension, $allowedExtensions)) {
                try {
                    $spreadsheet = IOFactory::load($fileTmpPath);
                    $sheet = $spreadsheet->getActiveSheet();
                    foreach ($sheet->getRowIterator() as $row) {
                        $index = $row->getRowIndex();
                        //skip index 1 karena itu header
                        if($index == 1) {
                            continue;
                        }
                        //mengambil data dari tiap komumn dan index tertentu (index akan terus bertambah)
                        $cardID = $sheet->getCell("A$index")->getValue();
                        $traditional = $sheet->getCell("B$index")->getValue();
                        $simplified = $sheet->getCell("C$index")->getValue();
                        $priority = $sheet->getCell("D$index")->getValue();
                        $pinyin = $sheet->getCell("E$index")->getValue();
                        $class = $sheet->getCell("F$index")->getValue();
                        $english = $sheet->getCell("G$index")->getValue();
                        $indo = $sheet->getCell("H$index")->getValue();
    
                        // echo "<tr>";
                        //     echo "<td>$cardID</td>";
                        //     echo "<td>$traditional</td>";
                        //     echo "<td>$simplified</td>";
                        //     echo "<td>$priority</td>";
                        //     echo "<td>" . convert($pinyin) . "</td>";
                        //     echo "<td>$class</td>";
                        //     echo "<td id = 'long'>$english</td>";
                        //     echo "<td id = 'long'>$indo</td>";
                        //     echo "<td>0</td>";
                        // echo "</tr>";
    
                        $reason = "";
                        //check for invalid email format
                        if($cardID == "") {
                            $cardID = "#invalidID_" . $index;
                            $reason .= "<p id = 'invalid'>Card ID is empty ato row $index</p>";
                        }
    
                        //check for duplicates in uploaded excel
                        if(isset($validCards[$cardID])) {
                            $reason .= "<p id = 'invalid'>Card ID already exists in the excel file</p>";
                        }
    
                        //check for error in user role
                        if(!is_numeric($priority)) {
                            $reason .= "<p id = 'invalid'>Invalid Priority</p>";
                        }
    
                        //membangun session untuk semua kartu
                        $allCards[$cardID] = [
                            "cardID" => $cardID, 
                            "traditional" => $traditional, 
                            "simplified" => $simplified, 
                            "priority" => $priority,
                            "pinyin" => $pinyin,
                            "class" => $class,
                            "english" => $english,
                            "indo" => $indo,
                        ];
                        //logika valid / tidak valid
                        if($reason == "") {
                            //membantun session untuk kartu yang valid
                            $validCards[$cardID] = [
                                "cardID" => $cardID, 
                                "traditional" => $traditional, 
                                "simplified" => $simplified, 
                                "priority" => $priority,
                                "pinyin" => $pinyin,
                                "class" => $class,
                                "english" => $english,
                                "indo" => $indo,
                            ];
                        }
                        else {
                            //membantun session untuk kartu yang tidak valid
                            $invalidCards[$cardID] = [
                                "cardID" => $cardID, 
                                "traditional" => $traditional, 
                                "simplified" => $simplified, 
                                "priority" => $priority,
                                "pinyin" => $pinyin,
                                "class" => $class,
                                "english" => $english,
                                "indo" => $indo,
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
            date_default_timezone_set("Asia/Jakarta");
            $date = date("ymd_Hi");
            $fileExtension = pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION);
            $fileName = "CRG_backup_card_$date" . "_" . $_COOKIE["user_id"] . "." . $fileExtension;
            $_SESSION["filePath"] = $fileName;
            move_uploaded_file($_FILES['excel_file']['tmp_name'], "../../../Backup/card/temp/" . $fileName);
        } 
    ?>
    <div id="loadingScreen">
        <img src="Components/loading.gif" alt="">
        <h1>Loading</h1>
    </div>
    <div id="container">
        <div id="header">
            <h1>Import Cards (Preview)</h1>
            <select id = "filter" onchange = 'previewModes(this.value)'>
                <option value="preview">Preview (Default)</option>
                <option value="valid">Valid Cards</option>
                <option value="invalid">Invalid Cards</option>
            </select>
            <div>
                <a href="dictionary.php" id = "cancel" class="button">Cancel</a>
                <button type = "submit" id = "importButton" class="button" onclick = "uploadCards();">Import</button>
            </div>
        </div>
        <h1 style = "display: flex; justify-content: space-evenly;">
            <span>Total Cards: <?php echo count($_SESSION["allCards"]); ?></span>
            <span>Valid Cards: <?php echo count($_SESSION["validCards"]); ?></span>
            <span>Invalid Cards: <?php echo count($_SESSION["invalidCards"]); ?></span>
        </h1>
        <div id="tables">
            <table id = "preview">
                <caption style = "background-color: white; color: black;">Uploaded Excel File</caption>
                <tr>
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
                    foreach($_SESSION["allCards"] as $key => $value) {
                        if(isset($_SESSION["validCards"][$key])) {
                            echo "<tr style = 'background-color: green;'>";
                        }
                        else if(isset($_SESSION["invalidCards"][$key])) {
                            echo "<tr style = 'background-color: red;'>";
                        }
                            echo "<td>" . $value["cardID"] . "</td>";
                            echo "<td>" . $value["traditional"] . "</td>";
                            echo "<td>" . $value["simplified"] . "</td>";
                            echo "<td>" . $value["priority"] . "</td>";
                            echo "<td>" . convert($value["pinyin"]) . "</td>";
                            echo "<td>" . $value["class"] . "</td>";
                            echo "<td id = 'long'>" . $value["english"] . "</td>";
                            echo "<td id = 'long'>" . $value["indo"] . "</td>";
                            echo "<td>0</td>";
                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
    </div>
</body>
<style>
    
    #dictionary {
        color: #ffa72a;
    }
</style>
</html>