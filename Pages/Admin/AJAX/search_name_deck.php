<?php
    include "../../../SQL_Queries/connection.php";
    $name = $_GET["name"];
    $userID = $_GET["userID"];
    $type = $_GET["type"];
    if($type == "teacher") {
        $getNames = mysqli_query($con, "SELECT name, user_id FROM users WHERE role = 2 AND name LIKE '%$name%'");
    }
    else if ($type == "student"){
        $getNames = mysqli_query($con, "SELECT name, user_id FROM users WHERE role = 3 AND name LIKE '%$name%'");
    }
    while($studentNames = mysqli_fetch_assoc($getNames)) {
        $name = $studentNames["name"];
        $tempID = $studentNames["user_id"];
        // if($tempID == $userID) {
        //     continue;
        // }
        if($type == "student") {
            $dir = "deckAssigned.php?id=$tempID";
        }
        else {
            $dir = "deckPool.php?id=$tempID";
        }
        echo "<a onclick=\"window.location.href='$dir'\">$name</a>";
    }
?>