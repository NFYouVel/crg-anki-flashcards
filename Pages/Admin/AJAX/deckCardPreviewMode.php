<?php
    session_start();
    $mode = $_GET["mode"];
?>
<?php if($mode == "preview"){ ?>
     <table>
        <caption style = "background-color: white; color: black;">Uploaded Excel File</caption>
        <tr>
            <th>No</th>
            <th>Card ID</th>
            <th>Traditional</th>
            <th>Simplified</th>
            <th>Prio</th>
            <th>Pinyin</th>
            <th>Word Class</th>
            <th>English</th>
            <th>Indo</th>
        </tr>
        <?php
            $id = 1;
            foreach($_SESSION["allCards"] as $key => $value) {
                if(isset($_SESSION["validCards"][$key])) {
                    echo "<tr style = 'background-color: green;'>";
                }
                else if(isset($_SESSION["invalidCards"][$key])) {
                    echo "<tr style = 'background-color: red;'>";
                }
                echo "
                    <td>" . $id++ . "</td>
                    <td>" . $value["cardID"] . "</td>
                    <td>" . $value["traditional"] . "</td>
                    <td>" . $value["simplified"] . "</td>
                    <td>" . $value["priority"] . "</td>
                    <td>" . $value["pinyin"] . "</td>
                    <td>" . $value["class"] . "</td>
                    <td class = 'long'>" . $value["english"] . "</td>
                    <td class = 'long'>" . $value["indo"] . "</td>
                </tr>";
            }
        ?>
    </table>
<?php } else if($mode == "valid"){ ?>
    <table>
        <caption style = "background-color: green;">Valid Users</caption>
        <tr>
            <th>No</th>
            <th>Card ID</th>
            <th>Traditional</th>
            <th>Simplified</th>
            <th>Prio</th>
            <th>Pinyin</th>
            <th>Word Class</th>
            <th>English</th>
            <th>Indo</th>
        </tr>
        <?php
            $id = 1;
            foreach($_SESSION["validCards"] as $key => $value) {
                echo "
                <tr>
                    <td>" . $id++ . "</td>
                    <td>" . $value["cardID"] . "</td>
                    <td>" . $value["traditional"] . "</td>
                    <td>" . $value["simplified"] . "</td>
                    <td>" . $value["priority"] . "</td>
                    <td>" . $value["pinyin"] . "</td>
                    <td>" . $value["class"] . "</td>
                    <td class = 'long'>" . $value["english"] . "</td>
                    <td class = 'long'>" . $value["indo"] . "</td>
                </tr>";
            }
        ?>
    </table>
<?php } else if($mode == "invalid"){ ?>
    <table>
        <caption style = "background-color: red;">Invalid Users</caption>
        <tr>
            <th>No</th>
            <th>Card ID</th>
            <th>Traditional</th>
            <th>Simplified</th>
            <th>Prio</th>
            <th>Pinyin</th>
            <th>Word Class</th>
            <th>English</th>
            <th>Indo</th>
            <th>Reason</th>
        </tr>
        <?php
            $id = 1;
            foreach($_SESSION["invalidCards"] as $key => $value) {
                echo "
                <tr>
                    <td>" . $id++ . "</td>
                    <td>" . $value["cardID"] . "</td>
                    <td>" . $value["traditional"] . "</td>
                    <td>" . $value["simplified"] . "</td>
                    <td>" . $value["priority"] . "</td>
                    <td>" . $value["pinyin"] . "</td>
                    <td>" . $value["class"] . "</td>
                    <td class = 'long'>" . $value["english"] . "</td>
                    <td class = 'long'>" . $value["indo"] . "</td>
                    <td class = 'long'>" . $value["reason"] . "</td>
                </tr>";
            }
        ?>
    </table>
<?php } ?>  
