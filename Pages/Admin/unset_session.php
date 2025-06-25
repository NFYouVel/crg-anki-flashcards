<?php
    include "../../SQL_Queries/connection.php";
    mysqli_query($con, "DELETE FROM cards");
?>