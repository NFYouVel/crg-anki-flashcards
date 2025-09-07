<?php
    session_start();
    include_once "../../SQL_Queries/connection.php";
    $user_id = $_SESSION["user_id"];
    if(mysqli_fetch_assoc(mysqli_query($con, "SELECT role FROM users WHERE user_id = '$user_id'"))["role"] != 1) {
        header("Location: ../Login");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="icon" href="../../Logo/circle.png">
    <style>
        h1 {
            color: white;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>
    <div id="container">
        <div id="header">
            <h1>Sentence Report</h1>
        </div>
    </div>
</body>
<style>
    #report {
        color: #ffa72a;
    }
    #sentence {
        color: #ffa72a;
    }
    #sentence + ul{
        display: block;
    }
</style>
</html>