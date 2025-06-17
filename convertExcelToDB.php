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
                $query = "INSERT INTO cards (card_id, chinese_tc, chinese_sc, priority, pinyin, word_class, meaning_eng, meaning_ina) VALUES ";
                
                // echo "<h2>Contents of $fileName</h2>";
                // echo "<table border='1' cellpadding='5'>";

                $count = -1;
                foreach ($sheet->getRowIterator() as $row) {
                    $count++;
                    if($count == 0) {
                        continue;
                    }
                    if($count == 41) {
                        $count = 1;
                        $query = substr($query, 0, strlen($query) - 2);

                        // echo $query . "<br><br><br><br>";
                        if(mysqli_query($con, $query)) {
                            echo "<h1>Berhasil</h1>";
                        }
                        else {
                            echo "<h1>Gagal</h1>";
                        }

                        $query = "INSERT INTO cards (card_id, chinese_tc, chinese_sc, priority, pinyin, word_class, meaning_eng, meaning_ina) VALUES ";
                    }
                    // echo "<tr>";
                    $word = "(";
                    foreach ($row->getCellIterator() as $cell) {
                        // echo "<td>" . htmlspecialchars($cell->getValue()) . "</td>";
                        $value = $cell->getValue();
                        if(is_numeric($value)) {
                            $word .= (int) $value . ", ";
                        }
                        else {
                            $word .= "'" . mysqli_real_escape_string($con, $value ?? ""). "', ";
                        }
                        
                    }
                    $word = substr($word, 0, strlen($word) - 2);
                    $word .= ")";
                    $query .= "$word, ";

                    // echo "</tr>";
                }

                $query = substr($query, 0, strlen($query) - 2);

                // echo $query . "<br><br><br><br>";
                if(mysqli_query($con, $query)) {
                    echo "<h1>Berhasil</h1>";
                }
                else {
                    echo "<h1>Gagal</h1>";
                }

                // $query = substr($query, 0, strlen($query) - 2);

                // echo "</table>";

                // echo $query;
                
                
            } catch (Exception $e) {
                echo "Error loading file: " . $e->getMessage();
            }
        } else {
            echo "Invalid file type. Only .xls and .xlsx files are allowed.";
        }
    } else {
        echo "No file uploaded.";
    }
    ?>

</body>

</html>