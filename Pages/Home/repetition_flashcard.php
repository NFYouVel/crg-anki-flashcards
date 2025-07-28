<?php
$query_flashcard_rbg_count = mysqli_query($con, "
SELECT
    SUM(CASE 
        WHEN cp.current_stage = 0 THEN 1 
        ELSE 0 
    END) AS blue,
    SUM(CASE 
        WHEN cp.current_stage != 0 AND cp.review_due <= NOW() THEN 1 
        ELSE 0 
    END) AS green,
    SUM(CASE 
        WHEN cp.review_due > NOW() THEN 1 
        ELSE 0 
    END) AS red
FROM cards AS card
JOIN junction_deck_card AS jdc ON card.card_id = jdc.card_id
JOIN junction_deck_user AS jdu ON jdc.deck_id = jdu.deck_id
JOIN card_progress AS cp ON cp.user_id = jdu.user_id AND cp.card_id = card.card_id
WHERE jdc.deck_id = '$deckID' AND jdu.user_id = '$user_id'
");
?>