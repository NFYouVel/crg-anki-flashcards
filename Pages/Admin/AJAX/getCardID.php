<?php
    include "../../../SQL_Queries/connection.php";
    $hanzi = $_GET["hanzi"];
    $id = mysqli_query($con, "SELECT card_id FROM cards WHERE chinese_sc = '$hanzi' OR chinese_sc = '$hanzi'");
    $id = mysqli_fetch_assoc($id);
    $id = $id["card_id"];
    echo $id;
?>