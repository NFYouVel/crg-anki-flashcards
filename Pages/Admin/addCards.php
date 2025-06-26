<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cards</title>
    <style>
        html {
            scroll-behavior: smooth;
        }
        h1 {
            color: white;
            font-size: 50px;
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
        caption {
            color: white;
            border: 2px solid black;
        }
        #invalid {
            background-color: red;
        }
        #bookmarks {
            position: fixed;
            left: 0;
            bottom: 250px;
            z-index: 100;
            background-color: white;
        }
        #bookmarks a {
            color: black;
            font-size: 24px;
        }
    </style>
    <script>
        //function ajax untuk upload cards
        function uploadCards() {
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
            xmlhttp.open("GET", "AJAX/batch_cards.php", true);
            xmlhttp.send();
        }
    </script>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>
    <div id="bookmarks">
        <a href="#preview">Preview</a>
        <a href="#valid">Valid</a>
        <a href="#invalid">Invalid</a>
    </div>
    <div id="container">
        <div id="header">
            <h1>Import User (Preview)</h1>
            <div>
                <a href="dictionary.php" class="button">Cancel</a>
                <button class="button" name = "import" onclick = "uploadCards()">Import</button>
            </div>
        </div>
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
                    include "../../SQL_Queries/connection.php";
                    require '../../Composer_Excel/vendor/autoload.php';
                    use PhpOffice\PhpSpreadsheet\IOFactory;
                    //jika page ke refresh, tidak perlu nge read ulang file, tapi mengambil dari session yang dibuat sebelum ke refresh
                    if(isset($_SESSION["allCards"])) {
                        foreach($_SESSION["allCards"] as $key => $value) {
                            echo "<tr>";
                                echo "<td>" . $value["cardID"] . "</td>";
                                echo "<td>" . $value["traditional"] . "</td>";
                                echo "<td>" . $value["simplified"] . "</td>";
                                echo "<td>" . $value["priority"] . "</td>";
                                echo "<td>" . $value["pinyin"] . "</td>";
                                echo "<td>" . $value["class"] . "</td>";
                                echo "<td id = 'long'>" . $value["english"] . "</td>";
                                echo "<td id = 'long'>" . $value["indo"] . "</td>";
                                echo "<td>0</td>";
                            echo "</tr>";
                        }
                    }
                    //read excel
                    else if (isset($_FILES['excel_file'])) {
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

                                    echo "<tr>";
                                        echo "<td>$cardID</td>";
                                        echo "<td>$traditional</td>";
                                        echo "<td>$simplified</td>";
                                        echo "<td>$priority</td>";
                                        echo "<td>$pinyin</td>";
                                        echo "<td>$class</td>";
                                        echo "<td id = 'long'>$english</td>";
                                        echo "<td id = 'long'>$indo</td>";
                                        echo "<td>0</td>";
                                    echo "</tr>";

                                    $reason = "";
                                    //check for invalid email format
                                    if($cardID == "") {
                                        $cardID = "#invalidID_" . $invalidID++;
                                        $reason .= "<p id = 'invalid'>Card ID is empty</p>";
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
                    } 
                ?>
            </table>

            <table id = "valid">
                <caption style = "background-color: green;">Valid Cards</caption>
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
                    //menampilkan hasil validasi card
                    foreach($_SESSION["validCards"] as $key => $value) {
                        echo "<tr>";
                            echo "<td>" . $value["cardID"] . "</td>";
                            echo "<td>" . $value["traditional"] . "</td>";
                            echo "<td>" . $value["simplified"] . "</td>";
                            echo "<td>" . $value["priority"] . "</td>";
                            echo "<td>" . $value["pinyin"] . "</td>";
                            echo "<td>" . $value["class"] . "</td>";
                            echo "<td id = 'long'>" . $value["english"] . "</td>";
                            echo "<td id = 'long'>" . $value["indo"] . "</td>";
                            echo "<td>0</td>";
                        echo "</tr>";
                    }
                ?>
            </table>

            <table id = "invalid">
                <caption style = "background-color: red;">Invalid Cards</caption>
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
                    <th>Reason</th>
                </tr>
                <?php
                    //menampilkan kartu2 yang tidak valid
                    foreach($_SESSION["invalidCards"] as $key => $value) {
                        echo "<tr>";
                            echo "<td>" . $value["cardID"] . "</td>";
                            echo "<td>" . $value["traditional"] . "</td>";
                            echo "<td>" . $value["simplified"] . "</td>";
                            echo "<td>" . $value["priority"] . "</td>";
                            echo "<td>" . $value["pinyin"] . "</td>";
                            echo "<td>" . $value["class"] . "</td>";
                            echo "<td id = 'long'>" . $value["english"] . "</td>";
                            echo "<td id = 'long'>" . $value["indo"] . "</td>";
                            echo "<td>0</td>";
                            echo "<td>" . $value["reason"] . "</td>";
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