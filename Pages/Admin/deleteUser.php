<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link rel="icon" href="../../Logo/circle.png">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #wrapper {
            background-color: #143d59;
            width: 75%;
            height: 250px;
            border-radius: 25px;
            padding: 12px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
        }
        h1 {
            color: white;
            text-align: center;
            margin: 0;
        }
        table {
            width: 100%;
            font-size: 20px;
            text-align: center;
        }
        th {
            color: white;
            background-color: #ffa72a;
        }
        tr {
            background-color: #a5a5a5;
        }
        td {
            word-break: break-word;
        }
        form {
            width: 100%;
            display: flex;
            justify-content: space-evenly;
        }
        #button {
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
    </style>
</head>
    <?php
        include "../../SQL_Queries/connection.php";
        if(isset($_POST["delete"])) {
            $deleteID = $_POST["delete"];
            
            if(mysqli_query($con, "UPDATE users SET user_status = 'deleted', deleted_at = CURRENT_TIMESTAMP WHERE user_id = '$deleteID'")) {
                echo "<script>alert('Data berhasil dihapus'); window.location.href = 'overview_user.php';</script>";
            }
            else {
                echo "<script>alert('Data gagal dihapus)</script>";
            }
        }
    ?>
<body>
    <?php
        include "Components/sidebar.php";
    ?>
    <?php
        $id = $_GET["id"];
    ?>
    <div id="container">
        <div id = "wrapper">
            <h1>Are you sure you want to delete this user?</h1>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>User Status</th>
                </tr>
                <tr>
                    <?php
                        $data = mysqli_query($con, "SELECT name, email, role, user_status FROM users WHERE user_id = '$id'");
                        $data = mysqli_fetch_array($data);
                        $name = $data["name"];
                        $email = $data["email"];
                        $role = $data["role"];
                        $status = $data["user_status"];

                        echo "<td>$name</td>";
                        echo "<td>$email</td>";
                        echo "<td>$role</td>";
                        echo "<td>$status</td>";
                    ?>
                </tr>
            </table>
            <form method = "post">
                <a id = 'button' href = 'overview_user.php'>Cancel</a>
                <button id = 'button' name = "delete" value = "<?php echo $id; ?>" onclick = "">Confirm</button>
            </form>
        </div>
    </div>
</body>
<style>
    #overview_user {
        color: #ffa72a;
    }
    #user {
        color: #ffa72a;
    }
</style>
</html>