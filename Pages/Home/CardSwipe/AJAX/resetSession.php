<?php
include "../../../../SQL_Queries/connection.php";
$cardSwipeId = $_POST["cardSwipeId"];

$deckId = mysqli_query($con, "SELECT deck_id FROM card_swipe_session WHERE card_swipe_id = '$cardSwipeId'");
$deckId = mysqli_fetch_assoc($deckId)["deck_id"];

$user_id = $_COOKIE["user_id"] ?? null;
if ($deckId == "main" && empty($user_id)) {
    echo json_encode(["success" => false, "error" => "User not authenticated"]);
    exit();
}

if ($deckId == "main") {
    $getCards = mysqli_query($con, "
                SELECT DISTINCT jdc.card_id
                FROM junction_deck_user jdu
                INNER JOIN decks d ON jdu.deck_id = d.deck_id
                INNER JOIN junction_deck_card jdc ON jdu.deck_id = jdc.deck_id
                WHERE jdu.user_id = '$user_id' AND d.is_leaf = 1
            ");
} else {
    $getCards = mysqli_query(
        $con,
        " SELECT DISTINCT jdc.card_id
                FROM junction_deck_card jdc
                INNER JOIN leaf_deck_map ldm ON jdc.deck_id = ldm.leaf_deck_id
                WHERE ldm.deck_id = '$deckId'"
    );
}

$cards = array();
while ($card = mysqli_fetch_array($getCards)) {
    $cards[] = array(
        'cardId' => $card['card_id'],
        'status' => 'unseen'
    );
}

if (empty($cards)) {
    echo json_encode(["success" => false, "error" => "No cards found"]);
    exit();
}

mysqli_query(
    $con,
    "INSERT IGNORE INTO card_swipe_progress (card_swipe_id, card_id, status) VALUES " .
        implode(", ", array_map(function ($card) use ($cardSwipeId) {
            return "('$cardSwipeId', '{$card['cardId']}', 'unseen')";
        }, $cards))
);

mysqli_query($con, "UPDATE card_swipe_progress SET status = 'inactive' WHERE card_swipe_id = '$cardSwipeId'");

$query = "UPDATE card_swipe_progress SET status = 'unseen' WHERE card_swipe_id = '$cardSwipeId' AND card_id IN (" . implode(", ", array_map(function ($card) {
    return "'{$card['cardId']}'";
}, $cards)) . ")";
$result = mysqli_query($con, $query);

mysqli_query($con, "UPDATE card_swipe_session SET session_started_at = NOW() WHERE card_swipe_id = '$cardSwipeId'");

header("Content-Type: application/json");
echo json_encode([
    "success" => (bool)$result,
    "affected_rows" => mysqli_affected_rows($con),
    "cardSwipeId" => $cardSwipeId,
    "error" => mysqli_error($con)
]);
