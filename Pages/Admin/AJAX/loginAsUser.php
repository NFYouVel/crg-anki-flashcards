<?php
session_start();
include "../../../SQL_Queries/connection.php";

// Admin only
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/index.php");
    exit;
}

$adminId = $_SESSION['user_id'];

$stmt = $con->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->bind_param("s", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row || $row['role'] != 1) {
    header("Location: ../Login/index.php");
    exit;
}

// Validate target user
$userId = $_GET["id"];
if (!$userId) {
    header("Location: ../");
    exit;
}

$stmt = $con->prepare("SELECT user_id, user_status FROM users WHERE user_id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$target = $result->fetch_assoc();
$stmt->close();

if (!$target || $target['user_status'] == 'deleted') {
    header("Location: ../");
    exit;
}

// Store original admin id so they can return
$_SESSION['admin_id'] = $adminId;
$_SESSION['user_id'] = $target['user_id'];
setcookie('user_id', $target['user_id'], time() + 86400, '/', '', false, true);

header("Location: ../../Login");
exit;
