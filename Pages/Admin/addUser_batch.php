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
        }

        #container>* {
            margin: 24px 24px;
        }

        h1 {
            color: white;
        }
        table {
            font-size: 20px;
            border-collapse: collapse;
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
        }
        form {
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
    </style>
</head>

<body>
    <?php
    include "Components/sidebar.php";
    ?>
    <div id="container">
        <div id="header">
            <h1>Import User (Preview)</h1>
            <form method="post">
                <a href="overview_user.php" class="button">Cancel</a>
                <button class="button" name = "import">Import</button>
            </form>
        </div>
        <table>
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

            if (isset($_FILES['excel_file'])) {
                $id = 1;
                $fileTmpPath = $_FILES['excel_file']['tmp_name'];
                $fileName = $_FILES['excel_file']['name'];
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                $allowedExtensions = ['xls', 'xlsx'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    try {
                        $spreadsheet = IOFactory::load($fileTmpPath);
                        $sheet = $spreadsheet->getActiveSheet();
                        foreach ($sheet->getRowIterator() as $row) {
                            $index = $row->getRowIndex();
                            $name = $sheet->getCell("A$index")->getValue();
                            $email = $sheet->getCell("B$index")->getValue();
                            $role = $sheet->getCell("C$index")->getValue();
                            if($role == "") {
                                $role = "student";
                            }
                            $set = $sheet->getCell("D$index")->getValue();
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
            ?>
        </table>
    </div>
</body>

</html>