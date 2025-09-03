<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sentence</title>
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
        }
        th {
            color: white;
            background-color: #003b58;
        }
        th, td {
            border: 2px solid black;
            padding: 5px 10px;
            white-space: normal;
            word-break: break-word;
        }
        #short {
            word-break: normal;
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
    </style>
    <script>
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
            xmlhttp.open("GET", "AJAX/sentencePreviewMode.php?mode=" + str, true);
            xmlhttp.send();
        }

        function uploadSentences() {
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
            xmlhttp.open("GET", "AJAX/batch_sentences.php", true);
            xmlhttp.send();
        }
        </script>
</head>
<body>
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
        require '../../Composer_Excel/vendor/autoload.php';
        use PhpOffice\PhpSpreadsheet\IOFactory;
        //jika page ke refresh, tidak perlu nge read ulang file, tapi mengambil dari session yang dibuat sebelum ke refresh
        if (isset($_FILES['sentence'])) {
            $invalidID = 1;
            $fileTmpPath = $_FILES['sentence']['tmp_name'];
            $fileName = $_FILES['sentence']['name'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
            $allowedExtensions = ['xls', 'xlsx'];
        
            if (in_array($fileExtension, $allowedExtensions)) {
                try {
                    $spreadsheet = IOFactory::load($fileTmpPath);
                    $sheet = $spreadsheet->getActiveSheet();
                    $allSentences = [];
                    $validSentences = [];
                    $invalidSentences = [];
                    foreach ($sheet->getRowIterator() as $row) {
                        $index = $row->getRowIndex();
                        //skip index 1 karena itu header
                        if($index == 1) {
                            continue;
                        }
                        //mengambil data dari tiap komumn dan index tertentu (index akan terus bertambah)
                        $sentenceCode = $sheet->getCell("A$index")->getValue() ?? "";
                        $traditional = $sheet->getCell("B$index")->getValue() ?? "";
                        $simplified = $sheet->getCell("C$index")->getValue() ?? "";
                        $pinyin = $sheet->getCell("D$index")->getValue() ?? "";
                        $english = $sheet->getCell("E$index")->getValue() ?? "";
                        $indo = $sheet->getCell("F$index")->getValue() ?? "";
        
                        $reason = "";
                        //check for empty sentence code
                        if($sentenceCode == "") {
                            $sentenceCode = "#invalidID_" . $invalidID++;
                            $reason .= "<p id = 'invalid'>Sentence code is empty</p>";
                        }
        
                        //check for duplicates in uploaded excel
                        if(isset($validSentences[$sentenceCode])) {
                            $reason .= "<p id = 'invalid'>Duplicate sentence code</p>";
                        }
        
                        //membangun session untuk semua kartu
                        $allSentences[$sentenceCode] = [
                            "sentenceCode" => $sentenceCode, 
                            "traditional" => $traditional, 
                            "simplified" => $simplified, 
                            "pinyin" => $pinyin,
                            "english" => $english,
                            "indo" => $indo,
                        ];
                        //logika valid / tidak valid
                        if($reason == "") {
                            //membantun session untuk kartu yang valid
                            $validSentences[$sentenceCode] = [
                                "sentenceCode" => $sentenceCode, 
                                "traditional" => $traditional, 
                                "simplified" => $simplified, 
                                "pinyin" => $pinyin,
                                "english" => $english,
                                "indo" => $indo,
                            ];
                        }
                        else {
                            //membantun session untuk kartu yang tidak valid
                            $invalidSentences[$sentenceCode] = [
                                "sentenceCode" => $sentenceCode, 
                                "traditional" => $traditional, 
                                "simplified" => $simplified, 
                                "pinyin" => $pinyin,
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
            $_SESSION["allSentences"] = $allSentences;
            $_SESSION["validSentences"] = $validSentences;
            $_SESSION["invalidSentences"] = $invalidSentences;
        
            date_default_timezone_set("Asia/Jakarta");
            $date = date("ymd_Hi");
            $fileExtension = pathinfo($_FILES['sentence']['name'], PATHINFO_EXTENSION);
            $fileName = "CRG_backup_sentence_$date" . "_" . $_COOKIE["user_id"] . "." . $fileExtension;
            $_SESSION["filePath"] = $fileName;

            if (move_uploaded_file($_FILES['sentence']['tmp_name'], "../../../Backup/sentence/temp/" . $fileName)) {
                echo "<script>alert('Upload successful!');</script>";
            } else {
                echo "<script>alert('Upload failed: " . $_FILES['sentence']['error'] . "');</script>";
            }
        } 
    ?>
    <div id="loadingScreen">
        <img src="Components/loading.gif" alt="">
        <h1>Importing</h1>
    </div>
    <div id="container">
        <div id="header">
            <h1>Add Sentence (Preview)</h1>
            <select id = "filter" onchange = 'previewModes(this.value)'>
                <option value="preview">Preview (Default)</option>
                <option value="valid">Valid Sentences</option>
                <option value="invalid">Invalid Sentences</option>
            </select>
            <div id = "form">
                <a href="overview_sentence.php" id = "cancel" class="button">Cancel</a>
                <button class="button" id = "importButton" onclick = "uploadSentences()">Confirm</button>
            </div>
        </div>
        <h1 style = "display: flex; justify-content: space-evenly;">
            <span>Total Sentences: <?php echo count($_SESSION["allSentences"]); ?></span>
            <span>Valid Sentences: <?php echo count($_SESSION["validSentences"]); ?></span>
            <span>Invalid Sentences: <?php echo count($_SESSION["invalidSentences"]); ?></span>
        </h1>
        <div id="tables">
            <table>
                <caption style = "background-color: white; color: black;">Preview</caption>
                <tr>
                    <th id = 'short'>Code</th>
                    <th>Traditional</th>
                    <th>Simplified</th>
                    <th>Pinyin</th>
                    <th>English</th>
                    <th>Indo</th>
                </tr>
                <?php
                    foreach($_SESSION["allSentences"] as $key => $sentence) {
                        $sentenceCode = $sentence["sentenceCode"];
                        $traditional = $sentence["traditional"];
                        $simplified = $sentence["simplified"];
                        $pinyin = $sentence["pinyin"];
                        $english = $sentence["english"];
                        $indo = $sentence["indo"];
                        if(isset($_SESSION["validSentences"][$key])) {
                            echo "<tr style = 'background-color: green;'>";
                        }
                        else if(isset($_SESSION["invalidSentences"][$key])) {
                            echo "<tr style = 'background-color: red;'>";
                        }
                            echo "<td id = 'short'>$sentenceCode</td>";
                            echo "<td>$traditional</td>";
                            echo "<td>$simplified</td>";
                            echo "<td>$pinyin</td>";
                            echo "<td>$english</td>";
                            echo "<td>$indo</td>";
                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
    </div>
</body>
<style>
    #sentence {
        color: #ffa72a;
    }
    #sentence + ul{
        display: block;
    }
    #overview_sentence {
        color: #ffa72a;
    }
</style>
</html>