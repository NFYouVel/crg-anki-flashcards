<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentence Overview</title>
    <link rel="icon" href="../../Logo/circle.png">
    <style>
        h1 {
            color: white;
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
        #header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #forms {
            display: flex;
            gap: 16px;
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
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>
    <div id="container">
        <div id="header">
            <h1>Example Sentence Overview</h1>
            <div id="forms">
                <form action="addSentence.php" method="POST" enctype="multipart/form-data">
                    <input id = "import" name = "sentence" style = "display: none" class = "button" type="file" onchange="this.form.submit()">
                    <label class = "button" for="import">Import</label>
                </form>
                <form action="addLinked.php" method="POST" enctype="multipart/form-data">
                    <input id = "link" name = "link" style = "display: none" class = "button" type="file" onchange="this.form.submit()">
                    <label class = "button" for="link">Link Sentence</label>
                </form>
            </div>
        </div>

        <table>
            <tr>
                <th id = 'short'>Code</th>
                <th>Traditional</th>
                <th>Simplified</th>
                <th>Pinyin</th>
                <th>English</th>
                <th>Indo</th>
                <th id = "short">Linked Cards</th>
            </tr>

            <?php
                include "../../SQL_Queries/connection.php";
                $sentences = mysqli_query($con, "SELECT * FROM example_sentence");
                while ($sentence = mysqli_fetch_array($sentences)) {
                    $sentenceCode = $sentence["sentence_code"];
                    $countLinked = mysqli_query($con, "SELECT COUNT(*) as total FROM junction_card_sentence WHERE sentence_code = '$sentenceCode'");
                    $countLinked = mysqli_fetch_assoc($countLinked);
                    $countLinked = $countLinked["total"];
                    echo "<tr>";
                    echo "<td>" . $sentence["sentence_code"] . "</td>";
                    echo "<td>" . $sentence["chinese_tc"] . "</td>";
                    echo "<td>" . $sentence["chinese_sc"] . "</td>";
                    echo "<td>" . $sentence["pinyin"] . "</td>";
                    echo "<td>" . $sentence["meaning_eng"] . "</td>";
                    echo "<td>" . $sentence["meaning_ina"] . "</td>";
                    echo "<td>$countLinked</td>";
                    echo "</tr>";
                }
            ?>
        </table>
    </div>
</body>
<style>
    #sentence {
        color: #ffa72a;
    }
    #overview_sentence {
        color: #ffa72a;
    }
</style>
</html>