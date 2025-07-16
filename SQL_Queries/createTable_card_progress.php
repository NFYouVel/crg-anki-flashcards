<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE card_progress (
            card_progress_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            user_id CHAR(36), 
            card_id int(11), 
            current_stage INT DEFAULT 0,
            review_first TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            review_last TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            review_due TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            total_review INT DEFAULT 0,
            total_remember INT DEFAULT 0,
            total_hard INT DEFAULT 0,
            total_fail INT DEFAULT 0,
            last_actual_delay FLOAT DEFAULT NULL,
            is_assigned BOOLEAN DEFAULT 1
        )
    ")) {
        echo "Tabel card_progress berhasil dibuat";
    }
    else {
        echo "Tabel card_progress gagal dibuat";
    }
?>