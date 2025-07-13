<?php
    // fitur ajax untuk upload cards
    // data yang di read di dalam excel di simpan di dalam session agar tidak perlu read lagi
    session_start();
    include "../../../SQL_Queries/connection.php";
    $count = 0;

    // memulai transaksi database
    mysqli_begin_transaction($con);
    $success = true;

    try {
        // jika database cards sudah ada isinya, maka hapus dulu semuanya baru masukkan yang sudah di update jika belum, maka langsung masukkan data
        if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM cards")) > 0) {
            // menghapus data lama
            if (!mysqli_query($con, "DELETE FROM cards")) {
                throw new Exception("Gagal menghapus data lama: " . mysqli_error($con));
            }
        }

        // pembangunan string query insert sql
        $query = "INSERT INTO cards (card_id, chinese_tc, chinese_sc, priority, pinyin, word_class, meaning_eng, meaning_ina) VALUES ";
        foreach ($_SESSION["validCards"] as $key => $value) {
            // mysqli_real_escape_string untuk menghindari error saat input contohnya jika data dalam cell memiliki " / ' / ( / )
            $cardID = mysqli_real_escape_string($con, $value["cardID"]);
            $traditional = mysqli_real_escape_string($con, $value["traditional"]);
            $simplified = mysqli_real_escape_string($con, $value["simplified"]);
            $priority = mysqli_real_escape_string($con, $value["priority"]);
            $pinyin = mysqli_real_escape_string($con, $value["pinyin"]);
            $class = mysqli_real_escape_string($con, $value["class"]);
            $english = mysqli_real_escape_string($con, $value["english"]);
            $indo = mysqli_real_escape_string($con, $value["indo"]);

            if ($count == 35) {
                // query upload akan dikirim setiap selesai membangun query dengan 35 values, karena jika lebih, akan error
                $count = 0;
                $query = substr($query, 0, -2);
                if (!mysqli_query($con, $query)) {
                    throw new Exception("Gagal insert batch: " . mysqli_error($con));
                }
                $query = "INSERT INTO cards (card_id, chinese_tc, chinese_sc, priority, pinyin, word_class, meaning_eng, meaning_ina) VALUES ";
            }
            $count++;

            $query .= "($cardID, '$traditional', '$simplified', $priority, '$pinyin', '$class', '$english', '$indo'), ";
        }

        // mengirim query sisa
        if ($count > 0) {
            $query = substr($query, 0, -2);
            if (!mysqli_query($con, $query)) {
                throw new Exception("Gagal insert sisa: " . mysqli_error($con));
            }
        }

        // jika semua berhasil, commit perubahan
        mysqli_commit($con);
    } catch (Exception $e) {
        // jika ada error, batalkan semua perubahan
        mysqli_rollback($con);
        $success = false;

        // untuk pengembangan: tampilkan pesan error (hapus di produksi)
        echo "<script>console.error(" . json_encode($e->getMessage()) . ");</script>";
    }
?>

<!-- menampilkan data yang berhasil ke upload -->
 <?php if($success) { ?>
<table id="valid">
    <caption style="background-color: green;">Cards Successfully Uploaded</caption>
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
        $cards = mysqli_query($con, "SELECT * FROM cards");
        while ($card = mysqli_fetch_array($cards)) {
            echo "<tr>";
            echo "<td>" . $card["card_id"] . "</td>";
            echo "<td>" . $card["chinese_tc"] . "</td>";
            echo "<td>" . $card["chinese_sc"] . "</td>";
            echo "<td>" . $card["priority"] . "</td>";
            echo "<td>" . $card["pinyin"] . "</td>";
            echo "<td>" . $card["word_class"] . "</td>";
            echo "<td id='long'>" . $card["meaning_eng"] . "</td>";
            echo "<td id='long'>" . $card["meaning_ina"] . "</td>";
            echo "<td>0</td>";
            echo "</tr>";
        }
    ?>
</table>

<!-- menampilkan data yang gagal ke upload (invalid) -->
<table id="invalid">
    <caption style="background-color: red;">Invalid Cards</caption>
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
        <th>Reason</th>
    </tr>
    <?php
        foreach ($_SESSION["invalidCards"] as $key => $value) {
            echo "<tr>";
            echo "<td>" . $value["cardID"] . "</td>";
            echo "<td>" . $value["traditional"] . "</td>";
            echo "<td>" . $value["simplified"] . "</td>";
            echo "<td>" . $value["priority"] . "</td>";
            echo "<td>" . $value["pinyin"] . "</td>";
            echo "<td>" . $value["class"] . "</td>";
            echo "<td id='long'>" . $value["english"] . "</td>";
            echo "<td id='long'>" . $value["indo"] . "</td>";
            echo "<td>0</td>";
            echo "<td>" . $value["reason"] . "</td>";
            echo "</tr>";
        }

        unset($_SESSION["allCards"]);
        unset($_SESSION["validCards"]);
        unset($_SESSION["invalidCards"]);
    ?>
</table>
<?php }
else {
    echo "<h1>Upload Failed</h1>";
} ?>