<?php
    include "connection.php";
    if(mysqli_query($con, "
        CREATE TABLE users (
            user_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
            name VARCHAR(50) NOT NULL,
            email VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role INT NOT NULL,
            user_status ENUM('pending', 'active', 'suspended', 'deleted') DEFAULT 'pending',
            character_set ENUM('simplified', 'traditional') DEFAULT 'simplified',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL, 
            last_login TIMESTAMP NULL DEFAULT NULL,
            deleted_at TIMESTAMP NULL DEFAULT NULL,
            force_password_reset BOOLEAN DEFAULT TRUE
        )
    ")) {
        echo "Tabel User Berhasil Dibuat";
    }
    else {
        if(mysqli_query($con, "ALTER TABLE users ADD COLUMN remarks TEXT DEFAULT NULL")) {
            echo "column remarks ditambahkan";
        }
    }

    $pw = password_hash("Bloomingwordpress8!", PASSWORD_BCRYPT);
    mysqli_query($con, "INSERT INTO users(name,email,password_hashed,role,user_status,character_set,created_at) VALUES
    ('Herodian Petro Marlim','herodianpm@gmail.com','$pw','1','active','simplified',NOW())")
?>