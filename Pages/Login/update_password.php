<?php
$email = $_GET['email'];
include "../../SQL_Queries/connection.php";

$newPassword = password_hash("crg1638", PASSWORD_BCRYPT);

$query = "UPDATE users 
          SET password_hash = '$newPassword', 
              updatedPasswordAt = NOW() 
          WHERE email = '$email'";
$result = mysqli_query($con, $query);

if ($result) {
    echo "success";
} else {
    echo "error";
}
?>
