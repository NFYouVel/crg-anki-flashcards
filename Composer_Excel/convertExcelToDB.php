<!DOCTYPE html>
<html>

<head>
    <title>Upload and Read Excel</title>
</head>

<body>
    <h2>Upload Excel File (.xlsx or .xls)</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="excel_file" accept=".xls,.xlsx" required>
        <button type="submit">Upload and Read</button>
    </form>

    <?php
    $con = mysqli_connect("localhost", "root", "", "anki");
    require 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\IOFactory;

    if (isset($_FILES['excel_file'])) {
        $fileTmpPath = $_FILES['excel_file']['tmp_name'];
        $fileName = $_FILES['excel_file']['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        $allowedExtensions = ['xls', 'xlsx'];

        if (in_array($fileExtension, $allowedExtensions)) {
            try {
                $spreadsheet = IOFactory::load($fileTmpPath);
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->getRowIterator() as $row) {
                    foreach ($row->getCellIterator() as $cell) {
                        // echo "<td>" . htmlspecialchars($cell->getValue()) . "</td>";
                        $value = $cell->getValue();
                        
                    }
                }
            } catch (Exception $e) {
                echo "<h1>Error loading file: " . $e->getMessage() . "</h1>";
            }
        } else {
            echo "<h1>Invalid file type. Only .xls and .xlsx files are allowed.</h1>";
        }
    } else {
        echo "<h1>No file uploaded.<h1>";
    }
    ?>

</body>

</html>