<?php
    $con = mysqli_connect("localhost", "anki_marvel", "ihatep0tat0", "anki");

    if (!$con) {
        echo "Koneksi gagal";
    }
    echo "Koneksi berhasil!";
?>