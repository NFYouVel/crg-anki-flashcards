 <tr>
    <th>Classroom Name</th>
    <th>Teachers Count</th>
    <th>Students Count</th>
    <th>Action</th>
</tr>
<?php
    include "../../../SQL_Queries/connection.php";
    $search = $_GET["search"];
    if($search == "") {
        $getClassroom = mysqli_query($con, "SELECT classroom_id, name FROM classroom");
    }
    else {
        $getClassroom = mysqli_query($con, "SELECT classroom_id, name FROM classroom WHERE name LIKE '%$search%'");
    }
    if(mysqli_num_rows($getClassroom) == 0) {
        echo "<h1>Classroom Not Found</h1>";
    }
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