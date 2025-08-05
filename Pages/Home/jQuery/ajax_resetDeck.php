<?php
include "../../../SQL_Queries/connection.php";
$deck_id = $_GET['deck_id'];
$user_id_student = $_GET['user_id_student'];

// Step 1: Ambil semua deck_id anak dari parent
$query_decks = mysqli_query($con, "
    WITH RECURSIVE child_decks AS (
        SELECT deck_id FROM decks WHERE deck_id = '$deck_id'
        UNION ALL
        SELECT d.deck_id FROM decks d
        JOIN child_decks cd ON d.parent_deck_id = cd.deck_id
    )
    SELECT deck_id FROM decks WHERE is_leaf = 1
");

$deck_ids = [];
while ($row = mysqli_fetch_assoc($query_decks)) {
    $deck_ids[] = $row['deck_id'];
}

// Convert array deck_id jadi string buat IN clause
$deck_ids_string = implode(",", array_map('intval', $deck_ids));

// Step 2: Ambil semua card_id dari leaf decks
$query_cards = mysqli_query($con, "
    SELECT DISTINCT cp.card_id
    FROM junction_deck_user du
    JOIN junction_deck_card dc ON du.deck_id = dc.deck_id
    JOIN card_progress cp ON cp.card_id = dc.card_id AND cp.user_id = du.user_id
    WHERE du.deck_id IN ($deck_ids_string)
    AND du.user_id = '$user_id_student'
");

$card_ids = [];
while ($row = mysqli_fetch_assoc($query_cards)) {
    $card_ids[] = $row['card_id'];
}

// Step 3: Loop untuk update card_progress tiap card
$success = true;

foreach ($card_ids as $card_id) {
    $update = mysqli_query($con, "
        UPDATE card_progress
        SET current_stage = 0,
            total_review = 0,
            total_fail = 0,
            total_remember = 0,
            total_hard = 0,
            review_due = NOW(),
            review_last = NULL
        WHERE user_id = '$user_id_student'
        AND card_id = '$card_id'
    ");

    if (!$update) {
        $success = false;
        break;
    }
}

if ($success) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($con)]);
}

?>