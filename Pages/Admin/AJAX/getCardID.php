<?php
    include "../../../SQL_Queries/connection.php";
    $hanzi = $_GET["hanzi"];
    $id = mysqli_query($con, "SELECT card_id FROM cards WHERE chinese_sc LIKE '%$hanzi%' OR chinese_sc LIKE '%$hanzi%' OR REGEXP_REPLACE(pinyin, '[0-9]', '') LIKE '%$hanzi%'");
    $id = mysqli_fetch_assoc($id);
    $id = $id["card_id"];
    echo $id;
?>