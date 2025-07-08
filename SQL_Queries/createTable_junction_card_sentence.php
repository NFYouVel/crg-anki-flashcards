<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE junction_card_sentence (
            card_id int(11),
            sentence_code TEXT,
            priority INT
        )
    ")) {
        echo "Tabel junction_card_sentence berhasil dibuat";
    }
    else {
        echo "Tabel junction_card_sentence gagal dibuat";
    }
?>