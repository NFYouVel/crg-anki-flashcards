<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="icon" href="../../Logo/circle.png">
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
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
    ?>
</head>
<body>
    <?php
        if(isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["role"]) && isset($_POST["set"])) {
            $name = $_POST["name"];
            $email = $_POST["email"];
            $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $role = (int)$_POST["role"];
            $set = $_POST["set"];
            $remarks = $_POST["remarks"];
            //cek apakah sudah ada user dengan email tersebut
            if(mysqli_num_rows(mysqli_query($con, "SELECT email FROM users WHERE email = '$email'")) == 0) {
                if(mysqli_query($con, "INSERT INTO users (name, email, password_hash, role, character_set, remarks) VALUES
                ('$name', '$email', '$password', $role, '$set', '$remarks')")) {
                    echo "<script>alert('Akun berhasil ditambahkan')</script>";
                }
                else {
                    echo "<script>alert('Akun Gagal Ditambahkan')</script>";
                }
            }
            else {
                echo "<script>alert('Akun dengan email $email sudah terdapat di database')</script>";
            }

        }
    ?>
    <form method = "post">
        <div id="heading">
            <h1>Add User</h1>
            <div id="action">
                <a href="overview_user.php" id = "button">Cancel</a>
                <button type = "submit" id = "button">Save</button>
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
                    <td><h1>Remarks</h1></td>
                    <td><input type="text" name = "remarks" placeholder = "Remarks"></td>
                </tr>
                <tr>
                    <td><h1>User Role</h1></td>
                    <td>
                        <!-- pilihan roles diambil dari tabel user_role -->
                        <select name="role" required>
                            <?php
                                $getRoles = mysqli_query($con, "SELECT role_id, role_name FROM user_role");
                                while($role = mysqli_fetch_array($getRoles)) {
                                    $roleID = $role["role_id"];
                                    $roleName = $role["role_name"];

                                    if($roleName == "Student") {
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
    #user {
        color: #ffa72a;
    }
    #user + ul{
        display: block;
    }
</style>
</html>