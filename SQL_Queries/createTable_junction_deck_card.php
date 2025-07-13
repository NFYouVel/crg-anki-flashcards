<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE junction_deck_card (
            card_id int(11) NOT NULL,
            deck_id CHAR(36) NOT NULL
        )
    ")) {
        echo "Tabel junction_deck_card berhasil dibuat";
    }
    else {
        echo "Tabel junction_deck_card gagal dibuat";
    }
?>