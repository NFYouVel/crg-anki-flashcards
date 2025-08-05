
<?php
if($deckID = "main") {
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
    FROM card_progress AS cp WHERE cp.user_id = '$user_id_student'
    ");
}
else {
    $query_flashcard_rbg_count = mysqli_query($con, "
    WITH RECURSIVE child_decks AS (
        SELECT deck_id, is_leaf
        FROM decks WHERE deck_id = '$deck_id'
    
        UNION ALL
    
        SELECT d.deck_id, d.is_leaf
        FROM decks AS d
        JOIN child_decks AS cd
        ON d.parent_deck_id = cd.deck_id
    ),
    leaf_decks AS (
        SELECT deck_id FROM child_decks WHERE is_leaf = 1
    )
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
    FROM junction_deck_user AS jdu
    JOIN leaf_decks AS ld ON jdu.deck_id = ld.deck_id
    JOIN junction_deck_card AS jdc ON jdc.deck_id = ld.deck_id
    JOIN card_progress AS cp ON cp.card_id = jdc.card_id
    WHERE jdu.user_id = '$user_id_student';
    ");
}
?>