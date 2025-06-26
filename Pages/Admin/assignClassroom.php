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
            width: 50%;
            justify-content: space-evenly;
            align-items: center;
            margin-bottom: 24px;
        }
    </style>
    <?php
        include "../../SQL_Queries/connection.php";
        $classroomID = $_GET['classroomID'];
    ?>
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
                    document.getElementById("assigned").innerHTML = xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET", "AJAX/assign_classroom.php?remove=" + remove, true);
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

        if(isset($_POST["submit"]) && isset($_SESSION["users"])) {
            $query = "INSERT INTO junction_classroom_user (classroom_id, user_id, classroom_role_id) VALUES ";
            foreach($_SESSION["users"] as $value) {
                $userID = $value["id"];
                $role = $value["role"];
                $role = mysqli_query($con, "SELECT role_id FROM user_role WHERE role_key = '$role'");
                $role = mysqli_fetch_array($role);
                $role = $role["role_id"];

                $query .= "('$classroomID', '$userID', $role), ";
            }
            $query = substr($query, 0, -2);
            if(mysqli_query($con, $query)) {
                echo "<script>alert('Users Succesfully Added to Classroom')</script>";
            } 
            else {
                echo "<script>alert('Users Faild to be Added to Classroom')</script>";
            }
            session_unset();
        }
    ?>
    <div id="container">
        <?php
            $name = mysqli_query($con, "SELECT name FROM classroom WHERE classroom_id = '$classroomID'");
            $name = mysqli_fetch_array($name);
            $name = $name["name"];
            echo "<h1 style = 'margin: 0;'>$name</h1>";
        ?>
        <h1>Assign Classroom</h1>
        <div id="action">
            <input type="text" placeholder = "&#128269;Search" onkeyup = "searchUser(this.value)">
            <form method = "post">
                <input name = "submit" type="submit" class = "button">
            </form>
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
                                echo "<button onclick=\"assignUsers('$userID', '$name', 'student')\" class='button'>Student</button>";
                                echo "<button onclick=\"assignUsers('$userID', '$name', 'teacher')\" class='button'>Teacher</button>";
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
                    if(isset($_SESSION["users"])) {
                        foreach($_SESSION["users"] as $value) {
                            echo "<tr>";
                                $id = $value["id"];
                                echo "<td>" . $value["name"] . "</td>";
                                echo "<td>" . $value["role"] . "</td>";
                                echo "<td><button class = 'button' onclick=\"removeAssigned('$id')\">Remove</button></td>";
                            echo "</tr>";
                        }
                    }
                ?>
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