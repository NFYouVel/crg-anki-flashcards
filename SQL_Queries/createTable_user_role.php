<?php
    include "connection.php";
    if(mysqli_query($con, "CREATE TABLE user_role 
    (
        role_id INT PRIMARY KEY,
        role_key TEXT,
        role_name TEXT,
        role_description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
    ") && mysqli_query($con, "INSERT INTO user_role (role_id, role_key) VALUES (0, 'admin'), (1, 'teacher'), (2, 'student')")) {
        echo "Tabel user_role berhasil dibuat";
    }
    else {
        echo "Tabel user_role gagal dibuat";
    }
?>