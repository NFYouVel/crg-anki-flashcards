<?php
if($deckID == "main") {
    // $query_flashcard_rbg_count = mysqli_query($con, "
    // SELECT
    //     COUNT(cp.card_id) AS blue,
    //     COUNT(DISTINCT CASE 
    //         WHEN cp.current_stage != 0 THEN cp.card_id 
    //         ELSE NULL 
    //     END) AS green,
    //     COUNT(DISTINCT CASE 
    //         WHEN cp.review_due <= NOW() AND cp.review_due != cp.review_first THEN cp.card_id 
    //         ELSE NULL 
    //     END) AS red
    // FROM card_progress AS cp 
    // WHERE cp.user_id = '$user_id' AND cp.is_assigned = 1
    // ");
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
}
else {
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
    // $query_flashcard_rbg_count = mysqli_query($con, "
    // WITH RECURSIVE child_decks AS (
    //     SELECT deck_id, is_leaf
    //     FROM decks WHERE deck_id = '$deckID'
    
    //     UNION ALL
    
    //     SELECT d.deck_id, d.is_leaf
    //     FROM decks AS d
    //     JOIN child_decks AS cd
    //     ON d.parent_deck_id = cd.deck_id
    // ),
    // leaf_decks AS (
    //     SELECT deck_id FROM child_decks WHERE is_leaf = 1
    // ),
    // -- First get distinct cards from all relevant decks
    // distinct_cards AS (
    //     SELECT DISTINCT jdc.card_id
    //     FROM junction_deck_user AS jdu
    //     JOIN leaf_decks AS ld ON jdu.deck_id = ld.deck_id
    //     JOIN junction_deck_card AS jdc ON jdc.deck_id = ld.deck_id
    //     WHERE jdu.user_id = '$user_id'
    // )
    // SELECT
    //     COUNT(cp.card_id) AS blue,
    //     COUNT(DISTINCT CASE 
    //         WHEN cp.current_stage != 0 THEN cp.card_id 
    //         ELSE NULL 
    //     END) AS green,
    //     COUNT(DISTINCT CASE 
    //         WHEN cp.review_due <= NOW() AND cp.review_due != cp.review_first THEN cp.card_id 
    //         ELSE NULL 
    //     END) AS red
    // FROM distinct_cards AS dc
    // JOIN card_progress AS cp ON dc.card_id = cp.card_id
    // WHERE cp.user_id = '$user_id' AND cp.is_assigned = 1;
    // ");
}
?>