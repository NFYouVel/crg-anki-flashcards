<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Classroom</title>
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
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";

        $classroomID = $_GET["classroomID"];
        if(isset($_POST["name"])) {
            $name = $_POST["name"];
            mysqli_query($con, "UPDATE classroom SET name = '$name' WHERE classroom_id = '$classroomID'");
        }
        if(isset($_POST["desc"])) {
            $desc = $_POST["desc"];
            mysqli_query($con, "UPDATE classroom SET description = '$desc' WHERE classroom_id = '$classroomID'");
        }
        if(isset($_POST["desc"]) || isset($_POST["name"])) {
            echo "<script>alert('Classroom Succesfully Updated')</script>";
        }
        $classroomData = mysqli_query($con, "SELECT name, description FROM classroom WHERE classroom_id = '$classroomID'");
        $classroomData = mysqli_fetch_array($classroomData);
        $classroomName = $classroomData["name"];
        $classroomDesc = $classroomData["description"];
    ?>
    <form method = "post" id = "container">
        <div id="heading">
            <h1>Edit Classroom</h1>
            <div style = "display: flex; justify-content: space-evenly; align-items: center; gap: 16px;">
                <a href = "classroom.php" class = "button">Cancel</a>
                <button class = 'button'><span>Confirm</span></button>
            </div>
        </div>
        <table>
            <tr>
                <td><h1>Classroom Name</h1></td>
                <td><input type="text" name = "name" value = "<?php echo $classroomName; ?>"></td>
            </tr>
            <tr>
                <td><h1>Classroom Description</h1></td>
                <td><textarea name="desc"><?php echo $classroomDesc; ?></textarea></td>
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