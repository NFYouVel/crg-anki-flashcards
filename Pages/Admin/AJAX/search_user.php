<?php
    //mengirim data search bar melalui query string
    $search = $_GET["search"];
    include "../../../SQL_Queries/connection.php";

    //jika search bar kosong, makan semua user ditampilkan
    if ($search == "" || $search == "filterRoles" || $search == "filterStatus") {
        $getUsers = mysqli_query($con, "SELECT * FROM users");
    } 
    //filter berdasarkan role
    else if(str_contains($search, "filterRoles")) {
        $search = substr($search, 11);
        $getUsers = mysqli_query($con, "SELECT * FROM users WHERE role = $search");
    }
    //filter berdasarkan status
    else if(str_contains($search, "filterStatus")) {
        $search = substr($search, 12);
        $getUsers = mysqli_query($con, "SELECT * FROM users WHERE user_status = '$search'");
    }
    //filter berdasarkan nama / email
    else {
        $getUsers = mysqli_query($con, "SELECT * FROM users WHERE name LIKE '%$search%' OR email LIKE '%$search%'");
    }

    //jika hasil search tidak ada hasilnya, maka keluarkan pesan tidak ketemu
    if(mysqli_num_rows($getUsers) == 0) {
        echo "<h1>User Not Found</h1>";
    }
    //menampilkan hasil filter
    else {
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Name</th>";
        echo "<th>Email</th>";
        echo "<th>Role</th>";
        echo "<th>User Status</th>";
        echo "<th>Created At</th>";
        echo "<th>Updated At</th>";
        echo "<th>Last Review</th>";
        echo "<th>Deleted At</th>";
        echo "<th>Reset PW</th>";
        echo "<th>Character Set</th>";
        echo "<th>Treatment</th>";
        echo "</tr>";
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