
<?php
    session_start();
?>

<caption>Assigned Users</caption>
<tr>
    <th>Name</th>
    <th>Role</th>
    <th>Action</th>
</tr>
<?php
    if (!isset($_SESSION["users"])) {
        $_SESSION["users"] = [];
    }

    if(isset($_GET["remove"])) {
        unset($_SESSION["users"][$_GET["remove"]]);
    }
    else {
        $id = $_GET["id"];
        $name = $_GET["name"];
        $role = $_GET["role"];

        $_SESSION["users"][$id] = [
            "id" => $id,
            "name" => $name,
            "role" => $role
        ];
    }
    
    foreach($_SESSION["users"] as $value) {
        echo "<tr>";
            $id = $value["id"];
            echo "<td>" . $value["name"] . "</td>";
            echo "<td>" . $value["role"] . "</td>";
            echo "<td><button class = 'button' onclick=\"removeAssigned('$id')\">Remove</button></td>";
        echo "</tr>";
    }
?>