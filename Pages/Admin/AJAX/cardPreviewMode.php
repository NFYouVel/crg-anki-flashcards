<?php
session_start();
include "../convertPinyin.php";
$mode = $_GET["mode"];
?>
<?php if ($mode == "preview") { ?>
    <table id="preview">
        <caption style="background-color: white; color: black;">Uploaded Excel File</caption>
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
        //jika page ke refresh, tidak perlu nge read ulang file, tapi mengambil dari session yang dibuat sebelum ke refresh
        foreach ($_SESSION["allCards"] as $key => $value) {
            echo "<tr>";
            echo "<td>" . $value["cardID"] . "</td>";
            echo "<td>" . $value["traditional"] . "</td>";
            echo "<td>" . $value["simplified"] . "</td>";
            echo "<td>" . convert($value["priority"]) . "</td>";
            echo "<td>" . $value["pinyin"] . "</td>";
            echo "<td>" . $value["class"] . "</td>";
            echo "<td id = 'long'>" . $value["english"] . "</td>";
            echo "<td id = 'long'>" . $value["indo"] . "</td>";
            echo "<td>0</td>";
            echo "</tr>";
        }
        ?>
    <?php } else if ($mode == "valid") { ?>
        <table id="valid">
            <caption style="background-color: green;">Valid Cards</caption>
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
            //menampilkan hasil validasi card
            foreach ($_SESSION["validCards"] as $key => $value) {
                echo "<tr>";
                echo "<td>" . $value["cardID"] . "</td>";
                echo "<td>" . $value["traditional"] . "</td>";
                echo "<td>" . $value["simplified"] . "</td>";
                echo "<td>" . $value["priority"] . "</td>";
                echo "<td>" . convert($value["pinyin"]) . "</td>";
                echo "<td>" . $value["class"] . "</td>";
                echo "<td id = 'long'>" . $value["english"] . "</td>";
                echo "<td id = 'long'>" . $value["indo"] . "</td>";
                echo "<td>0</td>";
                echo "</tr>";
            }
            ?>
        </table>
    <?php } else if ($mode == "invalid") { ?>
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
            //menampilkan kartu2 yang tidak valid
            foreach ($_SESSION["invalidCards"] as $key => $value) {
                echo "<tr>";
                echo "<td>" . $value["cardID"] . "</td>";
                echo "<td>" . $value["traditional"] . "</td>";
                echo "<td>" . $value["simplified"] . "</td>";
                echo "<td>" . $value["priority"] . "</td>";
                echo "<td>" . convert($value["pinyin"]) . "</td>";
                echo "<td>" . $value["class"] . "</td>";
                echo "<td id = 'long'>" . $value["english"] . "</td>";
                echo "<td id = 'long'>" . $value["indo"] . "</td>";
                echo "<td>0</td>";
                echo "<td>" . $value["reason"] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    <?php } ?>