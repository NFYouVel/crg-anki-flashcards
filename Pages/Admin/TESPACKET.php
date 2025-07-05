<?php
    include "../../SQL_Queries/connection.php";
    $result = mysqli_query($con, "SHOW VARIABLES LIKE 'max_allowed_packet'");
    $row = mysqli_fetch_assoc($result);
    echo $row['Value'];
?>