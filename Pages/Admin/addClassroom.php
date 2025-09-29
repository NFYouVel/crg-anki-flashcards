<?php
    session_start();
    include_once "../../SQL_Queries/connection.php";
    $user_id = $_SESSION["user_id"];
    if(mysqli_fetch_assoc(mysqli_query($con, "SELECT role FROM users WHERE user_id = '$user_id'"))["role"] != 1) {
        header("Location: ../Login");
    }
?>
<?php
    include "Components/sidebar.php";
    include "../../SQL_Queries/connection.php";
    if(isset($_POST["name"])) {
        $userID = $_COOKIE["user_id"];
        $name = $_POST["name"];
        $desc = $_POST["desc"] ?? "";
        mysqli_query($con, "INSERT INTO classroom (name, description, created_by) VALUES ('$name', '$desc', '$userID')");

        $classroomID = mysqli_query($con, "SELECT classroom_id FROM classroom WHERE name = '$name'");
        $classroomID = mysqli_fetch_array($classroomID);
        $classroomID = $classroomID["classroom_id"];
        header("Location: assignClassroom.php?classroomID=$classroomID");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../Assets/Icons/1080.png">
    <title>Add Classroom</title>
    <style>
        h1 {
            color: white;
            font-size: 50px;
        }
        #heading {
            height: fit-content;
            width: 90%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #action {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        input {
            height: 40px;
            width: 250px;
            border-radius: 12px;
            border: 2px solid #e9a345;
            font-size: 20px;
        }
        input::placeholder {
            color: #e9a345;
            font-size: 20px;
        }
        .button {
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
        table h1 {
            margin: 12px;
            font-size: 30px;
        }
        textarea, input {
            height: 40px;
            width: 250px;
            border-radius: 12px;
            border: 2px solid #e9a345;
            font-size: 20px;
        }
        textarea {
            height: 100px;
        }
        input::placeholder {
            color: #e9a345;
            font-size: 20px;
        }
        td {
            width: 350px;
        }
    </style>
</head>
<body>
    <form method = "post" id = "container">
        <div id="heading">
            <h1>Create Classroom</h1>
            <button class = 'button'><span>Add</span></button>
        </div>
        <table>
            <tr>
                <td><h1>Classroom Name</h1></td>
                <td><input type="text" name = "name" required></td>
            </tr>
            <tr>
                <td><h1>Classroom Description</h1></td>
                <td><textarea name="desc"></textarea></td>
            </tr>
        </table>
    </form>
</body>
<style>
    #classroom {
        color: #ffa72a;
    }
</style>
</html>