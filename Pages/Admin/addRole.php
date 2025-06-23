<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 85%;
            margin-left: 15%;
        }
        form > * {
            margin: 24px 24px;
        }
        h1 {
            color: white;
            font-size: 40px;
        }
        #heading {
            display: flex;
            height: fit-content;
            width: 90%;
            align-items: center;
            justify-content: space-between;
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
            font-family: 'Arial', sans-serif;
            font-size: 24px;
            cursor: pointer;
        }
        #action {
            display: flex;
            gap: 24px;
        }
        span {
            font-size: 40px;
            color: white;
        }
        table h1 {
            margin: 12px;
        }
        option, select, input, textarea {
            height: 40px;
            width: 250px;
            border-radius: 12px;
            border: 2px solid #e9a345;
            font-size: 20px;
        }
        option {
            border-radius: 0;
        }
        input::placeholder {
            color: #e9a345;
            font-size: 20px;
        }
        textarea {
            height: 100px;
        }
        td {
            width: 350px;
        }

    </style>
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
    ?>
</head>
<body>
    <?php
        if(isset($_POST["roleKey"]) || isset($_POST["roleName"]) || isset($_POST["roleDesc"])) {
            $roleKey = $_POST["roleKey"];
            $roleName = $_POST["roleName"];
            $roleDesc = $_POST["roleDesc"];

            if(mysqli_query($con, "INSERT INTO user_role (role_key, role_name, role_description) VALUES ('$roleKey', '$roleName', '$roleDesc')")) {
                echo "<script>alert('Role berhasil ditambahkan')</script>";
            }
            else {
                echo "<script>alert('Role gagal ditambahkan')</script>";
            }
        }
    ?>

    <form method = "post">
        <div id="heading">
            <h1>Add Role</h1>
            <div id="action">
                <a href="overview_user.php" id = "button">Cancel</a>
                <button id = "button">Save</button>
            </div>
        </div>
        <div id="data">
            <table>
                <tr>
                    <td><h1>Role Key</h1></td>
                    <td><input type="text" name = "roleKey" placeholder = "Role Key" required></td>
                </tr>
                <tr>
                    <td><h1>Role Name</h1></td>
                    <td><input type="text" name = "roleName" placeholder = "Role Name" required></td>
                </tr>
                <tr>
                    <td><h1>Role Description</h1></td>
                    <td>
                        <textarea name="roleDesc"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</body>
<style>
    #overview_user {
        color: #ffa72a;
    }
</style>
</html>