<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE junction_classroom_user (
            classroom_user_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            classroom_id CHAR(36) NOT NULL,
            user_id CHAR(36) NOT NULL,
            classroom_role_id INT NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ")) {
        echo "Tabel classroom berhasil dibuat";
    }
    else {
        echo "Tabel classroom gagal dibuat";
    }
?>