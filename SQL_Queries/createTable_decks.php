<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE decks (
            deck_id CHAR(36) PRIMARY KEY DEFAULT UUID(),
            name VARCHAR(50) NOT NULL,
            created_by_user_id CHAR(36) NOT NULL,
            parent_deck_id CHAR(36) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_leaf BOOLEAN DEFAULT 0
        )
    ")) {
        echo "Tabel decks berhasil dibuat";
    }
    else {
        echo "Tabel decks gagal dibuat";
    }
?>