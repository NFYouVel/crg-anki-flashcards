<?php
include "../../../../SQL_Queries/connection.php";
$cardSwipeId = $_POST["cardSwipeId"];
$cardId = $_POST["cardId"];
$status = $_POST["status"];

if($status == "remember") {
    $updateCount = "remember_count = remember_count + 1,";
} else {
    $updateCount = "forgot_count = forgot_count + 1,";
}

$query = "UPDATE card_swipe_progress SET $updateCount status = '$status' WHERE card_swipe_id = '$cardSwipeId' AND card_id = '$cardId'";
$result = mysqli_query($con, $query);

header("Content-Type: application/json");
echo json_encode(["success" => (bool)$result]);
?>