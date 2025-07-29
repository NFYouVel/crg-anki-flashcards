<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE junction_deck_user (
            deck_user_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            deck_id CHAR(36) NOT NULL,
            user_id CHAR(36) NOT NULL,
            temp_added BOOLEAN DEFAULT 1,
            temp_deleted BOOLEAN DEFAULT 0,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ")) {
        echo "Tabel classroom berhasil dibuat";
    }
    else {
        echo "Tabel classroom gagal dibuat";
        if(mysqli_query($con, "ALTER TABLE junction_deck_user ADD COLUMN temp_added BOOLEAN DEFAULT 1, 
                                    ADD COLUMN temp_deleted BOOLEAN DEFAULT 0")) {
                echo "<br>column temp_added dan temp_deleted ditambahkan";
        }
    }
?>