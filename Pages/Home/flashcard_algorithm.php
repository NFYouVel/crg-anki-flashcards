<?php
if (isset($_GET['card_id']) && isset($_GET['status'])) {
    $card_id = $_GET['card_id'];
    $status = $_GET['status'];

    // Debug print biar lo yakin
    echo json_encode([
        'success' => true,
        'card_id' => $card_id,
        'status' => $status
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Missing card_id or status'
    ]);
}

session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
$user_id = $_SESSION["user_id"];

$card_id = $_GET['card_id'];
$status = $_GET['status'];
$stage = $_GET['stage'];
$table_stage = mysqli_query($con, "SELECT * FROM stages_intervals WHERE stage = $stage");
$time = mysqli_fetch_array($table_stage);
$iv = $time['interval_value'];
$iu = strtoupper($time['interval_unit']);

var_dump($iv, $iu);
if ($status == "forgot" && $stage == 0) {
    $send_query = "
    UPDATE card_progress SET total_review = total_review + 1, total_fail = total_fail + 1, review_due = NOW() + INTERVAL $iv $iu, review_last = NOW()
    WHERE user_id = '$user_id' AND card_id = '$card_id'
    ";
} else if ($status == "forgot") {
    $send_query = "
    UPDATE card_progress SET current_stage = current_stage - 1, total_review = total_review + 1, total_fail = total_fail + 1, review_due = NOW() + INTERVAL $iv $iu, review_last = NOW()
    WHERE user_id = '$user_id' AND card_id = '$card_id'
    ";
} else if ($status == "hard") {
    $send_query = "
    UPDATE card_progress SET total_review = total_review + 1, total_hard = total_hard + 1, review_due = NOW() + INTERVAL $iv $iu, review_last = NOW()
    WHERE user_id = '$user_id' AND card_id = '$card_id'
    ";
} else if ($status == "remember" && $stage == 18) {
    $send_query = "
    UPDATE card_progress SET total_review = total_review + 1, total_remember = total_remember + 1, review_due = NOW() + INTERVAL $iv $iu, review_last = NOW()
    WHERE user_id = '$user_id' AND card_id = '$card_id'
    ";
} else {
    $send_query = "
    UPDATE card_progress SET current_stage = current_stage + 1, total_review = total_review + 1, total_remember = total_remember + 1, review_due = NOW() + INTERVAL $iv $iu, review_last = NOW()
    WHERE user_id = '$user_id' AND card_id = '$card_id'
    ";
}

mysqli_query($con, $send_query);
header("Location: flashcard.php?deck_id = ".$_SESSION['deck_id'])
?>
