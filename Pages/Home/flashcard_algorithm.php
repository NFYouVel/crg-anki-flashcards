<?php
session_start();
include "../../SQL_Queries/connection.php";
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

// User and Card Identity
$user_id =  $_SESSION["user_id"];
$card_id = mysqli_real_escape_string($con, $_GET['card_id']);
$status  = mysqli_real_escape_string($con, $_GET['status']);
$stage   = mysqli_real_escape_string($con, $_GET['stage']);
if (($status == "forgot" && $stage == 0) || ($status == "hard") || ($status == "remember" && $stage == 18)) {
    $temp = 0;
} else if ($status == "forgot") {
    $temp = -1;
} else {
    $temp = 1;
}

// Get Stage Intervals
$table_stage = mysqli_query($con, "SELECT * FROM stages_intervals WHERE stage = $stage");
$time = mysqli_fetch_array($table_stage);
$iv = $time['interval_value'];
$iu = strtoupper($time['interval_unit']);

// Get Card Progress to history
$card_progress = mysqli_query($con, "SELECT * FROM card_progress WHERE user_id = '$user_id' AND card_id = '$card_id'");
$result_card_progress = mysqli_fetch_assoc($card_progress);
$stage_after = $result_card_progress['current_stage'] + $temp;
$review_due = $result_card_progress['review_due'];
$query_logs = "INSERT INTO logs_SRS_user_progress 
(user_id, card_id, stage_before, result, stage_after, review_due, review_actual, review_delay) VALUES
('$user_id', '$card_id', $stage, '$status', $stage_after, '$review_due', NOW(3), (UNIX_TIMESTAMP(NOW(3)) - UNIX_TIMESTAMP('$review_due')) * 1000)";
mysqli_query($con, $query_logs);

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
// History SRS User Progress
mysqli_query($con, $send_query);
header("Location: flashcard.php");
?>
