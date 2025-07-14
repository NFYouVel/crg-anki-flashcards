<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE data_backup (
            import_batch_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            import_type ENUM('card', 'sentence', 'card_sentence'),
            import_batch_name TEXT,
            total_records INT,
            successful_import INT,
            skipped_import INT,
            imported_by CHAR(36),
            imported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_current_version BOOLEAN
        )
    ")) {
        echo "Tabel data_backup berhasil dibuat";
    }
    else {
        echo "Tabel data_backup gagal dibuat";
    }
?>