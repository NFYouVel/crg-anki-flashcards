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
                    <span>Your New Password:</span>
                    <input type="password" name="password" placeholder="Your new password..." required><br>
                    <span>Confirm Your Password:</span>
                    <input type="password" name="password_confirmation" placeholder="Confirm your password..." required> <br>
                    <input type="submit" value="Change Password" name="submit" class="submit" id="submit">
                </form>
                <span class='alert' id="error" style='visibility: visible;'></span>

                <?php
                include "../../SQL_Queries/connection.php";
                $password = filter_input(INPUT_POST, 'password');
                $password_confirmation = filter_input(INPUT_POST, 'password_confirmation');
                $submit = filter_input(INPUT_POST, 'submit');
                $email = $_GET['email'];

                if ($password_confirmation && $password && $submit && (strlen($password) < 6 || strlen($password_confirmation) < 6)) {
                    echo "<script>alert('Your password can not less than 6 character.')</script>";
                } else {
                    if ($password_confirmation && $password && $submit) {
        
                        if ($password == $password_confirmation) {
                            $password_confirmation = password_hash($password_confirmation, PASSWORD_BCRYPT);
                            $query = "UPDATE users SET password_hash = '$password_confirmation' WHERE email = '$email'";
                            $query2 = "UPDATE users SET user_status = 'active' WHERE email = '$email'";
                            $result = mysqli_query($con, $query);
                            $result2 = mysqli_query($con, $query2);
                            echo "<script>
                    if (confirm('Are you sure with your password?')) {
                        window.location.href = 'index.php';
                    } else {
                        // Stay on current page or do nothing
                    }
                </script>";
                            exit;
                        } else {
                            echo "<script>document.getElementById('error').textContent = 'The Password Confirmation Does Not Match';</script>";
                        }
                    }
                }
                ?>
            </div>
        </div>

    </body>

    </html>