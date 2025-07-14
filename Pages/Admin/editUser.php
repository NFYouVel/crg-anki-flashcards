<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="icon" href="../../Logo/circle.png">
    <style>
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
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
        option, select, input {
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
        select {
            width: 260px;
            height: 45px;
        }
        td {
            width: 350px;
        }
    </style>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>
    <?php
        include "../../SQL_Queries/connection.php";
        $id = $_GET["id"];
        $data = mysqli_query($con, "SELECT name, email, role, character_set FROM users WHERE user_id = '$id'");
        $data = mysqli_fetch_array($data);
        $name = $data["name"];
        $email = $data["email"];
        $role = $data["role"];
        $set = $data["character_set"];


        if(isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["role"]) && isset($_POST["set"])) {
            $newName = $_POST["name"];
            $newEmail = $_POST["email"];
            $newPassword = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $newRole = (int)$_POST["role"];
            $newSet = $_POST["set"];
            $time = date('Y-m-d H:i:s');
            if(mysqli_query($con, "UPDATE users SET name = '$newName', email = '$newEmail', password_hash = '$newPassword', role = $newRole, character_set = '$newSet', updated_at = CURRENT_TIMESTAMP WHERE user_id = '$id'")) {
                echo "<script>alert('Data berhasil di update')</script>";
            }
            else {
                echo "<script>alert('Data gagal di update')</script>";
            }
        }
    ?>

    <form id = "container" method = "post">
        <div id="heading">
            <h1>Edit User</h1>
            <div id="action">
                <a href="overview_user.php" id = "button">Cancel</a>
                <button id = "button">Save</button>
            </div>
        </div>
        <div id="data">
            <table>
                <tr>
                    <td><h1>Full Name</h1></td>
                    <td><input type="text" name = "name" placeholder = "Full Name" value = "<?php echo $name; ?>" required></td>
                </tr>
                <tr>
                    <td><h1>Email</h1></td>
                    <td><input type="email" name = "email" placeholder = "Email" value = "<?php echo $email; ?>" required></td>
                </tr>
                <tr>
                    <td><h1>Password</h1></td>
                    <td><input type="password" name = "password" value = "123456" placeholder = "Password" required></td>
                </tr>
                <tr>
                    <td><h1>User Role</h1></td>
                    <td>
                        <select name="role" required>
                            <?php
                                $getRoles = mysqli_query($con, "SELECT role_id, role_name FROM user_role");
                                while($roles = mysqli_fetch_array($getRoles)) {
                                    $roleID = $roles["role_id"];
                                    $roleName = $roles["role_name"];
                                    if($roleID == $role) {
                                        echo "<option value = '$roleID' selected>$roleName</option>";
                                    }
                                    else {
                                        echo "<option value = '$roleID'>$roleName</option>";
                                    }
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><h1>Character Set</h1></td>
                    <td>
                        <select name="set"  required>
                            <option <?php if($set == "simplified") echo "selected"; ?> value="simplified">Simplified</option>
                            <option <?php if($set == "traditional") echo "selected"; ?> value="traditional">Traditional</option>
                        </select>
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
    #user {
        color: #ffa72a;
    }
</style>
</html>