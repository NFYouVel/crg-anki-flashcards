<?php
    include "../../../SQL_Queries/connection.php";
    $count = 1;
    $cards = [];
    function getDeck($deckID) {
        global $cards;
        global $count;
        global $con;
        $getCards = mysqli_query($con, "
            SELECT c.* 
            FROM junction_deck_card dc 
            JOIN cards c ON dc.card_id = c.card_id 
            WHERE dc.deck_id = '$deckID'
        ");

        while ($card = mysqli_fetch_array($getCards)) {
            $cardID = $card["card_id"];
            $traditional = $card["chinese_tc"];
            $simplified = $card["chinese_sc"];
            $prio = $card["priority"];
            $pinyin = $card["pinyin"];
            $class = $card["word_class"];
            $eng = $card["meaning_eng"];
            $indo = $card["meaning_ina"];

            if(!isset($cards[$cardID])) {
                $cards[$cardID] = [
                    "card_id" => $cardID, 
                    "chinese_tc" => $traditional, 
                    "chinese_sc" => $simplified, 
                    "priority" => $prio, 
                    "pinyin" => $pinyin, 
                    "word_class" => $class, 
                    "meaning_eng" => $eng, 
                    "meaning_ina" => $indo, 
                ];
            }
        }

        $deck = mysqli_query($con, "SELECT is_leaf FROM decks WHERE deck_id = '$deckID'");
        $deck = mysqli_fetch_assoc($deck);
        if(!$deck || $deck["is_leaf"] == 1) {
            return;
        }
        else {
            $getChild = mysqli_query($con, "SELECT deck_id FROM decks WHERE parent_deck_id = '$deckID'");
            while($child = mysqli_fetch_assoc($getChild)) {
                $deck_id = $child["deck_id"];
                getDeck($deck_id);
            }
        }
    }
?>
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
    </tr>
    <?php
    $deckID = $_GET["deckID"];
    $count = 1;
    if($deckID == "masterDeck") {
        $getCards = mysqli_query($con, "SELECT c.* FROM junction_deck_card dc JOIN cards c ON dc.card_id = c.card_id");
        while ($card = mysqli_fetch_array($getCards)) {
            $cardID = $card["card_id"];
            $traditional = $card["chinese_tc"];
            $simplified = $card["chinese_sc"];
            $prio = $card["priority"];
            $pinyin = $card["pinyin"];
            $class = $card["word_class"];
            $eng = $card["meaning_eng"];
            $indo = $card["meaning_ina"];
            echo "
            <tr>
                <td>$count</td>
                <td class = 'short'>$cardID</td>
                <td>$traditional</td>
                <td>$simplified</td>
                <td>$prio</td>
                <td>$pinyin</td>
                <td class = 'short'>$class</td>
                <td>$eng</td>
                <td>$indo</td>
            </tr>";
            $count++;
        }
    }
    else {
        getDeck($deckID);
        $count = 1;
        foreach($cards as $card) {
            $cardID = $card["card_id"];
            $traditional = $card["chinese_tc"];
            $simplified = $card["chinese_sc"];
            $prio = $card["priority"];
            $pinyin = $card["pinyin"];
            $class = $card["word_class"];
            $eng = $card["meaning_eng"];
            $indo = $card["meaning_ina"];

            echo "
            <tr>
                <td>$count</td>
                <td class = 'short'>$cardID</td>
                <td>$traditional</td>
                <td>$simplified</td>
                <td>$prio</td>
                <td>$pinyin</td>
                <td class = 'short'>$class</td>
                <td>$eng</td>
                <td>$indo</td>
            </tr>";
            $count++;
        }
    }
    ?>
</table>