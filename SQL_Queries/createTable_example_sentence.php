<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE `example_sentence` (
            sentence_code TEXT NOT NULL,
            chinese_tc TEXT,
            chinese_sc TEXT,
            pinyin TEXT,
            meaning_eng TEXT,
            meaning_ina TEXT
        )
    ")) {
        echo "table example_sentence succesffuly made";
    }
    else {
        echo "table example_sentence failed to be made";
    }
?>