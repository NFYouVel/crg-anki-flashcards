<?php
    include "../../../SQL_Queries/connection.php";
    function getDecks($parentID) {
        global $con;
        if($parentID == "root") {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id FROM decks WHERE parent_deck_id IS NULL ORDER BY name ASC");
        }
        else {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name ASC");
        }
        if(mysqli_num_rows($getDecks) > 0) {
            if($parentID == "root") {
                echo "<ul class = 'maximized' style = 'height: fit-content;'>";
            }
            else {
                echo "<ul>";
            }
                while($deck = mysqli_fetch_assoc($getDecks)) {
                    $deckID = $deck["deck_id"];
                    $name = $deck["name"];

                    echo "
                        <li>
                            <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                            <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon'> $name</span>
                        ";
                        getDecks($deckID);
                    echo"</li>
                    ";  
                }
            echo "</ul>";
        }
    }

    $userID = $_COOKIE["user_id"];
    $name = $_GET["name"];
    $parentID = $_GET["parent"];

    if($parentID == "masterDeck") {
        mysqli_query($con, "INSERT INTO decks (name, created_by_user_id) VALUES ('$name', '$userID')");
    }
    else {
        mysqli_query($con, "INSERT INTO decks (name, created_by_user_id, parent_deck_id) VALUES ('$name', '$userID', '$parentID')");
    }
?>

<li>
    <span class="toggle"><img src="../../Assets//Icons/minimizeDeck.png" class = "min"></span> 
    <span class="label" id = "masterDeck"><img src="../../Assets//Icons/folder.png" class = "icon"> Master Deck Folder</span>
    <?php
        getDecks("root");
    ?>
</li>