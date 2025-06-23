<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Overview</title>
    <style>
        #overview {
            width: 85%;
            margin-left: 15%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #overview > * {
            margin: 24px 24px;
        }
        h1 {
            color: white;
            font-size: 40px;
        }
        #heading {
            height: fit-content;
            width: 90%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #action {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        input {
            height: 40px;
            width: 250px;
            border-radius: 12px;
            border: 2px solid #e9a345;
            font-size: 20px;
        }
        input::placeholder {
            color: #e9a345;
            font-size: 20px;
        }
        .button {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            width: 150px;
            height: 50px;
            background-color: #ffa72a;
            border-radius: 25px;
            font-size: 24px;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        table {
            font-size: 18px;
            border-collapse: collapse;
        }
        th {
            color: white;
            background-color: #003b58;
        }
        th, td {
            border: 2px solid black;
            word-break: break-word;
        }
        td {
            padding: 5px;
        }
        tr:nth-child(even) {
            background-color: #838383;
        }
        tr:nth-child(odd) {
            background-color: #a5a5a5;
        }
        #email {
            white-space: normal;
            word-break: break-word;
        }
        #filter {
            display: flex;
            justify-content: start;
            gap: 24px;
        }
        select {
            appearance: none;
            width: 250px;
            padding: 10px 16px;
            border: 2px solid #e9a345;
            border-radius: 12px;
            background-color: white;
            font-size: 18px;
            color: #333;
            cursor: pointer;
        }

        select:focus {
            outline: none;
            border-color: #ffa72a;
            box-shadow: 0 0 5px #ffa72a;
        }

        select option[disabled] {
            color: #999;
        }

    </style>
    <script>
        function searchUser(str) {
            var xmlhttp;
            if (window.XMLHttpRequest != null) {
                xmlhttp = new XMLHttpRequest();
            }
            else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("users").innerHTML = xmlhttp.responseText;
                    console.log(str)
                }
            }
            xmlhttp.open("GET", "AJAX/search_user.php?search=" + str, true);
            xmlhttp.send();
        }
    </script>
</head>
<body>
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
    ?>
    <div id="overview">
        <div id="heading">
            <h1>User Overview</h1>
            <div id="action">
                <input type="text" placeholder = "&#128269;Search" onkeyup = "searchUser(this.value)">
                <a class = 'button' href = 'addUser.php'><span>Add</span></a>
                 <form action="addUser_batch.php" method="POST" enctype="multipart/form-data">
                    <input id = "file" name = "excel_file" style = "display: none" class = "button" type="file" onchange="this.form.submit()">
                    <label class = "button" for="file">Upload File</label>
                </form>
            </div>
        </div>
        
        <div id="filter">
            <select onchange = 'searchUser("filterRoles" + this.value)'>
                <option value="">Select Roles (Default)</option>
                <?php
                    $getRole = mysqli_query($con, "SELECT role_id, role_name FROM user_role");
                    while($role = mysqli_fetch_array($getRole)) {
                        $roleName = $role["role_name"];
                        $roleID = $role["role_id"];
                        echo "<option onclick = 'searchUser(this.value)' value = '$roleID'>$roleName</option>";
                    }
                ?>
            </select>
            <select onchange = 'searchUser("filterStatus" + this.value)'>
                    <option value="">Select Status (Default)</option>
                    <option value="pending">Pending</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="deleted">Deleted</option>
            </select>
        </div>

        <table id = 'users'>
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
                $getUsers = mysqli_query($con, "SELECT * FROM users");
                while($user = mysqli_fetch_array($getUsers)) {
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
                    if($resetPW == 1) {
                        $resetPW = "YES";
                    }
                    else {
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
        </table>
    </div>
</body>
<style>
    #overview_user {
        color: #ffa72a;
    }
</style>
</html>