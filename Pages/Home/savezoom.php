<?php
    session_start();
    if (isset($_POST['zoom'])) {
        $_SESSION['zoom'] = intval($_POST['zoom']);
    }
    echo $_SESSION['zoom'];
?>