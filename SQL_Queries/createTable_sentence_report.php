<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE sentence_report (
            sentence_report_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            user_id CHAR(36),
            sentence_code CHAR(36),
            reason ENUM('bad_sentence', 'bad_pinyin', 'bad_translation_eng', 'bad_translation_ina'),
            details TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            report_status ENUM('pending', 'solved', 'rejected') DEFAULT 'pending',
            handled_by CHAR(36),
            handled_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
        )
    ")) {
        echo "Tabel classroom berhasil dibuat";
    }
    else {
        echo "Tabel classroom gagal dibuat";
    }
?>