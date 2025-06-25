<?php
    session_start();
    session_unset();
    session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictionary</title>
    <style>
        h1 {
            color: white;
            font-size: 45px;
            margin: 16px 0;
        }
        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
    ?>
    <div id="container">
        <div id="header">
            <h1>Dictionary (Card) Overview</h1>
            <input type="text" placeholder = "&#128269;Search" onkeyup = "searchUser(this.value)">
            <form action="addCards.php" method="POST" enctype="multipart/form-data">
                <input id = "file" name = "excel_file" style = "display: none" class = "button" type="file" onchange="this.form.submit()">
                <label class = "button" for="file">Import</label>
            </form>
        </div>
        <table>
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
                $cards = mysqli_query($con, "SELECT * FROM cards");
                while($card = mysqli_fetch_array($cards)) {
                    echo "<tr>";
                    echo "<td>" . $card["card_id"] . "</td>";
                    echo "<td>" . $card["chinese_tc"] . "</td>";
                    echo "<td>" . $card["chinese_sc"] . "</td>";
                    echo "<td>" . $card["priority"] . "</td>";
                    echo "<td>" . $card["pinyin"] . "</td>";
                    echo "<td>" . $card["word_class"] . "</td>";
                    echo "<td id = 'long'>" . $card["meaning_eng"] . "</td>";
                    echo "<td id = 'long'>" . $card["meaning_ina"] . "</td>";
                    echo "<td>0</td>";
                    echo "</tr>";
                }
            ?>
        </table>
    </div>
</body>
<style>
    #dictionary {
        color: #ffa72a;
    }
</style>
</html>