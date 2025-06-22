<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>User Status</th>
    <th>Created At</th>
    <th>Updated At</th>
    <th>Last Review</th>
    <th>Deleted At</th>
    <th>Reset PW</th>
    <th>Character Set</th>
    <th>Treatment</th>
</tr>

<?php
    $search = $_GET["search"];
    include "../../SQL_Queries/connection.php";
    if ($search == "") {
        $getUsers = mysqli_query($con, "SELECT * FROM users");
    } 
    else if(str_contains($search, "filter")) {
        
    }
    else {
        $getUsers = mysqli_query($con, "SELECT * FROM users WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR user_status LIKE '$search' OR role = '$search'");
    }
    while ($user = mysqli_fetch_array($getUsers)) {
        $id = $user["user_id"];
        $name = $user["name"];
        $email = $user["email"];
        $role = $user["role"];
        $role = mysqli_query($con, "SELECT role_key FROM user_role WHERE role_id = $role");
        $role = mysqli_fetch_array($role);
        $role = ucfirst($role["role_key"]);
        $status = $user["user_status"];
        $created = $user["created_at"];
        $updated = $user["updated_at"];
        $lastReview = $user["last_login"];
        $deleted = $user["deleted_at"];
        $resetPW = $user["force_password_reset"];
        if ($resetPW == 1) {
            $resetPW = "YES";
        } else {
            $resetPW = "-";
        }
        $set = $user["character_set"];

        echo "<tr>";
        echo "<td>$id</td>";
        echo "<td>$name</td>";
        echo "<td id = 'email'>$email</td>";
        echo "<td>$role</td>";
        echo "<td>$status</td>";
        echo "<td>$created</td>";
        echo "<td>$updated</td>";
        echo "<td>$lastReview</td>";
        echo "<td>$deleted</td>";
        echo "<td>$resetPW</td>";
        echo "<td>$set</td>";
        echo "<td>
                <a href = 'editUser.php?id=$id'>Edit</a>
                <a href = 'deleteUser.php?id=$id'>Delete</a>
            </td>";
        echo "</tr>";
    }
?>