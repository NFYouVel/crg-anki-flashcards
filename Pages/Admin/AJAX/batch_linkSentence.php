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
        if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM junction_card_sentence")) > 0) {
            // menghapus data lama
            if (!mysqli_query($con, "DELETE FROM junction_card_sentence")) {
                throw new Exception("Gagal menghapus data lama: " . mysqli_error($con));
            }
        }

        // pembangunan string query insert sql
        $query = "INSERT INTO junction_card_sentence (card_id, sentence_code, priority) VALUES ";
        foreach ($_SESSION["validLinks"] as $key => $value) {
            // mysqli_real_escape_string untuk menghindari error saat input contohnya jika data dalam cell memiliki " / ' / ( / )
            $cardID = mysqli_real_escape_string($con, $value["cardID"]);
            $sentenceCode = mysqli_real_escape_string($con, $value["sentenceCode"]);
            $priority = mysqli_real_escape_string($con, $value["priority"]);

            if ($count == 35) {
                // query upload akan dikirim setiap selesai membangun query dengan 35 values, karena jika lebih, akan error
                $count = 0;
                $query = substr($query, 0, -2);
                if (!mysqli_query($con, $query)) {
                    throw new Exception("Gagal insert batch: " . mysqli_error($con));
                }
                $query = "INSERT INTO junction_card_sentence (card_id, sentence_code, priority) VALUES ";
            }
            $count++;

            $query .= "($cardID, '$sentenceCode', $priority), ";
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
        <th>Card ID</th>
        <th>Sentence Code</th>
        <th>Priority</th>
    </tr>

    <?php
        $links = mysqli_query($con, "SELECT * FROM junction_card_sentence");
        while ($link = mysqli_fetch_array($links)) {
            echo "<tr>";
            echo "<td>" . $link["card_id"] . "</td>";
            echo "<td>" . $link["sentence_code"] . "</td>";
            echo "<td>" . $link["priority"] . "</td>";
            echo "</tr>";
        }
    ?>
</table>

<!-- menampilkan data yang gagal ke upload (invalid) -->
<table>
    <caption style = "background-color: red;">Invalid Links</caption>
    <tr>
        <th>Card ID</th>
        <th>Sentence Code</th>
        <th>Priority</th>
        <th>Reason</th>
    </tr>
    <?php
        foreach($_SESSION["invalidLinks"] as $links) {
            $cardID = $links["cardID"];
            $sentenceCode = $links["sentenceCode"];
            $priority = $links["priority"];
            $reason = $links["reason"];
            echo "<tr>";
                echo "<td>$cardID</td>";
                echo "<td>$cardID</td>";
                echo "<td>$priority</td>";
                echo "<td>$reason</td>";
            echo "</tr>";
        }
        unset($_SESSION["allLinks"]);
        unset($_SESSION["validLinks"]);
        unset($_SESSION["invalidLinks"]);

    ?>
</table>
<?php }
else {
    echo "<h1>Upload Failed</h1>";
} ?>