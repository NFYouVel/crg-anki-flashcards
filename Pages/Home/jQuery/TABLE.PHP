<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            display: flex;
            gap: 50px;
            font-size: 24px;
        }
        table, td {
            border: 1px solid black;
        }
        th, td {
            width: 300px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php
        include "../../../SQL_Queries/connection.php";
    ?>
    <table>
        <caption>Users</caption>
        <tr>
            <th>Name</th>
        </tr>
        <?php
            $decks = mysqli_query($con, 'SELECT deck.name
            FROM junction_deck_user AS junction 
            JOIN decks AS deck ON junction.deck_id = deck.deck_id');
            while($deck = mysqli_fetch_assoc($decks)) {
                echo "
                <tr>
                    <td>{$deck["name"]}</td>
                </tr>";
            }
        ?>
    </table>

    <table>
        <caption>Classroom</caption>
        <tr>
            <th>Name</th>
        </tr>
        <?php
            $decks = mysqli_query($con, 'SELECT deck.name
            FROM junction_deck_classroom AS junction 
            JOIN decks AS deck ON junction.deck_id = deck.deck_id');
            while($deck = mysqli_fetch_assoc($decks)) {
                echo "
                <tr>
                    <td>{$deck["name"]}</td>
                </tr>";
            }
        ?>
    </table>
</body>
</html>