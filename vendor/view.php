<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        $con = mysqli_connect("localhost", "root", "", "anki");
        $count = mysqli_query($con, "SELECT COUNT(*) as total FROM cards");
        $count = mysqli_fetch_array($count);
        $count = $count["total"];
        echo "<h1>$count</h1>";
    ?>
    <table border='1' cellpadding='5'>
        <tr>
            <th>count</th>
            <th>card_id</th>
            <th>chinese_tc</th>
            <th>chinese_sc</th>
            <th>priority</th>
            <th>pinyin</th>
            <th>word class</th>
            <th>meaning eng</th>
            <th>meaning ind</th>
            <th>sentence count</th>
        </tr>
        <?php
            $count = 0;
            $result = mysqli_query($con, "SELECT * FROM cards");
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>$count</td>";
                foreach($row as $var) {
                    echo "<td>$var</td>";
                }
                $count++;
                echo "</tr>";
            }
        ?>
    </table>
</body>
</html>