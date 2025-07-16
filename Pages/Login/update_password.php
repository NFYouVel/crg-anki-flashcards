<?php
$email = $_GET['email'];
include "../../SQL_Queries/connection.php";

$newPassword = password_hash("123456", PASSWORD_BCRYPT);

$query = "UPDATE users SET password_hash = '$newPassword' WHERE email = '$email'";
$result = mysqli_query($con, $query);

if ($result) {
    echo "success";
} else {
    echo "error";
}
?>
