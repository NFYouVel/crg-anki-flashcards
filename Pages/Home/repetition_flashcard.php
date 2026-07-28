<?php
if ($deckID == "main") {
    $query_flashcard_rbg_count = mysqli_query($con, "
    SELECT
        COUNT(DISTINCT cp.card_id) AS blue,
        COUNT(DISTINCT CASE 
            WHEN cp.current_stage != 0 THEN cp.card_id 
        END) AS green,
        COUNT(DISTINCT CASE 
            WHEN cp.review_due <= NOW() AND cp.review_due != cp.review_first THEN cp.card_id 
        END) AS red
    FROM card_progress cp 
    INNER JOIN junction_deck_card jdc
        ON cp.card_id = jdc.card_id
    INNER JOIN junction_deck_user jdu
        ON jdu.deck_id = jdc.deck_id
    WHERE cp.user_id = '$user_id' AND jdu.user_id = '$user_id'
    ");
} else {
    $query_flashcard_rbg_count = mysqli_query($con, "
    SELECT
        COUNT(DISTINCT cp.card_id) AS blue,
        COUNT(DISTINCT CASE 
            WHEN cp.current_stage != 0 THEN cp.card_id 
        END) AS green,
        COUNT(DISTINCT CASE 
            WHEN cp.review_due <= NOW() AND cp.review_due != cp.review_first THEN cp.card_id 
        END) AS red
    FROM card_progress cp 
    INNER JOIN junction_deck_card jdc
        ON cp.card_id = jdc.card_id
    INNER JOIN junction_deck_user jdu
        ON jdu.deck_id = jdc.deck_id
    INNER JOIN leaf_deck_map ldm
        ON ldm.leaf_deck_id = jdu.deck_id
    WHERE cp.user_id = '$user_id' AND jdu.user_id = '$user_id' AND ldm.deck_id = '$deckID'
    ");
}
