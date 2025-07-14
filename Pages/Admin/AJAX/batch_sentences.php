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
        if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM example_sentence")) > 0) {
            // menghapus data lama
            if (!mysqli_query($con, "DELETE FROM example_sentence")) {
                throw new Exception("Gagal menghapus data lama: " . mysqli_error($con));
            }
        }

        // pembangunan string query insert sql
        $query = "INSERT INTO example_sentence (sentence_code, chinese_tc, chinese_sc, pinyin, meaning_eng, meaning_ina) VALUES ";
        foreach ($_SESSION["validSentences"] as $key => $value) {
            // mysqli_real_escape_string untuk menghindari error saat input contohnya jika data dalam cell memiliki " / ' / ( / )
            $sentenceCode = mysqli_real_escape_string($con, $value["sentenceCode"]);
            $traditional = mysqli_real_escape_string($con, $value["traditional"]);
            $simplified = mysqli_real_escape_string($con, $value["simplified"]);
            $pinyin = mysqli_real_escape_string($con, $value["pinyin"]);
            $english = mysqli_real_escape_string($con, $value["english"]);
            $indo = mysqli_real_escape_string($con, $value["indo"]);

            if ($count == 35) {
                // query upload akan dikirim setiap selesai membangun query dengan 35 values, karena jika lebih, akan error
                $count = 0;
                $query = substr($query, 0, -2);
                if (!mysqli_query($con, $query)) {
                    throw new Exception("Gagal insert batch: " . mysqli_error($con));
                }
                $query = "INSERT INTO example_sentence (sentence_code, chinese_tc, chinese_sc, pinyin, meaning_eng, meaning_ina) VALUES ";
            }
            $count++;

            $query .= "('$sentenceCode', '$traditional', '$simplified', '$pinyin', '$english', '$indo'), ";
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
<?php 
    if($success) { 
        if(mysqli_num_rows(mysqli_query($con, "SELECT import_type FROM data_backup WHERE import_type = 'sentence'")) == 7) {
            $old = mysqli_query($con, "SELECT import_batch_id, import_batch_name FROM data_backup WHERE import_type = 'sentence' ORDER BY imported_at ASC LIMIT 1");
            $old = mysqli_fetch_assoc($old);
            $importID = $old["import_batch_id"];
            $importName = $old["import_batch_name"];

            mysqli_query($con, "DELETE FROM data_backup WHERE import_batch_id = '$importID'");
            unlink("../../../Backup/sentence/" . $importName);
        }

        $countAll = count($_SESSION["allSentences"]);
        $countValid = count($_SESSION["validSentences"]);
        $countInvalid = count($_SESSION["invalidSentences"]);
        $importedBy = $_COOKIE["user_id"];

        $fileName = $_SESSION["filePath"];
        $oldPath = "../../../Backup/sentence/temp/" . $fileName;
        $newPath = "../../../Backup/sentence/" . $fileName;
        $userID = $_COOKIE["user_id"];

        if (!rename($oldPath, $newPath)) {
            echo "<h2>Failed to move backup file from temp folder.</h2>";
        }

        mysqli_query($con, "UPDATE data_backup SET is_current_version = 0 WHERE import_type = 'sentence'");
        mysqli_query($con, "INSERT INTO data_backup (import_type, import_batch_name, total_records, successful_import, skipped_import, imported_by, is_current_version) 
        VALUES ('sentence', '$fileName', $countAll, $countValid, $countInvalid, '$userID', 1)")
?>
<table id="valid">
    <caption style="background-color: green;">Cards Successfully Uploaded</caption>
    <tr>
        <th id = 'short'>Code</th>
        <th>Traditional</th>
        <th>Simplified</th>
        <th>Pinyin</th>
        <th>English</th>
        <th>Indo</th>
    </tr>

    <?php
        $sentences = mysqli_query($con, "SELECT * FROM example_sentence");
        while ($sentence = mysqli_fetch_array($sentences)) {
            echo "<tr>";
            echo "<td>" . $sentence["sentence_code"] . "</td>";
            echo "<td>" . $sentence["chinese_tc"] . "</td>";
            echo "<td>" . $sentence["chinese_sc"] . "</td>";
            echo "<td>" . $sentence["pinyin"] . "</td>";
            echo "<td>" . $sentence["meaning_eng"] . "</td>";
            echo "<td>" . $sentence["meaning_ina"] . "</td>";
            echo "</tr>";
        }
    ?>
</table>

<!-- menampilkan data yang gagal ke upload (invalid) -->
<table>
    <caption style = "background-color: red;">Invalid Sentences</caption>
    <tr>
        <th id = 'short'>Code</th>
        <th>Traditional</th>
        <th>Simplified</th>
        <th>Pinyin</th>
        <th>English</th>
        <th>Indo</th>
        <th>Reason</th>
    </tr>
    <?php
        foreach($_SESSION["invalidSentences"] as $sentence) {
            $sentenceCode = $sentence["sentenceCode"];
            $traditional = $sentence["traditional"];
            $simplified = $sentence["simplified"];
            $pinyin = $sentence["pinyin"];
            $english = $sentence["english"];
            $indo = $sentence["indo"];
            $reason = $sentence["reason"];
            echo "<tr>";
                echo "<td id = 'short'>$sentenceCode</td>";
                echo "<td>$traditional</td>";
                echo "<td>$simplified</td>";
                echo "<td>$pinyin</td>";
                echo "<td>$english</td>";
                echo "<td>$indo</td>";
                echo "<td>$reason</td>";
            echo "</tr>";
        }

        unset($_SESSION["allSentences"]);
        unset($_SESSION["validSentences"]);
        unset($_SESSION["invalidSentences"]);
        unset($_SESSION["filePath"]);
    ?>
</table>
<?php }
else {
    echo "<h1>Upload Failed</h1>";
} ?>