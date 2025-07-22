<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE junction_deck_classroom (
            deck_classroom_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            deck_id CHAR(36) NOT NULL,
            classroom_id CHAR(36) NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ")) {
        echo "Tabel junction_deck_classroom berhasil dibuat";
    }
    else {
        echo "Tabel junction_deck_classroom gagal dibuat";
    }
?>