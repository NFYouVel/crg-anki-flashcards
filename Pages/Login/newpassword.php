<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link rel="stylesheet" href="CSS/forgetpassword.css">
</head>

<body>
    <!-- Header -->
    <?php include "../Global Assets/header.php"; ?>

    <!-- Forget Password Form -->
    <div class="container">
        <div class="wrapper">
            <form method="post" id="loginForm">
                <h1>New Password</h1>
                <div class="inputbox">
                    <input type="password" name="password" required id="username">
                    <label for="username">Password</label>
                </div>
                <div class="inputbox">
                    <input type="password" name="password_confirmation" required>
                    <label for="password">Confirm Password</label>
                </div>
                <button class="btn" name="input" value="Submit">Submit</button>
            </form>
            <div id="response" style="color:red; text-align:center; margin-top:10px;"></div>
        </div>
    </div>
    <?php
    include "../../SQL_Queries/connection.php";
    $password = filter_input(INPUT_POST, 'password');
    $password_confirmation = filter_input(INPUT_POST, 'password_confirmation');

    if ($password_confirmation && $password) {

        if ($password == $password_confirmation) {
            $password_confirmation = password_hash($password_confirmation, PASSWORD_BCRYPT);
            $username = $_GET['name'];
            $query = "UPDATE users SET password_hash = '$password_confirmation' WHERE name = '$username'";
            $result = mysqli_query($con, $query);
            echo "<script>
            if (confirm('Are you sure with your password?')) {
                window.location.href = 'index.php';
            } else {
                // Stay on current page or do nothing
            }
        </script>";
        exit;
        } else {
            echo "<script>document.getElementById('response').textContent = 'Your confirm password is incorrect';</script>";
        }
    }
    ?>
</body>

</html>