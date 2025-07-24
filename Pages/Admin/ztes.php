<?php
    include "../../SQL_Queries/connection.php";
    $getDecks = mysqli_query($con, "SELECT deck_id FROM junction_deck_user WHERE user_id = '$userID'");
    while($decks = mysqli_fetch_assoc($getDecks)) {
        $deckID = $decks["deck_id"];
        $getCards = mysqli_query($con, "SELECT card_id FROM junction_deck_card WHERE deck_id = '$deckID'");
        while($card = mysqli_fetch_assoc($getCards)) {
            $cardID = $card["card_id"];
            $cardInfo = mysqli_query($con, "SELECT * FROM card_progress WHERE card_id = $cardID AND user_id = '$userID'");
            $cardInfo = mysqli_fetch_assoc($cardInfo);
        }
    }
?>