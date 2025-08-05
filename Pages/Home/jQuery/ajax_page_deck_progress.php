<?php
include "../../../SQL_Queries/connection.php";
include "../../Admin/convertPinyin.php";

$user_id_student = $_GET['user_id_student'];
$deck_id = $_GET['deck_id'];

$status = isset($_GET['status']) ? $_GET['status'] : [];
$status_condition = "";
$conditions = [];

foreach ($status as $s) {
    if ($s === "new") {
        $conditions[] = "cp.current_stage = 0";
    } elseif ($s === "weak") {
        $conditions[] = "cp.current_stage BETWEEN 1 AND 4";
    } elseif ($s === "unreviewed") {
        $conditions[] = "cp.review_due >= NOW()";
    }
}

if (!empty($conditions)) {
    $status_condition = "AND (" . implode(" OR ", $conditions) . ")";
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

// Hitung total rows
$count_query = mysqli_query($con, "
    SELECT COUNT(*) AS total 
    FROM junction_deck_user AS du 
    JOIN decks AS d ON d.deck_id = du.deck_id
    JOIN junction_deck_card AS dc ON d.deck_id = dc.deck_id
    JOIN cards AS c ON dc.card_id = c.card_id
    JOIN card_progress AS cp ON c.card_id = cp.card_id AND cp.user_id = du.user_id
    WHERE du.user_id = '$user_id_student' AND d.is_leaf = 1
    $status_condition
");
$total_rows = mysqli_fetch_assoc($count_query)['total'];
$total_pages = ceil($total_rows / $limit);

// Ambil data flashcard
$query = mysqli_query($con, "
    SELECT c.pinyin, c.chinese_sc, c.word_class, c.meaning_eng, c.meaning_ina, c.card_id, cp.current_stage, cp.review_due
    FROM junction_deck_user AS du 
    JOIN decks AS d ON d.deck_id = du.deck_id
    JOIN junction_deck_card AS dc ON d.deck_id = dc.deck_id
    JOIN cards AS c ON dc.card_id = c.card_id
    JOIN card_progress AS cp ON c.card_id = cp.card_id AND cp.user_id = du.user_id
    WHERE du.user_id = '$user_id_student' AND d.is_leaf = 1
    $status_condition
    LIMIT $limit OFFSET $offset
");

$rows = "";
while ($line = mysqli_fetch_assoc($query)) {
    $color = "";
    if ($line['current_stage'] == 0) $color = "color: green;";
    else if ($line['current_stage'] < 5) $color = "color: red;";
    else $color = "visibility: hidden;";

    $pinyin = convert($line["pinyin"]);

    $rows .= "<tr>";
    $rows .= "<td class='no-border' style='$color'><div class='review-color'>.</div></td>";
    $rows .= "<td class='words'>{$line['chinese_sc']}</td>";
    $rows .= "<td><div class='words-contain'><span class='pinyin'>$pinyin</span></div></td>";
    $rows .= "<td><span>{$line['meaning_eng']}</span></td>";
    $rows .= "</tr>";
}

// Buat pagination button
$pagination = "";
for ($i = 1; $i <= $total_pages; $i++) {
    $active = ($i == $page) ? "font-weight: bold; text-decoration: underline;" : "";
    $pagination .= "<a href='#' class='pagination-link' data-page='$i' style='margin: 0 5px; $active'>$i</a>";
}

echo json_encode([
    'rows' => $rows,
    'pagination' => $pagination
]);
