<?php
    $con = mysqli_connect("202.10.36.216", "root", "", "anki");

    if (!$con) {
        echo "Koneksi gagal";
    }
    echo "Koneksi berhasil!";
?>