<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Sentence</title>
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
        #tables {
            display: flex;
            flex-direction: column;
            align-items: center;
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
            xmlhttp.open("GET", "AJAX/linkedPreviewMode.php?mode=" + str, true);
            xmlhttp.send();
        }

        function uploadLinks() {
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
            xmlhttp.open("GET", "AJAX/batch_linkSentence.php", true);
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
        if (isset($_FILES['link'])) {
            $fileTmpPath = $_FILES['link']['tmp_name'];
            $fileName = $_FILES['link']['name'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    
            $allowedExtensions = ['xls', 'xlsx'];
    
            if (in_array($fileExtension, $allowedExtensions)) {
                try {
                    $spreadsheet = IOFactory::load($fileTmpPath);
                    $sheet = $spreadsheet->getActiveSheet();
                    $allLinks = [];
                    $validLinks = [];
                    $invalidLinks = [];
                    foreach ($sheet->getRowIterator() as $row) {
                        $index = $row->getRowIndex();
                        //skip index 1 karena itu header
                        if($index == 1) {
                            continue;
                        }
                        //mengambil data dari tiap komumn dan index tertentu (index akan terus bertambah)
                        $cardID = $sheet->getCell("A$index")->getValue();
                        $sentenceCode = $sheet->getCell("B$index")->getValue();
                        $priority = $sheet->getCell("C$index")->getCalculatedValue();
    
                        $cardInfo = mysqli_query($con, "SELECT chinese_sc, pinyin, word_class, meaning_eng FROM cards WHERE card_id = $cardID");
                        $cardInfo = mysqli_fetch_array($cardInfo);
    
                        $sentenceInfo = mysqli_query($con, "SELECT chinese_sc FROM example_sentence WHERE sentence_code = '$sentenceCode'");
                        $sentenceInfo = mysqli_fetch_array($sentenceInfo);
    
                        $cardSc     = $cardInfo ? $cardInfo['chinese_sc']    : 'Not Found';
                        $pinyin     = $cardInfo ? $cardInfo['pinyin']        : 'Not Found';
                        $wordClass  = $cardInfo ? $cardInfo['word_class']    : 'Not Found';
                        $meaningEng = $cardInfo ? $cardInfo['meaning_eng']   : 'Not Found';
                        $sentSc     = $sentenceInfo ? $sentenceInfo['chinese_sc'] : 'Not Found';
    
                        // echo "
                        // <tr>
                        //     <td>$cardID</td>
                        //     <td>$cardSc</td>
                        //     <td>$pinyin</td>
                        //     <td>$wordClass</td>
                        //     <td id='long'>$meaningEng</td>
                        //     <td>$sentenceCode</td>
                        //     <td>$priority</td>
                        //     <td id='long'>$sentSc</td>
                        // </tr>";
    
                        $reason = "";
                        //check if card id exists
                        if(mysqli_num_rows(mysqli_query($con, "SELECT card_id FROM cards WHERE card_id = '$cardID'")) == 0) {
                            $reason .= "<p id = 'invalid'>Card ID Not Found</p>";
                        }
    
                        //check if sentence code exists
                        if(mysqli_num_rows(mysqli_query($con, "SELECT sentence_code FROM example_sentence WHERE sentence_code = '$sentenceCode'")) == 0) {
                            $reason .= "<p id = 'invalid'>Sentence Code Not Found</p>";
                        }
    
                        //check if priority is a number
                        if(!is_numeric($priority)) {
                            $reason .= "<p id = 'invalid'>Invalid Priority</p>";
                        }
    
                        //membangun session untuk semua kartu
                        $allLinks[] = [
                            "cardID" => $cardID, 
                            "sentenceCode" => $sentenceCode, 
                            "priority" => $priority
                        ];
                        //logika valid / tidak valid
                        if($reason == "") {
                            //membantun session untuk kartu yang valid
                            $validLinks[] = [
                                "cardID" => $cardID, 
                                "sentenceCode" => $sentenceCode, 
                                "priority" => $priority
                            ];
                        }
                        else {
                            //membantun session untuk kartu yang tidak valid
                            $invalidLinks[] = [
                                "cardID" => $cardID, 
                                "sentenceCode" => $sentenceCode, 
                                "priority" => $priority,
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
            $_SESSION["allLinks"] = $allLinks;
            $_SESSION["validLinks"] = $validLinks;
            $_SESSION["invalidLinks"] = $invalidLinks;
    
            date_default_timezone_set("Asia/Jakarta");
            $date = date("ymd_Hi");
            $fileExtension = pathinfo($_FILES['link']['name'], PATHINFO_EXTENSION);
            $fileName = "CRG_backup_junction_$date" . "_" . $_COOKIE["user_id"] . "." . $fileExtension;
            $_SESSION["filePath"] = $fileName;
            move_uploaded_file($_FILES['link']['tmp_name'], "../../../Backup/card_sentence/temp/" . $fileName);
        } 
    ?>
    <div id="loadingScreen">
        <img src="Components/loading.gif" alt="">
        <h1>Importing</h1>
    </div>
    <div id="container">
        <div id="header">
            <h1>Link Sentence (Preview)</h1>
            <select id = "filter" onchange = 'previewModes(this.value)'>
                <option value="preview">Preview (Default)</option>
                <option value="valid">Valid Links</option>
                <option value="invalid">Invalid Links</option>
            </select>
            <div id = "form">
                <a href="overview_sentence.php" id = "cancel" class="button">Cancel</a>
                <button class="button" id = "importButton" onclick = "uploadLinks()">Confirm</button>
            </div>
        </div>
        <h1 style = "display: flex; justify-content: space-evenly;">
            <span>Total Sentences: <?php echo count($_SESSION["allLinks"]); ?></span>
            <span>Valid Sentences: <?php echo count($_SESSION["validLinks"]); ?></span>
            <span>Invalid Sentences: <?php echo count($_SESSION["invalidLinks"]); ?></span>
        </h1>
        <div id="tables">
            <table>
                <caption style = "background-color: white; color: black;">Preview</caption>
                <tr>
                    <th>Card ID</th>
                    <th>Card Simplified</th>
                    <th>Pinyin</th>
                    <th>Word Class</th>
                    <th id = 'long'>English</th>
                    <th>Sentence Code</th>
                    <th>Priority</th>
                    <th id = 'long'>Sentence Simplified</th>
                </tr>
                <?php
                    foreach($_SESSION["allLinks"] as $key => $links) {
                        $cardID = $links["cardID"];
                        $sentenceCode = $links["sentenceCode"];
                        $priority = $links["priority"];

                        $cardInfo = mysqli_query($con, "SELECT chinese_sc, pinyin, word_class, meaning_eng FROM cards WHERE card_id = $cardID");
                        $cardInfo = mysqli_fetch_array($cardInfo);

                        $sentenceInfo = mysqli_query($con, "SELECT chinese_sc FROM example_sentence WHERE sentence_code = '$sentenceCode'");
                        $sentenceInfo = mysqli_fetch_array($sentenceInfo);

                        $cardSc     = $cardInfo ? $cardInfo['chinese_sc']    : 'Not Found';
                        $pinyin     = $cardInfo ? $cardInfo['pinyin']        : 'Not Found';
                        $wordClass  = $cardInfo ? $cardInfo['word_class']    : 'Not Found';
                        $meaningEng = $cardInfo ? $cardInfo['meaning_eng']   : 'Not Found';
                        $sentSc     = $sentenceInfo ? $sentenceInfo['chinese_sc'] : 'Not Found';

                        if(isset($_SESSION["validLinks"][$key])) {
                            echo "<tr style = 'background-color: green;'>";
                        }
                        else if(isset($_SESSION["invalidLinks"][$key])) {
                            echo "<tr style = 'background-color: red;'>";
                        }
                        echo "
                            <td>$cardID</td>
                            <td>$cardSc</td>
                            <td>$pinyin</td>
                            <td>$wordClass</td>
                            <td id='long'>$meaningEng</td>
                            <td>$sentenceCode</td>
                            <td>$priority</td>
                            <td id='long'>$sentSc</td>
                        </tr>";
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
    #sentence + ul {
        display: block;
    }
    #overview_sentence {
        color: #ffa72a;
    }
</style>
</html>