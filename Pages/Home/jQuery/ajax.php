<?php
session_start();
include "../../../SQL_Queries/connection.php";

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit;
}

$user_id = $_SESSION["user_id"];
$query = "SELECT character_set FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$line = mysqli_fetch_assoc($result);

$current = $line['character_set'];
$newSet = ($current == "simplified") ? "traditional" : "simplified";

$updateQuery = "UPDATE users SET character_set = '$newSet' WHERE user_id = '$user_id'";
$updateResult = mysqli_query($con, $updateQuery);

if ($updateResult) {
    echo $newSet; // ini yang akan masuk ke innerText di <td>
} else {
    echo "Error";
}
?>
