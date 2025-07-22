<?php
    session_start();
    $mode = $_GET["mode"];
?>
<?php if($mode == "preview"){ ?>
     <table>
        <caption style = "background-color: white; color: black;">Uploaded Excel File</caption>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Character Set</th>
        </tr>
        <?php
            $id = 1;
            foreach($_SESSION["allUsers"] as $key => $value) {
                echo "<tr>";
                    echo "<td>" . $id++ . "</td>";
                    echo "<td>" . $value["name"] . "</td>";
                    echo "<td>" . $value["email"] . "</td>";
                    echo "<td>" . $value["role"] . "</td>";
                    echo "<td>" . $value["set"] . "</td>";
                echo "</tr>";
            }
        ?>
    </table>
<?php } else if($mode == "valid"){ ?>
    <table>
        <caption style = "background-color: green;">Valid Users</caption>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Character Set</th>
        </tr>
        <?php
            $id = 1;
            foreach($_SESSION["validUsers"] as $key => $value) {
                echo "<tr>";
                    echo "<td>" . $id++ . "</td>";
                    echo "<td>" . $value["name"] . "</td>";
                    echo "<td>" . $value["email"] . "</td>";
                    echo "<td>" . $value["role"] . "</td>";
                    echo "<td>" . $value["set"] . "</td>";
                echo "</tr>";
            }
        ?>
    </table>
<?php } else if($mode == "invalid"){ ?>
    <table>
        <caption style = "background-color: red;">Invalid Users</caption>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Character Set</th>
            <th>Reason</th>
        </tr>
        <?php
            $id = 1;
            foreach($_SESSION["invalidUsers"] as $key => $value) {
                echo "<tr>";
                    echo "<td>" . $id++ . "</td>";
                    echo "<td>" . $value["name"] . "</td>";
                    echo "<td>" . $value["email"] . "</td>";
                    echo "<td>" . $value["role"] . "</td>";
                    echo "<td>" . $value["set"] . "</td>";
                    echo "<td>" . $value["reason"] . "</td>";
                echo "</tr>";
            }
        ?>
    </table>
<?php } ?>  
