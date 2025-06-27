<caption>Users</caption>
<tr>
    <th>Name</th>
    <th>Classroom Role</th>
</tr>
<?php
    include "../../../SQL_Queries/connection.php";
    $search = $_GET["search"];
    $classroomID = $_GET["classroomID"];

    if($search == "") {
        $getUsers = mysqli_query($con, "SELECT user_id, name FROM users");
    }
    else {
        $getUsers = mysqli_query($con, "SELECT user_id, name FROM users WHERE name LIKE '%$search%'");
    }

    if(mysqli_num_rows($getUsers) > 0) {
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
    }
    else {
        echo "<th colspan = '2'><h1>User Not Found</h1></th>";
    }
?>