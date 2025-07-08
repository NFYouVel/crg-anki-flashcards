<?php
    session_start();
    $mode = $_GET["mode"];
?>
<?php if($mode == "preview"){ ?>
    <table>
        <caption style = "background-color: white; color: black;">Preview</caption>
        <tr>
            <th>Card ID</th>
            <th>Sentence Code</th>
            <th>Priority</th>
        </tr>
        <?php
            foreach($_SESSION["allLinks"] as $links) {
                $cardID = $links["cardID"];
                $sentenceCode = $links["sentenceCode"];
                $priority = $links["priority"];
                echo "<tr>";
                    echo "<td>$cardID</td>";
                    echo "<td>$cardID</td>";
                    echo "<td>$priority</td>";
                echo "</tr>";
            }
        ?>
    </table>
<?php } else if($mode == "valid"){ ?>
    <table>
        <caption style = "background-color: green;">Valid Links</caption>
        <tr>
            <th>Card ID</th>
            <th>Sentence Code</th>
            <th>Priority</th>
        </tr>
        <?php
            foreach($_SESSION["validLinks"] as $links) {
                $cardID = $links["cardID"];
                $sentenceCode = $links["sentenceCode"];
                $priority = $links["priority"];
                echo "<tr>";
                    echo "<td>$cardID</td>";
                    echo "<td>$cardID</td>";
                    echo "<td>$priority</td>";
                echo "</tr>";
            }
        ?>
    </table>
<?php } else if($mode == "invalid"){ ?>
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
        ?>
    </table>
<?php } ?>  
