<?php
    session_start();
    session_unset(); 
    session_destroy(); 
    setcookie('user_id', $line['user_id'], time() - (86400 * 30), '/', '', false, true);
    header("Location: ../Login/");
?>