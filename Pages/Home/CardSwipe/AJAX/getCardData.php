<?php
    include "../../../../SQL_Queries/connection.php";
    $cardId = $_POST["cardId"];
    $query = "SELECT * FROM cards WHERE card_id = '$cardId'";
    $result = mysqli_query($con, $query);
    $line = mysqli_fetch_assoc($result);
    echo json_encode($line);
?>