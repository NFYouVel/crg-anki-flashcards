<?php
    // fitur ajax untuk upload cards
    // data yang di read di dalam excel di simpan di dalam session agar tidak perlu read lagi
    session_start();
    include "../../../SQL_Queries/connection.php";
    $count = 0;
    $deckID = $_GET["deckID"];

    // memulai transaksi database
    mysqli_begin_transaction($con);
    $success = true;

    try {
        // jika database cards sudah ada isinya, maka hapus dulu semuanya baru masukkan yang sudah di update jika belum, maka langsung masukkan data
        if (mysqli_num_rows(mysqli_query($con, "SELECT card_id FROM junction_deck_card WHERE deck_id = '$deckID'")) > 0) {
            // menghapus data lama
            if (!mysqli_query($con, "DELETE FROM junction_deck_card WHERE deck_id = '$deckID'")) {
                throw new Exception("Gagal menghapus data lama: " . mysqli_error($con));
            }
        }

        // pembangunan string query insert sql
        $query = "INSERT INTO junction_deck_card (card_id, deck_id) VALUES ";
        foreach ($_SESSION["validCards"] as $key => $value) {
            // mysqli_real_escape_string untuk menghindari error saat input contohnya jika data dalam cell memiliki " / ' / ( / )
            $cardID = mysqli_real_escape_string($con, $value["cardID"]);

            if ($count == 35) {
                // query upload akan dikirim setiap selesai membangun query dengan 35 values, karena jika lebih, akan error
                $count = 0;
                $query = substr($query, 0, -2);
                if (!mysqli_query($con, $query)) {
                    throw new Exception("Gagal insert batch: " . mysqli_error($con));
                }
                $query = "INSERT INTO junction_deck_card (card_id, deck_id) VALUES ";
            }
            $count++;

            $query .= "($cardID, '$deckID'), ";
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
        <th>No</th>
        <th>Card ID</th>
    </tr>

    <?php
        $count = 1;
        $cards = mysqli_query($con, "SELECT card_id FROM junction_deck_card WHERE deck_id = '$deckID'");
        while ($card = mysqli_fetch_array($cards)) {
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . $card["card_id"] . "</td>";
            echo "</tr>";
        }
    ?>
</table>
<table>
    <caption style = "background-color: red; color: white;">Invalid Cards</caption>
    <tr>
        <th>No</th>
        <th>Card ID</th>
        <th>Reason</th>
    </tr>
    <?php
        $count = 1;
        $aval = false;
        foreach($_SESSION["invalidCards"] as $card) {
            $aval = false;
            $cardID = $card["cardID"];
            $reason = $card["reason"];
            echo "
            <tr>
                <td>" . $count++ . "</td>
                <td>$cardID</td>
                <td>$reason</td>
            </tr>";
        }
    ?>
</table>

<!-- menampilkan data yang gagal ke upload (invalid) -->
<?php } else {
    echo "<h1>Upload Failed</h1>"; ?>
<?php } ?>