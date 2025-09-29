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
    <title>Sentence Overview</title>
    <link rel="icon" href="../../Assets/Icons/1080.png">
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
        th {
            position: sticky;
            z-index: 200;
            top: 0;
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
                $page = $_GET["page"] ?? 0;
                $offset = $page * 100;
                $sentences = mysqli_query($con, "SELECT * FROM example_sentence ORDER BY sentence_code ASC LIMIT 100 OFFSET $offset");
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
        <div id="pageNav">
            <div id="actions">
                <a href="overview_sentence.php?page=0"><span><<</span></a>
                <a href="overview_sentence.php?page=<?php echo $page - 1 ?>"><span><</span></a>
                <div><input type="number" id="pageInput" value="<?php echo $page + 1; ?>"> <span style="color: white; font-size: 18px;"> / <?php 
                    $maxPage = mysqli_query($con, "SELECT COUNT(*) as total FROM example_sentence");
                    $maxPage = (int)(mysqli_fetch_array($maxPage)["total"] / 100) + 1;
                    echo $maxPage;
                ?></span></div>
                <a href="overview_sentence.php?page=<?php echo $page + 1 ?>"><span>></span></a>
                <a href="overview_sentence.php?page=<?php echo $maxPage - 1; ?>"><span>>></span></a>
            </div>
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