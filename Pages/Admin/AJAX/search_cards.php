<!-- fitur ajax untuk search bar cards -->
<table>
    <tr>
        <th>ID</th>
        <th>Traditional</th>
        <th>Simplified</th>
        <th>Priority</th>
        <th>Pinyin</th>
        <th>Word Class</th>
        <th>English</th>
        <th>Indo</th>
        <th>Sentence Count</th>
    </tr>

    <?php
        include "../../../SQL_Queries/connection.php";
        $search = $_GET["search"];
        //jika search bar kosong, maka menampilkan semua (fitur ini dimatikan karena menunggu pendapat)
        if($search == "") {
            $cards = mysqli_query($con, "SELECT * FROM cards");
        }
        else {
            //search berdasarkan id (bisa berdasarkan field lain)
            $cards = mysqli_query($con, "SELECT * FROM cards WHERE card_id = $search");
        }
        while ($card = mysqli_fetch_array($cards)) {
            echo "<tr>";
            echo "<td>" . $card["card_id"] . "</td>";
            echo "<td>" . $card["chinese_tc"] . "</td>";
            echo "<td>" . $card["chinese_sc"] . "</td>";
            echo "<td>" . $card["priority"] . "</td>";
            echo "<td>" . $card["pinyin"] . "</td>";
            echo "<td>" . $card["word_class"] . "</td>";
            echo "<td id = 'long'>" . $card["meaning_eng"] . "</td>";
            echo "<td id = 'long'>" . $card["meaning_ina"] . "</td>";
            echo "<td>0</td>";
            echo "</tr>";
        }
    ?>
</table>