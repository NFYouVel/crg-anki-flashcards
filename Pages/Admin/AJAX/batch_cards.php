<?php
session_start();
include "../../../SQL_Queries/connection.php";

mysqli_begin_transaction($con);

// Test DELETE
if (!mysqli_query($con, "DELETE FROM cards")) {
    die("DELETE failed: " . mysqli_error($con));
}
echo "DELETE OK<br>";

// Test just the first card insert
$first = reset($_SESSION["validCards"]);
$cardID = mysqli_real_escape_string($con, $first["cardID"]);
$traditional = mysqli_real_escape_string($con, $first["traditional"]);
$simplified = mysqli_real_escape_string($con, $first["simplified"]);
$priority = mysqli_real_escape_string($con, $first["priority"]);
$pinyin = mysqli_real_escape_string($con, $first["pinyin"]);
$class = mysqli_real_escape_string($con, $first["class"]);
$english = mysqli_real_escape_string($con, $first["english"]);
$indo = mysqli_real_escape_string($con, $first["indo"]);

$query = "INSERT INTO cards (card_id, chinese_tc, chinese_sc, priority, pinyin, word_class, meaning_eng, meaning_ina) VALUES ($cardID, '$traditional', '$simplified', $priority, '$pinyin', '$class', '$english', '$indo')";

echo "Query: " . $query . "<br>";

if (!mysqli_query($con, $query)) {
    die("INSERT failed: " . mysqli_error($con));
}
echo "INSERT OK<br>";

mysqli_rollback($con);
die("DEBUG STOP");
