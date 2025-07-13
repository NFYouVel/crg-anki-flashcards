<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE junction_deck_user (
            deck_user_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            deck_id CHAR(36) NOT NULL,
            user_id CHAR(36) NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ")) {
        echo "Tabel classroom berhasil dibuat";
    }
    else {
        echo "Tabel classroom gagal dibuat";
    }
?>