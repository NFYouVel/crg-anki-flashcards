<?php
    include "../../../SQL_Queries/connection.php";
    $deckID = $_POST["deck_id"];
    $deckName = mysqli_query($con, "SELECT name FROM decks WHERE deck_id = '$deckID'");
    $deckName = mysqli_fetch_assoc($deckName);
    echo $deckName["name"];
?>