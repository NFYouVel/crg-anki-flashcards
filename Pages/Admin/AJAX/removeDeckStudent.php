<?php
    include "../../../SQL_Queries/connection.php";
    $userID = $_GET["userID"];

    function deleteParentsUser($deckID) {
        global $con, $userID;
        $parentID = mysqli_query($con, "SELECT parent_deck_id FROM decks WHERE deck_id = '$deckID'");
        $parentID = mysqli_fetch_assoc($parentID);
        $parentID = $parentID["parent_deck_id"];

        mysqli_query($con, "DELETE FROM junction_deck_user WHERE deck_id = '$deckID' AND user_id = '$userID'");

        if($parentID !== null) {
            deleteParentsUser($parentID);
        }
    }
    function deleteDeck($parentID) {
        global $con, $userID;
        $getDecks = mysqli_query($con, "SELECT deck_id, parent_deck_id, is_leaf FROM decks WHERE deck_id = '$parentID'");
        if (mysqli_num_rows($getDecks) > 0) {
            if ($deck = mysqli_fetch_assoc($getDecks)) {
                $deckID = $deck["deck_id"];
                $isLeaf = $deck["is_leaf"];

                $check = mysqli_query($con, "SELECT 1 FROM junction_deck_user WHERE deck_id = '$deckID' AND user_id = '$userID'");
                if (mysqli_num_rows($check) > 0) {
                    // Delete from junction_deck_user
                    deleteParentsUser($deckID);
                    // mysqli_query($con, "DELETE FROM junction_deck_user WHERE deck_id = '$deckID' AND user_id = '$studentID'");

                    mysqli_query($con, "
                        UPDATE card_progress cp
                        JOIN junction_deck_card jdc ON cp.card_id = jdc.card_id
                        SET cp.is_assigned = 0
                        WHERE jdc.deck_id = '$deckID' AND cp.user_id = '$userID'
                    ");
                }

                // Recursively delete children
                $getChildren = mysqli_query($con, "SELECT deck_id FROM decks WHERE parent_deck_id = '$deckID' ORDER BY name ASC");
                while ($children = mysqli_fetch_assoc($getChildren)) {
                    deleteDeck($children["deck_id"]);
                }
            }
        }
    }

    $deckID = $_GET["deckID"];
    deleteDeck($deckID);
    $ownedDecks = [];

    function getDeckAncestor($deckID) {
        global $con, $userID, $ownedDecks;

        if ($deckID === "root") {
            $getDecks = mysqli_query($con, "SELECT deck_id FROM junction_deck_user WHERE user_id = '$userID'");
            while ($deck = mysqli_fetch_assoc($getDecks)) {
                getDeckAncestor($deck["deck_id"]);
            }
        } 
        else {
            if (!in_array($deckID, $ownedDecks)) {
                $ownedDecks[] = $deckID;
            }
            $result = mysqli_query($con, "SELECT parent_deck_id FROM decks WHERE deck_id = '$deckID'");
            if ($row = mysqli_fetch_assoc($result)) {
                $parent = $row["parent_deck_id"];
                if ($parent !== null) {
                    getDeckAncestor($parent);
                }
            }
        }
    }
    getDeckAncestor("root");

    function getDecks($parentID) {
        global $userID, $con, $ownedDecks;
        if($parentID == "root") {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE parent_deck_id IS NULL ORDER BY name ASC");
        }
        else {
            $getDecks = mysqli_query($con, "SELECT deck_id, name, parent_deck_id, is_leaf FROM decks WHERE parent_deck_id = '$parentID' ORDER BY name ASC");
        }
        if(mysqli_num_rows($getDecks) > 0) {
            if($parentID == "root" || in_array($parentID, $ownedDecks)) {
                echo "<ul class = 'maximized' style = 'height: fit-content;'>";
            }
            else {
                echo "<ul>";
            }
                while($deck = mysqli_fetch_assoc($getDecks)) {
                    $deckID = $deck["deck_id"];
                    $name = $deck["name"];
                    $owned = (mysqli_num_rows(mysqli_query($con, "SELECT 1 FROM junction_deck_user WHERE user_id = '$userID' AND deck_id = '$deckID'")) > 0) ? true : false;

                    if($deck["is_leaf"] == 0) {
                        if(mysqli_num_rows(mysqli_query($con, "SELECT is_leaf FROM decks WHERE parent_deck_id = '$deckID' AND is_leaf = 1")) > 0) {
                            if($owned) {
                                echo "
                                <li>
                                    <span class = 'toggle'><img src = '../../Assets//Icons/minimizeDeck.png' class = 'min'></span>
                                    <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_deck'> $name </span>
                                    <button class = 'action' onclick = 'removeDeck($(this))'>✅</button>
                                ";
                            }
                            else {
                                echo "
                                <li>
                                    <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                                    <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_deck'> $name </span>
                                    <button class = 'action' onclick = 'addDeck($(this))'>❌</button>
                                ";
                            }
                        }
                        else {
                            if($owned) {
                                echo "
                                <li>
                                    <span class = 'toggle'><img src = '../../Assets//Icons/minimizeDeck.png' class = 'min'></span> 
                                    <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_folder'> $name </span>
                                    <button class = 'action' onclick = 'removeDeck($(this))'>✅</button>
                                ";
                            }
                            else {
                                echo "
                                <li>
                                    <span class = 'toggle'><img src = '../../Assets//Icons/maximizeDeck.png' class = 'min'></span>
                                    <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/folder.png' class = 'icon' id = 'folder_folder'> $name </span>
                                    <button class = 'action' onclick = 'addDeck($(this))'>❌</button>
                                ";
                            }
                        }
                    }
                    else {
                        if($owned) {
                            echo "
                            <li>
                                <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/deck.png' class = 'icon' id = 'deck'> $name </span>
                                <button class = 'action' onclick = 'removeDeck($(this))'>✅</button>
                            ";
                        }
                        else {
                            echo "
                            <li>
                                <span class = 'label' id = '$deckID'><img src = '../../Assets//Icons/deck.png' class = 'icon' id = 'deck'> $name </span>
                                <button class = 'action' onclick = 'addDeck($(this))'>❌</button>
                            ";
                        }
                    }
                        getDecks($deckID);
                    echo"</li>";  
                }
            echo "</ul>";
        }
    }
?>

<li>
    <span class="toggle"><img src="../../Assets//Icons/minimizeDeck.png" class = "min"></span> 
    <span class="label selectedDeck" id = "masterDeck"><img src="../../Assets//Icons/folder.png" class = "icon"> Master Deck Folder</span>
    <?php
        getDecks("root");
    ?>
</li>