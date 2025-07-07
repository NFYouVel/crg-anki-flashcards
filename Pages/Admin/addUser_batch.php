<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        #container {
            width: 85%;
            margin-left: 15%;
            padding: 24px 24px;
            box-sizing: border-box;
        }
        h1 {
            color: white;
        }
        table {
            width: 100%;
            font-size: 20px;
            border-collapse: collapse;
            margin-bottom: 48px;
        }
        th {
            color: white;
            background-color: #003b58;
        }
        th, td {
            border: 2px solid black;
            padding: 5px 10px;
        }
        td {
            padding: 5px;
        }
        tr:nth-child(even) {
            background-color: #838383;
        }
        tr:nth-child(odd) {
            background-color: #a5a5a5;
        }
        #header {
            display: flex;
            justify-content: space-between;
            align-items: center
        }
        #header > div {
            display: flex;
            gap: 16px;
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
        caption {
            color: white;
            border: 2px solid black;
        }
        #invalid {
            background-color: red;
        }
    </style>
    <script>
        //function ajax untuk upload user
        function uploadUsers() {
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
            xmlhttp.open("GET", "AJAX/batch_user.php", true);
            xmlhttp.send();
        }
    </script>
</head>

<body>
    <?php
        include "Components/sidebar.php";
    ?>
    <div id="container">
        <div id="header">
            <h1>Import User (Preview)</h1>
            <div>
                <a href="overview_user.php" class="button">Cancel</a>
                <!-- memanggil function ajax melalui tombol -->
                <button class="button" name = "import" onclick = "uploadUsers()">Import</button>
            </div>
        </div>
        <div id="tables">
            <table>
                <caption style = "background-color: white; color: black;">Uploaded Excel File</caption>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Character Set</th>
                </tr>
                <?php
                    include "../../SQL_Queries/connection.php";
                    require '../../Composer_Excel/vendor/autoload.php';
                    use PhpOffice\PhpSpreadsheet\IOFactory;

                    //read file excel
                    if (isset($_FILES['excel_file'])) {
                        $id = 1;
                        //membangun var session untuk data valid dan invalid
                        $validUsers = [];
                        $invalidUsers = [];
                        $fileTmpPath = $_FILES['excel_file']['tmp_name'];
                        $fileName = $_FILES['excel_file']['name'];
                        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                        $allowedExtensions = ['xls', 'xlsx'];

                        if (in_array($fileExtension, $allowedExtensions)) {
                            try {
                                $spreadsheet = IOFactory::load($fileTmpPath);
                                $sheet = $spreadsheet->getActiveSheet();
                                foreach ($sheet->getRowIterator() as $row) {
                                    //mengambil data dari setiap kolumn
                                    $index = $row->getRowIndex();
                                    if($index == 1) {
                                        continue;
                                    }
                                    $name = $sheet->getCell("A$index")->getValue();
                                    $email = $sheet->getCell("B$index")->getValue();
                                    $role = $sheet->getCell("C$index")->getValue();
                                    //jika cell role null, maka jadi student
                                    if($role == "") {
                                        $role = "student";
                                    }
                                    $set = $sheet->getCell("D$index")->getValue();
                                    //jika cell charset null, maka jadi simplified
                                    if($set == "") {
                                        $set = "simplified";
                                    }
                                    echo "<tr>";
                                        echo "<td>" . $id++ . "</td>";
                                        echo "<td>$name</td>";
                                        echo "<td>$email</td>";
                                        echo "<td>$role</td>";
                                        echo "<td>$set</td>";
                                    echo "</tr>";


                                    $reason = "";
                                    //check for invalid email format
                                    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        $reason .= "<p id = 'invalid'>Invalid Email Format</p>";
                                    }

                                    //check for duplicates in existing database
                                    if(mysqli_num_rows(mysqli_query($con, "SELECT email FROM users WHERE email = '$email'")) > 0) {
                                        $reason .= "<p id = 'invalid'>Email already exist in database</p>";
                                    }

                                    //check for duplicates in uploaded excel
                                    if(isset($validUsers[$email])) {
                                        $reason .= "<p id = 'invalid'>Email already exists in the excel file</p>";
                                    }

                                    //check for error in user role
                                    if(!in_array($role, ['student', 'teacher', 'admin'])) {
                                        $reason .= "<p id = 'invalid'>Invalid user role</p>";
                                    }

                                    //check for error in user character set
                                    if(!in_array($set, ['simplified', 'traditional'])) {
                                        $reason .= "<p id = 'invalid'>Invalid user character set</p>";
                                    }

                                    //logic untuk validasi data
                                    if($reason == "") {
                                        //membangun var session data valid
                                        $validUsers[$email] = [
                                            "name" => $name, 
                                            "email" => $email, 
                                            "role" => $role, 
                                            "set" => $set
                                        ];
                                    }
                                    else {
                                        //membangun var session data invalid
                                        $invalidUsers[$email] = [
                                            "name" => $name, 
                                            "email" => $email, 
                                            "role" => $role, 
                                            "set" => $set,
                                            "reason" => $reason
                                        ];
                                    }
                                }
                            } catch (Exception $e) {
                                echo "<h1>Error loading file: " . $e->getMessage() . "</h1>";
                            }
                        } else {
                            echo "<h1>Invalid file type. Only .xls and .xlsx files are allowed.</h1>";
                        }
                    } else {
                        echo "<h1>No file uploaded.</h1>";
                    }
                    
                    $_SESSION["validUsers"] = $validUsers;
                ?>
            </table>

            <table>
                <caption style = "background-color: green;">Valid Users</caption>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Character Set</th>
                </tr>
                <?php
                    $id = 1;
                    foreach($validUsers as $key => $value) {
                        echo "<tr>";
                            echo "<td>" . $id++ . "</td>";
                            echo "<td>" . $value["name"] . "</td>";
                            echo "<td>" . $value["email"] . "</td>";
                            echo "<td>" . $value["role"] . "</td>";
                            echo "<td>" . $value["set"] . "</td>";
                        echo "</tr>";
                    }
                ?>
            </table>

            <table>
                <caption style = "background-color: red;">Invalid Users</caption>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Character Set</th>
                    <th>Reason</th>
                </tr>
                <?php
                    $id = 1;
                    foreach($invalidUsers as $key => $value) {
                        echo "<tr>";
                            echo "<td>" . $id++ . "</td>";
                            echo "<td>" . $value["name"] . "</td>";
                            echo "<td>" . $value["email"] . "</td>";
                            echo "<td>" . $value["role"] . "</td>";
                            echo "<td>" . $value["set"] . "</td>";
                            echo "<td>" . $value["reason"] . "</td>";
                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
    </div>
</body>
<style>
    #user {
        color: #ffa72a;
    }
    #overview_user {
        color: #ffa72a;
    }
</style>
</html>