<?php
    include "connection.php";
    if(mysqli_query($con, "CREATE TABLE user_role 
    (
        role_id INT PRIMARY KEY AUTO_INCREMENT,
        role_key TEXT,
        role_name TEXT,
        role_description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
    ") && mysqli_query($con, "INSERT INTO user_role (role_key, role_name) VALUES ('admin', 'Admin'), ('teacher', 'Teacher'), ('student', 'Student')")) {
        echo "Tabel user_role berhasil dibuat";
    }
    else {
        echo "Tabel user_role gagal dibuat";
    }
?>