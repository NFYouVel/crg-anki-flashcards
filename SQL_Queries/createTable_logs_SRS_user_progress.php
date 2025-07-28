<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE users (
            log_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            user_id CHAR(36),
            card_id CHAR(36),
            stage_before INT NOT NULL,
            result ENUM('remember', 'hard', 'forget'),
            stage_after INT NOT NULL,
            review_due TIMESTAMP NULL DEFAULT NULL,
            review_actual TIMESTAMP NULL DEFAULT NULL,
            review_delay VARCHAR(50) NOT NULL,
        )
    ")) {
        echo "Tabel User Berhasil Dibuat";
    }
?>