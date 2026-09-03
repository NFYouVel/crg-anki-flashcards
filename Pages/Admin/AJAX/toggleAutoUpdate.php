<?php
include "../../../SQL_Queries/connection.php";
$deckId = $_GET["deckID"];

$getDeck = mysqli_query($con, "SELECT auto_update FROM decks WHERE deck_id = '$deckId'");
$getDeck = mysqli_fetch_assoc($getDeck);
$autoUpdate = $getDeck["auto_update"];

$newValue = $autoUpdate ? 0 : 1;
mysqli_query($con, "UPDATE decks SET auto_update = '$newValue' WHERE deck_id = '$deckId'");

function getDecks($parentID)
{
    global $con;
    if ($parentID == "root") {
        $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf, auto_update FROM decks WHERE parent_deck_id IS NULL AND name != 'Main Deck' ORDER BY name ASC");
    } else {
        $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf, auto_update FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name ASC");
    }
    if (mysqli_num_rows($getDecks) > 0) {
        if ($parentID == "root") {
            echo "<ul class = 'maximized' style = 'height: fit-content;'>";
        } else {
            echo "<ul>";
        }
        while ($deck = mysqli_fetch_assoc($getDecks)) {
            $deckID = $deck["deck_id"];
            $name = $deck["name"];
            $autoUpdate = $deck["auto_update"];
            $updateIconStyle = $autoUpdate ? "margin-left: 8px;" : "margin-left: 8px; filter: brightness(0.4);";
            $updateIcon = "<img src='../../Assets/icons/update-icon.png' alt='' class='icon' style='$updateIconStyle' onclick='event.stopPropagation(); toggleAutoUpdate(\"$deckID\");'>";

            if ($deck["is_leaf"] == 0) {
                if (mysqli_num_rows(mysqli_query($con, "SELECT is_leaf FROM decks WHERE parent_deck_id = '$deckID' AND is_leaf = 1")) > 0) {
                    echo "
                        <li>
                            <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                            <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_deck'> $name $updateIcon</span>
                        ";
                } else if (mysqli_num_rows(mysqli_query($con, "SELECT is_leaf FROM decks WHERE parent_deck_id = '$deckID'")) == 0) {
                    echo "
                        <li>
                            <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                            <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'empty'> $name $updateIcon</span>
                        ";
                } else {
                    echo "
                        <li>
                            <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                            <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_folder'> $name $updateIcon</span>
                        ";
                }
            } else {
                echo "
                    <li>
                        <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/deck.png' class = 'icon' id = 'deck'> $name</span>
                    ";
            }
            getDecks($deckID);
            echo "</li>
                        ";
        }
        echo "</ul>";
    }
}

echo "
    <li>
        <span class = 'toggle'><img src = '../../Assets//Icons/minimizeDeck.png' class = 'min'></span>
        <span class = 'label selectedDeck' id = 'masterDeck'><img src = '../../Assets//Icons/folder.png' class = 'icon'> Master Deck Folder</span>
    ";
getDecks("root");
echo "</li>";
