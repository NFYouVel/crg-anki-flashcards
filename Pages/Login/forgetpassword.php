<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link rel="icon" href="../../Logo/circle.png">
    <link rel="stylesheet" href="CSS/index.css">
    <link rel="stylesheet" href="CSS/forgetpassword.css">
</head>

<body>
    <!-- Header -->
    <?php include "../Global Assets/header.php"; ?>

    <!-- Form -->
    <div class="wrapper">
        <div class="form-side">
            <span class="h2">Reset Password</span>
            <span class="description">Reset your account</span>
            <form method="post">
                <span>Your account name:</span>
                <input type="text" name="username" placeholder="Your username..."><br>
                <span>Your email:</span>
                <input type="email" name="email" placeholder="Your email..." required> <br>
                <input type="submit" value="Reset Password" name="submit" class="submit" id="submit">
            </form>
            <span class='alert' id="error" style='visibility: hidden;'>Wrong email or password. Please contact our admin</span>

            <?php
            include "../../SQL_Queries/connection.php";
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $submit = filter_input(INPUT_POST, 'input');
            $username = filter_input(INPUT_POST, 'username');

            if ($email && $username && $submit) {
                $query_username = "SELECT * FROM users WHERE name = '$username'";
                $result = mysqli_query($con, $query_username);
                $line = mysqli_fetch_assoc($result);

                if ($line) {
                    if ($line['email'] == $email) {
                        $text = "Location: newpassword.php?name=" . $username;
                        header($text);
                        exit;
                    } else {
                        echo "<script>document.getElementById('response').textContent = 'Email does not match the username.';</script>";
                    }
                } else {
                    echo "<script>document.getElementById('response').textContent = 'Email not found.';</script>";
                }
            }
            ?>
        </div>
    </div>

</body>

</html>