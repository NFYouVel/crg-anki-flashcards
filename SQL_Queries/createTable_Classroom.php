<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE classroom (
            classroom_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            name TEXT NOT NULL,
            description TEXT,
            created_by CHAR(36) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_by CHAR(36),
            updated_at TIMESTAMP NULL DEFAULT NULL
        )
    ")) {
        echo "Tabel classroom berhasil dibuat";
    }
    else {
        echo "Tabel classroom gagal dibuat";
    }
?>