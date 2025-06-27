<?php
    include "../../../SQL_Queries/connection.php";
    if(isset($_GET["remove"])) {
        $removeID = $_GET["remove"];
        $classroomID = $_GET["classroomID"];

        mysqli_query($con, "DELETE FROM junction_classroom_user WHERE user_id = '$removeID' AND classroom_id = '$classroomID'");
    }
    else {
        $userID = $_GET["userID"];
        $role = $_GET["userRole"];
        $classroomID = $_GET["classroomID"];
    
        mysqli_query($con, "INSERT INTO junction_classroom_user (classroom_id, user_id, classroom_role_id) VALUES ('$classroomID', '$userID', $role)");
    }
?>

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