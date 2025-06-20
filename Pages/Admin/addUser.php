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

        if(isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["role"]) && isset($_POST["set"])) {
            include "../../SQL_Queries/connection.php";
            $name = $_POST["name"];
            $email = $_POST["email"];
            $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $role = (int)$_POST["role"];
            $set = $_POST["set"];

            if(mysqli_query($con, "INSERT INTO users (name, email, password_hash, role, character_set) VALUES
            ('$name', '$email', '$password', $role, '$set')")) {
                echo "<script>alert('Akun berhasil ditambahkan')</script>";
            }
        }
    ?>

    <form method = "post">
        <div id="heading">
            <h1>Add User</h1>
            <div id="action">
                <a href="" id = "button">Cancel</a>
                <button id = "button">Save</button>
            </div>
        </div>
        <div id="data">
            <table>
                <tr>
                    <td><h1>Full Name</h1></td>
                    <td><input type="text" name = "name" placeholder = "Full Name" required></td>
                </tr>
                <tr>
                    <td><h1>Email</h1></td>
                    <td><input type="email" name = "email" placeholder = "Email" required></td>
                </tr>
                <tr>
                    <td><h1>Password</h1></td>
                    <td><input type="password" name = "password" value = "123456" placeholder = "Password" required></td>
                </tr>
                <tr>
                    <td><h1>User Role</h1></td>
                    <td>
                        <select name="role"  required>
                            <option value=1>Student</option>
                            <option value=0>Admin</option>
                            <option value=2>Teacher</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><h1>Character Set</h1></td>
                    <td>
                        <select name="set"  required>
                            <option value="simplified">Simplified</option>
                            <option value="traditional">Traditional</option>
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
</style>
</html>