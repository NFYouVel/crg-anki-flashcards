<?php
session_start();
include "../../../SQL_Queries/connection.php";
if (!isset($_SESSION['count_reports'])) {
    $_SESSION['count_reports'] = 0;
}

$user_id = $_SESSION["user_id"];
if (isset($_GET['reason']) && !empty($_GET['reason'])) {
    $reasons = $_GET['reason'];
    $reasons_text = implode(", ", $reasons);
}

if (isset($_GET['details']) && !empty($_GET['details'])) {
    $details = $_GET['details'];
}
$sentence_code = $_GET['sentence-id'];

//Prevent spam
if (isset($_COOKIE['vars-ones']) && isset($_COOKIE['vars-twos']) && isset($_COOKIE['vars-threes'])) {
    echo "Action temporarily disabled. Try again in 1 minute.";
    exit;
}
if ($_SESSION['count_reports'] == 1) {
    setcookie("vars-ones", "1", time() + 60);
}
if ($_SESSION['count_reports'] == 2) {
    setcookie("vars-twos", "2", time() + 60);
}
if ($_SESSION['count_reports'] == 3) {
    setcookie("vars-threes", "3", time() + 60);
    $_SESSION['count_reports'] = 0;
}

// Prevent dia ngereport yang sama 2x
$stmt = $con->prepare("SELECT * FROM sentence_report WHERE user_id = ? AND sentence_code = ? AND report_status = 'pending'");
$stmt->bind_param("ss", $user_id, $sentence_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "You have reported this sentence!";
    exit;
}

$sql = "INSERT INTO sentence_report (user_id, sentence_code, reason, details, created_at) 
            VALUES (?, ?, ?, ?, NOW())";

$stmt = $con->prepare($sql);
$stmt->bind_param("ssss", $user_id, $sentence_code, $reasons_text, $details);

if ($stmt->execute()) {
    echo "Your report has been successfully sent, we will process it as soon as possible, Thank you.";
    $_SESSION['count_reports']++;
}

$stmt->close();
$con->close();
