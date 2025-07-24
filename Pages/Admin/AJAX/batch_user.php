<table>
    <caption style = "background-color: green;">Users Succesfully Imported</caption>
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Character Set</th>
        <th>Remarks</th>
    </tr>
    <?php
        session_start();
        include "../../../SQL_Queries/connection.php";
        //data batch user diambil dari session yang sudah dibangun pada addUser_batch.php
        $validUsers = $_SESSION["validUsers"];
        $id = 1;
        //membangun query insert
        $query = "INSERT INTO users (name, email, password_hash, role, character_set, remarks) VALUES ";
        foreach($validUsers as $key => $value) {
            $name = $value["name"];
            $email = $value["email"];
            $password = password_hash("123456", PASSWORD_BCRYPT);
            $role = $value["role"];
            $roleID = mysqli_query($con, "SELECT role_id FROM user_role WHERE role_key = '$role'");
            $roleID = mysqli_fetch_array($roleID);
            $roleID = (int)$roleID["role_id"];
            $set = $value["set"];
            $remarks = $value["remarks"];

            $query .= "('$name', '$email', '$password', $roleID, '$set', '$remarks'), ";

            echo "<tr>";
                echo "<td>" . $id++ . "</td>";
                echo "<td>" . $name . "</td>";
                echo "<td>" . $email . "</td>";
                echo "<td>" . $role . "</td>";
                echo "<td>" . $set . "</td>";
                echo "<td>" . $remarks . "</td>";
            echo "</tr>";
        }

        $query = substr($query, 0, -2);
    ?>
</table>
<?php
    if(mysqli_query($con, $query)) {
        echo "<h1>Upload Successful</h1>";
        unset($_SESSION["validUsers"]);
    }
    else {
        echo "<h1>Upload Failed</h1>";
    }
?>