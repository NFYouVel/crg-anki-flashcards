<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Overview</title>
    <style>
        #overview {
            border: 2px solid white;
            width: 100%;
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
        #button {
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
            width: 95%;
            font-size: 20px;
        }
        th {
            color: white;
            background-color: #003b58;
        }
        tr:nth-child(even) {
            background-color: #838383;
        }
        tr:nth-child(odd) {
            background-color: #a5a5a5;
        }
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>

    <div id="overview">
        <div id="heading">
            <h1>User Overview</h1>
            <div id="action">
                <input type="text" placeholder = "&#128269;Search">
                <a id = 'button' href = 'addUser.php'><span>Add</span></a>
                <a id = 'button'><span>Import</span></a>
            </div>
        </div>

        <table>
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
                include "../../SQL_Queries/connection.php";
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
                    $set = $user["character_set"];

                    echo "<tr>";
                        echo "<td>$id</td>";
                        echo "<td>$name</td>";
                        echo "<td>$email</td>";
                        echo "<td>$role</td>";
                        echo "<td>$status</td>";
                        echo "<td>$created</td>";
                        echo "<td>$updated</td>";
                        echo "<td>$lastReview</td>";
                        echo "<td>$deleted</td>";
                        echo "<td>$resetPW</td>";
                        echo "<td>$set</td>";
                        echo "<td>tes</td>";
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