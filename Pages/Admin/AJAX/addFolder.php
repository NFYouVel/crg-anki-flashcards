<?php
    include "../../../SQL_Queries/connection.php";
    function getDecks($parentID) {
        global $con;
        if($parentID == "root") {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE parent_deck_id IS NULL ORDER BY name ASC");
        }
        else {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name ASC");
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

                    if($deck["is_leaf"] == 0) {
                        if(mysqli_num_rows(mysqli_query($con, "SELECT is_leaf FROM decks WHERE parent_deck_id = '$deckID' AND is_leaf = 1")) > 0) {
                            echo "
                            <li>
                                <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                                <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_deck'> $name</span>
                            ";
                        }
                        else if(mysqli_num_rows(mysqli_query($con, "SELECT is_leaf FROM decks WHERE parent_deck_id = '$deckID'")) == 0 ) {
                            echo "
                            <li>
                                <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                                <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'empty'> $name</span>
                            ";
                        }
                        else {
                            echo "
                            <li>
                                <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                                <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_folder'> $name</span>
                            ";
                        }
                    }
                    else {
                        echo "
                        <li>
                            <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/deck.png' class = 'icon' id = 'deck'> $name</span>
                        ";
                    }
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
    $type = $_GET["type"];

    if($parentID == "masterDeck") {
        mysqli_query($con, "INSERT INTO decks (name, created_by_user_id, is_leaf) VALUES ('$name', '$userID', $type)");
    }
    else {
        mysqli_query($con, "INSERT INTO decks (name, created_by_user_id, parent_deck_id, is_leaf) VALUES ('$name', '$userID', '$parentID', $type)");
    }
?>

<li>
    <span class="toggle"><img src="../../Assets//Icons/minimizeDeck.png" class = "min"></span> 
    <span class="label" id = "masterDeck"><img src="../../Assets//Icons/folder.png" class = "icon"> Master Deck Folder</span>
    <?php
        getDecks("root");
    ?>
</li>