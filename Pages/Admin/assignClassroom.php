<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Users</title>
    <style>
        h1 {
            color: white;
            font-size: 50px;
            text-align: center;
        }
        table {
            font-size: 24px;
            border-collapse: collapse;
            width: 35%;
            height: fit-content;
        }
        th {
            color: white;
            background-color: #003b58;
        }
        th, td {
            border: 2px solid black;
            padding: 8px 16px;
        }
        tr:nth-child(even) {
            background-color: #838383;
        }
        tr:nth-child(odd) {
            background-color: #a5a5a5;
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
            display: inline-block;
        }
        #role {
            display: flex;
            gap: 8px;
        }
        caption {
            color: white;
        }
        #tables {
            display: flex;
            justify-content: space-around;
        }
    </style>
    <script>
        function assignUsers(id, name, role) {
            var xmlhttp;
            if (window.XMLHttpRequest != null) {
                xmlhttp = new XMLHttpRequest();
            }
            else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("assigned").innerHTML = xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET", "AJAX/assign_classroom.php?id=" + id + "&name=" + name + "&role=" + role, true);
            xmlhttp.send();
        }
    </script>
</head>
<body>
    <?php
        include "Components/sidebar.php";
        include "../../SQL_Queries/connection.php";
        $classroomID = $_GET['classroomID'];
    ?>
    <div id="container">
        <h1>Assign Classroom</h1>
        <div id="tables">
            <table id="users">
                <caption>Users</caption>
                <tr>
                    <th>Name</th>
                    <th>Classroom Role</th>
                </tr>
                <?php
                    $getUsers = mysqli_query($con, "SELECT user_id, name FROM users");
                    while($user = mysqli_fetch_array($getUsers)) {
                        $userID = htmlspecialchars($user["user_id"], ENT_QUOTES);
                        $name = htmlspecialchars($user["name"], ENT_QUOTES);
                        
                        echo "<tr>";
                            echo "<td>$name</td>";
                            echo "<td><div id = 'role'>";
                                echo "<button onclick = 'assignUsers('$userID', '$name', 'student')' class='button'>Student</button>";
                                echo "<button onclick = 'assignUsers('$userID', '$name', 'teacher')' class='button'>Teacher</button>";


                                // echo "<button onclick = 'assignUsers('$userID', '$name', 'student')' class='button'>Student</button>";
                                // echo "<button onclick = 'assignUsers('$userID', '$name', 'teacher')' class='button'>Teacher</button>";
                            echo "</div></td>";
                        echo "</tr>";

                    }
                ?>
            </table>

            <table id="assigned">
                <caption>Assigned Users</caption>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </table>
        </div>
    </div>
</body>
<style>
    #classroom {
        color: #ffa72a;
    }
</style>
</html>