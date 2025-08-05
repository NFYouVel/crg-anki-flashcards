<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE deck_pool (
            deck_id CHAR(36) NOT NULL,
            user_id CHAR(36) NOT NULL
        )
    ")) {
        echo "Tabel deck_pool berhasil dibuat";
    }
    else {
        echo "Tabel deck_pool gagal dibuat";
    }
?>