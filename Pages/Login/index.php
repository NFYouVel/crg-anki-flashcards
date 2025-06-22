<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../../Logo/circle.png">
    <link rel="stylesheet" href="CSS/index.css">
</head>
<body>
    <?php include "../Global Assets/header.php"; ?>
    <div class="wrapper">
        <div class="form-side">
            <span class="h2">Login</span>
            <span class="description">Login in to your account</span>
            <form action="">
                <input type="email" name="email" placeholder="Email"> <br>
                <input type="password" name="email" placeholder="Password"> <br>
                <input type="submit" value="Log In" name="submit" class="submit">
            </form>
            <span class="alert">Wrong email or password. Please contact our admin</span>
        </div>
    </div>
</body>
</html>