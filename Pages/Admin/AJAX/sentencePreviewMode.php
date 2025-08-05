<?php
    session_start();
    $mode = $_GET["mode"];
?>
<?php if($mode == "preview"){ ?>
    <table>
        <caption style = "background-color: white; color: black;">Preview</caption>
        <tr>
            <th id = 'short'>Code</th>
            <th>Traditional</th>
            <th>Simplified</th>
            <th>Pinyin</th>
            <th>English</th>
            <th>Indo</th>
        </tr>
        <?php
            foreach($_SESSION["allSentences"] as $key => $sentence) {
                $sentenceCode = $sentence["sentenceCode"];
                $traditional = $sentence["traditional"];
                $simplified = $sentence["simplified"];
                $pinyin = $sentence["pinyin"];
                $english = $sentence["english"];
                $indo = $sentence["indo"];
                if(isset($_SESSION["validSentences"][$key])) {
                    echo "<tr style = 'background-color: green;'>";
                }
                else if(isset($_SESSION["invalidSentences"][$key])) {
                    echo "<tr style = 'background-color: red;'>";
                }
                    echo "<td id = 'short'>$sentenceCode</td>";
                    echo "<td>$traditional</td>";
                    echo "<td>$simplified</td>";
                    echo "<td>$pinyin</td>";
                    echo "<td>$english</td>";
                    echo "<td>$indo</td>";
                echo "</tr>";
            }
        ?>
    </table>
<?php } else if($mode == "valid"){ ?>
    <table>
        <caption style = "background-color: green;">Valid Sentences</caption>
        <tr>
            <th id = 'short'>Code</th>
            <th>Traditional</th>
            <th>Simplified</th>
            <th>Pinyin</th>
            <th>English</th>
            <th>Indo</th>
        </tr>
        <?php
            foreach($_SESSION["validSentences"] as $sentence) {
                $sentenceCode = $sentence["sentenceCode"];
                $traditional = $sentence["traditional"];
                $simplified = $sentence["simplified"];
                $pinyin = $sentence["pinyin"];
                $english = $sentence["english"];
                $indo = $sentence["indo"];
                echo "<tr>";
                    echo "<td id = 'short'>$sentenceCode</td>";
                    echo "<td>$traditional</td>";
                    echo "<td>$simplified</td>";
                    echo "<td>$pinyin</td>";
                    echo "<td>$english</td>";
                    echo "<td>$indo</td>";
                echo "</tr>";
            }
        ?>
    </table>
<?php } else if($mode == "invalid"){ ?>
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
        ?>
    </table>
<?php } ?>  
