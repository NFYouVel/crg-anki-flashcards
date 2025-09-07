<?php
    session_start();
    include_once "../../SQL_Queries/connection.php";
    $user_id = $_SESSION["user_id"];
    if(mysqli_fetch_assoc(mysqli_query($con, "SELECT role FROM users WHERE user_id = '$user_id'"))["role"] != 1) {
        header("Location: ../Login");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles</title>
    <link rel="icon" href="../../Logo/circle.png">
    <style>
        #container {

        }
        h1 {
            color: white;
            font-size: 40px;
        }
        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            width: 100%;
            font-size: 20px;
            border-collapse: collapse;
            margin-bottom: 48px;
        }
        th {
            color: white;
            background-color: #003b58;
        }
        th, td {
            border: 2px solid black;
            padding: 5px 10px;
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
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>
    <div id="container">
        <div id="header">
            <h1>Role Overview</h1>
            <a class = "button" href="addRole.php">Add Role</a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Role Key</th>
                <th>Role Name</th>
                <th>Role Description</th>
                <th>Created At</th>
            </tr>

            <?php
                include "../../SQL_Queries/connection.php";
                $getRoles = mysqli_query($con, "SELECT * FROM user_role");
                while($role = mysqli_fetch_array($getRoles)) {
                    $roleID = $role["role_id"];
                    $roleKey = $role["role_key"];
                    $roleName = $role["role_name"];
                    $roleDesc = $role["role_description"];
                    $created = $role["created_at"];

                    echo "<tr>";
                        echo "<td>$roleID</td>";
                        echo "<td>$roleKey</td>";
                        echo "<td>$roleName</td>";
                        echo "<td>$roleDesc</td>";
                        echo "<td>$created</td>";
                    echo "</tr>";
                }
            ?>
        </table>
    </div>
</body>
<style>
    #role {
        color: #ffa72a;
    }
    #user {
        color: #ffa72a;
    }
    #user + ul{
        display: block;
    }
</style>
</html>