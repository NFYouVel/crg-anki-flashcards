<?php
    session_start();
    include_once "../../SQL_Queries/connection.php";
    $user_id = $_SESSION["user_id"];
    if(mysqli_fetch_assoc(mysqli_query($con, "SELECT role FROM users WHERE user_id = '$user_id'"))["role"] != 1) {
        header("Location: ../Login");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom</title>
    <link rel="icon" href="../../Logo/circle.png">
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
            border: 1px solid black;
        }
        table {
            font-size: 24px;
            width: 100%;
            border-collapse: collapse;
        }
        th {
            color: white;
            background-color: #003b58;
        }
        th, td {
            border: 2px solid black;
            padding: 8px 16px;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #838383;
        }
        tr:nth-child(odd) {
            background-color: #a5a5a5;
        }
        th {
            position: sticky;
            z-index: 200;
            top: 0;
        }
        #classroomAction {
            display: flex;
            justify-content: space-evenly;
            gap: 8px;
        }
    </style>
    <script>
        function searchClassroom(str) {
            var xmlhttp;
            if (window.XMLHttpRequest != null) {
                xmlhttp = new XMLHttpRequest();
            }
            else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("tables").innerHTML = xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET", "AJAX/search_classroom.php?search=" + str, true);
            xmlhttp.send();
        }
    </script>
</head>
<body>
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
    ?>
    <div id="container">
        <div id="heading">
            <h1>Classroom</h1>
            <div id="action">
                <input type="text" placeholder = "&#128269;Search" onkeyup = "searchClassroom(this.value)">
                <a class = 'button' href = 'addClassroom.php'><span>Add</span></a>
            </div>
        </div>

        <table id = "tables">
            <tr>
                <th>Classroom Name</th>
                <th>Teachers Count</th>
                <th>Students Count</th>
                <th>Action</th>
            </tr>
            <?php
                $getClassroom = mysqli_query($con, "SELECT classroom_id, name FROM classroom");
                while($classroom = mysqli_fetch_array($getClassroom)) {
                    $classroomID = $classroom["classroom_id"];
                    $classroomName = $classroom["name"];

                    $teacherCount = mysqli_query($con, "SELECT COUNT(*) as total FROM junction_classroom_user WHERE classroom_id = '$classroomID' AND classroom_role_id = 2");
                    $teacherCount = mysqli_fetch_array($teacherCount);
                    $teacherCount = $teacherCount["total"];

                    $studentCount = mysqli_query($con, "SELECT COUNT(*) as total FROM junction_classroom_user WHERE classroom_id = '$classroomID' AND classroom_role_id = 3");
                    $studentCount = mysqli_fetch_array($studentCount);
                    $studentCount = $studentCount["total"];

                    echo "<tr>";
                        echo "<td>$classroomName</td>";
                        echo "<td>$teacherCount</td>";
                        echo "<td>$studentCount</td>";
                        echo "<td><div id = 'classroomAction'>
                            <a class = 'button' href = 'editClassroom.php?classroomID=$classroomID'>Edit</a>
                            <a href = 'assignClassroom.php?classroomID=$classroomID' style = 'font-size: 20px;' class = 'button'>Assign Users</a>
                        </div></td>";
                    echo  "</tr>";
                }
            ?>
        </table>
    </div>
</body>
<style>
    #classroom {
        color: #ffa72a;
    }
</style>
</html>