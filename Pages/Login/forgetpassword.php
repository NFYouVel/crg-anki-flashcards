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
            <form method="post">
                <h1>Forget Password</h1>
                <div class="inputbox">
                    <input type="text" name="username" required>
                    <label for="username">Username</label>
                </div>
                <div class="inputbox">
                    <input type="email" name="email" required id="username">
                    <label for="email">Email</label>
                </div>
                <button class="btn" name="input" value="Submit">Submit</button>
            </form>
            <div id="response" style="color:red; text-align:center; margin-top:10px;"></div>
        </div>
    </div>

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
                        $text = "Location: newpassword.php?name=".$username;
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
</body>

</html>