<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Deck</title>
    <style>
        form {
            color: white;
        }
    </style>
</head>

<body>
    <?php
    include "Components/sidebar.php";
    include "../../SQL_Queries/connection.php";
    if (isset($_POST["userID"])) {
        $userID = $_POST["userID"];
        $deckID = $_GET["deckID"];

        mysqli_query($con, "INSERT INTO junction_deck_user (deck_id, user_id) VALUES ('$deckID', '$userID')");

        $getCards = mysqli_query($con, "SELECT card_id FROM junction_deck_card WHERE deck_id = '$deckID'");
        $query = "INSERT INTO card_progress (user_id, card_id) VALUES ";
        $count = 0;
        while($card = mysqli_fetch_assoc($getCards)) {
            $cardID = $card["card_id"];

            if ($count == 35) {
                $count = 0;
                $query = substr($query, 0, -2);
                mysqli_query($con, $query);
                $query = "INSERT INTO card_progress (user_id, card_id) VALUES ";
            }
            if(mysqli_num_rows(mysqli_query($con, "SELECT user_id FROM card_progress WHERE card_id = '$cardID' AND user_id = '$userID'")) == 0) {
                $count++;
                $query .= "('$userID', '$cardID'), ";
            }
        }

        // mengirim query sisa
        if ($count > 0) {
            $query = substr($query, 0, -2);
            mysqli_query($con, $query);
        }
    }
    ?>
    <div id="container">
        <form method="post">
            <?php
            $getUsers = mysqli_query($con, "SELECT user_id, name FROM users");
            while ($user = mysqli_fetch_assoc($getUsers)) {
                echo "<input type = 'submit' name = 'userID' value = '{$user['user_id']}'> {$user['name']}<br>";
            }
            ?>
        </form>
    </div>
</body>

</html>