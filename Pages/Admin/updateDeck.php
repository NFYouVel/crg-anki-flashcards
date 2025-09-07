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
    <link rel="icon" href="../../Logo/circle.png">
    <style>
        h1 {
            color: white;
            margin: 0;
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
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #menu {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #actions {
            display: flex;
            gap: 16px;  
        }
        table {
            margin-top: 24px;
            width: 100%;
            font-size: 20px;
            border-collapse: collapse;
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
        td {
            padding: 5px;
        }
        th {
            position: sticky;
            z-index: 200;
            top: 0;
        }
        .long {
            word-break: break-word;
            white-space: normal;
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
        $deckID = $_GET["deckID"];
    ?>
    <div id="container">
        <div id="header">
            <h1>Update Deck</h1>
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
            <div id="actions">
                <form action="link_deck_card.php?deckID=<?php echo $_GET["deckID"]; ?>" method="POST" enctype="multipart/form-data">
                    <input id = "file" name = "excel_file" style = "display: none" type="file" onchange="this.form.submit()">
                    <label class = "button" style = "padding-inline: 16px;" for="file">Import Deck</label>
                </form>
                <button class="button">Save Deck</button>
            </div>
        </div>
        <div id="table">
            <table>
                <tr>
                    <th>No.</th>
                    <th>ID</th>
                    <th>Trad</th>
                    <th>Simp</th>
                    <th>Prio</th>
                    <th>Pinyin</th>
                    <th>Word Class</th>
                    <th>English</th>
                    <th>Indo</th>
                    <th>Sentence Count</th>
                </tr>
                <?php
                    $count = 1;
                    $deckID = $_GET["deckID"];
                    $aval = false;
                    $getCards = mysqli_query($con, "SELECT cards.* FROM junction_deck_card junction JOIN cards cards ON junction.card_id = cards.card_id WHERE junction.deck_id = '$deckID'");
                    while($card = mysqli_fetch_assoc($getCards)) {
                        $aval = true;
                        $cardID = $card["card_id"];
                        $traditional = $card["chinese_tc"];
                        $simplified = $card["chinese_sc"];
                        $prio = $card["priority"];
                        $pinyin = $card["pinyin"];
                        $class = $card["word_class"];
                        $eng = $card["meaning_eng"];
                        $indo = $card["meaning_ina"];
                        $sentenceCount = mysqli_query($con, "SELECT COUNT(*) AS total FROM junction_card_sentence WHERE card_id = $cardID");
                        $sentenceCount = mysqli_fetch_assoc($sentenceCount);
                        $sentenceCount = $sentenceCount["total"];

                        echo "
                        <tr>
                            <td>$count</td>
                            <td>$cardID</td>
                            <td>$traditional</td>
                            <td>$simplified</td>
                            <td>$prio</td>
                            <td>$pinyin</td>
                            <td>$class</td>
                            <td class = 'long'>$eng</td>
                            <td class = 'long'>$indo</td>
                            <td style = 'text-align: center;'>$sentenceCount</td>
                        </tr>
                        ";
                        $count++;
                    }
                ?>
            </table>
            <?php
                if(!$aval) {
                    echo "<h1 style = 'text-align: center'>This deck is empty</h1>";
                }
            ?>
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