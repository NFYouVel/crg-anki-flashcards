<?php
session_start();
include "../../../../SQL_Queries/connection.php";
include "../../../Admin/convertPinyin.php";

$cardId = $_POST["cardId"];
$user_id = $_COOKIE["user_id"];

$query = "
    SELECT c.card_id, c.pinyin, c.meaning_eng, c.meaning_ina, c.word_class,
        CASE WHEN u.character_set = 'traditional' THEN c.chinese_tc 
             ELSE c.chinese_sc 
        END AS hanzi
    FROM cards c
    JOIN users u ON u.user_id = '$user_id'
    WHERE c.card_id = '$cardId'
";

$result = mysqli_query($con, $query);
$line = mysqli_fetch_assoc($result);
$line["pinyin"] = convert($line["pinyin"]);

header("Content-Type: application/json");
echo json_encode($line);
