<?php
    $con = mysqli_connect("localhost", "root", "", "anki");

    if (!$con) {
        echo "Koneksi gagal";
    }
    echo "Koneksi berhasil!";
?>