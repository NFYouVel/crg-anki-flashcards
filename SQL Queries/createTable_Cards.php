<?php
    include "connection.php";
    mysqli_query($con, "
        CREATE TABLE `cards` (
            `card_id` int(11) NOT NULL,
            `chinese_tc` text NOT NULL,
            `chinese_sc` text NOT NULL,
            `priority` int(11) NOT NULL,
            `pinyin` text NOT NULL,
            `word_class` text NOT NULL,
            `meaning_eng` text NOT NULL,
            `meaning_ina` text NOT NULL,
            `sentence_count` int(11) DEFAULT 0
        )
    ")
?>