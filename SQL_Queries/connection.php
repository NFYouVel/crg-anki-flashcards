<?php
    $con = mysqli_connect("localhost", "anki_marvel", "ihatep0tat0", "anki");

    if(!isset($_SESSION["user_id"])) {
        header("Location: ../Pages/Login");
        exit;
    }
    // $con = mysqli_connect("localhost", "root", "", "anki");
?>