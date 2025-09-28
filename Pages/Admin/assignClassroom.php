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
    <title>Assign Users</title>
    <link rel="icon" href="../../Assets/Icons/1080.png">
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
        th {
            position: sticky;
            z-index: 200;
            top: 0;
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
            justify-content: space-between;
            width: 90%;
        }
        #container {
            display: flex;
            flex-direction: column;
            align-items: center;
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
        #action {
            display: flex;
            width: 90%;
            justify-content: start;
            align-items: center;
            margin-bottom: 24px;
        }
    </style>
    <?php
        include "../../SQL_Queries/connection.php";
        $classroomID = $_GET['classroomID'];
    ?>
    <script>
        function assignUsers(id, role) {
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
            xmlhttp.open("GET", "AJAX/assign_classroom.php?userID=" + id + "&userRole=" + role + "&classroomID=<?php echo $classroomID; ?>", true);
            xmlhttp.send();
        }

        function removeAssigned(remove) {
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
            xmlhttp.open("GET", "AJAX/assign_classroom.php?remove=" + remove + "&classroomID=<?php echo $classroomID; ?>", true);
            xmlhttp.send();
        }

        function searchUser(str) {
            var xmlhttp;
            if (window.XMLHttpRequest != null) {
                xmlhttp = new XMLHttpRequest();
            }
            else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("users").innerHTML = xmlhttp.responseText;
                    console.log(str)
                }
            }
            xmlhttp.open("GET", "AJAX/search_user_assignClassroom.php?search=" + str + "&classroomID=<?php echo $classroomID; ?>", true);
            xmlhttp.send();
        }
    </script>
</head>
<body>
    <?php
        include "Components/sidebar.php";
    ?>
    <div id="container">
        <?php
            $name = mysqli_query($con, "SELECT name FROM classroom WHERE classroom_id = '$classroomID'");
            $name = mysqli_fetch_array($name);
            $name = $name["name"];
            echo "<h1>Assign Classroom: $name</h1>";
        ?>
        <div id="action">
            <input type="text" placeholder = "&#128269;Search" onkeyup = "searchUser(this.value)">
        </div>
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

                        if(mysqli_num_rows(mysqli_query($con, "SELECT user_id FROM junction_classroom_user WHERE user_id = '$userID' AND classroom_id = '$classroomID'")) > 0) {
                            continue;
                        }
                        
                        echo "<tr>";
                            echo "<td>$name</td>";
                            echo "<td><div id = 'role'>";
                                echo "<button onclick=\"assignUsers('$userID', 3)\" class='button'>Student</button>";
                                echo "<button onclick=\"assignUsers('$userID', 2)\" class='button'>Teacher</button>";
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
                <?php
                    $getUsers = mysqli_query($con, "SELECT user_id, classroom_role_id FROM junction_classroom_user WHERE classroom_id = '$classroomID'");
                    while($user = mysqli_fetch_array($getUsers)) {
                        $userID = $user["user_id"];
                        $name = mysqli_query($con, "SELECT name FROM users WHERE user_id = '$userID'");
                        $name = mysqli_fetch_array($name);
                        $name = $name["name"];
                        $role = $user["classroom_role_id"];
                        $role = mysqli_query($con, "SELECT role_name FROM user_role WHERE role_id = $role");
                        $role = mysqli_fetch_array($role);
                        $role = $role["role_name"];

                        echo "<tr>";
                            echo "<td>$name</td>";
                            echo "<td>$role</td>";
                            echo "<td><button class = 'button' onclick=\"removeAssigned('$userID')\">Remove</button></td>";
                        echo "<tr>";
                    }
                ?>
            </table>
        </div>
    </div>

    <a href="classroom.php" style = "position: fixed; bottom: 24px; left: 30px; display: flex; justify-content: center; align-items: center;" class="button"><span>Back</span></a>
</body>
<style>
    #classroom {
        color: #ffa72a;
    }
</style>
</html>